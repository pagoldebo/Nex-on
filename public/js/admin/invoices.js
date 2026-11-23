/**
 * ==========================================================================
 * NEXON ADMIN PANEL — INVOICES BUILDER
 * Reactive GST / Tax Invoice Calculation, Dynamic Item Rows & State Tax Engine
 * ==========================================================================
 */

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
    };
}
window.invoiceBuilder = invoiceBuilder;
