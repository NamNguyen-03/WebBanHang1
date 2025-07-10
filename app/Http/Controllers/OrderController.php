<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Statistics;
use App\Models\OrderDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Shipping;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ImportProduct;
use App\Models\OrderChangeLog;

use Illuminate\Database\QueryException;

class OrderController extends Controller
{
    // Lấy danh sách đơn hàng cùng shipping và chi tiết đơn hàng
    public function index(Request $request)
    {
        $search = $request->query('search'); // Lấy từ khóa tìm kiếm nếu có

        $orders = Order::with(['shipping', 'order_details'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', '%' . $search . '%')
                        ->orWhereDate('created_at', $search)
                        ->orWhereHas('shipping', function ($q2) use ($search) {
                            $q2->where('customer_name', 'like', '%' . $search . '%')
                                ->orWhere('shipping_address', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('order_id', 'desc')
            ->get();


        // Kiểm tra nếu không có đơn hàng nào
        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => $search
                    ? "Không có đơn hàng nào phù hợp với từ khóa \"$search\"."
                    : "Không có đơn hàng nào."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }



    // Tạo mới đơn hàng

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'customer_id' => 'required|integer',
    //         'order_coupon' => 'nullable|string',
    //         'order_ship' => 'required|numeric',
    //         'order_status' => 'required|integer',
    //         'order_total' => 'required|numeric',
    //         'order_code' => 'string|unique:tbl_order,order_code',

    //         'shipping.customer_name' => 'required|string|max:255',
    //         'shipping.shipping_address' => 'required|string|max:500',
    //         'shipping.shipping_phone' => 'required|string|max:20',
    //         'shipping.shipping_email' => 'required|email|max:255',
    //         'shipping.shipping_method' => 'required|integer',
    //         'shipping.shipping_note' => 'nullable|string|max:500',

    //         'order_details' => 'required|array|min:1',
    //         'order_details.*.product_id' => 'required|integer',
    //         'order_details.*.product_name' => 'required|string|max:255',
    //         'order_details.*.product_price' => 'required|numeric',
    //         'order_details.*.product_quantity' => 'required|integer|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Dữ liệu không hợp lệ!',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $order = Order::create([
    //             'customer_id' => $request->customer_id,
    //             'order_coupon' => $request->order_coupon,
    //             'order_ship' => $request->order_ship,
    //             'order_status' => $request->order_status,
    //             'order_code' => $request->order_code ? $request->order_code : 'ORDER-' . now('Asia/Ho_Chi_Minh')->format('dmYHis') . '-' . Str::upper(Str::random(10)),
    //             'order_total' => $request->order_total,
    //             'created_at' => now('Asia/Ho_Chi_Minh'),
    //         ]);

    //         Shipping::create([
    //             'customer_name' => $request->shipping['customer_name'],
    //             'shipping_address' => $request->shipping['shipping_address'],
    //             'shipping_phone' => $request->shipping['shipping_phone'],
    //             'shipping_email' => $request->shipping['shipping_email'],
    //             'shipping_method' => $request->shipping['shipping_method'],
    //             'shipping_note' => $request->shipping['shipping_note'],
    //             'order_id' => $order->order_id,
    //         ]);

    //         foreach ($request->order_details as $detail) {
    //             $detail['order_id'] = $order->order_id;
    //             OrderDetails::create($detail);

    //             $product = Product::find($detail['product_id']);
    //             if ($product) {
    //                 if ($product->product_quantity < $detail['product_quantity']) {
    //                     throw new \Exception("Sản phẩm {$product->product_name} không đủ hàng trong kho.");
    //                 }

    //                 $product->product_quantity -= $detail['product_quantity'];
    //                 $product->product_sold += $detail['product_quantity'];
    //                 $product->save();
    //             }
    //         }

    //         if ($request->order_coupon) {
    //             $couponParts = explode('-', $request->order_coupon);
    //             $couponCode = $couponParts[0];
    //             $coupon = Coupon::where('coupon_code', $couponCode)->first();

    //             if ($coupon && $coupon->coupon_qty > 0) {
    //                 $coupon->coupon_qty -= 1;
    //                 $coupon->save();
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Tạo đơn hàng thành công!',
    //             'data' => $order->load(['shipping', 'order_details'])
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Đã xảy ra lỗi khi tạo đơn hàng.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'order_coupon' => 'nullable|string',
            'order_ship' => 'required|numeric',
            'order_status' => 'required|integer',
            'order_total' => 'required|numeric',
            'order_code' => 'nullable|string',

            'shipping.customer_name' => 'required|string|max:255',
            'shipping.shipping_address' => 'required|string|max:500',
            'shipping.shipping_phone' => 'required|string|max:20',
            'shipping.shipping_email' => 'required|email|max:255',
            'shipping.shipping_method' => 'required|integer',
            'shipping.shipping_note' => 'nullable|string|max:500',

            'order_details' => 'required|array|min:1',
            'order_details.*.product_id' => 'required|integer',
            'order_details.*.product_name' => 'required|string|max:255',
            'order_details.*.product_price' => 'required|numeric',
            'order_details.*.product_quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ!',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $orderCode = $request->order_code ?? 'ORDER-' . now('Asia/Ho_Chi_Minh')->format('dmYHis') . '-' . Str::upper(Str::random(10));

            // Kiểm tra nếu order_code đã tồn tại
            if (Order::where('order_code', $orderCode)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã được tạo trước đó với mã này.',
                ], 409); // 409 Conflict
            }

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'order_coupon' => $request->order_coupon,
                'order_ship' => $request->order_ship,
                'order_status' => $request->order_status,
                'order_code' => $orderCode,
                'order_total' => $request->order_total,
                'created_at' => now('Asia/Ho_Chi_Minh'),
            ]);

            Shipping::create([
                'customer_name' => $request->shipping['customer_name'],
                'shipping_address' => $request->shipping['shipping_address'],
                'shipping_phone' => $request->shipping['shipping_phone'],
                'shipping_email' => $request->shipping['shipping_email'],
                'shipping_method' => $request->shipping['shipping_method'],
                'shipping_note' => $request->shipping['shipping_note'],
                'order_id' => $order->order_id,
            ]);

            foreach ($request->order_details as $detail) {
                $detail['order_id'] = $order->order_id;
                OrderDetails::create($detail);

                $product = Product::find($detail['product_id']);
                if ($product) {
                    if ($product->product_quantity < $detail['product_quantity']) {
                        throw new \Exception("Sản phẩm {$product->product_name} không đủ hàng trong kho.");
                    }

                    $product->product_quantity -= $detail['product_quantity'];
                    $product->product_sold += $detail['product_quantity'];
                    $product->save();
                }
            }

            if ($request->order_coupon) {
                $couponParts = explode('-', $request->order_coupon);
                $couponCode = $couponParts[0];
                $coupon = Coupon::where('coupon_code', $couponCode)->first();

                if ($coupon && $coupon->coupon_qty > 0) {
                    $coupon->coupon_qty -= 1;
                    $coupon->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công!',
                'data' => $order->load(['shipping', 'order_details'])
            ]);
        } catch (QueryException $e) {
            DB::rollBack();

            // Kiểm tra mã lỗi trùng key (unique constraint violation)
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã được tạo trước đó.',
                ], 409); // Conflict
            }

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi truy vấn khi tạo đơn hàng.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo đơn hàng.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function show(Order $order)
    {
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ], 404);
        }

        $order->load([
            'shipping',
            'order_details.products',
            'change_logs'
        ]);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }




    public function update(Request $request, Order $order) //user update (cancel)
    {
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ], 404);
        }

        $rules = [
            'customer_id'   => 'integer',
            'order_coupon'  => 'nullable|string',
            'order_ship'    => 'numeric',
            'order_status'  => 'integer',
        ];

        $filteredRules = array_filter($rules, fn($key) => $request->has($key), ARRAY_FILTER_USE_KEY);
        $validator = Validator::make($request->all(), $filteredRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $oldStatus = $order->order_status;
        $newStatus = (int) ($validatedData['order_status'] ?? $oldStatus);

        try {
            DB::transaction(function () use ($order, $validatedData, $oldStatus, $newStatus, $request) {
                $user = auth('api')->user(); // lấy người dùng đang đăng nhập

                // Lưu các thay đổi để log lại
                foreach ($validatedData as $field => $newValue) {
                    $oldValue = $order->$field;

                    // Nếu có thay đổi
                    if ($oldValue != $newValue) {
                        OrderChangeLog::create([
                            'order_id'      => $order->order_id,
                            'user_id'       => $user->id,
                            'field'         => $field,
                            'old_value'     => $oldValue,
                            'new_value'     => $newValue,
                            'reason_change' => $request->input('reason_change'), // nullable
                            'changed_at'    => now('Asia/Ho_Chi_Minh'),
                        ]);
                    }
                }

                // Cập nhật đơn hàng
                $order->update($validatedData);
                $order->updated_at = now('Asia/Ho_Chi_Minh');
                $order->save();

                // Nếu hủy đơn (trạng thái từ 0 -> 4)
                if ($oldStatus === 0 && $newStatus === 4) {
                    $order->order_details->each(function ($orderDetail) {
                        $product = $orderDetail->products;
                        if ($product) {
                            $product->product_quantity += $orderDetail->product_quantity;
                            $product->product_sold -= $orderDetail->product_quantity;
                            $product->save();
                        } else {
                            Log::error("Không tìm thấy sản phẩm: order_details_id {$orderDetail->order_details_id}");
                        }
                    });

                    if ($order->order_coupon) {
                        $couponCode = explode('-', $order->order_coupon)[0];
                        $coupon = Coupon::where('coupon_code', $couponCode)->first();
                        if ($coupon) {
                            $coupon->coupon_qty += 1;
                            $coupon->save();
                        }
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công!',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật đơn hàng!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function updateOrderStatus(Request $request, $order_code) //admin update
    {
        $request->validate([
            'order_status'   => 'required|integer|in:0,1,2,3,4,5',
            'reason_change'  => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::where('order_code', $order_code)->first();
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng không tồn tại!'
                ], 404);
            }

            $old_status = $order->order_status;
            $new_status = $request->order_status;

            // Nếu trạng thái mới là hủy/hoàn
            if (in_array($old_status, [0, 1, 2]) && in_array($new_status, [4, 5])) {
                $order->order_details->each(function ($orderDetail) {
                    $product = $orderDetail->products;
                    if ($product) {
                        $product->product_sold -= $orderDetail->product_quantity;
                        $product->product_quantity += $orderDetail->product_quantity;
                        $product->save();
                    } else {
                        Log::error("Không tìm thấy sản phẩm: order_details_id {$orderDetail->order_details_id}");
                    }
                });

                if ($old_status == 0 && $order->order_coupon) {
                    $couponCode = explode('-', $order->order_coupon)[0];
                    $coupon = Coupon::where('coupon_code', $couponCode)->first();
                    if ($coupon) {
                        $coupon->coupon_qty += 1;
                        $coupon->save();
                    }
                }
            }

            $order->order_status = $new_status;
            $order->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            $order->save();

            // Ghi log thay đổi trạng thái đơn hàng
            OrderChangeLog::create([
                'order_id'      => $order->order_id,
                'admin_id'      => auth('admins')->id() ?? null,
                'field'         => 'order_status',
                'old_value'     => [
                    0 => 'Đang xử lí',
                    1 => 'Đã xử lí, chờ giao hàng',
                    2 => 'Đang giao hàng',
                    3 => 'Đã hoàn tất',
                    4 => 'Hủy đơn',
                    5 => 'Hoàn đơn'
                ][$old_status] ?? 'Không xác định',
                'new_value'      => [
                    0 => 'Đang xử lí',
                    1 => 'Đã xử lí, chờ giao hàng',
                    2 => 'Đang giao hàng',
                    3 => 'Đã hoàn tất',
                    4 => 'Hủy đơn',
                    5 => 'Hoàn đơn'
                ][$old_status] ?? 'Không xác định',
                'changed_at'    => Carbon::now('Asia/Ho_Chi_Minh'),
                'reason_change' => $request->reason_change ?? null,
            ]);

            // Nếu trạng thái chuyển từ đang giao (2) sang thành công (3)
            if ($old_status == 2 && $new_status == 3) {
                $orderDate = Carbon::parse($order->created_at)->format('Y-m-d');
                $totalQuantity = 0;
                $totalSales = 0;
                $totalProfit = 0;
                $totalCost = 0;

                $orderCodeParts = explode('-', $order->order_coupon);
                $discountAmount = isset($orderCodeParts[1]) ? (float)$orderCodeParts[1] : 0;

                $order->load('order_details.products');

                foreach ($order->order_details as $detail) {
                    $totalQuantity += $detail->product_quantity;
                    $totalSales += $detail->product_quantity * $detail->product_price;

                    if ($detail->products) {
                        $productId = $detail->product_id;
                        $quantityNeeded = $detail->product_quantity;
                        $costForThisDetail = 0;

                        $importBatches = ImportProduct::where('product_id', $productId)
                            ->whereDate('created_at', '<=', $orderDate)
                            ->orderBy('created_at', 'asc')
                            ->get();

                        foreach ($importBatches as $batch) {
                            if ($quantityNeeded <= 0) break;

                            $availableQty = $batch->quantity_in - $batch->quantity_sold;
                            if ($availableQty <= 0) continue;

                            $usedQty = min($quantityNeeded, $availableQty);

                            $costForThisDetail += $usedQty * $batch->price_in;

                            $batch->quantity_sold += $usedQty;
                            $batch->save();

                            $quantityNeeded -= $usedQty;
                        }

                        if ($quantityNeeded > 0) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Số lượng sản phẩm ID {$productId} không đủ trong kho để xử lý đơn hàng."
                            ], 400);
                        }

                        $totalCost += $costForThisDetail;
                    }
                }

                $totalBeforeTax = $totalSales - $discountAmount;
                $tax = $totalBeforeTax * 0.08;
                $totalSales = $totalBeforeTax + $tax;
                $totalProfit = $totalSales - $totalCost;

                $statistic = Statistics::where('order_date', $orderDate)->first();

                if ($statistic) {
                    $statistic->quantity += $totalQuantity;
                    $statistic->total_order += 1;
                    $statistic->sales += $totalSales;
                    $statistic->profit += $totalProfit;
                    $statistic->save();
                } else {
                    Statistics::create([
                        'order_date'   => $orderDate,
                        'quantity'     => $totalQuantity,
                        'total_order'  => 1,
                        'sales'        => $totalSales,
                        'profit'       => $totalProfit,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật trạng thái đơn hàng thất bại!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    // Xoá đơn hàng
    public function destroy(Request $request, Order $order)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ. Bạn cần đăng nhập.'
            ], 401);
        }
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng!'
            ], 404);
        }


        OrderDetails::where('order_id', $order->order_id)->delete();
        Shipping::where('order_id', $order->order_id)->delete();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá đơn hàng thành công!'
        ]);
    }
    public function downloadPdf($order_code)
    {
        $order = Order::with(['order_details.products', 'shipping'])
            ->where('order_code', $order_code)
            ->firstOrFail();

        $pdf = Pdf::loadView('admin.order.order_pdf', compact('order'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="order_' . $order_code . '.pdf"');
    }






    public function createVnpayUrl(Request $request)
    {
        $vnp_TmnCode = 'CR82E1UG';
        $vnp_HashSecret = 'GNLRH7YIVH55E2Q1QS7V21AW1OE2UZSB';
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl  = 'http://127.0.0.1:8000/check-out-completed';

        $vnp_TxnRef = 'ORDER-' . now('Asia/Ho_Chi_Minh')->format('dmYHis') . '-' . Str::upper(Str::random(6));
        $vnp_OrderInfo = 'Thanh toán đơn - ' . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int)$request->order_total * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,

        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json([
            'code' => '00',
            'message' => 'success',
            'vnpUrl' => $vnp_Url,
        ]);
    }
    public function createVnpayShippingUrl(Request $request)
    {
        $vnp_TmnCode = 'CR82E1UG';
        $vnp_HashSecret = 'GNLRH7YIVH55E2Q1QS7V21AW1OE2UZSB';
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl  = 'http://127.0.0.1:8000/order_details/' . $request->order_code;

        $vnp_TxnRef = 'Change_Address_Order_' . Str::upper(Str::random(6));
        $vnp_OrderInfo = 'Thanh toán phần chênh lệch phí vận chuyển cho đơn hàng: ' . $request->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int)$request->amount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,

        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json([
            'code' => '00',
            'message' => 'success',
            'vnpUrl' => $vnp_Url,
        ]);
    }
    public function getOrderHistory($order_code)
    {
        // Tìm đơn hàng theo mã
        $order = Order::where('order_code', $order_code)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.'
            ], 404);
        }

        // Lấy lịch sử cập nhật theo order_id và load quan hệ admin, user
        $logs = OrderChangeLog::with(['admin', 'user'])
            ->where('order_id', $order->order_id)
            ->orderByDesc('changed_at')
            ->get();

        // Chuẩn bị dữ liệu trả về
        $histories = $logs->map(function ($log) use ($order) {
            return [
                'order_code' => $order->order_code,
                'reason_change' => $log->reason_change,
                'admin_name' => $log->admin ? $log->admin->admin_name : null,
                'customer_name' => $log->user ? $log->user->name : null,
                'field' => $log->field,
                'old_value' => $log->old_value,
                'new_value' => $log->new_value,
                'created_at' => $log->changed_at,
            ];
        });

        return response()->json([
            'success' => true,
            'histories' => $histories
        ]);
    }
    public function updateShipping(Request $request)
    {
        $validatedOrderCode = $request->input('order_code');

        $order = Order::with('shipping')->where('order_code', $validatedOrderCode)->first();

        if (!$order || !$order->shipping) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng hoặc thông tin giao hàng.'
            ], 404);
        }

        // Xây dựng rules chỉ cho các trường gửi lên
        $rules = [];

        if ($request->has('name')) {
            $rules['name'] = ['string', 'max:255'];
        }

        if ($request->has('phone')) {
            $rules['phone'] = ['regex:/^0\d{9}$/'];
        }

        if ($request->has('email')) {
            $rules['email'] = ['email'];
        }

        if ($request->has('address')) {
            $rules['address'] = ['string', 'max:500'];
        }

        if ($request->has('feeship')) {
            $rules['feeship'] = ['numeric', 'min:0'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $shipping = $order->shipping;
        $changedAt = Carbon::now('Asia/Ho_Chi_Minh');
        $reason = $request->reason_change ?? null;
        $userId = $request->user_id ?? null; // Nếu có xác thực người dùng

        // Danh sách các trường có thể thay đổi
        $changes = [
            'name'    => ['old' => $shipping->customer_name, 'new' => $request->name ?? null, 'field' => 'customer_name'],
            'phone'   => ['old' => $shipping->shipping_phone, 'new' => $request->phone ?? null, 'field' => 'shipping_phone'],
            'email'   => ['old' => $shipping->shipping_email, 'new' => $request->email ?? null, 'field' => 'shipping_email'],
            'address' => ['old' => $shipping->shipping_address, 'new' => $request->address ?? null, 'field' => 'shipping_address'],
            'feeship' => ['old' => $order->order_ship, 'new' => $request->feeship ?? null, 'field' => 'order_ship'],
        ];

        $hasChanges = false;

        foreach ($changes as $key => $change) {
            if ($request->has($key) && $change['old'] != $change['new']) {
                $hasChanges = true;

                // Ghi log thay đổi
                OrderChangeLog::create([
                    'order_id'      => $order->order_id,
                    'user_id'       => $userId,
                    'field'         => $change['field'],
                    'old_value'     => $change['old'],
                    'new_value'     => $change['new'],
                    'changed_at'    => $changedAt,
                    'reason_change' => $reason,
                ]);

                // Cập nhật giá trị mới
                if ($key === 'name') {
                    $shipping->customer_name = $change['new'];
                } elseif ($key === 'phone') {
                    $shipping->shipping_phone = $change['new'];
                } elseif ($key === 'email') {
                    $shipping->shipping_email = $change['new'];
                } elseif ($key === 'address') {
                    $shipping->shipping_address = $change['new'];
                } elseif ($key === 'feeship') {
                    $order->order_ship = $change['new'];
                }
            }
        }

        if (!$hasChanges) {
            return response()->json([
                'success' => false,
                'message' => 'Không có thay đổi nào để cập nhật.'
            ], 200);
        }

        $shipping->save();
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin giao hàng thành công!'
        ]);
    }
}
