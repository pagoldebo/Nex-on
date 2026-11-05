{{-- resources/views/storefront/vyra/order-confirmation.blade.php --}}
<x-storefront-layout :theme="$theme" title="Order Confirmed">
<div class="max-w-2xl mx-auto px-6 py-20 text-center">
  <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6" style="background:#FF3D5A22;clip-path:polygon(15% 0,100% 0,85% 100%,0 100%)">
    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#FF3D5A"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
  </div>
  <h1 class="font-display uppercase text-4xl md:text-5xl mb-3 reveal text-[#F2F0EC]">Order Confirmed!</h1>
  <p class="font-body opacity-60 mb-2 text-[#F2F0EC]">Thank you for shopping with VYRA.</p>
  <p class="font-body text-sm opacity-60 mb-10 text-[#F2F0EC]">Order Reference: <span id="orderId" class="font-mono font-bold" style="color:#FF3D5A">{{ $orderModel->reference ?? $order ?? '—' }}</span></p>

  <div class="bg-[#1A1A1D] border p-6 text-left mb-8" style="border-color:#FF3D5A22">
    <h3 class="font-display uppercase text-lg mb-4 text-[#F2F0EC]">Items Ordered</h3>
    <div id="orderItems" class="space-y-2 font-body text-sm text-[#F2F0EC]">
      @if(isset($orderModel) && $orderModel)
        @foreach($orderModel->items as $item)
          <div class="flex justify-between opacity-80 py-1 border-b border-[#FF3D5A15]">
            <span>{{ $item->product_name }} ({{ $item->color_name }}/{{ $item->size }}) ×{{ $item->qty }}</span>
            <span class="font-bold">₹{{ number_format($item->line_total, 0) }}</span>
          </div>
        @endforeach
      @endif
    </div>
    <div class="border-t mt-4 pt-4 flex justify-between font-bold" style="border-color:#FF3D5A22">
      <span class="text-[#F2F0EC]">Total</span>
      <span id="orderTotal" style="color:#FF3D5A">
        {{ isset($orderModel) && $orderModel ? '₹' . number_format($orderModel->total, 0) : '₹0' }}
      </span>
    </div>
  </div>

  <div class="flex flex-wrap justify-center gap-4">
    <a href="{{ route('home') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest transition-all" style="background:#FF3D5A;color:#0E0E10;clip-path:polygon(4% 0,100% 0,96% 100%,0 100%)">Continue Shopping</a>
    @auth
      <a href="{{ route('account') }}" class="px-8 py-3.5 text-xs font-bold uppercase tracking-widest border border-[#FF3D5A] text-[#F2F0EC] transition-colors">My Orders</a>
    @endauth
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const serverOrder = @json(isset($orderModel) && $orderModel ? true : false);
  if (!serverOrder) {
    const o = JSON.parse(sessionStorage.getItem('lc_last_order') || 'null');
    if (!o) return;
    document.getElementById('orderId').textContent = o.id || o.reference || '—';
    document.getElementById('orderTotal').textContent = '₹' + Number(o.total || 0).toLocaleString('en-IN');
    if (o.items?.length) {
      document.getElementById('orderItems').innerHTML = o.items.map(i =>
        `<div class="flex justify-between opacity-70 py-1"><span>${i.name} (${i.colorName}/${i.size}) ×${i.qty}</span><span class="font-bold">₹${(i.price*i.qty).toLocaleString('en-IN')}</span></div>`
      ).join('');
    }
  }
});
</script>
@endpush
</x-storefront-layout>
