<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        $photoDefault = $user->role === 'admin'
            ? asset('admin_assets/images/profile/user-7.jpg')
            : asset('inspector_assets/images/profile/user-7.jpg');

        $data = [
            'name'       => $user->name ?? '-',
            'email'      => $user->email ?? '-',
            'role'       => $user->role,
            'photo_url'  => $user->photo_url ?: $photoDefault,
            'nip'        => '-',
            'phone_num'  => '-',
            'portfolio'  => '-',
            'department' => '-',
        ];

        if ($user->role === 'inspector' && $user->inspector) {
            $user->loadMissing('inspector.portfolio.department');
            $data['name']       = $user->inspector->name ?? $data['name'];
            $data['nip']        = $user->inspector->nip ?? '-';
            $data['phone_num']  = $user->inspector->phone_num ?? '-';
            $data['portfolio']  = optional($user->inspector->portfolio)->name ?? '-';
            $data['department'] = optional($user->inspector->portfolio->department)->name ?? '-';
        }

        if ($user->role === 'admin' && $user->admin) {
            $user->loadMissing('admin.portfolios.department');
            $data['name']       = $user->admin->name ?? $data['name'];
            $data['nip']        = $user->admin->nip ?? '-';
            $data['phone_num']  = $user->admin->phone_num ?? '-';
            $data['portfolio']  = optional($user->admin->portfolios)->name ?? '-';
            $data['department'] = optional($user->admin->portfolios->department)->name ?? '-';
        }

        return $request->expectsJson()
            ? response()->json(['success' => true, 'data' => $data])
            : view('profile.show', ['profile' => $data]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'name'       => 'sometimes|required|string|max:255',
            'email'      => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'phone_num'  => 'sometimes|nullable|string|max:20',
            'photo_url'  => 'sometimes|nullable|file|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'errors' => $validator->errors()], 422)
                : back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Handle upload foto baru
        if ($request->hasFile('photo_url')) {
            // Hapus file lama jika ada
            if ($user->photo_url && Storage::disk('public')->exists(str_replace('storage/', '', $user->photo_url))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->photo_url));
            }

            $file = $request->file('photo_url');
            $extension = $file->getClientOriginalExtension();
            $filename = 'profile_' . Str::slug($user->name) . '_' . time() . '.' . $extension;
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $validated['photo_url'] = 'storage/' . $path;
        }

        // Update ke tabel users
        $user->fill([
            'email'     => $validated['email']     ?? $user->email,
            'photo_url' => $validated['photo_url'] ?? $user->photo_url,
        ])->save();

        // Update ke admin/inspector
        if ($user->role === 'admin' && $user->admin) {
            $user->admin->fill([
                'name'      => $validated['name']      ?? $user->admin->name,
                'phone_num' => $validated['phone_num'] ?? $user->admin->phone_num,
            ])->save();
        } elseif ($user->role === 'inspector' && $user->inspector) {
            $user->inspector->fill([
                'name'      => $validated['name']      ?? $user->inspector->name,
                'phone_num' => $validated['phone_num'] ?? $user->inspector->phone_num,
            ])->save();
        }

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui'])
            : back()->with('success', 'Profil berhasil diperbarui');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.'])->withInput();
        }

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
