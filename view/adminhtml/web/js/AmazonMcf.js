define([
    'jquery',
    'AmazonMcf/Url',
    'AmazonMcf/Php',
    'AmazonMcf/Translator',
    'AmazonMcf/Common',
    'prototype',
    'AmazonMcf/Plugin/BlockNotice',
    'AmazonMcf/Plugin/Prototype/Event.Simulate',
    'AmazonMcf/Plugin/Fieldset',
    'AmazonMcf/Plugin/Validator',
    'AmazonMcf/General/PhpFunctions',
    'mage/loader_old'
], function (jQuery, Url, Php, Translator) {

    jQuery('body').loader();

    Ajax.Responders.register({
        onException: function (event, error) {
            console.error(error);
        }
    });

    return {
        url: Url,
        php: Php,
        translator: Translator
    };

});
