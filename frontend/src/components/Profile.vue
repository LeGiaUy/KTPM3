<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <Header :user="user" @logout="$emit('logout')" />
    <Navigation />

    <!-- Main Content -->
    <main class="flex-1 max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">Thông tin cá nhân</h1>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-lg shadow p-8">
        <div class="animate-pulse">
          <div class="h-8 bg-gray-200 rounded w-1/3 mb-4"></div>
          <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>

      <!-- Profile Form -->
      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-800">
            Thông tin tài khoản
          </h2>
        </div>

        <form @submit.prevent="saveProfile" class="p-6 space-y-6">
          <!-- Name Field -->
          <div>
            <label
              for="name"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Họ và tên <span class="text-red-500">*</span>
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Email Field -->
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Email <span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Role Display (Read-only) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Vai trò
            </label>
            <div
              class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg"
            >
              <span
                :class="
                  form.role === 'admin'
                    ? 'bg-purple-100 text-purple-800'
                    : 'bg-blue-100 text-blue-800'
                "
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ form.role === "admin" ? "Admin" : "Người dùng" }}
              </span>
            </div>
          </div>

          <!-- Change Password Section -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
              Đổi mật khẩu
            </h3>
            <p class="text-sm text-gray-500 mb-4">
              Để trống nếu không muốn thay đổi mật khẩu
            </p>

            <!-- Current Password -->
            <div class="mb-4">
              <label
                for="current_password"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Mật khẩu hiện tại
              </label>
              <input
                id="current_password"
                v-model="form.current_password"
                type="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <!-- New Password -->
            <div class="mb-4">
              <label
                for="password"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Mật khẩu mới
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                :minlength="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
              <p class="mt-1 text-xs text-gray-500">
                Mật khẩu phải có ít nhất 8 ký tự
              </p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label
                for="password_confirmation"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Xác nhận mật khẩu mới
              </label>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                :minlength="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Error Message -->
          <div
            v-if="error_message"
            class="bg-red-50 border border-red-200 rounded-lg p-4"
          >
            <p class="text-sm text-red-800">{{ error_message }}</p>
          </div>

          <!-- Success Message -->
          <div
            v-if="success_message"
            class="bg-green-50 border border-green-200 rounded-lg p-4"
          >
            <p class="text-sm text-green-800">{{ success_message }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="resetForm"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ saving ? "Đang lưu..." : "Lưu thay đổi" }}
            </button>
          </div>
        </form>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
// H1: import runtime functions
import { ref, onMounted } from "vue";

// H2: import components
import Header from "./Header.vue";
import Navigation from "./Navigation.vue";
import Footer from "./Footer.vue";

// H6: i18n, store
import api from "../services/api.js";
import { authService } from "../services/auth.js";

// H5: props, emits
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["logout", "profile-updated"]);

// H7: variables
const loading = ref(false);
const saving = ref(false);
const error_message = ref("");
const success_message = ref("");

const form = ref({
  name: "",
  email: "",
  role: "",
  password: "",
  current_password: "",
  password_confirmation: "",
});

// H10: functions
/**
 * Lấy thông tin cá nhân từ server
 */
const fetchProfile = async () => {
  try {
    loading.value = true;
    const response = await api.get("/profile");
    const user_data = response.data;

    form.value = {
      name: user_data.name || "",
      email: user_data.email || "",
      role: user_data.role || "user",
      password: "",
      current_password: "",
      password_confirmation: "",
    };
  } catch (error) {
    console.error("Lỗi khi lấy thông tin cá nhân:", error);
    error_message.value =
      error.response?.data?.message || "Lỗi khi tải thông tin cá nhân";
  } finally {
    loading.value = false;
  }
};

/**
 * Reset form về giá trị ban đầu
 */
const resetForm = () => {
  fetchProfile();
  error_message.value = "";
  success_message.value = "";
};

/**
 * Validate form trước khi submit
 * @returns {boolean} - True nếu hợp lệ
 */
const validateForm = () => {
  error_message.value = "";

  // Nếu có nhập mật khẩu mới, phải có mật khẩu hiện tại
  if (form.value.password && !form.value.current_password) {
    error_message.value = "Vui lòng nhập mật khẩu hiện tại";
    return false;
  }

  // Nếu có mật khẩu hiện tại, phải có mật khẩu mới
  if (form.value.current_password && !form.value.password) {
    error_message.value = "Vui lòng nhập mật khẩu mới";
    return false;
  }

  // Kiểm tra mật khẩu mới và xác nhận khớp nhau
  if (form.value.password && form.value.password_confirmation) {
    if (form.value.password !== form.value.password_confirmation) {
      error_message.value = "Mật khẩu mới và xác nhận không khớp";
      return false;
    }
  }

  // Kiểm tra độ dài mật khẩu
  if (form.value.password && form.value.password.length < 8) {
    error_message.value = "Mật khẩu phải có ít nhất 8 ký tự";
    return false;
  }

  return true;
};

/**
 * Lưu thông tin cá nhân
 */
const saveProfile = async () => {
  if (!validateForm()) {
    return;
  }

  try {
    saving.value = true;
    error_message.value = "";
    success_message.value = "";

    const data_to_send = {
      name: form.value.name,
      email: form.value.email,
    };

    // Chỉ gửi password nếu có nhập
    if (form.value.password && form.value.current_password) {
      data_to_send.password = form.value.password;
      data_to_send.current_password = form.value.current_password;
    }

    const response = await api.put("/profile", data_to_send);

    // Cập nhật localStorage với thông tin mới
    const updated_user = response.data.user;
    localStorage.setItem("user", JSON.stringify(updated_user));

    // Emit event để cập nhật user trong App.vue
    emit("profile-updated", updated_user);

    success_message.value = "Cập nhật thông tin thành công!";

    // Reset password fields
    form.value.password = "";
    form.value.current_password = "";
    form.value.password_confirmation = "";

    // Clear success message after 3 seconds
    setTimeout(() => {
      success_message.value = "";
    }, 3000);
  } catch (error) {
    console.error("Lỗi khi cập nhật thông tin:", error);
    error_message.value =
      error.response?.data?.message || "Lỗi khi cập nhật thông tin";
  } finally {
    saving.value = false;
  }
};

// H8: lifecycle hooks
onMounted(() => {
  fetchProfile();
});
</script>
