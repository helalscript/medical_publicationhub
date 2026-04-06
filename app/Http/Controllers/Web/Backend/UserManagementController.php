<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Clergy;
use App\Models\Mentor;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class UserManagementController extends Controller
{

    // ===================== Mentor =====================
    public function mentorIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('mentor')->where('role', 'mentor')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('avatar', function ($row) {
                    $src = $row->mentor?->avatar
                        ? asset($row->mentor->avatar)
                        : ($row->avatar ? asset($row->avatar) : asset('backend/admin/assets/images/avatar_defult.png'));

                    return '<img src="'.$src.'" class="wh-40 rounded-3" alt="avatar">';
                })
                ->addColumn('mentor_name', function ($row) {
                    return e($row->mentor?->name ?? $row->name ?? '-');
                })
                ->addColumn('status', function ($row) {
                    $s = '<div class="form-check form-switch">';
                    $s .= '<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>';
                    $s .= '</div>';

                    return $s;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('user_management.mentor.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>
                        <a type="button" href="'.route('user_management.mentor.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['avatar', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.user_management.mentor.index');
    }

    public function mentorCreate()
    {
        return view('backend.layouts.user_management.mentor.create');
    }

    public function mentorStore(Request $request)
    {
        $validatedUser = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:50|unique:users,phone',
            'gender' => 'nullable|in:male,female,others',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'password' => 'required|string|min:6',
            'mentor_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'university' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'social_links' => 'nullable|string',
            'mentor_avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $socialLinks = null;
        if (! empty($validatedUser['social_links'])) {
            $decoded = json_decode($validatedUser['social_links'], true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return redirect()->back()->withInput()->with('error', 'Social links must be valid JSON (array of objects).');
            }
            $socialLinks = $decoded;
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('avatar')) {
                $validatedUser['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar', time().'_'.getFileName($request->file('avatar')));
            }

            $mentorAvatarPath = null;
            if ($request->hasFile('mentor_avatar')) {
                $mentorAvatarPath = Helper::fileUpload($request->file('mentor_avatar'), 'mentor/avatar', time().'_'.getFileName($request->file('mentor_avatar')));
            }

            $validatedUser['password'] = bcrypt($validatedUser['password']);
            $validatedUser['role'] = 'mentor';
            $validatedUser['email_verified_at'] = now();
            $validatedUser['status'] = 'active';

            unset($validatedUser['mentor_name'], $validatedUser['bio'], $validatedUser['university'], $validatedUser['department'], $validatedUser['social_links'], $validatedUser['mentor_avatar']);

            $user = User::create($validatedUser);

            Mentor::create([
                'user_id' => $user->id,
                'name' => $request->string('mentor_name')->toString(),
                'avatar' => $mentorAvatarPath,
                'bio' => $request->input('bio'),
                'university' => $request->input('university'),
                'department' => $request->input('department'),
                'social_links' => $socialLinks,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('user_management.mentor.index')->with('success', 'Mentor user created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('UserManagementController::mentorStore '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Mentor user create failed.');
        }
    }

    public function mentorShow(string $id)
    {
        $data = User::with('mentor')->where('role', 'mentor')->findOrFail($id);

        return view('backend.layouts.user_management.mentor.show', compact('data'));
    }

    public function mentorEdit(string $id)
    {
        $data = User::with('mentor')->where('role', 'mentor')->findOrFail($id);

        return view('backend.layouts.user_management.mentor.edit', compact('data'));
    }

    public function mentorUpdate(Request $request, string $id)
    {
        $validatedUser = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:100|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:50|unique:users,phone,'.$id,
            'gender' => 'nullable|in:male,female,others',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'password' => 'sometimes|nullable|string|min:6',
            'mentor_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'university' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'social_links' => 'nullable|string',
            'mentor_avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'mentor_status' => 'nullable|in:active,inactive',
        ]);

        $socialLinks = null;
        if (! empty($validatedUser['social_links'])) {
            $decoded = json_decode($validatedUser['social_links'], true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return redirect()->back()->withInput()->with('error', 'Social links must be valid JSON (array of objects).');
            }
            $socialLinks = $decoded;
        }

        try {
            DB::beginTransaction();

            $user = User::where('role', 'mentor')->findOrFail($id);

            if ($request->hasFile('avatar')) {
                if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
                    Helper::fileDelete(public_path($user->avatar));
                }
                $validatedUser['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar', time().'_'.getFileName($request->file('avatar')));
            } else {
                unset($validatedUser['avatar']);
            }

            if ($request->filled('password')) {
                $validatedUser['password'] = bcrypt($validatedUser['password']);
            } else {
                unset($validatedUser['password']);
            }

            unset($validatedUser['mentor_name'], $validatedUser['bio'], $validatedUser['university'], $validatedUser['department'], $validatedUser['social_links'], $validatedUser['mentor_avatar'], $validatedUser['mentor_status']);

            $user->update($validatedUser);

            $mentor = Mentor::firstOrCreate(['user_id' => $user->id], ['name' => $request->string('mentor_name')->toString(), 'status' => 'active']);

            $mentorPayload = [
                'name' => $request->string('mentor_name')->toString(),
                'bio' => $request->input('bio'),
                'university' => $request->input('university'),
                'department' => $request->input('department'),
                'social_links' => $socialLinks,
                'status' => $request->input('mentor_status', $mentor->status ?? 'active'),
            ];

            if ($request->hasFile('mentor_avatar')) {
                if (! empty($mentor->avatar) && file_exists(public_path($mentor->avatar))) {
                    Helper::fileDelete(public_path($mentor->avatar));
                }
                $mentorPayload['avatar'] = Helper::fileUpload($request->file('mentor_avatar'), 'mentor/avatar', time().'_'.getFileName($request->file('mentor_avatar')));
            }

            $mentor->update($mentorPayload);

            DB::commit();

            return redirect()->route('user_management.mentor.index')->with('success', 'Mentor user updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('UserManagementController::mentorUpdate '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Mentor user update failed.');
        }
    }

    public function mentorDestroy(string $id)
    {
        $user = User::with('mentor')->where('role', 'mentor')->findOrFail($id);
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        if ($user->mentor && ! empty($user->mentor->avatar) && file_exists(public_path($user->mentor->avatar))) {
            Helper::fileDelete(public_path($user->mentor->avatar));
        }
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    public function mentorStatus(Request $request, $id)
    {
        $user = User::where('role', 'mentor')->findOrFail($id);
        $user->status = $user->status == 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }
}
