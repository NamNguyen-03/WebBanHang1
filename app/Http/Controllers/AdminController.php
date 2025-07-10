<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Admin::with('roles');

        // Nếu có tham số search, thì lọc theo tên hoặc email
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admin_name', 'like', '%' . $search . '%')
                    ->orWhere('admin_email', 'like', '%' . $search . '%');
            });
        }

        $admins = $query->get();

        return response()->json([
            'success' => true,
            'data' => $admins,
            'message' => 'Danh sách admin được lấy thành công.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $admin = auth('admins')->user();
            if (!$admin instanceof \App\Models\Admin || !$admin->hasRole('superadmin')) {
                return response()->json(['message' => 'Bạn không có quyền tạo admin mới.'], 403);
            }

            // Validate dữ liệu
            $validated = $request->validate([
                'admin_name' => 'required',
                'admin_email' => 'required|email|unique:tbl_admin,admin_email',
                'admin_password' => 'required|min:6',
                'admin_phone' => 'required|min:10|numeric',
            ]);

            $admin = Admin::create([
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'admin_phone' => $validated['admin_phone'],
                'admin_password' => Hash::make($validated['admin_password']),
                'created_at' => now('Asia/Ho_Chi_Minh')
            ]);

            // Gán vai trò admin thường
            $role = Roles::where('role_name', 'admin')->first();
            $admin->roles()->attach($role);

            return response()->json([
                'success' => true,
                'message' => 'Admin mới đã được tạo!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $roles = $admin->roles->pluck('role_name');

        return response()->json([
            'success' => true,
            'message' => 'Lấy admin thành công',
            'data' =>  $admin,
            'roles' => $roles
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loggedInAdmin = auth('admins')->user();

        // Kiểm tra instance hợp lệ
        if (!$loggedInAdmin instanceof \App\Models\Admin) {
            return response()->json([
                'success' => false,
                'message' => 'Không xác định được người dùng.'
            ], 401);
        }

        $targetAdmin = Admin::findOrFail($id);

        $isSelf = $loggedInAdmin->admin_id === $targetAdmin->admin_id;
        $isSuperAdmin = $loggedInAdmin->hasRole('superadmin');

        if (!$isSelf && !$isSuperAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền sửa tài khoản của người khác.'
            ], 403);
        }

        // Validate dữ liệu
        $rules = [
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:tbl_admin,admin_email,' . $id . ',admin_id',
            'admin_phone' => 'required|numeric|digits_between:9,12',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cập nhật thông tin admin
        $targetAdmin->update([
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
            'admin_phone' => $request->admin_phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin admin thành công.',
            'data' => $targetAdmin
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin not found'
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully'
        ]);
    }

    public function createAdmin(Request $request)
    {
        try {
            $admin = auth('admins')->user();
            if (!$admin instanceof \App\Models\Admin || !$admin->hasRole('superadmin')) {
                return response()->json(['message' => 'Bạn không có quyền tạo admin mới.'], 403);
            }

            // Validate dữ liệu
            $validated = $request->validate([
                'admin_name' => 'required',
                'admin_email' => 'required|email|unique:tbl_admin,admin_email',
                'admin_password' => 'required|min:6',
                'admin_phone' => 'required|min:10|numeric',
            ]);

            $admin = Admin::create([
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'admin_phone' => $validated['admin_phone'],
                'admin_password' => Hash::make($validated['admin_password']),
                'created_at' => now('Asia/Ho_Chi_Minh')
            ]);

            // Gán vai trò admin thường
            $role = Roles::where('role_name', 'admin')->first();
            $admin->roles()->attach($role);

            return response()->json([
                'success' => true,
                'message' => 'Admin mới đã được tạo!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function updateAdminProfile(Request $request)
    {
        $admin = auth('admins')->user();

        if (!$admin instanceof \App\Models\Admin) {
            return response()->json(['message' => 'Không xác định được người dùng.'], 401);
        }

        if ($admin->admin_id != $request->admin_id && !$admin->hasRole('superadmin')) {
            return response()->json(['message' => 'Bạn không có quyền sửa tài khoản của người khác.'], 403);
        }

        $admin->update([
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
        ]);

        return response()->json(['message' => 'Cập nhật thông tin thành công!']);
    }
}
