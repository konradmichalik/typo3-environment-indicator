/**
 * Environment Console Badge
 *
 * Both the text (including its "%c" directive and escaping) and the style are
 * composed server-side, so this only prints what it is handed.
 */

(function() {

    const BADGE_SELECTOR = '.environment-indicator-console';

    function printBadge() {
        const carrier = document.querySelector(BADGE_SELECTOR);

        if (!carrier) {
            return;
        }

        const text = carrier.dataset.badgeText || '';

        if (text !== '') {
            console.info(text, carrier.dataset.badgeStyle || '');
        }

        // The carrier has done its job - leave no stray element behind.
        carrier.remove();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', printBadge);
    } else {
        printBadge();
    }
})();
