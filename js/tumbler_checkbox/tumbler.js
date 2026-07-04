(function(_, $) {
    function initTumblers() {
        $('input[type="checkbox"]').not('.tumbler-processed').each(function() {
            var $checkbox = $(this);
            // Skip hidden checkboxes and those explicitly marked not to be tumblers if needed
            // Also skip if it's already inside a switch
            if ($checkbox.closest('.switch').length > 0 || $checkbox.is(':hidden') || $checkbox.hasClass('cm-no-tumbler')) {
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
            var $label = $('<label class="switch-mini">&nbsp;</label>');
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
                    $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
                }
            });
        });
    }

    $(document).ready(function() {
        initTumblers();

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

        // Use MutationObserver to catch dynamically added checkboxes
        var observer = new MutationObserver(function(mutations) {
            var shouldInit = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    shouldInit = true;
                }
            });
            if (shouldInit) {
                initTumblers();
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });

})(Tygh, Tygh.$);
