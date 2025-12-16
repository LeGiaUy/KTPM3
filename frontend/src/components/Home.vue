<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Header :user="user" @logout="$emit('logout')" />
    <Navigation />

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
      <!-- Page Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Trang chủ</h1>
        <p class="mt-2 text-gray-600">
          Chào mừng bạn đến với KTPM3 - Khám phá bộ sưu tập sách đa dạng
        </p>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
      >
        <div
          v-for="i in 8"
          :key="i"
          class="bg-white rounded-lg shadow-md overflow-hidden animate-pulse"
        >
          <div class="h-64 bg-gray-200"></div>
          <div class="p-4">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div
        v-else-if="products.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
      >
        <div
          v-for="product in products"
          :key="product.id"
          @click="viewProduct(product.id)"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow cursor-pointer"
        >
          <div class="h-64 bg-gray-100 flex items-center justify-center p-4">
            <img
              v-if="product.cover_url"
              :src="product.cover_url"
              :alt="product.title"
              class="max-h-full max-w-full object-contain rounded shadow-sm"
            />
            <div
              v-else
              class="w-full h-full flex items-center justify-center bg-gray-200"
            >
              <svg
                class="w-16 h-16 text-gray-400"
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
          </div>
          <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
              {{ product.title }}
            </h3>
            <p v-if="product.author" class="text-sm text-gray-600 mb-2">
              {{ product.author }}
            </p>
            <div class="flex items-center justify-between">
              <span class="text-xl font-bold text-indigo-600">
                {{ formatCurrency(product.price) }}
              </span>
              <span v-if="product.stock > 0" class="text-sm text-gray-500">
                Còn {{ product.stock }} sản phẩm
              </span>
              <span v-else class="text-sm text-red-500 font-medium">
                Hết hàng
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
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
        <h3 class="mt-2 text-sm font-medium text-gray-900">
          Không tìm thấy sản phẩm
        </h3>
        <p class="mt-1 text-sm text-gray-500">Hiện chưa có sản phẩm nào</p>
      </div>

      <!-- View All Products Link -->
      <div v-if="products.length > 0" class="mt-8 text-center">
        <router-link
          :to="{ name: 'products' }"
          class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
        >
          Xem tất cả sản phẩm
        </router-link>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api.js";
import Header from "./Header.vue";
import Navigation from "./Navigation.vue";
import Footer from "./Footer.vue";

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const router = useRouter();
const emit = defineEmits(["logout"]);

const loading = ref(false);
const products = ref([]);

const fetchProducts = async () => {
  try {
    loading.value = true;
    const params = new URLSearchParams({
      per_page: "8",
    });

    const response = await api.get(`/products?${params.toString()}`);
    products.value = response.data.data || [];
  } catch (error) {
    console.error("Error fetching products:", error);
    alert("Lỗi khi tải danh sách sản phẩm");
  } finally {
    loading.value = false;
  }
};

const viewProduct = (productId) => {
  router.push({ name: "product-detail", params: { id: productId } });
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(value);
};

onMounted(() => {
  fetchProducts();
});
</script>
