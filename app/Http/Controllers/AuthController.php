<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if user exists and password matches
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // Assign default role if the user has no roles
        if ($user->roles->isEmpty()) {
            $user->assignRole('user');
        }

        // Create a token for the user
        $token = $user->createToken('API Token')->plainTextToken;

        // Fetch user roles
        $roles = $user->getRoleNames(); // Get roles as array

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles, // Pass the roles here
                ],
                'token' => $token,
            ],
        ]);
    }


    public function fetchPermissions(User $user, Request $request)
{
    try {
        // Initialize query for all permissions
        $permissionsQuery = \Spatie\Permission\Models\Permission::query();

        // Get role and page from query parameters
        $role = $request->query('role');
        $page = $request->query('page');

        // If a page is provided, filter permissions by that page (all permissions for the page)
        if ($page) {
            $permissionsQuery = $permissionsQuery->where('name', 'like', "$page.%");
        }

        // Get all permissions based on the filters (page)
        $permissions = $permissionsQuery->get();

        // If no permissions found, return empty permissions
        if ($permissions->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'permissions' => [],
                ],
            ]);
        }

        // Get the role's permissions (if a role is provided)
        $rolePermissions = [];
        if ($role) {
            $roleModel = \Spatie\Permission\Models\Role::where('name', $role)->first();
            if ($roleModel) {
                $rolePermissions = $roleModel->permissions->pluck('name')->toArray();
            }
        }

        // Group permissions by page (using the part before the first dot)
        $permissionsByPage = $permissions->groupBy(function ($permission) {
            // Extract the page name from the permission name format "page.action"
            return explode('.', $permission->name)[0];
        });

        // Structure permissions by page and set each permission to true/false based on the role's permissions
        $structuredPermissions = [];
        foreach ($permissionsByPage as $pageName => $permissions) {
            // Include all permissions of the page
            $structuredPermissions[$pageName] = [];
            foreach ($permissions as $permission) {
                // Set each permission to true or false based on whether the role has it
                $structuredPermissions[$pageName][$permission->name] = in_array($permission->name, $rolePermissions);
            }
        }

        // Return the permissions in the required format
        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $structuredPermissions, // Permissions grouped by page, with true/false values
            ],
        ]);

    } catch (\Exception $e) {
        // Return error response with exception message
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching permissions.',
            'error' => $e->getMessage(),
        ], 500);
    }
}









    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
