{{--
    ======================================================================
    ADMIN MODULE: INVOICES CREATE
    ======================================================================
    File: resources/views/admin/invoices/create.blade.php
    Responsibility: Back-office administration for INVOICES CREATE
    Features:
      - Standard administration UI following the Nex-on design guidelines
      - Accessible controls, Tailwind CSS styling, and reactive Alpine.js components
    Security & Access Control:
      - Guarded by Laravel authentication and RBAC permission middleware
    Operations & Maintenance:
      - Integrates with master administration shell (@extends('admin.layout'))
--}}
@extends('admin.layout')

@section('content')
<div x-data="invoiceBuilder()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.invoices.index') }}" class="hover:underline">B2B Commercial Invoices</a>
                <span>•</span>
                <span class="text-slate-500 font-medium">Invoice Studio</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight" x-text="type === 'credit_note' ? 'New Credit Note' : (type === 'proforma' ? 'New Proforma Invoice' : 'New B2B Tax Invoice')"></h1>
            <p class="text-sm text-slate-500 mt-0.5">Draft a compliant commercial billing document with itemized HSN codes and GST tax breakdown.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                Cancel
            </a>
            <button type="button" @click="$refs.invoiceForm.submit()" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save &amp; Generate Invoice
            </button>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form x-ref="invoiceForm" method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-6">
        @csrf

        <!-- SECTION 1: Document Settings & Client Entity -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">1. Document Type &amp; Billing Entity</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Document Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Document Type *</label>
                    <select name="type" x-model="type" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-semibold focus:ring-2 focus:ring-indigo-500">
                        <option value="tax_invoice">Tax Invoice (Standard GST B2B)</option>
                        <option value="credit_note">Credit Note (Adjustment / Refund)</option>
                        <option value="proforma">Proforma Invoice (Pre-Shipment Quotation)</option>
                    </select>
                </div>

                <!-- Invoice Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required 
                           class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Due Date</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+15 days')) }}" 
                           class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Client & Company Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Client Contact Name *</label>
                    <input type="text" name="client_name" x-model="clientName" required placeholder="e.g. Priya Sharma" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Company / Organization</label>
                    <input type="text" name="company_name" x-model="companyName" placeholder="e.g. Silk Heritage Boutique Pvt Ltd" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Client GSTIN</label>
                    <input type="text" name="client_gstin" x-model="clientGstin" placeholder="e.g. 27AABCS1429B1ZB" 
                           class="w-full text-xs px-3.5 py-2 font-mono uppercase bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Place of Supply (State) *</label>
                    <input type="text" name="place_of_supply" x-model="placeOfSupply" required placeholder="e.g. Maharashtra" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-slate-400 mt-0.5">Determines CGST/SGST vs IGST split</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Client Email</label>
                    <input type="email" name="client_email" x-model="clientEmail" placeholder="billing@client.com" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Client Phone</label>
                    <input type="text" name="client_phone" x-model="clientPhone" placeholder="+91 98765 00000" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Billing Address</label>
                    <input type="text" name="billing_address" x-model="billingAddress" placeholder="Suite 401, Nariman Point, Mumbai" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Shipping Address</label>
                    <input type="text" name="shipping_address" x-model="shippingAddress" placeholder="Same as billing or warehouse address" 
                           class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Dynamic Multi-Line Items Repeater -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">2. Line Items &amp; Taxable Values</h2>
                    <p class="text-xs text-slate-500">Add apparel garments, fabric rolls, or wholesale sets.</p>
                </div>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Line Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-2.5 px-3 w-52">Product / Description *</th>
                            <th class="py-2.5 px-3 w-28">HSN Code</th>
                            <th class="py-2.5 px-3 w-24">Qty</th>
                            <th class="py-2.5 px-3 w-20">Unit</th>
                            <th class="py-2.5 px-3 w-28">Rate (₹)</th>
                            <th class="py-2.5 px-3 w-24">Disc (₹)</th>
                            <th class="py-2.5 px-3 w-24">GST %</th>
                            <th class="py-2.5 px-3 text-right w-28">Line Total (₹)</th>
                            <th class="py-2.5 px-2 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3">
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description" required placeholder="Item description" 
                                           class="w-full text-xs px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="text" :name="`items[${index}][hsn_code]`" x-model="item.hsn_code" placeholder="621210" 
                                           class="w-full text-xs font-mono px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required min="0.01" 
                                           class="w-full text-xs font-mono px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 text-center focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="py-2.5 px-3">
                                    <select :name="`items[${index}][unit]`" x-model="item.unit" class="w-full text-xs px-1.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                                        <option value="pcs">pcs</option>
                                        <option value="set">set</option>
                                        <option value="mtr">mtr</option>
                                        <option value="box">box</option>
                                    </select>
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" required min="0" 
                                           class="w-full text-xs font-mono px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 text-right focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" :name="`items[${index}][discount_amount]`" x-model.number="item.discount_amount" min="0" 
                                           class="w-full text-xs font-mono px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 text-right focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="py-2.5 px-3">
                                    <select :name="`items[${index}][tax_rate]`" x-model.number="item.tax_rate" class="w-full text-xs px-1.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                                        <option value="0">0%</option>
                                        <option value="5">5%</option>
                                        <option value="12">12%</option>
                                        <option value="18">18%</option>
                                        <option value="28">28%</option>
                                    </select>
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                    ₹<span x-text="calculateRowTotal(item).toFixed(2)"></span>
                                </td>
                                <td class="py-2.5 px-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="p-1 text-slate-400 hover:text-rose-600 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Tax Calculation Summary Panel -->
            <div class="flex flex-col sm:flex-row justify-end pt-4 border-t border-slate-100">
                <div class="w-full sm:w-80 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Items Subtotal:</span>
                        <span class="font-mono font-semibold text-slate-900">₹<span x-text="computedSubtotal().toFixed(2)"></span></span>
                    </div>
                    <div class="flex items-center justify-between text-rose-600">
                        <span>Less Discount:</span>
                        <span class="font-mono font-semibold">-₹<span x-text="computedTotalDiscount().toFixed(2)"></span></span>
                    </div>

                    <!-- GST Split Callouts -->
                    <template x-if="isLocalState()">
                        <div class="space-y-1.5 py-1 border-y border-slate-100">
                            <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                <span>Central GST (CGST):</span>
                                <span class="font-mono font-semibold text-slate-800">₹<span x-text="(computedTax() / 2).toFixed(2)"></span></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                <span>State GST (SGST):</span>
                                <span class="font-mono font-semibold text-slate-800">₹<span x-text="(computedTax() / 2).toFixed(2)"></span></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="!isLocalState()">
                        <div class="flex items-center justify-between text-slate-500 text-[11px] py-1 border-y border-slate-100">
                            <span>Integrated GST (IGST):</span>
                            <span class="font-mono font-semibold text-slate-800">₹<span x-text="computedTax().toFixed(2)"></span></span>
                        </div>
                    </template>

                    <div class="flex items-center justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                        <span>Grand Total (INR):</span>
                        <span class="font-mono text-base font-black text-indigo-700">₹<span x-text="computedGrandTotal().toFixed(2)"></span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Terms, Bank Details & Notes -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">3. Commercial Terms &amp; Instructions</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Terms &amp; Conditions</label>
                    <textarea name="terms" rows="3" class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500">Payment due within 15 days of invoice date. Bank remittance to HDFC Bank A/C: 50200012345678, IFSC: HDFC0000123. Subject to Mumbai jurisdiction.</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Customer Notes / Dispatch Details</label>
                    <textarea name="notes" rows="3" placeholder="Special delivery instructions, purchase order cross-reference..." 
                              class="w-full text-xs px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function invoiceBuilder() {
    return {
        type: 'tax_invoice',
        clientName: '',
        companyName: '',
        clientGstin: '',
        clientEmail: '',
        clientPhone: '',
        billingAddress: '',
        shippingAddress: '',
        placeOfSupply: 'Maharashtra',
        items: [
            {
                description: 'Mulberry Silk Slip Dress - Noir / M',
                hsn_code: '621210',
                quantity: 10,
                unit: 'pcs',
                unit_price: 3499.00,
                discount_amount: 0,
                tax_rate: 18,
            }
        ],
        addItem() {
            this.items.push({
                description: '',
                hsn_code: '621210',
                quantity: 1,
                unit: 'pcs',
                unit_price: 0,
                discount_amount: 0,
                tax_rate: 18,
            });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        calculateRowTotal(item) {
            const net = Math.max(0, (item.quantity * item.unit_price) - (item.discount_amount || 0));
            const tax = net * (item.tax_rate / 100);
            return net + tax;
        },
        computedSubtotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        },
        computedTotalDiscount() {
            return this.items.reduce((sum, item) => sum + (item.discount_amount || 0), 0);
        },
        computedTax() {
            return this.items.reduce((sum, item) => {
                const net = Math.max(0, (item.quantity * item.unit_price) - (item.discount_amount || 0));
                return sum + (net * (item.tax_rate / 100));
            }, 0);
        },
        computedGrandTotal() {
            return (this.computedSubtotal() - this.computedTotalDiscount()) + this.computedTax();
        },
        isLocalState() {
            return (this.placeOfSupply || '').trim().toLowerCase() === 'maharashtra';
        }
    }
}
</script>
@endsection

