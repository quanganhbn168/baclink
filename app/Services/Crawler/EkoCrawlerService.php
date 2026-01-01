<?php

namespace App\Services\Crawler;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class EkoCrawlerService
{
    protected Client $http;

    protected array $userAgents = [
        // Chrome Windows
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119 Safari/537.36',
        // Chrome Mac
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119 Safari/537.36',
        // Firefox
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:119.0) Gecko/20100101 Firefox/119.0',
        // iPhone Safari
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
    ];

    public function __construct(?Client $client = null)
    {
        $this->http = $client ?: new Client([
            'timeout'       => 25,
            'http_errors'   => false,               // không ném exception khi 4xx/5xx
            'allow_redirects' => true,
            // Nếu dev cần, có thể set verify=false trong .env rồi đọc vào đây
            'verify'        => env('CRAWLER_VERIFY_SSL', true),
            'headers'       => [
                'Accept'            => 'text/html,application/xhtml+xml',
                'Accept-Language'   => 'vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control'     => 'no-cache',
                'Pragma'            => 'no-cache',
                'Connection'        => 'keep-alive',
            ],
        ]);
    }

    public function fetch(string $url, int $maxRetry = 4, int $sleepMs = 350): string
{
    $uaCount = count($this->userAgents);
    $attempt = 0; $lastBody = '';

    while ($attempt < $maxRetry) {
        try {
            $headers = [
                'User-Agent' => $this->userAgents[$attempt % $uaCount],
                'Referer'    => $this->originOf($url),
                'Accept-Encoding' => 'gzip, deflate, br',
            ];
            $res = $this->http->get(
                $url . (str_contains($url, '?') ? '&' : '?') . 'nocache=' . mt_rand(10000,99999),
                ['headers' => $headers]
            );
            $code = $res->getStatusCode();
            $body = (string)$res->getBody();
            $lastBody = $body;

            if ($code >= 200 && $code < 300 && $body !== '') {
                usleep($sleepMs * 1000);
                return $body;
            }
            if (in_array($code, [403,429]) || $code >= 500) {
                usleep(($sleepMs + $attempt*250) * 1000);
                $attempt++;
                continue;
            }
            break;
        } catch (\Throwable $e) {
            // network error: backoff rồi thử UA khác
            usleep(($sleepMs + $attempt*300) * 1000);
            $attempt++;
        }
    }
    return $lastBody;
}


    /** Fallback phát hiện danh mục từ nhiều entry */
    public function discoverCategoryLinks(string $entryUrl): array
    {
        $trials = [
            $entryUrl,
            $this->originOf($entryUrl) . '/',
            $this->originOf($entryUrl) . '/san-pham.html',
            $this->originOf($entryUrl) . '/san-pham.html?page=1',
        ];

        foreach ($trials as $u) {
            $html = $this->fetch($u);
            $links = $this->parseCategories($u, $html);
            if (!empty($links)) return $links;
        }
        return [];
    }

    /** Lấy toàn bộ link danh mục từ block .box-category-product1 */
    public function parseCategories(string $entryUrl, string $html): array
    {
        $c = new Crawler($html);
        $links = [];
        // chuẩn
        $c->filter('.box-category-product1 ul a[href]')->each(function(Crawler $a) use (&$links, $entryUrl){
            $href = trim($a->attr('href') ?? '');
            if ($href) $links[] = $this->absolutize($href, $entryUrl);
        });
        // fallback: bất cứ sidebar nào có chữ "Danh mục"
        if (empty($links)) {
            $c->filter('a[href]')->each(function(Crawler $a) use (&$links, $entryUrl){
                $txt = mb_strtolower(trim($a->text()));
                if ($txt !== '' && (str_contains($txt, 'danh mục') || str_contains($txt, 'sản phẩm'))) {
                    $href = $a->attr('href') ?? '';
                    if ($href && preg_match('#https?://[^"]+\.html#i', $href)) {
                        $links[] = $this->absolutize($href, $entryUrl);
                    }
                }
            });
        }
        return array_values(array_unique($links));
    }

    public function parseListing(string $listUrl, string $html): array
    {
        $c = new Crawler($html);

        // links sản phẩm (chỉ lấy thẻ <a> đầu tiên trong card để tránh trùng ảnh/tên)
        $productLinks = [];
        $c->filter('#js_data_product_filter .item.item-product')->each(function(Crawler $card) use (&$productLinks, $listUrl){
            $a = $card->filter('.img a[href], h3 a[href]')->first();
            if ($a->count()) {
                $href = trim($a->attr('href') ?? '');
                if ($href) $productLinks[] = $this->absolutize($href, $listUrl);
            }
        });
        $productLinks = array_values(array_unique($productLinks));

        // next page:
        $nextUrl = null;

        // 1) rel="next"
        $relNext = $c->filter('a[rel="next"]')->first();
        if ($relNext->count()) {
            $nextUrl = $this->absolutize($relNext->attr('href') ?? '', $listUrl);
        }

        // 2) aria-label
        if (!$nextUrl) {
            $aria = $c->filter('a[aria-label*="Next"], a[aria-label*="Sau"]')->first();
            if ($aria->count()) $nextUrl = $this->absolutize($aria->attr('href') ?? '', $listUrl);
        }

        // 3) current page + sibling
        if (!$nextUrl) {
            $curr = $c->filter('.pagination .active a, .pagination .active span, .pagination [aria-current="page"]')->first();
            if ($curr->count()) {
                $li = $curr->ancestors()->filter('li')->first();
                if ($li->count()) {
                    $sib = $li->nextAll()->filter('a[href]')->first();
                    if ($sib->count()) $nextUrl = $this->absolutize($sib->attr('href') ?? '', $listUrl);
                }
            }
        }

        // 4) fallback cũ nếu trang không có markup chuẩn
        if (!$nextUrl) {
            $c->filter('a[href*="page="]')->each(function(Crawler $a) use (&$nextUrl, $listUrl) {
                if (!$nextUrl) $nextUrl = $this->absolutize($a->attr('href') ?? '', $listUrl);
            });
        }

        return [$productLinks, $nextUrl];
    }


    /** Parse chi tiết */
    public function parseProduct(string $url, string $html): array
    {
        $c = new Crawler($html);

        $name = $this->textOrNull($c, 'h1, h1.product-title, h1.product-name');

        $priceText = $this->textOrNull($c, '.price .text-red-600, .price, [itemprop=price]');
        $price     = $this->normalizePrice($priceText);
        if ($priceText && (mb_stripos($priceText, 'liên hệ') !== false)) $price = null;

        $sku = $this->textOrNull($c, '.sku, [itemprop=sku], .product-sku');

        $category = null;
$c->filter('ol.breadcrumb, nav[aria-label*="Bread"], nav[aria-label*="crumb"], ol')->each(function(Crawler $ol) use (&$category){
    $links = $ol->filter('a');
    if ($links->count() >= 1) {
        $category = trim($links->last()->text(''));
    }
});

        // ảnh
        $image = $this->firstImage($c, [
            '.product-gallery img',
            'meta[property="og:image"]',
            '.swiper img',
            'img.product-image',
            'img',
        ], $url);

        $detailImages = $this->collectImages($c, [
            '.product-gallery img',
            '.product-detail img',
            '.content img',
            '.entry-content img',
        ], $url);
        $detailImages = array_values(array_unique(array_filter($detailImages)));
        if ($image) $detailImages = array_values(array_filter($detailImages, fn($u) => $u !== $image));

        $descriptionHtml = $this->htmlOrNull($c, '.product-description, .content, .entry-content, [itemprop=description]');
        $descriptionHtml = $this->stripVideos($descriptionHtml);

        return [
            'name'               => $name,
            'sku'                => $sku,
            'price'              => $price,
            'image_url'          => $image,
            'detail_image_urls'  => $detailImages,
            'category'           => $category,
            'description_html'   => $descriptionHtml,
        ];
    }

    // ===== Helpers =====
    protected function textOrNull(Crawler $c, string $selector): ?string
    {
        try {
            $n = $c->filter($selector)->first();
            if ($n->count()) return trim(preg_replace('/\s+/', ' ', html_entity_decode($n->text())));
        } catch (\Throwable $e) {}
        return null;
    }
    protected function htmlOrNull(Crawler $c, string $selector): ?string
    {
        try {
            $n = $c->filter($selector)->first();
            if ($n->count()) return $n->html();
        } catch (\Throwable $e) {}
        return null;
    }
    protected function normalizePrice(?string $text): ?int
{
    if (!$text) return null;
    $t = mb_strtolower(trim(html_entity_decode($text)));
    if (str_contains($t, 'liên hệ')) return null;

    $digits = preg_replace('/[^\d]/u', '', $t);
    return $digits === '' ? null : (int)$digits;
}

    protected function absolutize(string $maybe, string $base): string
    {
        if (str_starts_with($maybe, '//')) return 'https:' . $maybe;
        if (preg_match('#^https?://#i', $maybe)) return $maybe;

        $parts = parse_url($base);
        $origin = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
        if (str_starts_with($maybe, '/')) return $origin . $maybe;

        $path = $parts['path'] ?? '/';
        $dir  = preg_replace('#/[^/]*$#', '/', $path);
        return $origin . $dir . $maybe;
    }
    protected function originOf(string $url): string
    {
        $p = parse_url($url);
        if (!$p || !isset($p['scheme'], $p['host'])) return $url;
        return $p['scheme'].'://'.$p['host'].(isset($p['port'])?':'.$p['port']:'');
    }
    protected function firstImage(Crawler $c, array $selectors, string $baseUrl): ?string
    {
        foreach ($selectors as $sel) {
            $n = $c->filter($sel);
            if (!$n->count()) continue;

            $src = null;
            if (str_contains($sel, 'meta[')) $src = $n->attr('content');
            else $src = $n->first()->attr('src') ?? $n->first()->attr('data-src') ?? $n->first()->attr('data-original');

            if ($src) return $this->absolutize($src, $baseUrl);
        }
        return null;
    }
    protected function collectImages(Crawler $c, array $selectors, string $baseUrl): array
{
    $urls = [];
    foreach ($selectors as $sel) {
        $c->filter($sel)->each(function(Crawler $n) use (&$urls, $baseUrl, $sel) {
            if (str_starts_with($sel, 'meta[')) {
                $src = $n->attr('content');
            } else {
                if (strtolower($n->nodeName()) !== 'img') return;
                $src = $n->attr('src') ?: $n->attr('data-src') ?: $n->attr('data-original');
            }
            if ($src) $urls[] = $this->absolutize($src, $baseUrl);
        });
    }
    return $urls;
}

    protected function stripVideos(?string $html): ?string
    {
        if (!$html) return null;
        $html = preg_replace('#<video[\s\S]*?</video>#i', '', $html);
        $html = preg_replace('#<iframe[\s\S]*?</iframe>#i', '', $html);
        return $html;
    }
}
