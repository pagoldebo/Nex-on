/**
 * ==========================================================================
 * NEXON ADMIN PANEL — INVENTORY CONTROLLER
 * Quick Stock Inline Adjustments & Inventory Log Auditing Modal
 * ==========================================================================
 */

function inventoryManager() {
    return {
        quickAddModalOpen: false,
        logsModalOpen: false,
        loadingLogs: false,
        activeSku: '',
        activeProduct: '',
        activeSize: '',
        logs: [],

        openQuickAddModal() {
            this.quickAddModalOpen = true;
        },

        stepStock(variantId, delta) {
            const input = document.getElementById(`stock-input-${variantId}`);
            let val = parseInt(input.value || 0, 10);
            val = Math.max(0, val + delta);
            input.value = val;
        },

        async saveStock(variantId) {
            const input = document.getElementById(`stock-input-${variantId}`);
            const newStock = parseInt(input.value || 0, 10);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/admin/inventory/variants/${variantId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({
                        stock: newStock,
                        type: 'manual_adjustment',
                        notes: 'Quick inline adjustment from Inventory Center'
                    })
                });

                const data = await res.json();
                if (data.success) {
                    input.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => input.classList.remove('ring-2', 'ring-emerald-500'), 1500);
                } else {
                    alert('Error updating stock: ' + (data.message || 'Unknown error'));
                }
            } catch (e) {
                alert('Network error while saving stock.');
            }
        },

        async viewLogs(variantId) {
            this.logsModalOpen = true;
            this.loadingLogs = true;
            this.logs = [];

            try {
                const res = await fetch(`/admin/inventory/variants/${variantId}/logs`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.activeSku = data.sku;
                    this.activeProduct = data.product;
                    this.activeSize = data.size;
                    this.logs = data.logs;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingLogs = false;
            }
        }
    };
}
window.inventoryManager = inventoryManager;
