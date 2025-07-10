<?php

// AdminAuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::where('admin_email', $request->email)->first();

        if (!$admin) {
            return response()->json(['message' => 'Email không tồn tại!'], 401);
        }

        if (!Hash::check($request->password, $admin->admin_password)) {
            return response()->json(['message' => 'Mật khẩu không chính xác!'], 401);
        }

        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'token' => $token,
            'admin' => $admin
        ]);
    }

    public function logout(Request $request)
    {
        // Lấy token từ header Authorization
        $token = $request->bearerToken();

        // Kiểm tra nếu không có token
        if (!$token) {
            return response()->json([
                'message' => 'Token không hợp lệ hoặc không có token.'
            ], 401);
        }


        $user = auth('admins')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Token không hợp lệ'
            ], 401);
        }



        // Xóa tất cả các token có tên 'AdminToken' từ bảng personal_access_tokens
        $user->tokens->where('name', 'AdminToken')->each(function ($token) {
            $token->delete(); // Xóa token
        });

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }
    public function changeAdminPassword(Request $request) //Admin tự đổi mật khẩu (biết mật khẩu cũ)
    {
        $admin = $request->user();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->current_password, $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.'
            ], 403);
        }

        $admin->admin_password = Hash::make($request->new_password);
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
    public function verifyAdminPass(Request $request)
    {
        $admin = $request->user();

        // Debug
        logger('Admin:', [$admin]);
        logger('Request password:', [$request->password]);
        logger('Hash check result:', [Hash::check($request->password, $admin->admin_password)]);

        if (!$admin || !Hash::check($request->password, $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Sai mật khẩu!'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verify thành công!',
        ]);
    }
    public function changeAdminForgotPassword(Request $request) //Admin quên mật khẩu
    {
        $validator = Validator::make($request->all(), [
            'admin_email' => 'required|email|exists:tbl_admin,admin_email',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('admin_email');

        $otpToken = $request->header('X-Otp-Token');

        if (!$otpToken) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận OTP không được cung cấp.',
            ], 400);
        }

        $cachedEmail = Cache::get('otp_admin_token_' . $otpToken);

        if (!$cachedEmail || $cachedEmail !== $email) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận hoặc email không hợp lệ.'
            ], 403);
        }

        $admin = Admin::where('admin_email', $email)->first();
        $admin->admin_password = Hash::make($request->input('new_password'));
        $admin->save();

        // Xoá token sau khi dùng
        Cache::forget('otp_admin_token_' . $otpToken);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed',
            'super_password' => 'required'
        ]);

        // Lấy superadmin hiện tại đang đăng nhập (giả sử đã đăng nhập qua guard admin)
        $currentAdmin = auth('admin')->user();

        // Kiểm tra role superadmin
        if (!$currentAdmin || !$currentAdmin->roles->contains('role_name', 'superadmin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền superadmin.'], 403);
        }

        // Kiểm tra mật khẩu superadmin hiện tại
        if (!Hash::check($request->super_password, $currentAdmin->admin_password)) {
            return response()->json(['success' => false, 'message' => 'Mật khẩu superadmin không đúng.'], 403);
        }

        // Tìm admin cần đổi mật khẩu
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin không tồn tại.'], 404);
        }

        // Cập nhật mật khẩu mới
        $admin->admin_password = Hash::make($request->new_password);
        $admin->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật mật khẩu thành công.']);
    }
}
