<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SoalImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $metadata;
    public $rows = 0;

    /**
     * @param array $metadata Data kategori dari UI (subject_id, topic_id, dll)
     */
    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rows++;
        
        // Gabungkan data dari Excel dengan metadata dari UI
        return new Question([
            'subject_id'            => $this->metadata['subject_id'],
            'topic_id'              => $this->metadata['topic_id'],
            'class_level'           => $this->metadata['class_level'],
            'difficulty'            => $this->metadata['difficulty'],
            'type'                  => $this->metadata['type'],
            'status'                => $this->metadata['status'],
            'created_by'            => Auth::id(),
            
            // Data dari file Excel
            'question_text'         => $row['teks_soal'],
            'option_a'              => $row['opsi_a'] ?? null,
            'option_b'              => $row['opsi_b'] ?? null,
            'option_c'              => $row['opsi_c'] ?? null,
            'option_d'              => $row['opsi_d'] ?? null,
            'option_e'              => $row['opsi_e'] ?? null,
            'correct_answer'        => strtolower($row['jawaban_benar']),
            'explanation_text'      => $row['pembahasan'] ?? null,
            'explanation_video_url' => $row['link_pembahasan'] ?? null,
        ]);
    }

    /**
     * Validasi tiap baris di Excel
     */
    public function rules(): array
    {
        $rules = [
            'teks_soal'     => 'required|string',
            'jawaban_benar' => 'required',
        ];

        // Validasi tambahan jika tipenya pilihan ganda
        if ($this->metadata['type'] === 'multiple_choice') {
            $rules['opsi_a'] = 'required';
            $rules['opsi_b'] = 'required';
            $rules['opsi_c'] = 'required';
            $rules['opsi_d'] = 'required';
            // Opsi E opsional sesuai request
            $rules['jawaban_benar'] = 'required|in:a,b,c,d,e,A,B,C,D,E';
        }

        // Validasi jika Benar/Salah
        if ($this->metadata['type'] === 'true_false') {
            $rules['jawaban_benar'] = 'required|in:Benar,Salah,benar,salah';
        }

        return $rules;
    }

    /**
     * Custom error messages untuk validasi
     */
    public function customValidationMessages()
    {
        return [
            'teks_soal.required'     => 'Kolom Teks Soal wajib diisi.',
            'jawaban_benar.required' => 'Kolom Jawaban Benar wajib diisi.',
            'jawaban_benar.in'       => 'Format Jawaban Benar tidak valid (Gunakan a-e untuk Pilihan Ganda, atau Benar/Salah).',
            'opsi_a.required'        => 'Opsi A wajib diisi untuk Pilihan Ganda.',
            'opsi_b.required'        => 'Opsi B wajib diisi untuk Pilihan Ganda.',
            'opsi_c.required'        => 'Opsi C wajib diisi untuk Pilihan Ganda.',
            'opsi_d.required'        => 'Opsi D wajib diisi untuk Pilihan Ganda.',
        ];
    }
}
