<template>
  <nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-8">
        <router-link
          :to="{ name: 'home' }"
          :class="[
            'px-3 py-4 text-sm font-medium border-b-2 transition-colors',
            $route.name === 'home'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
          ]"
        >
          Trang chủ
        </router-link>
        <router-link
          :to="{ name: 'products' }"
          :class="[
            'px-3 py-4 text-sm font-medium border-b-2 transition-colors',
            $route.name === 'products' || $route.name === 'product-detail'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
          ]"
        >
          Tất cả sản phẩm
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRoute } from "vue-router";

const route = useRoute();
</script>
