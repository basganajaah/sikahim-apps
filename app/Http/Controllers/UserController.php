<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select(['id', 'name', 'email', 'role', 'is_active'])
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->get();

        $data = $users->map(function (User $user) {
            $detailUrl = route('users.show', $user);
            $btnDetail = '<a class="btn btn-xs btn-default text-info mx-1 shadow" href="'.$detailUrl.'" title="Detail & Kelola">
                            <i class="fa fa-lg fa-fw fa-eye"></i>
                         </a>';

            $btnDelete = $user->role !== 'admin'
                ? '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-btn" data-id="'.$user->id.'" title="Delete">
                        <i class="fa fa-lg fa-fw fa-trash"></i>
                   </button>'
                : '<span class="badge badge-secondary mx-1">Admin</span>';

            return [
                'id' => $user->id,
                'name' => e($user->name),
                'email' => e($user->email),
                'role' => e($user->role),
                'status' => $user->is_active !== false ? 'Aktif' : 'Nonaktif',
                'actions' => '<nobr>'.$btnDetail.$btnDelete.'</nobr>',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function show(User $user)
    {
        $user->load(['pendaftaran.kegiatan', 'evaluasi.kegiatan']);

        return view('pages.admin.detail-pengguna', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:user,admin'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newIsActive = $request->has('is_active') ? $request->boolean('is_active') : ($user->is_active ?? true);

        if ($user->role === 'admin' && ($validated['role'] !== 'admin' || ! $newIsActive)) {
            $otherActiveAdmins = User::where('role', 'admin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            abort_if($otherActiveAdmins === 0, 422, 'Tidak dapat mengubah role atau menonaktifkan admin terakhir.');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $newIsActive,
        ]);

        AuditLog::record('update_user', "Memperbarui data pengguna #{$user->id} ({$user->email})", 'User', $user->id);

        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'Anda tidak dapat menonaktifkan akun sendiri.');

        $nextActive = ! ($user->is_active ?? true);
        if ($user->role === 'admin' && ! $nextActive) {
            $otherActiveAdmins = User::where('role', 'admin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            abort_if($otherActiveAdmins === 0, 422, 'Tidak dapat menonaktifkan admin terakhir.');
        }

        $user->update(['is_active' => $nextActive]);

        $label = $nextActive ? 'mengaktifkan' : 'menonaktifkan';
        AuditLog::record('toggle_user_active', "Admin {$label} akun pengguna #{$user->id} ({$user->email})", 'User', $user->id);

        return back()->with('success', 'Status aktif pengguna berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::record('reset_user_password', "Mereset password pengguna #{$user->id} ({$user->email})", 'User', $user->id);

        return back()->with('success', 'Password pengguna berhasil direset.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if(
            $user->is(auth()->user()) || $user->role === 'admin',
            422,
            'Akun admin tidak dapat dihapus.'
        );

        $email = $user->email;
        $userId = $user->id;
        $user->delete();

        AuditLog::record('delete_user', "Menghapus pengguna #{$userId} ({$email})", 'User', $userId);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pengguna berhasil dihapus.']);
        }

        return redirect()->route('kelola-pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }
}
