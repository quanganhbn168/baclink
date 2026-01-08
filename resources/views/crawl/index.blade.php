<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển Crawl</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-4 md:p-8 max-w-2xl">
        <div x-data="crawlController()">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Crawl & Đồng bộ sản phẩm</h1>

            <div class="mb-6 bg-white p-6 rounded-lg shadow-md space-y-3">
                <label for="baseUrl" class="block text-sm font-medium text-gray-700">URL Danh mục (ví dụ: .../san-pham.html):</label>
                <input
                    type="text"
                    id="baseUrl"
                    x-model="baseUrl"
                    placeholder="https://ekokemika.com.vn/san-pham.html"
                    class="w-full p-3 border border-gray-300 rounded-lg shadow-sm"
                    :disabled="isLoading"
                >
                <button
                    @click="startFullCrawl()"
                    :disabled="isLoading"
                    class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition-colors disabled:opacity-50"
                >
                    <span x-show="!isLoading">Bắt đầu Crawl & Đồng bộ Database</span>
                    <span x-show="isLoading">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang xử lý... (Việc này có thể mất vài phút, vui lòng không tắt trình duyệt)
                    </span>
                </button>
            </div>

            <div x-show="statusMessage" class="mb-4">
                <div 
                    class="p-4 rounded-lg"
                    :class="{ 'bg-green-100 text-green-700': isSuccess, 'bg-red-100 text-gold-700': !isSuccess }"
                    x-text="statusMessage"
                >
                </div>
            </div>
        </div>
    </div>

    <script>
        function crawlController() {
            return {
                baseUrl: 'https://ekokemika.com.vn/san-pham.html', // URL mặc định
                isLoading: false,
                statusMessage: '',
                isSuccess: false,
                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                async startFullCrawl() {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    this.statusMessage = 'Đang bắt đầu...';
                    this.isSuccess = false;

                    try {
                        const response = await fetch('{{ route("crawl.run") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': this.csrfToken
                            },
                            body: JSON.stringify({ baseUrl: this.baseUrl })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (data.errors && data.errors.baseUrl) {
                                throw new Error(data.errors.baseUrl[0]); // Lỗi validation
                            }
                            throw new Error(data.message || 'Lỗi không xác định từ server.');
                        }
                        
                        // Thành công!
                        this.statusMessage = data.message; // Hiển thị thông báo (ví dụ: "Hoàn tất!...")
                        this.isSuccess = true;

                    } catch (error) {
                        this.statusMessage = `Lỗi: ${error.message}`;
                        this.isSuccess = false;
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
