<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      class="fixed left-0 top-0 h-full w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out z-30"
    >
      <div class="p-6">
        <h1 class="text-2xl font-bold mb-8">Admin Panel</h1>
        <nav class="space-y-2">
          <a
            href="#"
            @click.prevent="$emit('navigate', 'dashboard')"
            class="flex items-center px-4 py-3 bg-gray-800 rounded-lg text-white"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
              />
            </svg>
            Dashboard
          </a>
          <a
            href="#"
            @click.prevent="$emit('navigate', 'products')"
            class="flex items-center px-4 py-3 hover:bg-gray-800 rounded-lg text-gray-300"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
              />
            </svg>
            Sản phẩm
          </a>
          <a
            href="#"
            class="flex items-center px-4 py-3 hover:bg-gray-800 rounded-lg text-gray-300"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
            Người dùng
          </a>
          <a
            href="#"
            class="flex items-center px-4 py-3 hover:bg-gray-800 rounded-lg text-gray-300"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
            Thống kê
          </a>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm sticky top-0 z-20">
        <div class="px-6 py-4 flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
          <div class="flex items-center space-x-4">
            <span class="text-gray-700">Xin chào, {{ user?.name }}</span>
            <button
              @click="$emit('logout')"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            >
              Đăng xuất
            </button>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <main class="p-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 text-sm font-medium">Tổng sản phẩm</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                  {{ stats.totalProducts }}
                </p>
              </div>
              <div class="bg-blue-100 p-3 rounded-full">
                <svg
                  class="w-8 h-8 text-blue-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                  />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 text-sm font-medium">
                  Sản phẩm đang hoạt động
                </p>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                  {{ stats.activeProducts }}
                </p>
              </div>
              <div class="bg-green-100 p-3 rounded-full">
                <svg
                  class="w-8 h-8 text-green-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 text-sm font-medium">Tổng tồn kho</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                  {{ stats.totalStock }}
                </p>
              </div>
              <div class="bg-purple-100 p-3 rounded-full">
                <svg
                  class="w-8 h-8 text-purple-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                  />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 text-sm font-medium">Tổng giá trị</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                  {{ formatCurrency(stats.totalValue) }}
                </p>
              </div>
              <div class="bg-yellow-100 p-3 rounded-full">
                <svg
                  class="w-8 h-8 text-yellow-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Products Table -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Sản phẩm gần đây
            </h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    ID
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Hình ảnh
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Tên sản phẩm
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Giá
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Tồn kho
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Trạng thái
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Thao tác
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-if="loading"
                  v-for="i in 5"
                  :key="i"
                  class="animate-pulse"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-12"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-16 w-16 bg-gray-200 rounded"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-48"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-16"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-16"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                  </td>
                </tr>
                <tr v-else-if="products.length === 0">
                  <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    Chưa có sản phẩm nào
                  </td>
                </tr>
                <tr
                  v-else
                  v-for="product in products"
                  :key="product.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    #{{ product.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <img
                      v-if="product.cover_url"
                      :src="product.cover_url"
                      :alt="product.title"
                      class="h-16 w-16 object-cover rounded"
                    />
                    <div
                      v-else
                      class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center"
                    >
                      <svg
                        class="w-8 h-8 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                      </svg>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ product.title }}
                    </div>
                    <div v-if="product.author" class="text-sm text-gray-500">
                      {{ product.author }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(product.price) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ product.stock }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="
                        product.status === 'active'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      "
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    >
                      {{
                        product.status === "active"
                          ? "Hoạt động"
                          : "Ngừng hoạt động"
                      }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button
                      @click="$emit('navigate', 'products')"
                      class="text-indigo-600 hover:text-indigo-900 mr-3"
                    >
                      Xem
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div
            v-if="products.length > 0"
            class="px-6 py-4 border-t border-gray-200 flex justify-between items-center"
          >
            <div class="text-sm text-gray-600">
              <span v-if="pagination && pagination.total > 5">
                Hiển thị 5 sản phẩm đầu tiên trong tổng số
                {{ pagination.total }} sản phẩm
              </span>
              <span v-else>
                Hiển thị tất cả {{ pagination?.total || products.length }} sản
                phẩm
              </span>
            </div>
            <button
              @click="$emit('navigate', 'products')"
              class="text-indigo-600 hover:text-indigo-900 font-medium"
            >
              Xem tất cả sản phẩm →
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api.js";

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

defineEmits(["logout", "navigate"]);

const loading = ref(true);
const products = ref([]);
const pagination = ref(null);
const stats = ref({
  totalProducts: 0,
  activeProducts: 0,
  totalStock: 0,
  totalValue: 0,
});

const fetchProducts = async () => {
  try {
    loading.value = true;

    // Fetch recent products
    const productsResponse = await api.get("/admin/products?per_page=5");
    products.value = productsResponse.data.data || [];
    pagination.value = {
      current_page: productsResponse.data.current_page || 1,
      last_page: productsResponse.data.last_page || 1,
      total: productsResponse.data.total || 0,
    };
  } catch (error) {
    console.error("Error fetching products:", error);
  } finally {
    loading.value = false;
  }
};

const fetchStatistics = async () => {
  try {
    const statsResponse = await api.get("/admin/products/statistics");
    if (statsResponse.data) {
      stats.value = {
        totalProducts: statsResponse.data.totalProducts ?? 0,
        activeProducts: statsResponse.data.activeProducts ?? 0,
        totalStock: statsResponse.data.totalStock ?? 0,
        totalValue: statsResponse.data.totalValue ?? 0,
      };
    }
  } catch (error) {
    console.error("Error fetching statistics:", error);
    // Giữ nguyên giá trị mặc định nếu có lỗi
  }
};

const formatCurrency = (value) => {
  const numValue = Number(value) || 0;
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(numValue);
};

onMounted(() => {
  fetchProducts();
  fetchStatistics();
});
</script>
