<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Header :user="user" @logout="$emit('logout')" />
    <Navigation />

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-lg shadow-md p-8">
        <div class="animate-pulse">
          <div class="h-64 bg-gray-200 rounded-lg mb-4"></div>
          <div class="h-8 bg-gray-200 rounded w-3/4 mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>

      <!-- Product Detail -->
      <div
        v-else-if="product"
        class="bg-white rounded-lg shadow-md overflow-hidden"
      >
        <div class="md:flex">
          <!-- Product Image -->
          <div class="md:w-1/2">
            <div
              class="h-96 md:min-h-[500px] bg-gray-100 flex items-center justify-center p-8"
            >
              <img
                v-if="product.cover_url"
                :src="product.cover_url"
                :alt="product.title"
                class="max-h-full max-w-full object-contain rounded shadow-lg"
              />
              <svg
                v-else
                class="w-32 h-32 text-gray-400"
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

          <!-- Product Info -->
          <div class="md:w-1/2 p-6 md:p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
              {{ product.title }}
            </h1>

            <div class="space-y-4 mb-6">
              <div v-if="product.author">
                <span class="text-sm font-medium text-gray-500">Tác giả:</span>
                <span class="ml-2 text-gray-900">{{ product.author }}</span>
              </div>

              <div v-if="product.publisher">
                <span class="text-sm font-medium text-gray-500"
                  >Nhà xuất bản:</span
                >
                <span class="ml-2 text-gray-900">{{ product.publisher }}</span>
              </div>

              <div v-if="product.isbn">
                <span class="text-sm font-medium text-gray-500">ISBN:</span>
                <span class="ml-2 text-gray-900">{{ product.isbn }}</span>
              </div>

              <div v-if="product.publish_date">
                <span class="text-sm font-medium text-gray-500"
                  >Ngày xuất bản:</span
                >
                <span class="ml-2 text-gray-900">
                  {{
                    new Date(product.publish_date).toLocaleDateString("vi-VN")
                  }}
                </span>
              </div>

              <div v-if="product.pages">
                <span class="text-sm font-medium text-gray-500">Số trang:</span>
                <span class="ml-2 text-gray-900"
                  >{{ product.pages }} trang</span
                >
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6 mb-6">
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl font-bold text-indigo-600">
                  {{ formatCurrency(product.price) }}
                </span>
                <span
                  v-if="product.stock > 0"
                  class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium"
                >
                  Còn {{ product.stock }} sản phẩm
                </span>
                <span
                  v-else
                  class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium"
                >
                  Hết hàng
                </span>
              </div>

              <div class="flex space-x-3">
                <button
                  :disabled="product.stock === 0"
                  :class="
                    product.stock === 0
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-indigo-700'
                  "
                  class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium transition-colors"
                >
                  Thêm vào giỏ hàng
                </button>
                <button
                  :disabled="product.stock === 0"
                  :class="
                    product.stock === 0
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-green-700'
                  "
                  class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg font-medium transition-colors"
                >
                  Mua ngay
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div
          v-if="product.description"
          class="border-t border-gray-200 p-6 md:p-8"
        >
          <h2 class="text-xl font-semibold text-gray-900 mb-4">
            Mô tả sản phẩm
          </h2>
          <p class="text-gray-700 whitespace-pre-line">
            {{ product.description }}
          </p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="bg-white rounded-lg shadow-md p-8 text-center">
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
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">
          Không tìm thấy sản phẩm
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          Sản phẩm không tồn tại hoặc đã bị xóa
        </p>
        <button
          @click="$router.push({ name: 'home' })"
          class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
        >
          Quay lại
        </button>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
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

const route = useRoute();
const emit = defineEmits(["logout"]);

const loading = ref(false);
const product = ref(null);

const fetchProduct = async () => {
  const productId = route.params.id;
  if (!productId) return;

  try {
    loading.value = true;
    const response = await api.get(`/products/${productId}`);
    product.value = response.data;
  } catch (error) {
    console.error("Error fetching product:", error);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(value);
};

watch(
  () => route.params.id,
  () => {
    fetchProduct();
  },
  { immediate: true }
);

onMounted(() => {
  fetchProduct();
});
</script>
