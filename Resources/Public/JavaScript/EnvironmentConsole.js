/**
 * Environment Console Badge
 */

(function() {

    const BADGE_SELECTOR = '.environment-indicator-console';
    const BADGE_STYLE_TEMPLATE = 'background:%bg%;color:%fg%;padding:2px 6px;border-radius:3px';

    function printBadge() {
        const carrier = document.querySelector(BADGE_SELECTOR);

        if (!carrier) {
            return;
        }

        const text = carrier.dataset.text || '';

        if (text !== '') {
            const style = BADGE_STYLE_TEMPLATE
                .replace('%bg%', carrier.dataset.color || '#767676')
                .replace('%fg%', carrier.dataset.textColor || '#ffffff');

            // A percent sign in the text would be read as another console
            // format directive and swallow the styling of everything after it.
            console.info('%c' + text.replace(/%/g, '%%'), style);
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
