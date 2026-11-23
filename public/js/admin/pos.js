/**
 * ==========================================================================
 * NEXON ADMIN PANEL — POS TERMINAL CONTROLLER
 * Reactive POS Cart, Barcode Scanning, Multi-Variant Selection & Quick Checkout
 * ==========================================================================
 */

function posTerminal() {
    var initialCatalog = window.posCatalog || [];
    if (!initialCatalog.length) {
        try {
            var raw = document.getElementById('posCatalogData')?.textContent;
            if (raw) initialCatalog = JSON.parse(raw);
        } catch (e) {
            initialCatalog = [];
        }
    }

    return {
        catalog: initialCatalog,
        activeCategory: null,
        searchQuery: '',
        cart: [],
        discount: 0,
        paymentMethod: 'cash',
        tenderedAmount: 0,
        isCheckingOut: false,
        currentTime: '',
        currentDate: '',

        // Customer state
        customer: {
            id: null,
            name: 'Walk-in Retail Guest',
            phone: '',
            email: ''
        },
        customerForm: {
            name: '',
            phone: '',
            email: '',
            city: ''
        },

        // Modals
        variantModalOpen: false,
        customerModalOpen: false,
        receiptModalOpen: false,
        recentOrdersOpen: false,
        shortcutsModalOpen: false,
        selectedProduct: null,
        completedOrder: null,

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.$nextTick(() => {
                this.$refs.barcodeInput?.focus();
            });
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-IN', { hour12: true });
            this.currentDate = now.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        get filteredCatalog() {
            let list = this.catalog;
            if (this.activeCategory !== null) {
                list = list.filter(p => p.category_id === this.activeCategory);
            }
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase().trim();
                list = list.filter(p => 
                    p.name.toLowerCase().includes(q) ||
                    (p.sku && p.sku.toLowerCase().includes(q)) ||
                    p.variants.some(v => v.sku && v.sku.toLowerCase().includes(q))
                );
            }
            return list;
        },

        totalStock(product) {
            return product.variants.reduce((acc, v) => acc + (v.stock || 0), 0);
        },

        selectProduct(product) {
            if (product.variants.length === 0) {
                alert('No variant inventory found for this product.');
                return;
            }
            if (product.variants.length === 1) {
                this.addVariantToCart(product, product.variants[0]);
            } else {
                this.selectedProduct = product;
                this.variantModalOpen = true;
            }
        },

        addVariantToCart(product, variant) {
            if (variant.stock <= 0) {
                alert('This variant is out of stock.');
                return;
            }

            const existing = this.cart.find(item => item.variant_id === variant.id);
            if (existing) {
                if (existing.qty + 1 > variant.stock) {
                    alert('Cannot exceed available stock of ' + variant.stock + ' units.');
                    return;
                }
                existing.qty++;
            } else {
                this.cart.push({
                    product_id: product.id,
                    variant_id: variant.id,
                    sku: variant.sku,
                    product_name: product.name,
                    color_name: variant.color_name,
                    size: variant.size,
                    price: variant.price,
                    stock: variant.stock,
                    image: variant.image || product.cover_image,
                    qty: 1
                });
            }

            this.variantModalOpen = false;
            this.tenderedAmount = this.total;
            this.$refs.barcodeInput?.focus();
        },

        // Barcode scanner auto add
        processBarcodeScan() {
            const code = this.searchQuery.trim();
            if (!code) return;

            let matchedProduct = null;
            let matchedVariant = null;

            for (const prod of this.catalog) {
                for (const v of prod.variants) {
                    if (v.sku && v.sku.toLowerCase() === code.toLowerCase()) {
                        matchedProduct = prod;
                        matchedVariant = v;
                        break;
                    }
                }
                if (matchedVariant) break;
            }

            if (matchedProduct && matchedVariant) {
                this.addVariantToCart(matchedProduct, matchedVariant);
                this.searchQuery = '';
            }
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            const next = item.qty + delta;
            if (next <= 0) {
                this.removeFromCart(index);
            } else if (next > item.stock) {
                alert('Cannot exceed on-hand stock (' + item.stock + ').');
            } else {
                item.qty = next;
                this.tenderedAmount = this.total;
            }
        },

        validateQty(index) {
            const item = this.cart[index];
            if (item.qty <= 0) {
                this.removeFromCart(index);
            } else if (item.qty > item.stock) {
                alert('Stock limit: ' + item.stock);
                item.qty = item.stock;
            }
            this.tenderedAmount = this.total;
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.tenderedAmount = this.total;
        },

        clearCart() {
            if (confirm('Clear all items from current cart?')) {
                this.cart = [];
                this.discount = 0;
                this.tenderedAmount = 0;
            }
        },

        get cartItemCount() {
            return this.cart.reduce((acc, item) => acc + item.qty, 0);
        },

        get subtotal() {
            return this.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
        },

        get total() {
            return Math.max(0, this.subtotal - (this.discount || 0));
        },

        get changeAmount() {
            return (this.tenderedAmount || 0) - this.total;
        },

        updateTendered(val) {
            this.tenderedAmount = val;
        },

        roundUp(amount, unit) {
            if (amount <= 0) return unit;
            return Math.ceil(amount / unit) * unit;
        },

        saveQuickCustomer() {
            var url = window.posCustomerStoreUrl || document.getElementById('posApp')?.dataset?.customerStoreUrl || '/admin/pos/customers/quick-store';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.customerForm)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.customer = data.customer;
                    this.customerModalOpen = false;
                    this.customerForm = { name: '', phone: '', email: '', city: '' };
                } else {
                    alert(data.message || 'Error creating customer');
                }
            })
            .catch(() => alert('Customer save failed. Check input data.'));
        },

        resetCustomer() {
            this.customer = {
                id: null,
                name: 'Walk-in Retail Guest',
                phone: '',
                email: ''
            };
        },

        executeCheckout() {
            if (this.cart.length === 0) return;
            if (this.paymentMethod === 'cash' && this.tenderedAmount < this.total) {
                alert('Tendered cash is less than payable total.');
                return;
            }

            this.isCheckingOut = true;

            const payload = {
                items: this.cart.map(i => ({
                    variant_id: i.variant_id,
                    qty: i.qty,
                    price: i.price
                })),
                customer_id: this.customer.id,
                customer_name: this.customer.name,
                customer_phone: this.customer.phone || '9999999999',
                customer_email: this.customer.email,
                payment_method: this.paymentMethod,
                tendered_amount: this.paymentMethod === 'cash' ? this.tenderedAmount : this.total,
                discount: this.discount || 0
            };

            var checkoutUrl = window.posCheckoutUrl || document.getElementById('posApp')?.dataset?.checkoutUrl || '/admin/pos/checkout';
            fetch(checkoutUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                this.isCheckingOut = false;
                if (data.success) {
                    this.completedOrder = data;
                    this.receiptModalOpen = true;
                    // Update local stock in catalog
                    for (const item of this.cart) {
                        for (const p of this.catalog) {
                            const v = p.variants.find(vr => vr.id === item.variant_id);
                            if (v) v.stock = Math.max(0, v.stock - item.qty);
                        }
                    }
                } else {
                    alert(data.message || 'Checkout failed.');
                }
            })
            .catch(() => {
                this.isCheckingOut = false;
                alert('POS order execution error. Please try again.');
            });
        },

        resetForNextCustomer() {
            this.receiptModalOpen = false;
            this.cart = [];
            this.discount = 0;
            this.tenderedAmount = 0;
            this.completedOrder = null;
            this.resetCustomer();
            this.$refs.barcodeInput?.focus();
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
            }
        },

        handleGlobalKeydown(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                this.$refs.barcodeInput?.focus();
            } else if (e.key === 'F4') {
                e.preventDefault();
                this.customerModalOpen = true;
            } else if (e.key === 'F8') {
                e.preventDefault();
                this.clearCart();
            } else if (e.key === 'F9' || (e.ctrlKey && e.key === 'Enter')) {
                e.preventDefault();
                if (!this.receiptModalOpen && this.cart.length > 0) {
                    this.executeCheckout();
                }
            } else if (e.key === 'Escape') {
                this.variantModalOpen = false;
                this.customerModalOpen = false;
                this.recentOrdersOpen = false;
                this.shortcutsModalOpen = false;
            } else if (e.key === 'Enter' && this.receiptModalOpen) {
                e.preventDefault();
                this.resetForNextCustomer();
            }
        }
    };
}
window.posTerminal = posTerminal;
