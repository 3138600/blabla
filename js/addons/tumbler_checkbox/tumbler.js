(function(_, $) {
    function initTumblers(context) {
        var $checkboxes = context.find('input[type="checkbox"].cm-product-filters-checkbox').not('.tumbler-processed');

        // Batch read operations
        var toProcess = [];
        $checkboxes.each(function() {
            var $checkbox = $(this);
            // Skip those explicitly marked not to be tumblers if needed
            // Also skip if it's already inside a switch
            if ($checkbox.closest('.switch').length > 0 || $checkbox.hasClass('cm-no-tumbler')) {
                return;
            }

            // We avoid computed styles like $checkbox.css('display') here to prevent layout thrashing.
            // Check for explicit hidden class or type hidden
            if ($checkbox.closest('.hidden').length > 0) {
                 return;
            }

            var isChecked = $checkbox.prop('checked');
            var isDisabled = $checkbox.prop('disabled');
            var switchClass = isChecked ? 'switch-on' : 'switch-off';

            toProcess.push({
                checkbox: $checkbox,
                switchClass: switchClass,
                isDisabled: isDisabled
            });

            $checkbox.addClass('tumbler-processed');
        });

        // Batch write operations
        $.each(toProcess, function(index, data) {
            var $checkbox = data.checkbox;

            // Hide original checkbox visually but keep it functional
            $checkbox.css({
                'position': 'absolute',
                'opacity': '0',
                'pointer-events': 'none'
            });

            // Create tumbler structure
            var $wrapper = $('<div class="switch switch-mini cm-switch-change list-btns has-switch"></div>');
            var $inner = $('<div class="' + data.switchClass + '"></div>');
            var $leftSpan = $('<span class="switch-left switch-mini">ON</span>');
            var $label = $('<span class="switch-mini">&nbsp;</span>');
            var $rightSpan = $('<span class="switch-right switch-mini">OFF</span>');

            // Wrap checkbox
            $checkbox.wrap($wrapper);
            var $newWrapper = $checkbox.parent(); // Get the actual wrapper in DOM
            $checkbox.wrap($inner);
            var $newInner = $checkbox.parent();

            $newInner.append($leftSpan).append($label).append($rightSpan);

            // Handle disabled state initially
            if (data.isDisabled) {
                $newWrapper.hide();
            }

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

    // CS-Cart standard initialization method
    $.ceEvent('on', 'ce.commoninit', function(context) {
        initTumblers(context || $(document));
    });

    $(document).ready(function() {
        // Listen for changes on all checkboxes to update visually
        $(document).on('change', 'input[type="checkbox"].cm-product-filters-checkbox', function() {
            var $checkbox = $(this);
            var $wrapper = $checkbox.closest('.switch');
            var $inner = $checkbox.closest('.switch-on, .switch-off');

            if ($inner.length > 0) {
                if ($checkbox.prop('checked')) {
                    $inner.removeClass('switch-off').addClass('switch-on');
                } else {
                    $inner.removeClass('switch-on').addClass('switch-off');
                }
            }

            if ($wrapper.length > 0) {
                if ($checkbox.prop('disabled')) {
                    $wrapper.hide();
                } else {
                    $wrapper.show();
                }
            }
        });
    });

})(Tygh, Tygh.$);
