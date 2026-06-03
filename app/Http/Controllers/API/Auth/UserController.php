<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Fetch the authenticated user's details.
     *
     * @return JsonResponse
     */
    public function me()
    {
        try {
            $user = User::select('id', 'name', 'email', 'phone', 'avatar', 'cover_photo', 'gender', 'role', 'birthdate')
                ->find(auth('api')->id());
            if (! $user) {
                return Helper::jsonResponse(false, 'User not found', 404);
            }

            return Helper::jsonResponse(true, 'User details fetched successfully', 200, $user);
        } catch (Exception $e) {
            Log::error('UserController::me '.$e->getMessage());

            return Helper::jsonErrorResponse('something went wrong', 403);
        }
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @return JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'gender' => 'nullable|string|in:male,female,others',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'phone' => 'nullable|unique:users,phone,'.auth()->user()->id.'|string|regex:/^(\+?\d{1,4}[\s-]?)?(\(?\d{1,3}\)?[\s-]?)?[\d\s-]{7,15}$/|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'birthdate' => 'nullable|date:Y-m-d',
        ]);
        try {
            if (! empty($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            } elseif (array_key_exists('password', $validatedData)) {
                unset($validatedData['password']);
            }

            $user = auth('api')->user();
            // upload avatar photo
            if ($request->hasFile('avatar')) {
                if (! empty($user->avatar)) {
                    Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
                }
                $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar', getFileName($request->file('avatar')));
            } else {
                $validatedData['avatar'] = $user->avatar;
            }
            // upload cover photo
            if ($request->hasFile('cover_photo')) {
                if (! empty($user->cover_photo)) {
                    Helper::fileDelete(public_path($user->getRawOriginal('cover_photo')));
                }
                $validatedData['cover_photo'] = Helper::fileUpload($request->file('cover_photo'), 'user/cover_photo', getFileName($request->file('cover_photo')));
            } else {
                $validatedData['cover_photo'] = $user->cover_photo;
            }

            $user->update($validatedData);

            return Helper::jsonResponse(true, 'Profile updated successfully', 200, $user);
        } catch (Exception $e) {
            Log::error('UserController::updateProfile'.$e->getMessage());

            return Helper::jsonErrorResponse('something went wrong', 403);
        }
    }

    /**
     * Update the authenticated user's password.
     *
     * @return JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        try {
            $user = auth('api')->user();

            // Check if the current password matches the stored password
            if (! \Hash::check($validatedData['current_password'], $user->password)) {
                return Helper::jsonErrorResponse('Current password is incorrect', 422);
            }

            // Update the password
            $user->update([
                'password' => bcrypt($validatedData['new_password']),
            ]);

            return Helper::jsonResponse(true, 'Password updated successfully', 200);
        } catch (Exception $e) {
            Log::error('UserController::changePassword'.$e->getMessage());

            return Helper::jsonErrorResponse('something went wrong', 403);
        }
    }

    public function deleteProfile()
    {
        try {
            // Get the authenticated user
            $user = User::findOrFail(auth('api')->id());

            // // If the user has an avatar, attempt to delete it
            // if (!empty($user->avatar)) {
            //     // Ensure that the file exists before attempting to delete
            //     $avatarPath = public_path($user->avatar);
            //     if (file_exists($avatarPath)) {
            //         Helper::fileDelete($avatarPath);
            //     }
            // }

            // // Soft delete the user (if using SoftDeletes)
            // $user->delete();

            // Update email with timestamp
            $timestamp = time();

            $user->update([
                'email' => $user->email.'_'.$timestamp,
                'status' => 'inactive',
            ]);
            // Logout user
            Auth::guard('api')->logout();

            // Return success response
            return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
        } catch (Exception $e) {
            Log::error('UserController::deleteProfile'.$e->getMessage());

            // Return error response
            return Helper::jsonErrorResponse('Something went wrong. Please try again later.', 403);
        }
    }
}
