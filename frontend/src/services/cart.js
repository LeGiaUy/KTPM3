// H6: i18n, store
import api from "./api";

/**
 * Service quản lý các API liên quan đến giỏ hàng
 */
export default {
  /**
   * Lấy thông tin giỏ hàng của người dùng hiện tại
   * @returns {Promise} - Promise trả về dữ liệu giỏ hàng
   */
  getCart() {
    return api.get("/cart");
  },

  /**
   * Thêm sản phẩm vào giỏ hàng
   * @param {number} product_id - ID của sản phẩm
   * @param {number} quantity - Số lượng sản phẩm
   * @returns {Promise} - Promise trả về kết quả thêm vào giỏ hàng
   */
  addToCart(product_id, quantity) {
    return api.post("/cart", { product_id, quantity });
  },

  /**
   * Cập nhật số lượng sản phẩm trong giỏ hàng
   * @param {number} item_id - ID của item trong giỏ hàng
   * @param {number} quantity - Số lượng mới
   * @returns {Promise} - Promise trả về kết quả cập nhật
   */
  updateItem(item_id, quantity) {
    return api.put(`/cart/${item_id}`, { quantity });
  },

  /**
   * Xóa một sản phẩm khỏi giỏ hàng
   * @param {number} item_id - ID của item trong giỏ hàng
   * @returns {Promise} - Promise trả về kết quả xóa
   */
  removeItem(item_id) {
    return api.delete(`/cart/${item_id}`);
  },

  /**
   * Xóa toàn bộ giỏ hàng
   * @returns {Promise} - Promise trả về kết quả xóa
   */
  clearCart() {
    return api.delete("/cart");
  },
};
