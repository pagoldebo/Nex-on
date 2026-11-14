<?php

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Direct web execution of sitemap generator is forbidden.');
}

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

$staticRoutes = [
    '' => '1.0',
    '/search' => '0.8',
    '/about' => '0.7',
    '/contact' => '0.7',
    '/faq' => '0.6',
    '/size-guide' => '0.7',
    '/shipping-policy' => '0.5',
    '/return-policy' => '0.5',
    '/privacy' => '0.3',
    '/terms' => '0.3',
];

foreach ($staticRoutes as $route => $priority) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . htmlspecialchars($baseUrl . $route) . '</loc>' . PHP_EOL;
    $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
    $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

foreach (\App\Models\Category::all() as $cat) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/c/' . $cat->slug) . '</loc>' . PHP_EOL;
    $xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
    $xml .= '    <priority>0.85</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

foreach (\App\Models\Product::where('is_active', true)->get() as $prod) {
    $xml .= '  <url>' . PHP_EOL;
    $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/p/' . $prod->slug) . '</loc>' . PHP_EOL;
    $xml .= '    <lastmod>' . $prod->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
    $xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
    $xml .= '    <priority>0.9</priority>' . PHP_EOL;
    $xml .= '  </url>' . PHP_EOL;
}

$xml .= '</urlset>';
file_put_contents(__DIR__ . '/public/sitemap.xml', $xml);
echo 'Sitemap successfully written to public/sitemap.xml with ' . substr_count($xml, '<url>') . ' URLs.' . PHP_EOL;
