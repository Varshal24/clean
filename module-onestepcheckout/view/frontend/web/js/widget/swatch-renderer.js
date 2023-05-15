define([
    'jquery',
    'Aheadworks_OneStepCheckout/js/action/update-quote-item-options',
    'Magento_Swatches/js/swatch-renderer'
], function ($, updateQuoteItemOptionsAction) {

    $.widget('mage.awOscSwatchRenderer', $.mage.SwatchRenderer, {

        /**
         * @inheritdoc
         */
        _init: function () {
            this._super();
            this._setDefaultValues();
        },

        /**
         * Update swatch option
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            if ($this.hasClass('selected') || $this.hasClass('disabled')) {
                return;
            }

            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.data('attribute-id'),
                attributeCode = this._getAttributeCodeById(attributeId),
                optionId = $this.data('option-id'),
                params = {};

                $parent.attr('data-option-selected', $this.data('option-id')).find('.selected').removeClass('selected');
                $label.text($this.data('option-label'));
                $this.addClass('selected');

                params[attributeCode] = optionId;
                updateQuoteItemOptionsAction(this.options.itemId, params);
        },

        /**
         * Update select option
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnChange: function ($this, $widget) {
            if ($this.hasClass('selected') || $this.hasClass('disabled')) {
                return;
            }

            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                attributeId = $parent.data('attribute-id'),
                attributeCode = this._getAttributeCodeById(attributeId),
                optionId = $this.val(),
                params = {};

            $parent.attr('data-option-selected', optionId);
            params[attributeCode] = optionId;
            updateQuoteItemOptionsAction(this.options.itemId, params);

            $widget._Rebuild();
        },

        /**
         * Set default values for widget
         *
         * @private
         */
        _setDefaultValues: function () {
            $.each(this.options.defaultValues, $.proxy(function (attributeId, optionId) {
                var $parent = this.element.find('.' + this.options.classes.attributeClass + '[data-attribute-id="' + attributeId + '"]'),
                    $elem = $parent.find('[data-option-id="' + optionId + '"]');

                if ($parent.find('.' + this.options.classes.selectClass).length) {
                    $parent.find('option[data-option-id="0"]').remove();
                    $elem.prop('selected', true);
                }

                $elem.addClass("selected");
                $parent.attr('data-option-selected', optionId);
            }, this));

            this._Rebuild();
        },
    });

    return $.mage.awOscSwatchRenderer;
});