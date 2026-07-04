(function(_, $) {
    function initTumblers(context) {
        context.find('input[type="checkbox"]').not('.tumbler-processed').each(function() {
            var $checkbox = $(this);
            // Skip those explicitly marked not to be tumblers if needed
            // Also skip if it's already inside a switch
            if ($checkbox.closest('.switch').length > 0 || $checkbox.hasClass('cm-no-tumbler')) {
                return;
            }

            // Technical/system hidden checkboxes (not just in inactive tabs) usually have type hidden or display none hardcoded
            // We'll skip if it has a style of display: none specifically, but .is(':hidden') catches inactive tabs which is bad
            if ($checkbox.css('display') === 'none' && $checkbox.closest('.hidden').length === 0 && $checkbox.closest('.ui-tabs-hide').length === 0) {
                 // Skip genuinely hidden
                 return;
            }

            $checkbox.addClass('tumbler-processed');

            var isChecked = $checkbox.prop('checked');
            var switchClass = isChecked ? 'switch-on' : 'switch-off';

            // Hide original checkbox visually but keep it functional
            $checkbox.css({
                'position': 'absolute',
                'opacity': '0',
                'pointer-events': 'none'
            });

            // Create tumbler structure
            var $wrapper = $('<div class="switch switch-mini cm-switch-change list-btns has-switch"></div>');
            var $inner = $('<div class="' + switchClass + '"></div>');
            var $leftSpan = $('<span class="switch-left switch-mini">ON</span>');
            var $label = $('<span class="switch-mini">&nbsp;</span>');
            var $rightSpan = $('<span class="switch-right switch-mini">OFF</span>');

            // Wrap checkbox
            $checkbox.wrap($wrapper);
            var $newWrapper = $checkbox.parent(); // Get the actual wrapper in DOM
            $checkbox.wrap($inner);
            var $newInner = $checkbox.parent();

            $newInner.append($leftSpan).append($label).append($rightSpan);

            // Handle click on the wrapper to toggle the checkbox
            $newWrapper.on('click', function(e) {
                if (e.target !== $checkbox[0]) {
                    e.preventDefault();
                    if (!$checkbox.prop('disabled')) {
                        $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
                    }
                }
            });
        });
    }

    $(document).ready(function() {
        initTumblers($(document));

        // Listen for changes on all checkboxes to update visually
        $(document).on('change', 'input[type="checkbox"]', function() {
            var $checkbox = $(this);
            var $inner = $checkbox.closest('.switch-on, .switch-off');
            if ($inner.length > 0) {
                if ($checkbox.prop('checked')) {
                    $inner.removeClass('switch-off').addClass('switch-on');
                } else {
                    $inner.removeClass('switch-on').addClass('switch-off');
                }
            }
        });

        // Use MutationObserver to catch dynamically added checkboxes efficiently
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        if (node.nodeType === 1) { // ELEMENT_NODE
                            initTumblers($(node));
                        }
                    }
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });

})(Tygh, Tygh.$);
