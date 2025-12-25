<template>
  <header class="bg-white shadow-sm sticky top-0 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <div class="flex items-center">
          <router-link
            :to="{ name: 'home' }"
            class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition-colors"
          >
            KTPM
          </router-link>
        </div>
        <div class="flex items-center space-x-4">
          <router-link
            :to="{ name: 'cart' }"
            class="relative p-2 text-gray-600 hover:text-indigo-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
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
            <span
              v-if="cartItemCount() > 0"
              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"
            >
              {{ cartItemCount() }}
            </span>
          </router-link>
          <router-link
            :to="{ name: 'profile' }"
            class="flex items-center space-x-2 px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
            <span class="text-sm font-medium">{{ user?.name }}</span>
          </router-link>
          <button
            @click="$emit('logout')"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            Đăng xuất
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { onMounted } from "vue";
import { useCart } from "../composables/useCart.js";

const { cartItemCount, fetchCart } = useCart();

onMounted(() => {
  fetchCart();
});

defineProps({
  user: {
    type: Object,
    required: true,
  },
});

defineEmits(["logout"]);
</script>
