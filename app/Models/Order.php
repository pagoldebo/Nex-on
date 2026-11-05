<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'pos_cashier_id',
        'reference',
        'channel',
        'status',
        'subtotal',
        'shipping_charge',
        'cod_charge',
        'discount',
        'total',
        'tendered_amount',
        'change_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address1',
        'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'coupon_code',
        'tracking_number',
        'courier',
        'notes',
        'shipped_at',
        'delivered_at',
        'delivery_personnel_id',
        'delivery_status',
        'proof_signature',
        'proof_notes',
    ];

    protected $casts = [
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ── Status constants ──────────────────────────────────────────────────────

    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REFUND_REQ = 'refund_requested';
    const STATUS_REFUNDED   = 'refunded';

    const PAYMENT_PENDING  = 'pending';
    const PAYMENT_PAID     = 'paid';
    const PAYMENT_FAILED   = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    // ── Boot: auto-generate reference ────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->reference)) {
                $order->reference = static::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        $prefix = 'SAA';
        $date   = now()->format('Ymd');
        $seq    = str_pad(
            static::whereDate('created_at', today())->count() + 1,
            4, '0', STR_PAD_LEFT
        );
        return "{$prefix}-{$date}-{$seq}";
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posCashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pos_cashier_id');
    }

    public function deliveryPersonnel(): BelongsTo
    {
        return $this->belongsTo(DeliveryPersonnel::class, 'delivery_personnel_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(OrderRequest::class);
    }

    public function isPos(): bool
    {
        return $this->channel === 'pos';
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Formatted subtotal, e.g. "₹1,499" */
    public function getFormattedSubtotalAttribute(): string
    {
        return '₹' . number_format($this->subtotal, 0);
    }

    /** Formatted total, e.g. "₹1,648" */
    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total, 0);
    }

    /** Human-readable status label */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_CONFIRMED  => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED    => 'Shipped',
            self::STATUS_DELIVERED  => 'Delivered',
            self::STATUS_CANCELLED  => 'Cancelled',
            self::STATUS_REFUND_REQ => 'Refund Requested',
            self::STATUS_REFUNDED   => 'Refunded',
            default                 => ucfirst($this->status),
        };
    }

    /** Tailwind colour pair for status badge */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED, self::STATUS_PROCESSING => 'bg-blue-50 text-blue-700',
            self::STATUS_SHIPPED                            => 'bg-amber-50 text-amber-700',
            self::STATUS_DELIVERED                          => 'bg-green-50 text-green-700',
            self::STATUS_CANCELLED, self::STATUS_REFUNDED   => 'bg-red-50 text-red-600',
            default                                         => 'bg-gray-50 text-gray-600',
        };
    }

    /** Shipping address as a single display string */
    public function getShippingAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address1,
            $this->shipping_address2,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_pincode,
        ]));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isCod(): bool
    {
        return $this->payment_method === 'cod';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    /**
     * Build an order from the sessionStorage payload the front-end POSTs.
     * $data mirrors the lc_last_order JS object shape.
     */
    public static function createFromCheckout(array $data, ?int $userId = null): static
    {
        $sub        = (int) ($data['subtotal'] ?? 0);
        $method     = $data['payment'] ?? $data['payment_method'] ?? 'upi';
        $discount   = (int) ($data['discount'] ?? 0);
        $couponCode = !empty($data['coupon_code']) ? strtoupper(trim($data['coupon_code'])) : null;
        
        $freeThreshold = (int) Setting::get('free_shipping_threshold', 1499);
        $standardShip  = (int) Setting::get('shipping_cost', 99);
        $shippingCharge= isset($data['shipping_charge']) ? (int) $data['shipping_charge'] : ($sub >= $freeThreshold ? 0 : $standardShip);
        
        $codFeeSetting = (int) Setting::get('cod_charge', 40);
        $codCharge     = $method === 'cod' ? (isset($data['cod_charge']) ? (int) $data['cod_charge'] : $codFeeSetting) : 0;
        
        $total         = isset($data['total']) ? (int) $data['total'] : max(0, $sub + $shippingCharge + $codCharge - $discount);
        $shipping      = $data['shipping'] ?? [];

        $order = static::create([
            'user_id'          => $userId,
            'status'           => self::STATUS_PENDING,
            'subtotal'         => $sub,
            'shipping_charge'  => $shippingCharge,
            'cod_charge'       => $codCharge,
            'discount'         => $discount,
            'coupon_code'      => $couponCode,
            'total'            => $total,
            'payment_method'   => $method,
            'payment_status'   => self::PAYMENT_PENDING,
            'shipping_name'    => $shipping['name']     ?? $data['customer_name']    ?? '',
            'shipping_phone'   => $shipping['phone']    ?? $data['customer_phone']   ?? '',
            'shipping_email'   => $shipping['email']    ?? $data['customer_email']   ?? '',
            'shipping_address1'=> $shipping['address1'] ?? $data['shipping_address'] ?? $data['shipping_address1'] ?? '',
            'shipping_address2'=> $shipping['address2'] ?? $data['shipping_address2']?? null,
            'shipping_city'    => $shipping['city']     ?? $data['shipping_city']    ?? '',
            'shipping_state'   => $shipping['state']    ?? $data['shipping_state']   ?? '',
            'shipping_pincode' => $shipping['pincode']  ?? $data['shipping_pincode'] ?? '',
        ]);

        foreach (($data['items'] ?? []) as $item) {
            $productId = $item['productId'] ?? $item['product_id'] ?? null;
            $variantId = $item['productVariantId'] ?? $item['product_variant_id'] ?? null;
            $colorId   = $item['productColorId'] ?? $item['product_color_id'] ?? null;
            $colorName = $item['colorName'] ?? $item['color_name'] ?? $item['color'] ?? '';
            $qty       = max(1, (int) ($item['qty'] ?? 1));

            // Variant lookup and stock deduction
            $variant = null;
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
            } elseif ($productId && !empty($item['size'])) {
                $colorQuery = ProductColor::where('product_id', $productId);
                if (!empty($colorName)) {
                    $colorQuery->where('name', $colorName);
                }
                $color = $colorQuery->first();
                if ($color) {
                    $colorId = $color->id;
                    $variant = ProductVariant::where('product_color_id', $color->id)
                        ->where('size', $item['size'])
                        ->first();
                }
            }

            $itemSku = null;
            if ($variant) {
                $variantId = $variant->id;
                $colorId   = $colorId ?? $variant->product_color_id;
                $itemSku   = $variant->sku;
                if ($variant->stock > 0) {
                    $deductQty = min($qty, $variant->stock);
                    $variant->adjustStock(-$deductQty, 'order_deduction', "Order #{$order->reference}", $userId);
                }
            } elseif ($productId) {
                $prod = Product::find($productId);
                $itemSku = $prod?->sku;
            }

            $order->items()->create([
                'product_id'         => $productId,
                'product_color_id'   => $colorId,
                'product_variant_id' => $variantId,
                'sku'                => $itemSku,
                'product_name'       => $item['name']             ?? '',
                'product_slug'       => $item['slug']             ?? '',
                'color_name'         => $colorName,
                'color_hex'          => $item['colorHex']         ?? $item['color_hex'] ?? null,
                'size'               => $item['size']             ?? '',
                'cover_image'        => $item['image']            ?? $item['cover_image'] ?? null,
                'price'              => (int) ($item['price']     ?? 0),
                'compare_at_price'   => isset($item['compareAtPrice']) ? (int) $item['compareAtPrice'] : (isset($item['compare_at_price']) ? (int) $item['compare_at_price'] : null),
                'qty'                => $qty,
                'line_total'         => (int) ($item['price'] ?? 0) * $qty,
            ]);
        }

        if ($couponCode) {
            Coupon::where('code', $couponCode)->increment('used_count');
        }

        return $order;
    }
}
