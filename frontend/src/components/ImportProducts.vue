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
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm sticky top-0 z-20">
        <div class="px-6 py-4 flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">Import sản phẩm</h2>
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

      <!-- Content -->
      <main class="p-6">
        <div class="max-w-4xl mx-auto">
          <!-- Open Library Import Section -->
          <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
              Import tự động từ Open Library
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Từ khóa tìm kiếm <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="openLibraryForm.keyword"
                  type="text"
                  placeholder="Ví dụ: clean code"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Số lượng sách <span class="text-red-500">*</span>
                </label>
                <input
                  v-model.number="openLibraryForm.limit"
                  type="number"
                  min="1"
                  max="20"
                  placeholder="5"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Giá (VND) <span class="text-red-500">*</span>
                </label>
                <input
                  v-model.number="openLibraryForm.price"
                  type="number"
                  min="0"
                  step="1000"
                  placeholder="100000"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Tồn kho <span class="text-red-500">*</span>
                </label>
                <input
                  v-model.number="openLibraryForm.stock"
                  type="number"
                  min="0"
                  placeholder="10"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
              </div>
            </div>
            <button
              @click="importFromOpenLibrary"
              :disabled="importingOpenLibrary || !isOpenLibraryFormValid"
              class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
            >
              <svg
                v-if="importingOpenLibrary"
                class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              <svg
                v-else
                class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                />
              </svg>
              {{
                importingOpenLibrary
                  ? "Đang import..."
                  : "Import từ Open Library"
              }}
            </button>
            <p class="mt-2 text-sm text-gray-500">
              💡 Hệ thống sẽ tự động tìm kiếm và import sách từ Open Library với
              thông tin đã nhập
            </p>
          </div>

          <!-- Divider -->
          <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-gray-50 text-gray-500">Hoặc</span>
            </div>
          </div>

          <!-- Instructions -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">
              Hướng dẫn import sản phẩm
            </h3>
            <ul class="list-disc list-inside space-y-2 text-blue-800">
              <li>File hỗ trợ: CSV (khuyến nghị), Excel (.xlsx, .xls)</li>
              <li>Kích thước tối đa: 10MB</li>
              <li>
                Định dạng cột (theo thứ tự): Title, Price, Stock, Author,
                Publisher, ISBN, Cover URL, Description, Status
              </li>
              <li>Các trường bắt buộc: Title, Price, Stock</li>
              <li>Status: "active" hoặc "inactive" (mặc định: "active")</li>
              <li class="text-blue-600 font-medium">
                💡 Lưu ý: Nếu file Excel không import được, vui lòng export sang
                CSV trong Excel (File → Save As → CSV)
              </li>
            </ul>
            <div class="mt-4">
              <a
                href="#"
                @click.prevent="downloadTemplate"
                class="text-blue-600 hover:text-blue-800 font-medium underline"
              >
                Tải file mẫu CSV →
              </a>
            </div>
          </div>

          <!-- Upload Area -->
          <div
            @drop.prevent="handleDrop"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            :class="
              isDragging
                ? 'border-indigo-500 bg-indigo-50'
                : 'border-gray-300 bg-white'
            "
            class="border-2 border-dashed rounded-lg p-12 text-center transition-colors"
          >
            <input
              ref="fileInput"
              type="file"
              accept=".csv,.xlsx,.xls"
              @change="handleFileSelect"
              class="hidden"
            />

            <div v-if="!selectedFile && !uploading">
              <svg
                class="mx-auto h-16 w-16 text-gray-400 mb-4"
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
              <p class="text-lg font-medium text-gray-700 mb-2">
                Kéo thả file vào đây hoặc
              </p>
              <button
                @click="$refs.fileInput.click()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
              >
                Chọn file
              </button>
              <p class="text-sm text-gray-500 mt-2">
                CSV, Excel (.xlsx, .xls) - Tối đa 10MB
              </p>
            </div>

            <div v-else-if="selectedFile && !uploading">
              <div class="flex items-center justify-center mb-4">
                <svg
                  class="h-12 w-12 text-green-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <p class="text-lg font-medium text-gray-700 mb-2">
                {{ selectedFile.name }}
              </p>
              <p class="text-sm text-gray-500 mb-4">
                {{ formatFileSize(selectedFile.size) }}
              </p>
              <div class="flex justify-center space-x-3">
                <button
                  @click="selectedFile = null"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                  Chọn file khác
                </button>
                <button
                  @click="uploadFile"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                >
                  Import
                </button>
              </div>
            </div>

            <div v-else-if="uploading">
              <div class="flex flex-col items-center">
                <svg
                  class="animate-spin h-12 w-12 text-indigo-600 mb-4"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                <p class="text-lg font-medium text-gray-700">
                  Đang import sản phẩm...
                </p>
              </div>
            </div>
          </div>

          <!-- Results -->
          <div v-if="importResults" class="mt-6">
            <div
              :class="
                importResults.success > 0
                  ? 'bg-green-50 border-green-200'
                  : 'bg-red-50 border-red-200'
              "
              class="border rounded-lg p-6"
            >
              <h3
                :class="
                  importResults.success > 0 ? 'text-green-900' : 'text-red-900'
                "
                class="text-lg font-semibold mb-4"
              >
                Kết quả import
              </h3>

              <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-green-600">
                    {{ importResults.success }}
                  </p>
                  <p class="text-sm text-gray-600">Thành công</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-red-600">
                    {{ importResults.failed }}
                  </p>
                  <p class="text-sm text-gray-600">Thất bại</p>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-yellow-600">
                    {{ importResults.skipped }}
                  </p>
                  <p class="text-sm text-gray-600">Bỏ qua</p>
                </div>
              </div>

              <div
                v-if="importResults.errors && importResults.errors.length > 0"
              >
                <details class="mt-4">
                  <summary
                    class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-900"
                  >
                    Xem chi tiết lỗi ({{ importResults.errors.length }} lỗi)
                  </summary>
                  <div class="mt-2 max-h-60 overflow-y-auto">
                    <ul
                      class="list-disc list-inside space-y-1 text-sm text-gray-600"
                    >
                      <li
                        v-for="(error, index) in importResults.errors"
                        :key="index"
                      >
                        {{ error }}
                      </li>
                    </ul>
                  </div>
                </details>
              </div>

              <div class="mt-4 flex justify-end">
                <router-link
                  :to="{ name: 'admin-products' }"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-block"
                >
                  Xem danh sách sản phẩm
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import api from "../services/api.js";

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

defineEmits(["logout"]);

const fileInput = ref(null);
const selectedFile = ref(null);
const isDragging = ref(false);
const uploading = ref(false);
const importResults = ref(null);

// Open Library import
const importingOpenLibrary = ref(false);
const openLibraryForm = ref({
  keyword: "",
  limit: 5,
  price: 100000,
  stock: 10,
});

const isOpenLibraryFormValid = computed(() => {
  return (
    openLibraryForm.value.keyword.trim() &&
    openLibraryForm.value.limit >= 1 &&
    openLibraryForm.value.limit <= 20 &&
    openLibraryForm.value.price >= 0 &&
    openLibraryForm.value.stock >= 0
  );
});

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    validateFile(file);
  }
};

const handleDrop = (event) => {
  isDragging.value = false;
  const file = event.dataTransfer.files[0];
  if (file) {
    validateFile(file);
  }
};

const validateFile = (file) => {
  const validTypes = [
    "text/csv",
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  ];
  const validExtensions = [".csv", ".xlsx", ".xls"];
  const fileExtension = "." + file.name.split(".").pop().toLowerCase();

  if (
    !validTypes.includes(file.type) &&
    !validExtensions.includes(fileExtension)
  ) {
    alert("File không hợp lệ. Vui lòng chọn file CSV hoặc Excel (.xlsx, .xls)");
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    alert("File quá lớn. Kích thước tối đa là 10MB");
    return;
  }

  selectedFile.value = file;
  importResults.value = null;
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const uploadFile = async () => {
  if (!selectedFile.value) return;

  uploading.value = true;
  importResults.value = null;

  try {
    const formData = new FormData();
    formData.append("file", selectedFile.value);

    const response = await api.post("/admin/products/import", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    importResults.value = response.data.results;

    if (response.data.results.success > 0) {
      // Emit event to refresh products list if needed
    }
  } catch (error) {
    console.error("Import error:", error);
    const errorMessage = error.response?.data?.message || "Lỗi khi import file";

    importResults.value = {
      success: 0,
      failed: 0,
      skipped: 0,
      errors: [errorMessage],
    };
  } finally {
    uploading.value = false;
  }
};

const downloadTemplate = () => {
  // Create CSV template with UTF-8 BOM for Excel compatibility
  const headers = [
    "Title",
    "Price",
    "Stock",
    "Author",
    "Publisher",
    "ISBN",
    "Cover URL",
    "Description",
    "Status",
  ];
  const exampleRow1 = [
    "Sách mẫu 1",
    "100000",
    "50",
    "Tác giả mẫu",
    "NXB mẫu",
    "1234567890",
    "https://example.com/image.jpg",
    "Mô tả sách mẫu",
    "active",
  ];
  const exampleRow2 = [
    "Sách mẫu 2",
    "200000",
    "30",
    "Tác giả khác",
    "NXB khác",
    "",
    "",
    "Mô tả sách mẫu 2",
    "active",
  ];

  // Add UTF-8 BOM for Excel compatibility
  const BOM = "\uFEFF";
  const csvContent =
    BOM +
    headers.join(",") +
    "\n" +
    exampleRow1.join(",") +
    "\n" +
    exampleRow2.join(",") +
    "\n";

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);

  link.setAttribute("href", url);
  link.setAttribute("download", "product_template.csv");
  link.style.visibility = "hidden";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

// Open Library import function
const importFromOpenLibrary = async () => {
  if (!isOpenLibraryFormValid.value) {
    alert("Vui lòng điền đầy đủ thông tin hợp lệ");
    return;
  }

  importingOpenLibrary.value = true;
  importResults.value = null;

  try {
    const response = await api.post("/admin/products/import/openlibrary", {
      keyword: openLibraryForm.value.keyword,
      limit: openLibraryForm.value.limit,
      price: openLibraryForm.value.price,
      stock: openLibraryForm.value.stock,
    });

    // Format results để hiển thị giống import file
    importResults.value = {
      success: response.data.total_imported || 0,
      failed: 0,
      skipped: response.data.total_skipped || 0,
      errors: response.data.errors || [],
    };

    if (response.data.total_imported > 0) {
      // Reset form sau khi import thành công
      openLibraryForm.value.keyword = "";
    }
  } catch (error) {
    console.error("Open Library import error:", error);
    const errorMessage =
      error.response?.data?.message || "Lỗi khi import từ Open Library";

    importResults.value = {
      success: 0,
      failed: openLibraryForm.value.limit,
      skipped: 0,
      errors: [errorMessage],
    };
  } finally {
    importingOpenLibrary.value = false;
  }
};
</script>
