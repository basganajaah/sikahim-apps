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
            $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </button>';
            $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-btn" data-id="'.$user->id.'" title="Delete">
                            <i class="fa fa-lg fa-fw fa-trash"></i>
                        </button>';
            $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                            <i class="fa fa-lg fa-fw fa-eye"></i>
                        </button>';

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'actions' => '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'Akun admin yang sedang digunakan tidak dapat dihapus.');

        $user->delete();

        return redirect()->route('kelola-pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }
}
