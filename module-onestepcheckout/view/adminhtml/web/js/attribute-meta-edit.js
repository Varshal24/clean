define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('mage.awOscAttributeMetaEdit', {
        options: {
            textInputs: 'input[type=text]',
            checkboxes: 'input[type=checkbox]',
            metaFieldAttribute: 'data-meta-field-name',
            fieldLinkage: {
                label: [
                    {visible: 'disabled'},
                    {required: 'data-required'}
                ],
                required: [
                    {'vat-validation': 'disable-and-check'}
                ]
            },
            fieldsToTriggerOnInit: [
                'vat-validation'
            ]
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._bind();
            this._triggerFields();
        },

        /**
         * Event binding
         */
        _bind: function () {
            var self = this,
                handlers = {};

            handlers['change ' + this.options.textInputs] = 'onTextInputChange';
            handlers['change ' + this.options.checkboxes] = 'onCheckboxChange';
            _.each(this.options.fieldLinkage, function (config, metaField) {
                _.each(config, function (configItem) {
                    _.each(configItem, function (prop, masterMetaField) {
                        var eventDeclare = 'change [' + self.options.metaFieldAttribute + '=' + masterMetaField + ']';

                        handlers[eventDeclare] = function (event) {
                            self.handleLinkage(
                                $(event.currentTarget),
                                self.element.find('[' + self.options.metaFieldAttribute + '=' + metaField + ']'),
                                prop
                            );
                        };
                    });
                });
            });

            this._on(handlers);
        },

        /**
         * Trigger fields to initialize state
         *
         * @private
         */
        _triggerFields: function () {
            var elementToTrigger;
            _.each(this.options.fieldsToTriggerOnInit, function (field) {
                elementToTrigger = this.element.find('[' + this.options.metaFieldAttribute + '=' + field + ']');
                if (elementToTrigger.length) {
                    elementToTrigger.trigger('change');
                }
            }, this);
        },

        /**
         * On text input value change event handler
         *
         * @param {Object} event
         */
        onTextInputChange: function (event) {
            var element = $(event.currentTarget);

            this._updateValue(element.attr('id'), element.val());
        },

        /**
         * On checkbox checked state change event handler
         *
         * @param {Object} event
         */
        onCheckboxChange: function (event) {
            var element = $(event.currentTarget);

            this._updateValue(
                element.attr('id'),
                element[0].checked ? '1' : '0'
            );
        },

        /**
         * Handle linkage between inputs
         *
         * @param {Object} master
         * @param {Object} slave
         * @param {string} prop
         */
        handleLinkage: function (master, slave, prop) {
            var value,
                addtitionalElement;

            if (master.is(this.options.checkboxes)) {
                value = master[0].checked;
                if (prop == 'disabled') {
                    if (!value) {
                        slave.prop(prop, 'disabled');
                    } else {
                        slave.prop('disabled', false);
                    }
                } else if (prop == 'data-required') {
                    slave.attr(prop, value ? '1': '0');
                    slave[0].labels[0].toggleClassName('required-mark');
                } else if (prop == 'disable-and-check') {
                    addtitionalElement = master.closest('.col-attributes')
                        .find('[' + this.options.metaFieldAttribute + '=label]');

                    if (value) {
                        slave.prop('disabled', 'disabled');
                        slave.prop('checked', 'checked');
                        this._updateValue(slave.attr('id'), slave.val());
                        addtitionalElement.attr('data-required', 1);
                        addtitionalElement[0].labels[0].addClassName('required-mark');
                    } else {
                        slave.prop('disabled', false);
                    }
                }
            }
        },

        /**
         * Update value of hidden related field
         *
         * @param {string} id
         * @param {string} value
         */
        _updateValue: function (id, value) {
            var hidden = this.element.find('[data-value-input-id=' + id + ']');

            hidden.val(value);
        }
    });

    return $.mage.awOscAttributeMetaEdit;
});
