<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Exception;
use Illuminate\Support\Str;
use App\Models\Product; // Model của anh
use App\Services\ProductService; // Service của anh
use Illuminate\Support\Facades\Storage; // Dùng để lưu ảnh

class CrawlController extends Controller
{
    private $guzzleOptions;
    private $client;
    protected $productService; // Service của anh

    /**
     * Khởi tạo Guzzle Client và inject Service
     */
    public function __construct(ProductService $productService) // Inject ProductService
    {
        $this->productService = $productService; // Gán service
        $this->guzzleOptions = [
            'verify' => false,
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            ]
        ];
        $this->client = new Client($this->guzzleOptions);
    }

    /**
     * Hiển thị trang điều khiển
     */
    public function index()
    {
        return view('crawl.index'); // View này đã OK
    }

    /**
     * Hàm chính: Tự động phân trang, crawl sâu, và lưu DB
     * Route: POST /crawl/run
     *
     * *** ĐÃ SỬA LẠI KHỐI 'CATCH' ĐỂ BÁO LỖI ***
     */
    public function runFullCrawl(Request $request)
    {
        $validated = $request->validate([
            'baseUrl' => 'required|url'
        ]);
        
        $baseUrl = $validated['baseUrl'];
        $currentPage = 1;
        $totalProductsSaved = 0;
        $totalProductsUpdated = 0;
        $productsFoundOnPage = 0;
        $processedUrls = [];

        do {
            $productsFoundOnPage = 0; 
            $urlToCrawl = ''; // Khởi tạo

            try {
                // Logic lấy URL (đã sửa ở lần trước, giữ nguyên)
                if ($currentPage == 1) {
                    $urlToCrawl = $baseUrl;
                } else {
                    $baseUrlClean = strtok($baseUrl, '?'); 
                    $urlToCrawl = $baseUrlClean . '?page=' . $currentPage;
                }
                
                // 1. CRAWL TRANG DANH SÁCH
                $htmlListPage = $this->client->request('GET', $urlToCrawl)->getBody()->getContents();
                $crawler = new Crawler($htmlListPage);

                $crawler->filter('.item.item-product')->each(
                    function (Crawler $node) use (&$totalProductsSaved, &$totalProductsUpdated, &$productsFoundOnPage, &$processedUrls) {
                        
                        // ... (Toàn bộ logic bên trong 'each' giữ nguyên) ...
                        
                        $productUrl = $node->filter('h3.title-1 a')->count() > 0 ? $node->filter('h3.title-1 a')->attr('href') : null;
                        if (empty($productUrl) || isset($processedUrls[$productUrl])) return;
                        $processedUrls[$productUrl] = true;
                        
                        $details = $this->crawlDetailPage($productUrl);
                        if(empty($details['sku'])) return;

                        $imageUrl = $node->filter('.img a img')->attr('src');
                        $localImagePath = $this->downloadAndStoreImage($imageUrl, $details['sku']); 
                        
                        $data = [
                            'name' => $details['name'],
                            'code' => $details['sku'], 
                            'slug' => Str::slug($details['name']),
                            'price' => $this->cleanPrice($details['price']),
                            'price_discount' => null, 
                            'content' => $details['description'], 
                            'description' => Str::limit(strip_tags($details['description']), 150), 
                            'status' => true,
                            'type' => Product::TYPE_PHYSICS, 
                            'stock' => 999, 
                            'has_variants' => false,
                            'image_original_path' => $localImagePath, 
                            'gallery_original_paths' => null, 
                        ];
                        
                        $product = Product::where('code', $details['sku'])->first();
                        
                        if ($product) {
                            $this->productService->update($product, $data);
                            $totalProductsUpdated++;
                        } else {
                            $this->productService->create($data);
                            $totalProductsSaved++;
                        }
                        
                        $productsFoundOnPage++;
                    }
                );

                $currentPage++; // Chuyển sang trang tiếp theo

            } catch (Exception $e) {
                // *** SỬA LỖI Ở ĐÂY ***
                // Nếu Guzzle lỗi, trả về JSON báo lỗi chi tiết
                // thay vì "break" trong im lặng.
                
                // Thêm GuzzleHttp\Exception\RequestException vào use
                // để bắt lỗi chi tiết (nếu anh muốn)
                if ($e instanceof \GuzzleHttp\Exception\RequestException) {
                     return response()->json([
                        'status' => 'Lỗi Guzzle Request',
                        'message' => "Không thể tải URL: " . $urlToCrawl . " | Mã lỗi: " . $e->getCode() . " | Chi tiết: " . $e->getMessage()
                    ], 500); // Trả về 500 để AlpineJS biết là lỗi
                }
                
                // Lỗi chung
                return response()->json([
                    'status' => 'Lỗi Exception',
                    'message' => "Đã xảy ra lỗi ở trang " . $currentPage . ": " . $e->getMessage()
                ], 500); 
            }

        } while ($productsFoundOnPage > 0); 

        // 6. TRẢ VỀ KẾT QUẢ TÓM TẮT
        return response()->json([
            'status' => 'Hoàn tất!',
            'message' => "Đã quét " . ($currentPage - 1) . " trang. Tạo mới " . $totalProductsSaved . " SP, Cập nhật " . $totalProductsUpdated . " SP."
        ]);
    }

    /**
     * Hàm phụ: Tải ảnh từ URL và lưu vào storage/userfiles
     */
    private function downloadAndStoreImage(string $imageUrl, string $baseFileName)
    {
        try {
            // Tải nội dung ảnh
            $imageData = $this->client->get($imageUrl)->getBody()->getContents();
            
            // Lấy đuôi file
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (empty($extension)) $extension = 'jpg'; // Mặc định
            
            // Tạo tên file an toàn
            $fileName = Str::slug($baseFileName) . '.' . $extension;
            
            // Đường dẫn lưu file mà ProductService của anh mong muốn
            // (Lưu vào storage/app/public/userfiles/)
            $path = 'userfiles/' . $fileName; 
            
            // Dùng Storage facade để lưu
            Storage::disk('public')->put($path, $imageData);
            
            // Trả về đường dẫn tương đối (relative path)
            // Vì ProductService của anh nhận 'image_original_path' là 'userfiles/...'
            return $path; 
            
        } catch (Exception $e) {
            // Nếu tải ảnh lỗi, trả về null
            return null;
        }
    }


    /**
     * Hàm phụ: Crawl trang chi tiết (đã cập nhật)
     */
    private function crawlDetailPage(string $url)
    {
        try {
            $html = $this->client->request('GET', $url)->getBody()->getContents();
            $crawler = new Crawler($html);

            $name = $crawler->filter('h1.font-black')->text('N/A');
            $price = $crawler->filter('span.js_product_price_final')->text('0');

            // Lấy Mã SP (HTML: <p>Mã sản phẩm: <span>SP0248</span></p>)
            $skuNode = $crawler->filter('p.text-f14')->eq(0)->filter('span.text-blue_primary');
            $sku = $skuNode->count() > 0 ? $skuNode->text() : null; // Lấy 'SP0248'
            
            // Lấy Mô tả
            $descNode = $crawler->filter('#tab-1 .box_content');
            $description = $descNode->count() > 0 ? $descNode->html() : null;

            return [
                'name' => trim($name),
                'price' => trim($price),
                'sku' => trim($sku),
                'description' => trim($description)
            ];

        } catch (Exception $e) {
            return ['name' => 'Lỗi Crawl', 'price' => '0', 'sku' => null, 'description' => null];
        }
    }

    /**
     * Hàm phụ: Làm sạch giá
     */
    private function cleanPrice(string $price)
    {
        return (float) preg_replace('/[^\d]/', '', $price);
    }
}