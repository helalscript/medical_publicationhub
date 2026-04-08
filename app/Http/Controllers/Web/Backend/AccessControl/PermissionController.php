<?php

namespace App\Http\Controllers\Web\Backend\AccessControl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(50);

        return view('backend.layouts.access_control.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $groups = Permission::query()
            ->where('guard_name', 'web')
            ->whereNotNull('group')
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();

        return view('backend.layouts.access_control.permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'group' => ['nullable', 'string', 'max:190'],
            'name' => ['required', 'string', 'max:190', 'unique:permissions,name'],
        ]);

        Permission::create([
            'group' => $payload['group'] ? trim($payload['group']) : null,
            'name' => strtolower(trim($payload['name'])),
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.access_control.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::query()
            ->where('guard_name', 'web')
            ->whereNotNull('group')
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->toArray();

        return view('backend.layouts.access_control.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $payload = $request->validate([
            'group' => ['nullable', 'string', 'max:190'],
            'name' => ['required', 'string', 'max:190', 'unique:permissions,name,'.$permission->id],
        ]);

        $permission->update([
            'group' => $payload['group'] ? trim($payload['group']) : null,
            'name' => strtolower(trim($payload['name'])),
        ]);

        return redirect()->route('admin.access_control.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return back()->with('success', 'Permission deleted successfully.');
    }
}

