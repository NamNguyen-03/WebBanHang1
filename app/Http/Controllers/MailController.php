<?php

namespace App\Http\Controllers;

use App\Mail\SendOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use App\Mail\sendPromotion;
use App\Models\ContactEmails;
use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MailController extends Controller
{


    public function sendOtpToEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->input('email');
        $otp = rand(100000, 999999);

        // Dùng md5 để tạo key cache an toàn
        $key = 'otp_' . md5($email);

        Cache::put($key, $otp, now()->addMinutes(5));

        Mail::to($email)->send(new SendOTP($otp));

        return response()->json([
            'success' => true,
            'message' => 'Mã xác nhận đã được gửi qua email.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');
        $key = 'otp_' . md5($email);

        if (Cache::get($key) == $otp) {
            $otpToken = Str::random(40);
            Cache::put('otp_token_' . $otpToken, $email, now()->addMinutes(5));
            Cache::forget($key);

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận thành công!',
                'otp_token' => $otpToken
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sai mã xác nhận'
            ], 422);
        }
    }


    public function sendOtpToAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        $email = $request->input('email');
        $otp = rand(100000, 999999);

        $key = 'otp_admin_' . md5($email);

        Cache::put($key, $otp, now()->addMinutes(5));

        Mail::to($email)->send(new SendOTP($otp)); // Dùng lại cùng class SendOTP

        return response()->json([
            'success' => true,
            'message' => 'Mã xác nhận đã được gửi đến email admin.'
        ]);
    }

    public function verifyAdminOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tbl_admin,admin_email',
            'otp' => 'required|numeric',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');
        $key = 'otp_admin_' . md5($email);

        if (Cache::get($key) == $otp) {
            $otpToken = Str::random(40);

            Cache::put('otp_admin_token_' . $otpToken, $email, now()->addMinutes(5));

            Cache::forget($key);

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận mã OTP admin thành công!',
                'otp_admin_token' => $otpToken
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sai mã xác nhận OTP admin.'
            ], 422);
        }
    }

    public function sendOrderEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|',
            'order_code' => 'required|exists:tbl_order,order_code'
        ]);
        $email = $request->email;
        $code = $request->order_code;
        $order = Order::with('order_details', 'shipping')->where('order_code', $code)->first();
        Mail::to($email)->send(new SendOrder($order));
        return response()->json([
            'success' => true,
            'message' => 'Đơn đã được gửi qua email.'
        ]);
    }
    public function promotionSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'content_id' => 'required|exists:tbl_promotional_content,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        $email_id = $request->id;
        $promotion = Promotion::find($request->content_id);
        $contactEmail = ContactEmails::find($email_id);

        if (!$contactEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy email với ID đã cung cấp.'
            ], 404);
        }

        try {
            // Gửi email
            Mail::to($contactEmail->email)->send(new sendPromotion($promotion));

            $contactEmail->sent = 1;
            $contactEmail->save();

            return response()->json([
                'success' => true,
                'message' => 'Email đã được gửi thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
