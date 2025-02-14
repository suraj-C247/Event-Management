<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    /**
     * Get a paginated list of users with the role 'user'
     */
    public function listUsers()
    {
        try {
            // Fetch paginated users data
            return User::select('id', 'name', 'email', 'role', 'is_active') 
                ->where('role', 'user')
                ->orderBy('id', 'desc')
                ->paginate(config('global.pagination.per_page'));
        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            throw new Exception('An error occurred while fetching users.');
        }
    }

    /**
     * Change the status of a user (active/inactive)
     */
    public function changeUserStatus($userId)
    {
        try {
            // Find the user by ID
            $user = User::find($userId);

            // If user is found, toggle the 'is_active' status
            if ($user) {
                $user->is_active = !$user->is_active;
                $user->save(); // Save the updated user status
                return $user;
            }

            return null; // Return null if user not found
        } catch (Exception $e) {
            Log::error('Error changing user status: ' . $e->getMessage());
            throw new Exception('An error occurred while changing the user status.');
        }
    }
}