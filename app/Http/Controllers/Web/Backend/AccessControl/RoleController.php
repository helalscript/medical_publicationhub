<?php

namespace App\Http\Controllers\Web\Backend\AccessControl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->paginate(25);

        return view('backend.layouts.access_control.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('backend.layouts.access_control.roles.create');
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:190', 'unique:roles,name'],
        ]);

        Role::create([
            'name' => strtolower(trim($payload['name'])),
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.access_control.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('backend.layouts.access_control.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:190', 'unique:roles,name,'.$role->id],
        ]);

        $role->update([
            'name' => strtolower(trim($payload['name'])),
        ]);

        return redirect()->route('admin.access_control.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['admin'], true)) {
            return back()->with('error', 'Admin role cannot be deleted.');
        }

        $role->delete();

        return back()->with('success', 'Role deleted successfully.');
    }
}

