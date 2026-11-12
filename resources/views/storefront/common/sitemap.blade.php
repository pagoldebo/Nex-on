{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    {{-- Homepage --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Curated Collections --}}
    @foreach($curatedCollections as $slug => $priority)
    <url>
        <loc>{{ url('/collections/' . $slug) }}</loc>
        <changefreq>daily</changefreq>
        <priority>{{ $priority }}</priority>
    </url>
    @endforeach

    {{-- Product Categories --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ route('category.show', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at ? $category->updated_at->toIso8601String() : now()->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Active Products with Google Image Sitemaps --}}
    @foreach($products as $product)
    <url>
        <loc>{{ route('product.show', $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at ? $product->updated_at->toIso8601String() : now()->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
        @if($product->cover_image)
        <image:image>
            <image:loc>{{ asset($product->cover_image) }}</image:loc>
            <image:title>{{ $product->name }}</image:title>
        </image:image>
        @endif
        @foreach($product->colors as $color)
            @foreach($color->images as $img)
                @if($img->url && $img->url !== $product->cover_image)
                <image:image>
                    <image:loc>{{ asset($img->url) }}</image:loc>
                    <image:title>{{ $product->name }} - {{ $color->name }}</image:title>
                </image:image>
                @endif
            @endforeach
        @endforeach
    </url>
    @endforeach

    {{-- Static Informational & Policy Pages --}}
    @foreach($staticPages as $path => $meta)
    <url>
        <loc>{{ url('/' . $path) }}</loc>
        <changefreq>{{ $meta['changefreq'] }}</changefreq>
        <priority>{{ $meta['priority'] }}</priority>
    </url>
    @endforeach
</urlset>

