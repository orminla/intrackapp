<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Inspector;
use App\Models\Admin;

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
            'gender'     => '-',
            'phone_num'  => '-',
            'portfolio'  => '-',
            'department' => '-',
        ];

        if ($user->role === 'inspector' && $user->inspector) {
            $user->loadMissing('inspector.portfolio.department');

            $data['name']       = $user->inspector->name ?? $data['name'];
            $data['nip']        = $user->inspector->nip ?? '-';
            $data['gender']     = $user->inspector->gender ?? '-';
            $data['phone_num']  = $user->inspector->phone_num ?? '-';
            $data['portfolio']  = optional($user->inspector->portfolio)->name ?? '-';
            $data['department'] = optional(optional($user->inspector->portfolio)->department)->name ?? '-';
        }

        if ($user->role === 'admin' && $user->admin) {
            $user->loadMissing('admin.portfolio.department'); // fixed here

            $data['name']       = $user->admin->name ?? $data['name'];
            $data['nip']        = $user->admin->nip ?? '-';
            $data['gender']     = $user->admin->gender ?? '-';
            $data['phone_num']  = $user->admin->phone_num ?? '-';
            $data['portfolio']  = optional($user->admin->portfolio)->name ?? '-';
            $data['department'] = optional(optional($user->admin->portfolio)->department)->name ?? '-';
        }

        logger()->info('User admin relasi:', ['admin' => $user->admin]);
        logger()->info('Final data:', $data);


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
            'gender'     => 'sometimes|in:Laki-laki,Perempuan',
            'photo_url'  => 'sometimes|nullable|file|image|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        if ($request->hasFile('photo_url')) {
            if ($user->photo_url) {
                $oldPath = str_replace(asset('storage') . '/', '', $user->photo_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $file = $request->file('photo_url');
            $extension = $file->getClientOriginalExtension();
            $filename = 'profile_' . Str::slug($user->name) . '_' . time() . '.' . $extension;
            $file->move(public_path('storage/profile_photos'), $filename);

            $validated['photo_url'] = asset('storage/profile_photos/' . $filename);
        }

        $user->fill([
            'email'     => $validated['email']     ?? $user->email,
            'photo_url' => $validated['photo_url'] ?? $user->photo_url,
        ])->save();

        if ($user->role === 'admin' && $user->admin) {
            $user->admin->fill([
                'name'      => $validated['name']      ?? $user->admin->name,
                'phone_num' => $validated['phone_num'] ?? $user->admin->phone_num,
                'gender'    => $validated['gender']    ?? $user->admin->gender,
            ])->save();
        } elseif ($user->role === 'inspector' && $user->inspector) {
            $user->inspector->fill([
                'name'      => $validated['name']      ?? $user->inspector->name,
                'phone_num' => $validated['phone_num'] ?? $user->inspector->phone_num,
                'gender'    => $validated['gender']    ?? $user->inspector->gender,
            ])->save();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui']);
        }

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => ['current_password' => ['Password lama salah.']]], 422);
            }
            return back()->withErrors(['current_password' => 'Password lama salah.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui']);
        }

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
