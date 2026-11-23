{{--
    ======================================================================
    ADMIN MODULE: PRODUCTS BARCODE PRINT
    ======================================================================
    File: resources/views/admin/products/barcode-print.blade.php
    Responsibility: Back-office administration for PRODUCTS BARCODE PRINT
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Barcode Labels ({{ count($labels) }} pcs) - {{ $labelStoreName ?? $storeName }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/barcode.css') }}">
</head>
<body class="auto-print">
    <div class="screen-toolbar">
        <div>
            <strong>Thermal Print Preview</strong> ({{ count($labels) }} labels &bull; {{ $config['name'] }})
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn" onclick="window.print()">🖨️ Print Labels Now</button>
            <button class="btn" style="background: #475569;" onclick="window.close()">Close Window</button>
        </div>
    </div>

    <div class="labels-container" style="--label-w: {{ $config['css_width'] }}; --label-h: {{ $config['css_height'] }}; --barcode-h: {{ $config['barcode_height'] }}px;">
        @foreach($labels as $label)
            <div class="thermal-label">
                @if($showStoreName)
                    <div class="label-store">{{ $labelStoreName ?? $storeName }}</div>
                @endif

                <div class="label-name">{{ $label['name'] }}</div>

                <div class="label-barcode">
                    {!! $label['barcode'] !!}
                </div>

                <div class="label-footer">
                    @if($showSku)
                        <span class="label-sku">{{ $label['sku'] }}</span>
                    @else
                        <span></span>
                    @endif

                    @if($showPrice)
                        <span class="label-price">₹{{ $label['price'] }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <script src="{{ asset('js/admin/print.js') }}"></script>
</body>
</html>
