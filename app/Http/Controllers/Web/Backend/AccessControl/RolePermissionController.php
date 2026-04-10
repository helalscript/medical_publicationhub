<?php

namespace App\Http\Controllers\Web\Backend\AccessControl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function edit(Role $role)
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $p) => $p->group ?: 'General');

        $assigned = $role->permissions()->pluck('id')->map(fn ($v) => (int) $v)->toArray();

        return view('backend.layouts.access_control.roles.permissions', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, Role $role)
    {
        $payload = $request->validate([
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $ids = collect($payload['permission_ids'] ?? [])
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->toArray();

        $role->syncPermissions(Permission::query()->whereIn('id', $ids)->get());

        return redirect()
            ->route('admin.access_control.roles.permissions.edit', $role)
            ->with('success', 'Role permissions updated successfully.');
    }
}

