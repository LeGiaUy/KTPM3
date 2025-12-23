<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller quản lý giỏ hàng của người dùng
 */
class CartController extends Controller
{
    /**
     * Lấy thông tin giỏ hàng của người dùng hiện tại
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cart = Cart::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$cart) {
            return response()->json(['items' => []]);
        }

        return response()->json($cart);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        // Tìm hoặc tạo giỏ hàng active cho user
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active']
        );

        $product = Product::findOrFail($request->product_id);

        // Kiểm tra số lượng tồn kho
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Không đủ số lượng tồn kho'
            ], 400);
        }

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $cartItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Cập nhật số lượng nếu sản phẩm đã có
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            // Kiểm tra tổng số lượng không vượt quá tồn kho
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'message' => 'Số lượng vượt quá tồn kho hiện có'
                ], 400);
            }
            
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Tạo mới item trong giỏ hàng
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price, // Lưu giá tại thời điểm thêm vào giỏ
            ]);
        }

        return response()->json([
            'message' => 'Đã thêm vào giỏ hàng thành công',
            'cart' => $cart->load('items.product')
        ]);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $cartItem = $cart->items()->where('id', $id)->firstOrFail();
        $product = $cartItem->product;

        // Kiểm tra số lượng không vượt quá tồn kho
        if ($product && $product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Số lượng vượt quá tồn kho hiện có'
            ], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Cập nhật giỏ hàng thành công',
            'cart' => $cart->load('items.product')
        ]);
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $cartItem = $cart->items()->where('id', $id)->firstOrFail();
        $cartItem->delete();

        return response()->json([
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'cart' => $cart->load('items.product')
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($cart) {
            $cart->items()->delete();
            return response()->json([
                'message' => 'Đã xóa toàn bộ giỏ hàng',
                'cart' => $cart->load('items.product')
            ]);
        }

        return response()->json([
            'message' => 'Giỏ hàng đã trống',
            'cart' => ['items' => []]
        ]);
    }
}
