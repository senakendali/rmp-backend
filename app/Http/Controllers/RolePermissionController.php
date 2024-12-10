<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $role = Role::create(['name' => $request->name]);
        return response()->json(['message' => 'Role created successfully', 'role' => $role]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);
        $role->update(['name' => $request->name]);
        return response()->json(['message' => 'Role updated successfully', 'role' => $role]);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function assignPermission(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $permissions = Permission::whereIn('name', $request->permissions)->get();
        $role->syncPermissions($permissions);
        return response()->json(['message' => 'Permissions assigned successfully', 'role' => $role->load('permissions')]);
    }

    public function revokePermission(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission);
        return response()->json(['message' => 'Permission revoked successfully']);
    }

    public function assignRoleToUser(Request $request, User $user)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $user->assignRole($request->role);
        return response()->json(['message' => 'Role assigned to user successfully', 'user' => $user->load('roles')]);
    }

    public function removeRoleFromUser(User $user, Role $role)
    {
        $user->removeRole($role);
        return response()->json(['message' => 'Role removed from user successfully', 'user' => $user->load('roles')]);
    }
}

/*

{
  "name": "editor"
}

{
  "permissions": ["edit articles", "delete articles"]
}

{
  "role": "editor"
}

*/
