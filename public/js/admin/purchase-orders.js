/**
 * ==========================================================================
 * NEXON ADMIN PANEL — PURCHASE ORDERS BUILDER
 * Reactive PO Item Assembly, Cost Calculation & Variant Picker
 * ==========================================================================
 */

function poBuilder() {
    return {
        items: [],

        addItem(variantId, name, sku, qty, cost) {
            if (this.items.some(i => i.variant_id === variantId)) {
                alert('Item is already in this PO.');
                return;
            }
            this.items.push({
                variant_id: variantId,
                name: name,
                sku: sku,
                ordered_qty: qty || 20,
                unit_cost: cost || 500
            });
        },

        addFromPicker() {
            const select = this.$refs.variantPicker;
            const opt = select.options[select.selectedIndex];
            if (!opt.value) return;

            this.addItem(
                parseInt(opt.value),
                opt.getAttribute('data-name'),
                opt.getAttribute('data-sku'),
                parseInt(opt.getAttribute('data-qty')),
                parseInt(opt.getAttribute('data-cost'))
            );

            select.value = '';
        },

        removeItem(idx) {
            this.items.splice(idx, 1);
        },

        get totalAmount() {
            return this.items.reduce((acc, i) => acc + (i.ordered_qty * i.unit_cost), 0);
        }
    };
}
window.poBuilder = poBuilder;
