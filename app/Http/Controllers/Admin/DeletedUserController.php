<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeletedUser;
use Illuminate\Http\Request;

class DeletedUserController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');
    $query = DeletedUser::query();
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('username', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhere('role', 'like', "%{$search}%");
      });
    }
    $deletedUsers = $query->orderBy('deleted_at', 'desc')->paginate(10)->withQueryString();
    return view('pages.admin.history_delete_user', compact('deletedUsers', 'search'));
  }
}
