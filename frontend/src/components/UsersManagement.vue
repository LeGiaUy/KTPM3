<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      class="fixed left-0 top-0 h-full w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out z-30"
    >
      <div class="p-6">
        <h1 class="text-2xl font-bold mb-8">Admin Panel</h1>
        <nav class="space-y-2">
          <router-link
            :to="{ name: 'admin-dashboard' }"
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
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
              />
            </svg>
            Dashboard
          </router-link>
          <router-link
            :to="{ name: 'admin-products' }"
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
          </router-link>
          <router-link
            :to="{ name: 'admin-users' }"
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
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
            Người dùng
          </router-link>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm sticky top-0 z-20">
        <div class="px-6 py-4 flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">
            Quản lý người dùng
          </h2>
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
        <!-- Search and Add Button -->
        <div class="mb-6 flex justify-between items-center">
          <div class="flex-1 max-w-md">
            <div class="relative">
              <input
                v-model="search_query"
                @input="handleSearch"
                type="text"
                placeholder="Tìm kiếm người dùng..."
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
          <div class="ml-4 flex space-x-3">
            <button
              v-if="selected_users.length > 0"
              @click="confirmBulkDelete"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
            >
              <svg
                class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
              </svg>
              Xóa đã chọn ({{ selected_users.length }})
            </button>
            <button
              @click="openAddModal"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
            >
              <svg
                class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>
              Thêm người dùng
            </button>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    <input
                      type="checkbox"
                      :checked="is_all_selected"
                      @change="toggleSelectAll"
                      class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    ID
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Tên
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Email
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Vai trò
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Ngày tạo
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
                    <div class="h-4 w-4 bg-gray-200 rounded"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-12"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-32"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-48"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-16"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                  </td>
                </tr>
                <tr v-else-if="users.length === 0">
                  <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    Không tìm thấy người dùng nào
                  </td>
                </tr>
                <tr
                  v-else
                  v-for="user_item in users"
                  :key="user_item.id"
                  :class="[
                    'hover:bg-gray-50',
                    selected_users.includes(user_item.id) ? 'bg-blue-50' : '',
                  ]"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      :value="user_item.id"
                      v-model="selected_users"
                      class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    #{{ user_item.id }}
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ user_item.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ user_item.email }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="
                        user_item.role === 'admin'
                          ? 'bg-purple-100 text-purple-800'
                          : 'bg-blue-100 text-blue-800'
                      "
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    >
                      {{ user_item.role === "admin" ? "Admin" : "Người dùng" }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{
                      new Date(user_item.created_at).toLocaleDateString("vi-VN")
                    }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button
                      @click="openEditModal(user_item)"
                      class="text-indigo-600 hover:text-indigo-900 mr-3"
                    >
                      Sửa
                    </button>
                    <button
                      @click="confirmDelete(user_item)"
                      :disabled="user_item.id === user.id"
                      :class="
                        user_item.id === user.id
                          ? 'text-gray-400 cursor-not-allowed'
                          : 'text-red-600 hover:text-red-900'
                      "
                    >
                      Xóa
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div
            v-if="pagination && pagination.total > 0"
            class="px-6 py-4 border-t border-gray-200"
          >
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                <span v-if="pagination.from && pagination.to">
                  Hiển thị {{ pagination.from }} đến {{ pagination.to }} trong
                  tổng số {{ pagination.total }} người dùng
                </span>
                <span v-else> Tổng số {{ pagination.total }} người dùng </span>
              </div>

              <div
                v-if="pagination.last_page > 1"
                class="flex items-center space-x-2"
              >
                <!-- First Page -->
                <button
                  @click="changePage(1)"
                  :disabled="pagination.current_page === 1"
                  :class="
                    pagination.current_page === 1
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-gray-50'
                  "
                  class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium"
                  title="Trang đầu"
                >
                  ««
                </button>

                <!-- Previous Page -->
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

                <!-- Page Numbers -->
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

                <!-- Next Page -->
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

                <!-- Last Page -->
                <button
                  @click="changePage(pagination.last_page)"
                  :disabled="pagination.current_page === pagination.last_page"
                  :class="
                    pagination.current_page === pagination.last_page
                      ? 'opacity-50 cursor-not-allowed'
                      : 'hover:bg-gray-50'
                  "
                  class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium"
                  title="Trang cuối"
                >
                  »»
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="show_modal"
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
      >
        <div
          class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
        >
          <h3 class="text-xl font-semibold text-gray-800">
            {{ editing_user ? "Sửa người dùng" : "Thêm người dùng mới" }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveUser" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Email <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Mật khẩu
                <span class="text-red-500">*</span>
                <span
                  v-if="editing_user"
                  class="text-xs text-gray-500 font-normal"
                >
                  (Để trống nếu không muốn thay đổi)
                </span>
              </label>
              <input
                v-model="form.password"
                type="password"
                :required="!editing_user"
                :minlength="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Vai trò <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.role"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="user">Người dùng</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ saving ? "Đang lưu..." : "Lưu" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="show_delete_modal"
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
      @click.self="show_delete_modal = false"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Xác nhận xóa</h3>
          <p class="text-gray-600 mb-4">
            Bạn có chắc chắn muốn xóa người dùng
            <strong>"{{ user_to_delete?.name }}"</strong> không? Hành động này
            không thể hoàn tác.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="show_delete_modal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="deleteUser"
              :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? "Đang xóa..." : "Xóa" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div
      v-if="show_bulk_delete_modal"
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
      @click.self="show_bulk_delete_modal = false"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">
            Xác nhận xóa nhiều người dùng
          </h3>
          <p class="text-gray-600 mb-4">
            Bạn có chắc chắn muốn xóa
            <strong>{{ selected_users.length }} người dùng</strong> đã chọn
            không? Hành động này không thể hoàn tác.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="show_bulk_delete_modal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="bulkDeleteUsers"
              :disabled="bulk_deleting"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ bulk_deleting ? "Đang xóa..." : "Xóa" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// H1: import runtime functions
import { ref, onMounted, computed } from "vue";

// H6: i18n, store
import api from "../services/api.js";

// H5: props, emits
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["logout"]);

// H7: variables
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const bulk_deleting = ref(false);
const users = ref([]);
const pagination = ref(null);
const search_query = ref("");
const search_timeout = ref(null);
const selected_users = ref([]);

const show_modal = ref(false);
const show_delete_modal = ref(false);
const show_bulk_delete_modal = ref(false);
const editing_user = ref(null);
const user_to_delete = ref(null);

const form = ref({
  name: "",
  email: "",
  password: "",
  role: "user",
});

// H9: computed
/**
 * Kiểm tra xem tất cả người dùng đã được chọn chưa
 */
const is_all_selected = computed(() => {
  return (
    users.value.length > 0 &&
    users.value.every((user_item) =>
      selected_users.value.includes(user_item.id)
    )
  );
});

// H10: functions
/**
 * Lấy danh sách người dùng từ server
 * @param {number} page - Số trang
 */
const fetchUsers = async (page = 1) => {
  try {
    loading.value = true;
    const params = new URLSearchParams({
      page: page.toString(),
    });

    if (search_query.value) {
      params.append("search", search_query.value);
    }

    const response = await api.get(`/admin/users?${params.toString()}`);
    users.value = response.data.data || [];
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      from: response.data.from,
      to: response.data.to,
      total: response.data.total,
    };
  } catch (error) {
    console.error("Lỗi khi lấy danh sách người dùng:", error);
    alert("Lỗi khi tải danh sách người dùng");
  } finally {
    loading.value = false;
  }
};

/**
 * Xử lý tìm kiếm với debounce
 */
const handleSearch = () => {
  if (search_timeout.value) {
    clearTimeout(search_timeout.value);
  }
  search_timeout.value = setTimeout(() => {
    fetchUsers(1);
  }, 500);
};

/**
 * Chuyển trang
 * @param {number} page - Số trang
 */
const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
};

/**
 * Tính toán các số trang để hiển thị
 * @returns {Array} - Mảng các số trang
 */
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

/**
 * Mở modal thêm người dùng
 */
const openAddModal = () => {
  editing_user.value = null;
  resetForm();
  show_modal.value = true;
};

/**
 * Mở modal sửa người dùng
 * @param {Object} user_item - Thông tin người dùng
 */
const openEditModal = (user_item) => {
  editing_user.value = user_item;
  form.value = {
    name: user_item.name || "",
    email: user_item.email || "",
    password: "",
    role: user_item.role || "user",
  };
  show_modal.value = true;
};

/**
 * Đóng modal
 */
const closeModal = () => {
  show_modal.value = false;
  editing_user.value = null;
  resetForm();
};

/**
 * Reset form về giá trị mặc định
 */
const resetForm = () => {
  form.value = {
    name: "",
    email: "",
    password: "",
    role: "user",
  };
};

/**
 * Lưu người dùng (thêm mới hoặc cập nhật)
 */
const saveUser = async () => {
  try {
    saving.value = true;

    const data_to_send = { ...form.value };
    // Nếu đang sửa và không có password mới, không gửi password
    if (editing_user.value && !data_to_send.password) {
      delete data_to_send.password;
    }

    if (editing_user.value) {
      await api.put(`/admin/users/${editing_user.value.id}`, data_to_send);
      alert("Cập nhật người dùng thành công!");
    } else {
      await api.post("/admin/users", data_to_send);
      alert("Thêm người dùng thành công!");
    }

    closeModal();
    fetchUsers(pagination.value?.current_page || 1);
  } catch (error) {
    console.error("Lỗi khi lưu người dùng:", error);
    const error_message =
      error.response?.data?.message || "Lỗi khi lưu người dùng";
    alert(error_message);
  } finally {
    saving.value = false;
  }
};

/**
 * Xác nhận xóa người dùng
 * @param {Object} user_item - Thông tin người dùng
 */
const confirmDelete = (user_item) => {
  if (user_item.id === props.user.id) {
    alert("Không thể xóa chính tài khoản của bạn");
    return;
  }
  user_to_delete.value = user_item;
  show_delete_modal.value = true;
};

/**
 * Xóa người dùng
 */
const deleteUser = async () => {
  try {
    deleting.value = true;
    await api.delete(`/admin/users/${user_to_delete.value.id}`);
    alert("Xóa người dùng thành công!");
    show_delete_modal.value = false;
    user_to_delete.value = null;
    fetchUsers(pagination.value?.current_page || 1);
  } catch (error) {
    console.error("Lỗi khi xóa người dùng:", error);
    const error_message =
      error.response?.data?.message || "Lỗi khi xóa người dùng";
    alert(error_message);
  } finally {
    deleting.value = false;
  }
};

/**
 * Chọn/bỏ chọn tất cả người dùng
 */
const toggleSelectAll = () => {
  if (is_all_selected.value) {
    selected_users.value = [];
  } else {
    selected_users.value = users.value.map((user_item) => user_item.id);
  }
};

/**
 * Xác nhận xóa nhiều người dùng
 */
const confirmBulkDelete = () => {
  if (selected_users.value.length === 0) {
    alert("Vui lòng chọn ít nhất một người dùng để xóa");
    return;
  }
  show_bulk_delete_modal.value = true;
};

/**
 * Xóa nhiều người dùng cùng lúc
 */
const bulkDeleteUsers = async () => {
  try {
    bulk_deleting.value = true;
    const response = await api.post("/admin/users/bulk-delete", {
      ids: selected_users.value,
    });
    alert(response.data.message || "Xóa người dùng thành công!");
    show_bulk_delete_modal.value = false;
    selected_users.value = [];
    fetchUsers(pagination.value?.current_page || 1);
  } catch (error) {
    console.error("Lỗi khi xóa nhiều người dùng:", error);
    const error_message =
      error.response?.data?.message || "Lỗi khi xóa người dùng";
    alert(error_message);
  } finally {
    bulk_deleting.value = false;
  }
};

// H8: lifecycle hooks
onMounted(() => {
  fetchUsers();
});
</script>
