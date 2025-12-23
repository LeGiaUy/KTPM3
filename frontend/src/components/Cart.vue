<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Header :user="user" @logout="$emit('logout')" />
    <Navigation />

    <main class="flex-1 max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">Giỏ hàng của bạn</h1>

      <!-- Trạng thái đang tải -->
      <div v-if="loading" class="text-center py-10">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"
        ></div>
        <p class="mt-4 text-gray-500">Đang tải giỏ hàng...</p>
      </div>

      <!-- Danh sách sản phẩm trong giỏ hàng -->
      <div
        v-else-if="cart && cart.items && cart.items.length > 0"
        class="bg-white shadow overflow-hidden sm:rounded-lg"
      >
        <ul role="list" class="divide-y divide-gray-200">
          <li
            v-for="item in cart.items"
            :key="item.id"
            class="px-4 py-4 sm:px-6 hover:bg-gray-50"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <!-- Hình ảnh sản phẩm -->
                <div
                  class="shrink-0 h-16 w-16 bg-gray-100 rounded-md overflow-hidden"
                >
                  <img
                    v-if="item.product && item.product.cover_url"
                    :src="item.product.cover_url"
                    :alt="item.product.title"
                    class="h-full w-full object-cover"
                  />
                  <div
                    v-else
                    class="h-full w-full flex items-center justify-center text-gray-400"
                  >
                    <svg
                      class="h-8 w-8"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                  </div>
                </div>
                <!-- Thông tin sản phẩm -->
                <div class="ml-4">
                  <div class="text-sm font-medium text-indigo-600 truncate">
                    {{
                      item.product
                        ? item.product.title
                        : "Sản phẩm không còn tồn tại"
                    }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{
                      formatCurrency(
                        item.price || (item.product ? item.product.price : 0)
                      )
                    }}
                  </div>
                </div>
              </div>
              <!-- Điều khiển số lượng và giá -->
              <div class="flex items-center space-x-4">
                <!-- Nút tăng/giảm số lượng -->
                <div class="flex items-center border border-gray-300 rounded">
                  <button
                    @click="handleUpdateQuantity(item.id, item.quantity - 1)"
                    :disabled="item.quantity <= 1"
                    class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
                  >
                    -
                  </button>
                  <span class="px-2 py-1 text-gray-900 min-w-8 text-center">
                    {{ item.quantity }}
                  </span>
                  <button
                    @click="handleUpdateQuantity(item.id, item.quantity + 1)"
                    class="px-2 py-1 text-gray-600 hover:bg-gray-100"
                  >
                    +
                  </button>
                </div>
                <!-- Tổng tiền của sản phẩm -->
                <div class="font-bold text-gray-900 w-24 text-right">
                  {{
                    formatCurrency(
                      item.quantity *
                        (item.price || (item.product ? item.product.price : 0))
                    )
                  }}
                </div>
                <!-- Nút xóa sản phẩm -->
                <button
                  @click="handleRemoveFromCart(item.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </li>
        </ul>
        <!-- Tổng tiền và nút thanh toán -->
        <div class="px-4 py-4 sm:px-6 bg-gray-50 flex justify-end items-center">
          <div class="text-xl font-bold text-gray-900 mr-4">
            Tổng cộng: {{ formatCurrency(totalPrice) }}
          </div>
          <button
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700"
          >
            Thanh toán
          </button>
        </div>
      </div>

      <!-- Trạng thái giỏ hàng trống -->
      <div v-else class="text-center py-10 bg-white shadow rounded-lg">
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4m-2 5a2 2 0 11-4 0 2 2 0 014 0z"
          />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Giỏ hàng trống</h3>
        <p class="mt-1 text-sm text-gray-500">
          Hãy thêm sản phẩm vào giỏ hàng của bạn.
        </p>
        <div class="mt-6">
          <router-link
            :to="{ name: 'home' }"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Tiếp tục mua sắm
          </router-link>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
// H1: import runtime functions
import { computed, onMounted } from "vue";

// H2: import components
import Header from "./Header.vue";
import Navigation from "./Navigation.vue";
import Footer from "./Footer.vue";

// H6: i18n, store
import { useCart } from "../composables/useCart";

// H5: props, emits
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["logout"]);

// H7: variables
const { cart, loading, fetchCart, updateQuantity, removeFromCart } = useCart();

// H9: computed
/**
 * Tính tổng tiền của tất cả sản phẩm trong giỏ hàng
 */
const totalPrice = computed(() => {
  if (!cart.value || !cart.value.items) return 0;
  return cart.value.items.reduce((total, item) => {
    const price = item.price || (item.product ? item.product.price : 0);
    return total + price * item.quantity;
  }, 0);
});

// H10: functions
/**
 * Định dạng số tiền theo định dạng VND
 * @param {number} value - Giá trị cần định dạng
 * @returns {string} - Chuỗi đã được định dạng
 */
const formatCurrency = (value) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(value);
};

/**
 * Xử lý cập nhật số lượng sản phẩm trong giỏ hàng
 * @param {number} item_id - ID của item trong giỏ hàng
 * @param {number} quantity - Số lượng mới
 */
const handleUpdateQuantity = async (item_id, quantity) => {
  if (quantity < 1) return;
  try {
    await updateQuantity(item_id, quantity);
  } catch (error) {
    console.error("Lỗi khi cập nhật số lượng:", error);
    alert("Có lỗi xảy ra khi cập nhật số lượng");
  }
};

/**
 * Xử lý xóa sản phẩm khỏi giỏ hàng
 * @param {number} item_id - ID của item trong giỏ hàng
 */
const handleRemoveFromCart = async (item_id) => {
  if (!confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) {
    return;
  }
  try {
    await removeFromCart(item_id);
  } catch (error) {
    console.error("Lỗi khi xóa sản phẩm:", error);
    alert("Có lỗi xảy ra khi xóa sản phẩm");
  }
};

// H8: lifecycle hooks
onMounted(() => {
  fetchCart();
});
</script>
