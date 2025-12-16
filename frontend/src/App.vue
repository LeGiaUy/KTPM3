<script setup>
import { ref, onMounted, computed } from "vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import Dashboard from "./components/Dashboard.vue";
import ProductsManagement from "./components/ProductsManagement.vue";
import ImportProducts from "./components/ImportProducts.vue";
import { authService } from "./services/auth.js";

const currentView = ref("login"); // 'login' or 'register'
const adminView = ref("dashboard"); // 'dashboard', 'products', or 'import'
const isAuthenticated = ref(false);
const user = ref(null);

const isAdmin = computed(() => {
  if (!user.value) return false;
  // Kiểm tra role từ user object
  return user.value.role === "admin" || user.value.role === "admin";
});

onMounted(() => {
  checkAuth();
});

const checkAuth = () => {
  isAuthenticated.value = authService.isAuthenticated();
  if (isAuthenticated.value) {
    user.value = authService.getUser();
    // Reset adminView nếu user không phải admin
    if (
      user.value &&
      user.value.role !== "admin" &&
      adminView.value !== "dashboard"
    ) {
      adminView.value = "dashboard";
    }
  } else {
    user.value = null;
    adminView.value = "dashboard";
    currentView.value = "login";
  }
};

const switchToRegister = () => {
  currentView.value = "register";
};

const switchToLogin = () => {
  currentView.value = "login";
};

const handleLoginSuccess = () => {
  checkAuth();
};

const handleRegisterSuccess = () => {
  checkAuth();
};

const handleLogout = async () => {
  await authService.logout();
  isAuthenticated.value = false;
  user.value = null;
  currentView.value = "login";
  adminView.value = "dashboard";
};

const handleNavigate = (view) => {
  adminView.value = view;
};
</script>

<template>
  <div>
    <!-- Admin Views - Chỉ hiển thị khi đã đăng nhập và là admin -->
    <template v-if="isAuthenticated && isAdmin">
      <!-- Admin Dashboard -->
      <Dashboard
        v-if="adminView === 'dashboard'"
        :user="user"
        @logout="handleLogout"
        @navigate="handleNavigate"
      />

      <!-- Admin Products Management -->
      <ProductsManagement
        v-else-if="adminView === 'products'"
        :user="user"
        @logout="handleLogout"
        @navigate="handleNavigate"
      />

      <!-- Admin Import Products -->
      <ImportProducts
        v-else-if="adminView === 'import'"
        :user="user"
        @logout="handleLogout"
        @navigate="handleNavigate"
      />
    </template>

    <!-- Regular User View -->
    <div
      v-else-if="isAuthenticated && !isAdmin"
      class="min-h-screen bg-gray-50"
    >
      <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
              <h1 class="text-xl font-bold text-gray-900">KTPM3</h1>
            </div>
            <div class="flex items-center space-x-4">
              <span class="text-gray-700">Xin chào, {{ user?.name }}</span>
              <button
                @click="handleLogout"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
              >
                Đăng xuất
              </button>
            </div>
          </div>
        </div>
      </nav>

      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div
            class="border-4 border-dashed border-gray-200 rounded-lg h-96 flex items-center justify-center"
          >
            <div class="text-center">
              <h2 class="text-2xl font-semibold text-gray-700 mb-2">
                Chào mừng bạn đã đăng nhập!
              </h2>
              <p class="text-gray-500">
                Giao diện chính sẽ được phát triển tiếp...
              </p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Auth Views - Chỉ hiển thị khi chưa đăng nhập -->
    <div v-if="!isAuthenticated">
      <Login
        v-if="currentView === 'login'"
        @switch-to-register="switchToRegister"
        @login-success="handleLoginSuccess"
      />
      <Register
        v-else
        @switch-to-login="switchToLogin"
        @register-success="handleRegisterSuccess"
      />
    </div>
  </div>
</template>

<style scoped></style>
