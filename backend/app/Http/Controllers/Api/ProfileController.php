<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controller quản lý thông tin cá nhân của người dùng
 */
class ProfileController extends Controller
{
    /**
     * Lấy thông tin cá nhân của người dùng hiện tại
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json($user);
    }

    /**
     * Cập nhật thông tin cá nhân của người dùng hiện tại
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|nullable|string|min:8',
            'current_password' => 'required_with:password|string',
        ]);

        // Kiểm tra mật khẩu hiện tại nếu muốn đổi mật khẩu
        if (isset($data['password']) && $data['password']) {
            if (!Hash::check($data['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Mật khẩu hiện tại không đúng',
                ], 422);
            }
            
            $data['password'] = Hash::make($data['password']);
            unset($data['current_password']);
        } else {
            // Nếu không đổi mật khẩu, loại bỏ các trường liên quan
            unset($data['password']);
            unset($data['current_password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'user' => $user->fresh(),
        ]);
    }
}
