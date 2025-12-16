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
          <span class="text-gray-700">Xin chào, {{ user?.name }}</span>
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
defineProps({
  user: {
    type: Object,
    required: true,
  },
});

defineEmits(["logout"]);
</script>
