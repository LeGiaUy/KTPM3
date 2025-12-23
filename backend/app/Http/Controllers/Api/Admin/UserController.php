<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controller quản lý người dùng cho admin
 */
class UserController extends Controller
{
    /**
     * Lấy danh sách người dùng với phân trang và tìm kiếm
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        return $query->latest()->paginate($request->get('per_page', 10));
    }

    /**
     * Lấy thống kê về người dùng
     */
    public function statistics()
    {
        $total_users = User::count();
        $admin_users = User::where('role', 'admin')->count();
        $regular_users = User::where('role', 'user')->count();
        $recent_users = User::where('created_at', '>=', now()->subDays(30))->count();

        return response()->json([
            'total_users' => (int) $total_users,
            'admin_users' => (int) $admin_users,
            'regular_users' => (int) $regular_users,
            'recent_users' => (int) $recent_users,
        ]);
    }

    /**
     * Lấy thông tin chi tiết một người dùng
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Tạo người dùng mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'message' => 'Tạo người dùng thành công',
            'user' => $user,
        ], 201);
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|nullable|string|min:8',
            'role' => ['sometimes', 'required', Rule::in(['admin', 'user'])],
        ]);

        // Chỉ hash password nếu có cung cấp
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Cập nhật người dùng thành công',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Xóa người dùng
     */
    public function destroy(User $user)
    {
        // Không cho phép xóa chính mình
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Không thể xóa chính tài khoản của bạn',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Xóa người dùng thành công',
        ]);
    }

    /**
     * Xóa nhiều người dùng cùng lúc
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->ids;
        $current_user_id = auth()->id();

        // Loại bỏ ID của user hiện tại khỏi danh sách xóa
        $ids = array_filter($ids, function ($id) use ($current_user_id) {
            return $id != $current_user_id;
        });

        if (empty($ids)) {
            return response()->json([
                'message' => 'Không có người dùng nào được xóa',
            ], 400);
        }

        $deleted_count = User::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => "Đã xóa {$deleted_count} người dùng thành công",
            'deleted_count' => $deleted_count,
        ]);
    }
}
