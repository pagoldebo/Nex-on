/**
 * ==========================================================================
 * NEXON ADMIN PANEL — HRM PERFORMANCE & APPRAISAL CONTROLLER
 * ==========================================================================
 */

function appraisalBuilder(initialSelf, initialManager, initialPeer) {
    return {
        selfRating: initialSelf !== undefined ? initialSelf : 4.0,
        managerRating: initialManager !== undefined ? initialManager : 4.5,
        peerRating: initialPeer !== undefined ? initialPeer : 4.2,
        get composite() {
            var s = (parseFloat(this.selfRating) || 0) * 0.30;
            var m = (parseFloat(this.managerRating) || 0) * 0.50;
            var p = (parseFloat(this.peerRating) || 0) * 0.20;
            return Math.round((s + m + p) * 100) / 100;
        }
    };
}

window.appraisalBuilder = appraisalBuilder;
