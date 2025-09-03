<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Certification;
use App\Models\Inspector;
use App\Models\Portfolio;

class CertificationController extends Controller
{
    public function index()
    {
        $allPortfolios = Portfolio::all();
        $certifications = Certification::with(['inspector', 'portfolio'])->get();
        return view('certifications.index', compact('certifications', 'allPortfolios'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'inspector'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Base rules
        $rules = [
            'portfolio_id' => 'nullable|exists:portfolios,portfolio_id',
            'name'         => 'required|string|max:255',
            'issuer'       => 'required|string|max:255',
            'issued_at'    => 'required|date',
            'expired_at'   => 'nullable|date',
            'file_path'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        if ($user->role === 'admin') {
            $rules['inspector_id'] = 'required|exists:inspectors,inspector_id';
        }

        $request->validate($rules);

        $data = $request->only([
            'portfolio_id',
            'name',
            'issuer',
            'issued_at',
            'expired_at',
        ]);

        // Tentukan inspector_id
        if ($user->role === 'inspector' && $user->inspector) {
            $data['inspector_id'] = $user->inspector->inspector_id;
        } else {
            $data['inspector_id'] = $request->input('inspector_id');
        }

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $data['original_name'] = $file->getClientOriginalName(); // simpan nama asli
            $data['file_path'] = $file->store('certifications', 'public'); // simpan path di storage
        }

        Certification::create($data);

        return response()->json(['message' => 'Sertifikasi berhasil ditambahkan'], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'inspector'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $certification = Certification::findOrFail($id);

        $rules = [
            'portfolio_id' => 'nullable|exists:portfolios,portfolio_id',
            'name'         => 'required|string|max:255',
            'issuer'       => 'nullable|string|max:255',
            'issued_at'    => 'nullable|date',
            'expired_at'   => 'nullable|date',
            'file_path'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        if ($user->role === 'admin') {
            $rules['inspector_id'] = 'required|exists:inspectors,inspector_id';
        }

        $request->validate($rules);

        $data = $request->only([
            'portfolio_id',
            'name',
            'issuer',
            'issued_at',
            'expired_at',
        ]);

        if ($user->role === 'inspector' && $user->inspector) {
            $data['inspector_id'] = $user->inspector->inspector_id;
        } else {
            $data['inspector_id'] = $request->input('inspector_id');
        }

        if ($request->hasFile('file_path')) {
            if ($certification->file_path) {
                Storage::disk('public')->delete($certification->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('certifications', 'public');
        }

        $certification->update($data);

        return response()->json(['message' => 'Sertifikasi berhasil diperbarui']);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'inspector'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $certification = Certification::findOrFail($id);

        if ($certification->file_path) {
            Storage::disk('public')->delete($certification->file_path);
        }

        $certification->delete();

        return response()->json(['message' => 'Sertifikasi berhasil dihapus']);
    }
}
