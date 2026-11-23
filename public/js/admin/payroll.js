/**
 * ==========================================================================
 * NEXON ADMIN PANEL — HRM PAYROLL WIZARD CONTROLLER
 * Reactive Attendance, Wages, Statutory Deductions (PF, ESI, TDS) & Summary
 * ==========================================================================
 */

function payrollWizard(initialItems, initialMonth) {
    var items = Array.isArray(initialItems) ? initialItems : [];
    if (!items.length) {
        try {
            var raw = document.getElementById('payrollStaffData')?.textContent;
            if (raw) items = JSON.parse(raw);
        } catch (e) {
            items = [];
        }
    }

    return {
        step: 1,
        periodMonth: initialMonth || '',
        items: items,

        gross(i) {
            var it = this.items[i];
            if (!it) return 0;
            return (parseFloat(it.basic_salary) || 0) + 
                   (parseFloat(it.hra) || 0) + 
                   (parseFloat(it.transport_allowance) || 0) + 
                   (parseFloat(it.performance_bonus) || 0);
        },

        pf(i) {
            var it = this.items[i];
            if (!it) return 0;
            return Math.round((parseFloat(it.basic_salary) || 0) * 0.12 * 100) / 100;
        },

        esi(i) {
            var g = this.gross(i);
            return (g <= 21000 && g > 0) ? Math.round(g * 0.0075 * 100) / 100 : 0;
        },

        totalDeductions(i) {
            var it = this.items[i];
            if (!it) return 0;
            return this.pf(i) + this.esi(i) + (parseFloat(it.tds_tax) || 0);
        },

        net(i) {
            return Math.max(0, this.gross(i) - this.totalDeductions(i));
        },

        get totalNetAll() {
            var self = this;
            return this.items.reduce(function(acc, _, idx) {
                return acc + self.net(idx);
            }, 0);
        },

        get totalGrossAll() {
            var self = this;
            return this.items.reduce(function(acc, _, idx) {
                return acc + self.gross(idx);
            }, 0);
        },

        get totalDeductionsAll() {
            var self = this;
            return this.items.reduce(function(acc, _, idx) {
                return acc + self.totalDeductions(idx);
            }, 0);
        }
    };
}

window.payrollWizard = payrollWizard;
