<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Barcode Labels ({{ count($labels) }} pcs) - {{ $labelStoreName ?? $storeName }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f3f4f6;
            color: #000;
        }

        .screen-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .btn {
            background: #4f46e5;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #4338ca;
        }

        .labels-container {
            margin-top: 70px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            padding: 20px;
        }

        .thermal-label {
            background: #fff;
            width: {{ $config['css_width'] }};
            height: {{ $config['css_height'] }};
            padding: 2mm 3mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            border: 1px dashed #cbd5e1;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
        }

        .label-store {
            font-size: 7pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
        }

        .label-name {
            font-size: 7.5pt;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            line-height: 1.1;
        }

        .label-barcode {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1px 0;
        }

        .label-barcode svg {
            max-width: 95%;
            height: {{ $config['barcode_height'] }}px;
        }

        .label-footer {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 6.5pt;
            font-family: monospace, monospace;
            font-weight: 700;
            line-height: 1;
        }

        .label-price {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 7.5pt;
            font-weight: 900;
        }

        @media print {
            body {
                background: #fff;
            }
            .screen-toolbar {
                display: none !important;
            }
            .labels-container {
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
            .thermal-label {
                border: none !important;
                margin: 0 !important;
                width: {{ $config['css_width'] }} !important;
                height: {{ $config['css_height'] }} !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            @page {
                size: {{ $config['css_width'] }} {{ $config['css_height'] }};
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <div>
            <strong>Thermal Print Preview</strong> ({{ count($labels) }} labels &bull; {{ $config['name'] }})
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn" onclick="window.print()">🖨️ Print Labels Now</button>
            <button class="btn" style="background: #475569;" onclick="window.close()">Close Window</button>
        </div>
    </div>

    <div class="labels-container">
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

    <script>
        // Automatic print prompt on page load
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
