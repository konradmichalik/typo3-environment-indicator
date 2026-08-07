/**
 * Technical Context Hint
 */

(function() {

    /**
     * Apply dynamic styles from data attributes
     */
    function applyDynamicStyles(contextElement) {
        const bgColor = contextElement.dataset.bgColor;
        const textColor = contextElement.dataset.textColor;

        if (bgColor) {
            contextElement.style.setProperty('--technical-context-bg-color', bgColor);
        }
        if (textColor) {
            contextElement.style.setProperty('--technical-context-text-color', textColor);
        }
    }

    /**
     * Initialize technical context hint functionality
     */
    function initTechnicalContextHint() {
        const contextElements = document.querySelectorAll('.technical-context');

        contextElements.forEach(function(contextElement) {
            applyDynamicStyles(contextElement);

            const closeButton = contextElement.querySelector('.technical-context__close');
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    contextElement.remove();
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTechnicalContextHint);
    } else {
        initTechnicalContextHint();
    }
})();
