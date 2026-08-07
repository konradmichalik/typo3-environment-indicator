/**
 * Technical Context Hint
 */

(function() {

    const CORNER_CLASS_PREFIX = 'technical-context--';
    const CORNERS = ['top-left', 'top-right', 'bottom-left', 'bottom-right'];
    const DRAGGING_CLASS = 'technical-context--dragging';
    const INTERACTIVE_CLASS = 'technical-context--interactive';

    const CORNER_STORAGE_KEY = 'typo3-environment-indicator.hint-corner';
    const CLOSED_STORAGE_KEY = 'typo3-environment-indicator.hint-closed';

    /**
     * Distance in pixels before a pointer gesture counts as a drag rather than
     * a click, so a slightly shaky click on the close button still closes.
     */
    const DRAG_THRESHOLD = 4;

    /**
     * Web Storage throws in some privacy configurations. The hint must never
     * be the reason a page breaks, so every access degrades to "no memory".
     */
    function readStorage(storage, key) {
        try {
            return storage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function writeStorage(storage, key, value) {
        try {
            storage.setItem(key, value);
        } catch (error) {
            // Not being able to remember the choice is acceptable.
        }
    }

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

    function applyCorner(contextElement, corner) {
        CORNERS.forEach(function(candidate) {
            contextElement.classList.remove(CORNER_CLASS_PREFIX + candidate);
        });
        contextElement.classList.add(CORNER_CLASS_PREFIX + corner);
    }

    /**
     * A stored corner overrides the position from the configuration: it is an
     * explicit choice by the person looking at the page.
     */
    function applyStoredCorner(contextElement) {
        const stored = readStorage(localStorage, CORNER_STORAGE_KEY);

        if (CORNERS.indexOf(stored) !== -1) {
            applyCorner(contextElement, stored);
        }
    }

    function nearestCorner(rect) {
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        return (centerY < window.innerHeight / 2 ? 'top' : 'bottom')
            + '-'
            + (centerX < window.innerWidth / 2 ? 'left' : 'right');
    }

    function initDragging(contextElement) {
        let activePointer = null;
        let startX = 0;
        let startY = 0;
        let startRect = null;
        let dragging = false;

        contextElement.addEventListener('pointerdown', function(event) {
            // Secondary mouse buttons should not start a drag.
            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }
            if (event.target.closest('.technical-context__close')) {
                return;
            }

            activePointer = event.pointerId;
            startX = event.clientX;
            startY = event.clientY;
            startRect = contextElement.getBoundingClientRect();
            dragging = false;
            contextElement.setPointerCapture(activePointer);
        });

        contextElement.addEventListener('pointermove', function(event) {
            if (activePointer === null || event.pointerId !== activePointer) {
                return;
            }

            const deltaX = event.clientX - startX;
            const deltaY = event.clientY - startY;

            if (!dragging) {
                if (Math.abs(deltaX) < DRAG_THRESHOLD && Math.abs(deltaY) < DRAG_THRESHOLD) {
                    return;
                }

                dragging = true;
                contextElement.classList.add(DRAGGING_CLASS);
                // Detach from the corner anchoring for the duration of the drag.
                contextElement.style.margin = '0';
                contextElement.style.right = 'auto';
                contextElement.style.bottom = 'auto';
            }

            contextElement.style.left = (startRect.left + deltaX) + 'px';
            contextElement.style.top = (startRect.top + deltaY) + 'px';
        });

        function finishDrag(event) {
            if (activePointer === null || event.pointerId !== activePointer) {
                return;
            }

            if (contextElement.hasPointerCapture(activePointer)) {
                contextElement.releasePointerCapture(activePointer);
            }
            activePointer = null;

            if (!dragging) {
                return;
            }
            dragging = false;

            const corner = nearestCorner(contextElement.getBoundingClientRect());

            contextElement.style.left = '';
            contextElement.style.top = '';
            contextElement.style.right = '';
            contextElement.style.bottom = '';
            contextElement.style.margin = '';
            contextElement.classList.remove(DRAGGING_CLASS);

            applyCorner(contextElement, corner);
            writeStorage(localStorage, CORNER_STORAGE_KEY, corner);
        }

        contextElement.addEventListener('pointerup', finishDrag);
        contextElement.addEventListener('pointercancel', finishDrag);
    }

    function initClosing(contextElement) {
        const closeButton = contextElement.querySelector('.technical-context__close');

        if (!closeButton) {
            return;
        }

        closeButton.addEventListener('click', function() {
            // Deliberately session-scoped: the hint is a safety marker, so it
            // comes back in a new session rather than staying gone for good.
            writeStorage(sessionStorage, CLOSED_STORAGE_KEY, '1');
            contextElement.remove();
        });
    }

    /**
     * Initialize technical context hint functionality
     */
    function initTechnicalContextHint() {
        const closedForSession = readStorage(sessionStorage, CLOSED_STORAGE_KEY) === '1';
        const contextElements = document.querySelectorAll('.technical-context');

        contextElements.forEach(function(contextElement) {
            if (closedForSession) {
                contextElement.remove();
                return;
            }

            applyDynamicStyles(contextElement);
            applyStoredCorner(contextElement);
            initDragging(contextElement);
            initClosing(contextElement);

            // Enable the snap transition only after the restored corner has
            // been painted, so restoring a position never animates.
            requestAnimationFrame(function() {
                contextElement.classList.add(INTERACTIVE_CLASS);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTechnicalContextHint);
    } else {
        initTechnicalContextHint();
    }
})();
