<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Header :user="user" @logout="$emit('logout')" />
    <Navigation />

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
      <!-- Page Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tất cả sản phẩm</h1>
        <p class="mt-2 text-gray-600">
          Khám phá bộ sưu tập sách đa dạng của chúng tôi
        </p>
      </div>

      <!-- Search and Filter -->
      <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <input
                v-model="searchQuery"
                @input="handleSearch"
                type="text"
                placeholder="Tìm kiếm sản phẩm..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
              <svg
                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </div>
          </div>
        </div>
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
        <p class="mt-1 text-sm text-gray-500">
          {{
            searchQuery
              ? "Thử tìm kiếm với từ khóa khác"
              : "Hiện chưa có sản phẩm nào"
          }}
        </p>
      </div>

      <!-- Pagination -->
      <div
        v-if="pagination && pagination.last_page > 1"
        class="mt-8 flex justify-center"
      >
        <div class="flex items-center space-x-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            :class="
              pagination.current_page === 1
                ? 'opacity-50 cursor-not-allowed'
                : 'hover:bg-gray-50'
            "
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium"
          >
            ‹ Trước
          </button>

          <div class="flex space-x-1">
            <template v-for="page in getPageNumbers()" :key="page">
              <button
                v-if="page !== '...'"
                @click="changePage(page)"
                :class="
                  page === pagination.current_page
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'border-gray-300 hover:bg-gray-50'
                "
                class="px-3 py-2 border rounded-lg text-sm font-medium min-w-[40px]"
              >
                {{ page }}
              </button>
              <span v-else class="px-3 py-2 text-sm text-gray-500">
                {{ page }}
              </span>
            </template>
          </div>

          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            :class="
              pagination.current_page === pagination.last_page
                ? 'opacity-50 cursor-not-allowed'
                : 'hover:bg-gray-50'
            "
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium"
          >
            Sau ›
          </button>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
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

const emit = defineEmits(["logout"]);
const router = useRouter();

const loading = ref(false);
const products = ref([]);
const pagination = ref(null);
const searchQuery = ref("");
const searchTimeout = ref(null);

const fetchProducts = async (page = 1) => {
  try {
    loading.value = true;
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: "12",
    });

    if (searchQuery.value) {
      params.append("search", searchQuery.value);
    }

    const response = await api.get(`/products?${params.toString()}`);
    products.value = response.data.data || [];
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
    };
  } catch (error) {
    console.error("Error fetching products:", error);
    alert("Lỗi khi tải danh sách sản phẩm");
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  searchTimeout.value = setTimeout(() => {
    fetchProducts(1);
  }, 500);
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchProducts(page);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
};

const getPageNumbers = () => {
  if (!pagination.value) return [];

  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const pages = [];

  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i);
    }
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 4; i++) {
        pages.push(i);
      }
      pages.push("...");
      pages.push(last);
    } else if (current >= last - 2) {
      pages.push(1);
      pages.push("...");
      for (let i = last - 3; i <= last; i++) {
        pages.push(i);
      }
    } else {
      pages.push(1);
      pages.push("...");
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i);
      }
      pages.push("...");
      pages.push(last);
    }
  }

  return pages;
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
