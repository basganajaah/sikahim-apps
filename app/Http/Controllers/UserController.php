<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select(['id', 'name', 'email'])->get();

        $data = $users->map(function ($user) {
            $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-btn" data-id="'.$user->id.'" title="Delete">
                            <i class="fa fa-lg fa-fw fa-trash"></i>
                        </button>';

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'actions' => '<nobr>'.$btnDelete.'</nobr>',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function destroy(\Illuminate\Http\Request $request, User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'Akun admin yang sedang digunakan tidak dapat dihapus.');

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pengguna berhasil dihapus.']);
        }

        return redirect()->route('kelola-pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }
}
