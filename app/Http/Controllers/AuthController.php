<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean'
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Sai email hoặc mật khẩu!'
            ], 401);
        }

        $remember = $request->boolean('remember_me');
        $tokenName = $remember ? 'remember_token' : 'auth_token';

        // Tạo token
        $tokenResult = $user->createToken($tokenName);
        $plainTextToken = $tokenResult->plainTextToken;

        // Cập nhật expires_at theo remember_me
        $token = $tokenResult->accessToken; // Đây là model PersonalAccessToken
        $token->expires_at = $remember ? now()->addDays(30) : now()->addDay();
        $token->save();

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'user' => $user,
            'token' => $plainTextToken,
            'remember_me' => $remember,
            'expires_at' => $token->expires_at,
        ]);
    }




    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
    public function verifyPass(Request $request)
    {

        $user = $request->user();
        if (!$user || !Hash::check($request->password, $user->password)) {
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
    public function changePassword(Request $request)
    {
        $user = $request->user();
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

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.'
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }


    public function changeForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('user_email');
        // Lấy token từ header
        $otpToken = $request->header('X-Otp-Token');

        if (!$otpToken) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận OTP không được cung cấp.',
            ], 400);
        }

        $cachedEmail = Cache::get('otp_token_' . $otpToken);

        if (!$cachedEmail || $cachedEmail !== $email) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận hoặc email không hợp lệ.'
            ], 403);
        }

        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        // Xoá token sau khi dùng
        Cache::forget('otp_token_' . $otpToken);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
    public function changeUserPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed',
            'admin_password' => 'required'
        ]);

        // Lấy superadmin hiện tại đang đăng nhập (giả sử đã đăng nhập qua guard admin)
        $currentAdmin = auth('admins')->user();


        // Kiểm tra mật khẩu superadmin hiện tại
        if (!Hash::check($request->admin_password, $currentAdmin->admin_password)) {
            return response()->json(['success' => false, 'message' => 'Mật khẩu admin không đúng.'], 403);
        }

        // Tìm admin cần đổi mật khẩu
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại.'], 404);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật mật khẩu thành công.']);
    }
}
