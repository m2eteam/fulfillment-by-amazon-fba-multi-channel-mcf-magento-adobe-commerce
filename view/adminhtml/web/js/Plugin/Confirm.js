define([
    'jquery',
    'underscore',
    'jquery/ui',
    'Magento_Ui/js/modal/confirm'
], function ($, _) {
    'use strict';

    $.widget('mage.m2e-confirm', $.mage.confirm);

    return function (config) {

        config = _.extend({
            title: AmazonMcf.translator.translate('Confirmation'),
            content: AmazonMcf.translator.translate('Are you sure?'),
            buttons: [{
                text: AmazonMcf.translator.translate('Cancel'),
                class: 'action-secondary action-dismiss',
                click: function (event) {
                    this.closeModal(event);
                }
            }, {
                text: AmazonMcf.translator.translate('Confirm'),
                class: 'action-primary action-accept',
                click: function (event) {
                    this.closeModal(event, true);
                }
            }]
        }, config);

        return $('<div></div>').html(config.content).confirm(config);
    };
});
