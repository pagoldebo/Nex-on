<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\BarcodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminBarcodeController extends Controller
{
    public function __construct(
        protected BarcodeGeneratorService $barcodeService
    ) {}

    /**
     * Display the barcode label design & printing studio.
     */
    public function index(Request $request): View
    {
        $labelSizes = $this->barcodeService->getLabelSizes();
        $selectedSize = $request->input('size', '50x25');

        $query = Product::with(['category', 'variants'])->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        // Sample barcode preview SVG
        $sampleSku = $products->first()?->sku ?? 'LS-PRD-DEMO';
        $previewSvg = $this->barcodeService->generateSvg($sampleSku, 2, 45, true);

        return view('admin.products.barcode', compact('products', 'labelSizes', 'selectedSize', 'previewSvg', 'sampleSku'));
    }

    /**
     * Render the thermal print view ready for browser print dialog.
     */
    public function print(Request $request): View
    {
        $validated = $request->validate([
            'size'             => 'required|string|in:50x25,38x25,100x50',
            'show_store_name'  => 'nullable|boolean',
            'show_price'       => 'nullable|boolean',
            'show_sku'         => 'nullable|boolean',
            'store_name'       => 'nullable|string|max:50',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|integer',
            'items.*.sku'      => 'required|string',
            'items.*.name'     => 'required|string',
            'items.*.price'    => 'required|numeric',
            'items.*.copies'   => 'required|integer|min:1|max:500',
        ]);

        $labelSizes = $this->barcodeService->getLabelSizes();
        $config = $labelSizes[$validated['size']];

        $labels = [];
        foreach ($validated['items'] as $item) {
            $copies = (int) $item['copies'];
            $svg = $this->barcodeService->generateSvg(
                $item['sku'],
                $validated['size'] === '100x50' ? 2 : 1,
                $config['barcode_height'],
                false
            );

            for ($i = 0; $i < $copies; $i++) {
                $labels[] = [
                    'name'    => $item['name'],
                    'sku'     => $item['sku'],
                    'price'   => number_format((float) $item['price'], 2),
                    'barcode' => $svg,
                ];
            }
        }

        return view('admin.products.barcode-print', [
            'labels'          => $labels,
            'config'          => $config,
            'sizeKey'         => $validated['size'],
            'showStoreName'   => $request->boolean('show_store_name', true),
            'showPrice'       => $request->boolean('show_price', true),
            'showSku'         => $request->boolean('show_sku', true),
            'labelStoreName'  => !empty($validated['store_name']) ? $validated['store_name'] : 'NEXON LUXE',
        ]);
    }

    /**
     * Ajax preview endpoint for barcode rendering.
     */
    public function preview(Request $request): Response
    {
        $code = $request->input('code', 'NEXON-12345');
        $svg = $this->barcodeService->generateSvg($code, 2, 45, true);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
