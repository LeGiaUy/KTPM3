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
          </a>
          <a
            href="#"
            @click.prevent="$emit('navigate', 'products')"
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
          <h2 class="text-2xl font-semibold text-gray-800">Quản lý sản phẩm</h2>
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
          <div class="ml-4 flex space-x-3">
            <button
              v-if="selectedProducts.length > 0"
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
              Xóa đã chọn ({{ selectedProducts.length }})
            </button>
            <button
              v-if="pagination && pagination.total > 0"
              @click="confirmDeleteAll"
              class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900 transition-colors flex items-center"
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
              Xóa tất cả ({{ pagination.total }})
            </button>
            <button
              @click="$emit('navigate', 'import')"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center"
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
                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                />
              </svg>
              Import
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
              Thêm sản phẩm
            </button>
          </div>
        </div>

        <!-- Products Table -->
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
                      :checked="isAllSelected"
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
                    <div class="h-4 w-4 bg-gray-200 rounded"></div>
                  </td>
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
                  <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                    Không tìm thấy sản phẩm nào
                  </td>
                </tr>
                <tr
                  v-else
                  v-for="product in products"
                  :key="product.id"
                  :class="[
                    'hover:bg-gray-50',
                    selectedProducts.includes(product.id) ? 'bg-blue-50' : '',
                  ]"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      :value="product.id"
                      v-model="selectedProducts"
                      class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                  </td>
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
                    <div v-if="product.isbn" class="text-xs text-gray-400">
                      ISBN: {{ product.isbn }}
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
                      @click="openEditModal(product)"
                      class="text-indigo-600 hover:text-indigo-900 mr-3"
                    >
                      Sửa
                    </button>
                    <button
                      @click="confirmDelete(product)"
                      class="text-red-600 hover:text-red-900"
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
                  tổng số {{ pagination.total }} sản phẩm
                </span>
                <span v-else> Tổng số {{ pagination.total }} sản phẩm </span>
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
      v-if="showModal"
      <div
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
    >
      @click.self="closeModal" >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
      >
        <div
          class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
        >
          <h3 class="text-xl font-semibold text-gray-800">
            {{ editingProduct ? "Sửa sản phẩm" : "Thêm sản phẩm mới" }}
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

        <form @submit.prevent="saveProduct" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên sản phẩm <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Giá <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="form.price"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tồn kho <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="form.stock"
                type="number"
                min="0"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tác giả
              </label>
              <input
                v-model="form.author"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Nhà xuất bản
              </label>
              <input
                v-model="form.publisher"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ISBN
              </label>
              <input
                v-model="form.isbn"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Trạng thái
              </label>
              <select
                v-model="form.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="active">Hoạt động</option>
                <option value="inactive">Ngừng hoạt động</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                URL hình ảnh
              </label>
              <input
                v-model="form.cover_url"
                type="url"
                placeholder="https://example.com/image.jpg"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Mô tả
              </label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              ></textarea>
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
      v-if="showDeleteModal"
      <div
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
    >
      @click.self="showDeleteModal = false" >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Xác nhận xóa</h3>
          <p class="text-gray-600 mb-4">
            Bạn có chắc chắn muốn xóa sản phẩm
            <strong>"{{ productToDelete?.title }}"</strong> không? Hành động này
            không thể hoàn tác.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="deleteProduct"
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
      v-if="showBulkDeleteModal"
      <div
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
    >
      @click.self="showBulkDeleteModal = false" >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">
            Xác nhận xóa nhiều sản phẩm
          </h3>
          <p class="text-gray-600 mb-4">
            Bạn có chắc chắn muốn xóa
            <strong>{{ selectedProducts.length }} sản phẩm</strong> đã chọn
            không? Hành động này không thể hoàn tác.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showBulkDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="bulkDeleteProducts"
              :disabled="bulkDeleting"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ bulkDeleting ? "Đang xóa..." : "Xóa" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete All Confirmation Modal -->
    <div
      v-if="showDeleteAllModal"
      <div
      class="fixed inset-0 bg-black/20 flex items-center justify-center z-50"
    >
      @click.self="showDeleteAllModal = false" >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div
            class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full"
          >
            <svg
              class="w-6 h-6 text-red-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2 text-center">
            Xác nhận xóa tất cả sản phẩm
          </h3>
          <p class="text-gray-600 mb-4 text-center">
            Bạn có chắc chắn muốn xóa
            <strong class="text-red-600"
              >TẤT CẢ {{ pagination?.total || 0 }} sản phẩm</strong
            >
            không?<br />
            <span class="text-red-600 font-semibold"
              >Hành động này không thể hoàn tác!</span
            >
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteAllModal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="deleteAllProducts"
              :disabled="deletingAll"
              class="px-4 py-2 bg-red-800 text-white rounded-lg hover:bg-red-900 disabled:opacity-50"
            >
              {{ deletingAll ? "Đang xóa..." : "Xóa tất cả" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import api from "../services/api.js";

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

defineEmits(["logout", "navigate"]);

const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const bulkDeleting = ref(false);
const deletingAll = ref(false);
const products = ref([]);
const pagination = ref(null);
const searchQuery = ref("");
const searchTimeout = ref(null);
const selectedProducts = ref([]);

const showModal = ref(false);
const showDeleteModal = ref(false);
const showBulkDeleteModal = ref(false);
const showDeleteAllModal = ref(false);
const editingProduct = ref(null);
const productToDelete = ref(null);

const form = ref({
  title: "",
  price: 0,
  stock: 0,
  author: "",
  publisher: "",
  isbn: "",
  cover_url: "",
  description: "",
  status: "active",
});

const fetchProducts = async (page = 1) => {
  try {
    loading.value = true;
    const params = new URLSearchParams({
      page: page.toString(),
    });

    if (searchQuery.value) {
      params.append("search", searchQuery.value);
    }

    const response = await api.get(`/admin/products?${params.toString()}`);
    products.value = response.data.data || [];
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      from: response.data.from,
      to: response.data.to,
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
    // Scroll to top when changing page
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
};

const getPageNumbers = () => {
  if (!pagination.value) return [];

  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const pages = [];

  if (last <= 7) {
    // Hiển thị tất cả các trang nếu <= 7 trang
    for (let i = 1; i <= last; i++) {
      pages.push(i);
    }
  } else {
    // Logic hiển thị trang với ellipsis
    if (current <= 3) {
      // Đầu danh sách
      for (let i = 1; i <= 4; i++) {
        pages.push(i);
      }
      pages.push("...");
      pages.push(last);
    } else if (current >= last - 2) {
      // Cuối danh sách
      pages.push(1);
      pages.push("...");
      for (let i = last - 3; i <= last; i++) {
        pages.push(i);
      }
    } else {
      // Giữa danh sách
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

const openAddModal = () => {
  editingProduct.value = null;
  resetForm();
  showModal.value = true;
};

const openEditModal = (product) => {
  editingProduct.value = product;
  form.value = {
    title: product.title || "",
    price: product.price || 0,
    stock: product.stock || 0,
    author: product.author || "",
    publisher: product.publisher || "",
    isbn: product.isbn || "",
    cover_url: product.cover_url || "",
    description: product.description || "",
    status: product.status || "active",
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingProduct.value = null;
  resetForm();
};

const resetForm = () => {
  form.value = {
    title: "",
    price: 0,
    stock: 0,
    author: "",
    publisher: "",
    isbn: "",
    cover_url: "",
    description: "",
    status: "active",
  };
};

const saveProduct = async () => {
  try {
    saving.value = true;

    if (editingProduct.value) {
      // Update
      await api.put(`/admin/products/${editingProduct.value.id}`, form.value);
      alert("Cập nhật sản phẩm thành công!");
    } else {
      // Create
      await api.post("/admin/products", form.value);
      alert("Thêm sản phẩm thành công!");
    }

    closeModal();
    fetchProducts(pagination.value?.current_page || 1);
  } catch (error) {
    console.error("Error saving product:", error);
    const errorMessage =
      error.response?.data?.message || "Lỗi khi lưu sản phẩm";
    alert(errorMessage);
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (product) => {
  productToDelete.value = product;
  showDeleteModal.value = true;
};

const deleteProduct = async () => {
  try {
    deleting.value = true;
    await api.delete(`/admin/products/${productToDelete.value.id}`);
    alert("Xóa sản phẩm thành công!");
    showDeleteModal.value = false;
    productToDelete.value = null;
    fetchProducts(pagination.value?.current_page || 1);
  } catch (error) {
    console.error("Error deleting product:", error);
    alert("Lỗi khi xóa sản phẩm");
  } finally {
    deleting.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(value);
};

const isAllSelected = computed(() => {
  return (
    products.value.length > 0 &&
    products.value.every((product) =>
      selectedProducts.value.includes(product.id)
    )
  );
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedProducts.value = [];
  } else {
    selectedProducts.value = products.value.map((product) => product.id);
  }
};

const confirmBulkDelete = () => {
  if (selectedProducts.value.length === 0) {
    alert("Vui lòng chọn ít nhất một sản phẩm để xóa");
    return;
  }
  showBulkDeleteModal.value = true;
};

const bulkDeleteProducts = async () => {
  try {
    bulkDeleting.value = true;
    const response = await api.post("/admin/products/bulk-delete", {
      ids: selectedProducts.value,
    });
    alert(response.data.message || "Xóa sản phẩm thành công!");
    showBulkDeleteModal.value = false;
    selectedProducts.value = [];
    fetchProducts(pagination.value?.current_page || 1);
  } catch (error) {
    console.error("Error bulk deleting products:", error);
    const errorMessage =
      error.response?.data?.message || "Lỗi khi xóa sản phẩm";
    alert(errorMessage);
  } finally {
    bulkDeleting.value = false;
  }
};

const confirmDeleteAll = () => {
  if (!pagination.value || pagination.value.total === 0) {
    alert("Không có sản phẩm nào để xóa");
    return;
  }
  showDeleteAllModal.value = true;
};

const deleteAllProducts = async () => {
  try {
    deletingAll.value = true;
    const response = await api.delete("/admin/products/delete-all");
    alert(response.data.message || "Xóa tất cả sản phẩm thành công!");
    showDeleteAllModal.value = false;
    selectedProducts.value = [];
    fetchProducts(1);
  } catch (error) {
    console.error("Error deleting all products:", error);
    const errorMessage =
      error.response?.data?.message || "Lỗi khi xóa tất cả sản phẩm";
    alert(errorMessage);
  } finally {
    deletingAll.value = false;
  }
};

onMounted(() => {
  fetchProducts();
});
</script>
