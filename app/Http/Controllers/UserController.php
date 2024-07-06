<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $logService;

    public function __construct(UserService $userService, LogHelper $logService)
    {
        $this->userService = $userService;
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getUsers($request);
        $this->logService->logAction('View Users', 'Users page viewed.');
        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $user = $this->userService->updateUserRole($request, $id);
            $this->logService->logAction('Update User Role', "User role updated for user ID: {$id}");
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logService->logAction('Update User Role Failed', "Failed to update user role for user ID: {$id}");
            return response()->json(['success' => false, 'message' => 'Failed to update user role.']);
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->userService->deleteUser($id);
            $this->logService->logAction('Delete User', "User deleted with ID: {$id}");
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logService->logAction('Delete User Failed', "Failed to delete user with ID: {$id}");
            return response()->json(['success' => false, 'message' => 'Failed to delete user.']);
        }
    }
}