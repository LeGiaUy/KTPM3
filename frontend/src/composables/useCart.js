// H1: import runtime functions
import { ref } from "vue";

// H6: i18n, store
import cartService from "../services/cart";

// H7: variables
/**
 * Trạng thái giỏ hàng hiện tại
 */
const cart = ref(null);

/**
 * Trạng thái đang tải dữ liệu
 */
const loading = ref(false);

/**
 * Lỗi xảy ra khi thao tác với giỏ hàng
 */
const error = ref(null);

/**
 * Composable quản lý giỏ hàng của người dùng
 * @returns {Object} - Các hàm và biến liên quan đến giỏ hàng
 */
export function useCart() {
  /**
   * Lấy thông tin giỏ hàng từ server
   */
  const fetchCart = async () => {
    loading.value = true;
    try {
      const response = await cartService.getCart();
      cart.value = response.data;
    } catch (e) {
      error.value = e;
      console.error("Lỗi khi lấy giỏ hàng:", e);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Thêm sản phẩm vào giỏ hàng
   * @param {number} product_id - ID của sản phẩm
   * @param {number} quantity - Số lượng sản phẩm (mặc định: 1)
   * @returns {Promise<Object>} - Dữ liệu phản hồi từ server
   */
  const addToCart = async (product_id, quantity = 1) => {
    loading.value = true;
    try {
      const response = await cartService.addToCart(product_id, quantity);
      // Cập nhật trạng thái giỏ hàng từ response
      if (response.data.cart) {
        cart.value = response.data.cart;
      } else {
        // Nếu backend không trả về đầy đủ giỏ hàng, làm mới lại
        await fetchCart();
      }
      return response.data;
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Cập nhật số lượng sản phẩm trong giỏ hàng
   * @param {number} item_id - ID của item trong giỏ hàng
   * @param {number} quantity - Số lượng mới
   */
  const updateQuantity = async (item_id, quantity) => {
    loading.value = true;
    try {
      const response = await cartService.updateItem(item_id, quantity);
      if (response.data.cart) {
        cart.value = response.data.cart;
      } else {
        await fetchCart();
      }
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Xóa sản phẩm khỏi giỏ hàng
   * @param {number} item_id - ID của item trong giỏ hàng
   */
  const removeFromCart = async (item_id) => {
    loading.value = true;
    try {
      const response = await cartService.removeItem(item_id);
      if (response.data.cart) {
        cart.value = response.data.cart;
      } else {
        await fetchCart();
      }
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Xóa toàn bộ giỏ hàng
   */
  const clearCart = async () => {
    loading.value = true;
    try {
      const response = await cartService.clearCart();
      if (response.data.cart) {
        cart.value = response.data.cart;
      } else {
        await fetchCart();
      }
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Tính tổng số lượng sản phẩm trong giỏ hàng
   * @returns {number} - Tổng số lượng sản phẩm
   */
  const cartItemCount = () => {
    if (!cart.value || !cart.value.items) return 0;
    return cart.value.items.reduce((total, item) => total + item.quantity, 0);
  };

  return {
    cart,
    loading,
    error,
    fetchCart,
    addToCart,
    updateQuantity,
    removeFromCart,
    clearCart,
    cartItemCount,
  };
}
