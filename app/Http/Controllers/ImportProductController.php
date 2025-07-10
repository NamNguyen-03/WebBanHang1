<?php

namespace App\Http\Controllers;

use App\Models\ImportProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class ImportProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $imports = ImportProduct::with('products')->latest();

        if ($search) {
            $imports->whereHas('products', function ($query) use ($search) {
                $query->where('product_name', 'LIKE', "%$search%");
            });
        }

        $imports = $imports->get();

        if ($imports->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm nhập nào'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $imports,
            'message' => 'Lấy danh sách sản phẩm nhập thành công'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:tbl_product,product_id',
            'quantity_in' => 'required|integer|min:1',
            'price_in' => 'required|numeric|min:0',
        ]);

        $import = ImportProduct::create([
            'product_id' => $request->product_id,
            'quantity_in' => $request->quantity_in,
            'price_in' => $request->price_in,
            'created_at' => now('Asia/Ho_Chi_Minh'),
        ]);

        // Cập nhật tồn kho
        $product = Product::find($request->product_id);
        $product->increment('product_quantity', $request->quantity_in);

        return response()->json([
            'success' => true,
            'message' => 'Nhập hàng thành công',
            'data' => $import,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $import = ImportProduct::with('products')->find($id);

        if (!$import) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiếu nhập',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết phiếu nhập thành công',
            'data' => $import,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'quantity_in' => 'required|integer|min:1',
            'price_in' => 'required|numeric|min:0',
        ]);

        $import = ImportProduct::findOrFail($id);

        $oldQuantity = $import->quantity_in;

        // Cập nhật bản ghi nhập hàng
        $import->update([
            'quantity_in' => $request->quantity_in,
            'price_in' => $request->price_in,
        ]);

        // Cập nhật lại tồn kho
        $product = Product::find($import->product_id);
        $diff = $request->quantity_in - $oldQuantity;
        $product->increment('product_quantity', $diff);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật phiếu nhập thành công',
            'data' => $import,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $import = ImportProduct::findOrFail($id);
        $quantity = $import->quantity_in;

        // Trừ số lượng đã nhập khỏi tồn kho
        $product = Product::find($import->product_id);
        $product->decrement('product_quantity', $quantity);

        $import->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa phiếu nhập thành công',
        ]);
    }
}
