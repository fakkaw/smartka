<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Tampilkan Halaman Pengaturan & Profil
     */
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('akun.show', compact('user'));
    }

    /**
     * Perbarui data profil siswa (Nama, Email, HP, Avatar)
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input profil
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'       => ['nullable', 'string', 'max:20'],
            'class_level' => ['required', 'in:6,9,12'],
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maks 2MB
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format alamat email tidak valid.',
            'email.unique'         => 'Alamat email ini sudah digunakan oleh pengguna lain.',
            'class_level.required' => 'Jenjang kelas wajib dipilih.',
            'class_level.in'       => 'Jenjang kelas tidak valid.',
            'avatar.image'         => 'Berkas avatar harus berupa gambar.',
            'avatar.mimes'         => 'Format gambar avatar harus jpeg, png, jpg, atau webp.',
            'avatar.max'           => 'Ukuran gambar avatar tidak boleh lebih dari 2MB.',
        ]);

        // Update basic fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->class_level = $validated['class_level'];

        // Penanganan upload avatar
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada di storage public dan bukan default avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan avatar baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        // Log Aktivitas Pengguna (Opsional jika user_activity_logs diisi)
        try {
            $user->activityLogs()->create([
                'action_type' => 'update_profile',
                'detail' => json_encode(['updated_fields' => array_keys($validated)]),
            ]);
        } catch (\Exception $e) {
            // Silent catch if activity logs logic isn't fully configured in databases
        }

        return redirect()->route('akun.show')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Ganti password akun secara aman
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input password
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', Password::defaults()->min(8), 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal harus terdiri dari 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Cek kecocokan password saat ini
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('akun.show')
                ->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        // Simpan password baru
        $user->password = Hash::make($validated['password']);
        $user->save();

        try {
            $user->activityLogs()->create([
                'action_type' => 'update_password',
                'detail' => json_encode(['ip' => $request->ip()]),
            ]);
        } catch (\Exception $e) {
            // Silent catch
        }

        return redirect()->route('akun.show')->with('success', 'Kata sandi Anda berhasil diperbarui!');
    }
}
