<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\TestPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPackageController extends Controller
{
    public function index()
    {
        $packages = TestPackage::with('createdBy')
            ->withCount('questions')
            ->latest()
            ->paginate(15);

        return view('admin.paket.index', compact('packages'));
    }

    public function create()
    {
        $subjects  = Subject::all();
        $questions = Question::with(['subject', 'topic'])
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('admin.paket.create', compact('subjects', 'questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:200',
            'class_level'      => 'required|in:6,9,12',
            'duration_minutes' => 'required|integer|min:10',
            'type'             => 'required|in:free,premium',
            'status'           => 'required|in:draft,published',
            'is_tryout'        => 'sometimes|boolean',
            'question_ids'     => 'required|array|min:1',
            'question_ids.*'   => 'exists:questions,id',
        ]);

        $packageName = $request->name;
        if ($request->boolean('is_tryout') && !preg_match('/try\s*out/i', $packageName)) {
            $packageName = 'Try Out - ' . $packageName;
        }

        $package = TestPackage::create([
            'name'             => $packageName,
            'description'      => $request->description,
            'class_level'      => $request->class_level,
            'total_questions'  => count($request->question_ids),
            'duration_minutes' => $request->duration_minutes,
            'type'             => $request->type,
            'is_randomized'    => $request->boolean('is_randomized'),
            'available_from'   => $request->available_from,
            'available_until'  => $request->available_until,
            'status'           => $request->status,
            'created_by'       => Auth::id(),
        ]);

        // Attach questions
        foreach ($request->question_ids as $order => $questionId) {
            $package->questions()->attach($questionId, ['order_number' => $order + 1]);
        }

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket latihan berhasil dibuat!');
    }

    public function edit(TestPackage $package)
    {
        $package->load('questions');
        $subjects  = Subject::all();
        $questions = Question::with(['subject', 'topic'])
            ->where('status', 'active')->get();

        return view('admin.paket.edit', compact('package', 'subjects', 'questions'));
    }

    public function update(Request $request, TestPackage $package)
    {
        $request->validate([
            'name'             => 'required|string|max:200',
            'duration_minutes' => 'required|integer|min:10',
            'status'           => 'required|in:draft,published',
        ]);

        $package->update($request->except(['_token', '_method', 'question_ids']));

        if ($request->filled('question_ids')) {
            $package->questions()->detach();
            foreach ($request->question_ids as $order => $questionId) {
                $package->questions()->attach($questionId, ['order_number' => $order + 1]);
            }
            $package->update(['total_questions' => count($request->question_ids)]);
        }

        return redirect()->route('admin.paket.index')
            ->with('success', 'Paket latihan berhasil diperbarui!');
    }

    public function destroy(TestPackage $package)
    {
        $package->questions()->detach();
        $package->delete();
        return back()->with('success', 'Paket berhasil dihapus!');
    }

    public function importForm()
    {
        $subjects = Subject::with('topics')->get();
        return view('admin.paket.import', compact('subjects'));
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Import_Soal_Smartka.csv"',
        ];

        // Header CSV
        $columns = [
            'Teks Soal',
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D',
            'Opsi E (Opsional)',
            'Jawaban Benar (A/B/C/D/E)',
            'Pembahasan (Opsional)',
        ];

        // Data dummy
        $dummy = [
            'Siapa penemu lampu pijar?',
            'Albert Einstein',
            'Thomas Alva Edison',
            'Isaac Newton',
            'Nikola Tesla',
            '',
            'b',
            'Thomas Alva Edison adalah penemu bola lampu pijar komersial yang sukses.',
        ];

        $callback = function () use ($columns, $dummy) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $dummy);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'file'             => 'required|file|mimes:csv,xlsx,xls|max:5120',
            'name'             => 'required|string|max:200',
            'class_level'      => 'required|in:6,9,12',
            'subject_id'       => 'required|exists:subjects,id',
            'topic_name'       => 'required|string|max:100',
            'duration_minutes' => 'required|integer|min:10',
            'type'             => 'required|in:free,premium',
        ]);

        try {
            // Membaca file menggunakan Maatwebsite Excel
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array) {}
            }, $request->file('file'));

            if (empty($data) || empty($data[0])) {
                return back()->with('error', 'Gagal membaca file Excel. Pastikan file tidak kosong.');
            }

            $rows = $data[0];
            
            // Hapus baris pertama (header)
            array_shift($rows);

            if (count($rows) === 0) {
                return back()->with('error', 'File Excel tidak memiliki data soal (hanya baris judul).');
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            // Cari atau buat topik
            $topic = \App\Models\Topic::firstOrCreate([
                'subject_id' => $request->subject_id,
                'name'       => $request->topic_name
            ]);

            $questionIds = [];
            foreach ($rows as $index => $row) {
                // Pastikan minimal ada kolom teks soal
                if (empty(trim($row[0] ?? ''))) {
                    continue;
                }

                $correctAnswer = strtolower(trim($row[6] ?? 'a'));
                if (!in_array($correctAnswer, ['a', 'b', 'c', 'd', 'e'])) {
                    $correctAnswer = 'a';
                }

                $question = Question::create([
                    'subject_id'     => $request->subject_id,
                    'topic_id'       => $topic->id,
                    'class_level'    => $request->class_level,
                    'difficulty'     => 'medium',
                    'type'           => 'multiple_choice',
                    'question_text'  => $row[0] ?? 'Soal tidak terbaca',
                    'option_a'       => $row[1] ?? '',
                    'option_b'       => $row[2] ?? '',
                    'option_c'       => $row[3] ?? '',
                    'option_d'       => $row[4] ?? '',
                    'option_e'       => !empty($row[5]) ? $row[5] : null,
                    'correct_answer' => $correctAnswer,
                    'explanation_text'=> !empty($row[7]) ? $row[7] : null,
                    'status'         => 'active', // Karena formatnya sudah jelas dari excel, jadikan active
                    'created_by'     => Auth::id(),
                ]);
                $questionIds[] = $question->id;
            }

            if (count($questionIds) === 0) {
                throw new \Exception('Tidak ada satupun soal yang valid dari file tersebut.');
            }

            // Buat Paket
            $package = TestPackage::create([
                'name'             => $request->name,
                'class_level'      => $request->class_level,
                'total_questions'  => count($questionIds),
                'duration_minutes' => $request->duration_minutes,
                'type'             => $request->type,
                'is_randomized'    => true,
                'status'           => 'published',
                'created_by'       => Auth::id(),
            ]);

            // Attach ke pivot
            foreach ($questionIds as $order => $questionId) {
                $package->questions()->attach($questionId, ['order_number' => $order + 1]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.paket.index')
                ->with('success', 'Berhasil mengimpor ' . count($questionIds) . ' soal dari Excel ke dalam paket baru!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage());
        }
    }
}