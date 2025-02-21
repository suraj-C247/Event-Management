<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Services\UserService;

class UserController extends Controller
{   
    protected $userService;

    /**
     * Constructor to initialize the UserService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService; // Inject the UserService
    }

    /**
     * Display the list of users.
     */
    public function users(): View
    {
        // Fetch the paginated list of users using UserService
        $users = $this->userService->listUsers();

        return view('admin.users', [
            'users' => $users,
        ]);
    }

    /**
     * Change the status of a user (active/inactive).
     */ 
    public function changeStatus(Request $request):JsonResponse
    {
        // Change user status using UserService
        $user = $this->userService->changeUserStatus($request->user_id);

        if ($user) {
            return response()->json(['success' => true, 'status' => $user->is_active]);
        }
        return response()->json(['success' => false]);
    }
}
