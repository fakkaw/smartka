<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;

class QuestionController extends Controller
{
    public function importExcel(Request $request)
    {
        $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'topic_id'     => 'required|exists:topics,id',
            'class_level'  => 'required|in:6,9,12',
            'difficulty'   => 'required|in:easy,medium,hard',
            'type'         => 'required|in:multiple_choice,true_false,short_answer',
            'status'       => 'required|in:draft,active,archived',
            'excel_file'   => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $import = new SoalImport($request->only([
            'subject_id', 'topic_id', 'class_level', 'difficulty', 'type', 'status'
        ]));

        try {
            Excel::import($import, $request->file('excel_file'));
            
            $successCount = $import->rows;
            $failCount    = count($import->failures());

            if ($failCount > 0) {
                $errorMessage = "Berhasil import $successCount soal, tapi ada $failCount baris yang bermasalah.";
                return back()->with('warning', $errorMessage)->with('import_failures', $import->failures());
            }

            return redirect()->route('admin.soal.index')
                ->with('success', "Berhasil import $successCount soal sekaligus!");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = Question::with(['subject', 'topic'])
            ->latest();

        if ($request->filled('class_level')) {
            $query->where('class_level', $request->class_level);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        $questions = $query->paginate(20)->withQueryString();
        $subjects  = Subject::all();

        return view('admin.soal.index', compact('questions', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::with('topics')->get();
        return view('admin.soal.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'topic_id'     => 'required|exists:topics,id',
            'class_level'  => 'required|in:6,9,12',
            'difficulty'   => 'required|in:easy,medium,hard',
            'type'         => 'required|in:multiple_choice,true_false,short_answer',
            'question_text'=> 'required|string',
            'option_a'     => 'required_if:type,multiple_choice',
            'option_b'     => 'required_if:type,multiple_choice',
            'option_c'     => 'required_if:type,multiple_choice',
            'option_d'     => 'required_if:type,multiple_choice',
            'option_e'     => 'nullable',
            'correct_answer'    => 'required|string',
            'explanation_text'  => 'nullable|string',
            'explanation_video_url' => 'nullable|url',
            'status'            => 'required|in:draft,active,archived',
        ]);

        $data               = $request->all();
        $data['created_by'] = Auth::id();

        if ($request->hasFile('question_image')) {
            $data['question_image'] = $request->file('question_image')
                ->store('questions', 'public');
        }

        Question::create($data);

        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit(Question $question)
    {
        $subjects = Subject::with('topics')->get();
        return view('admin.soal.edit', compact('question', 'subjects'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'topic_id'      => 'required|exists:topics,id',
            'class_level'   => 'required|in:6,9,12',
            'difficulty'    => 'required|in:easy,medium,hard',
            'type'          => 'required|in:multiple_choice,true_false,short_answer',
            'question_text' => 'required|string',
            'option_a'      => 'nullable|required_if:type,multiple_choice',
            'option_b'      => 'nullable|required_if:type,multiple_choice',
            'option_c'      => 'nullable|required_if:type,multiple_choice',
            'option_d'      => 'nullable|required_if:type,multiple_choice',
            'option_e'      => 'nullable',
            'correct_answer'=> 'required|string',
            'explanation_text'  => 'nullable|string',
            'explanation_video_url' => 'nullable|url',
            'status'        => 'required|in:draft,active,archived',
        ]);

        $data = $request->except(['_token', '_method', 'question_image']);

        if ($request->hasFile('question_image')) {
            $data['question_image'] = $request->file('question_image')
                ->store('questions', 'public');
        }

        $question->update($data);

        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus!');
    }
}