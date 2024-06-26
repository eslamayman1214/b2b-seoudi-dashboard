<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('roles')) {
            $roles = $request->input('roles');
            $query->whereIn('role', $roles);
        }

        $users = $query->paginate(20);

        LogHelper::logAction('View Users', 'Users page viewed.');

        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->role = $request->role;

            if ($user->save()) {
                LogHelper::logAction('Update User Role', "User role updated for user ID: {$id}");
                return response()->json(['success' => true]);
            } else {
                LogHelper::logAction('Update User Role Failed', "Failed to update user role for user ID: {$id}");
                return response()->json(['success' => false, 'message' => 'Failed to update user role.']);
            }
        } catch (\Exception $e) {
            LogHelper::logAction('Update User Role Failed', "User not found with ID: {$id}");
            abort(404);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->delete()) {
                LogHelper::logAction('Delete User', "User deleted with ID: {$id}");
                return response()->json(['success' => true]);
            } else {
                LogHelper::logAction('Delete User Failed', "Failed to delete user with ID: {$id}");
                return response()->json(['success' => false, 'message' => 'Failed to delete user.']);
            }
        } catch (\Exception $e) {
            LogHelper::logAction('Delete User Failed', "User not found with ID: {$id}");
            abort(404);
        }
    }
}