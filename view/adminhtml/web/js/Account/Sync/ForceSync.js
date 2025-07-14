define([
    'jquery',
    'mage/storage'
], ($, storage) => {
    'use strict';

    return function (options) {

        const sync = {
            urlForSync: options.url_for_sync,
            urlSettings: options.url_settings,
            isNeedSync: options.is_need_sync,

            isWaiterActive: false,

            process: function () {
                this.startWaiter();

                storage.get(this.urlForSync)
                        .done(this.processDone.bind(this))
                        .fail(this.processError.bind(this));
            },

            processDone: function () {
                this.stopWaiter();
                location.href = this.urlSettings;
            },

            processError: function (e) {
                console.log(e.responseText);
                this.stopWaiter();
            },

            // ----------------------------------------

            startWaiter: function () {
                if (this.isWaiterActive) {
                    return;
                }

                $("body").trigger('processStart');
                this.isWaiterActive = true;
            },

            stopWaiter: function () {
                if (!this.isWaiterActive) {
                    return;
                }

                $("body").trigger('processStop');
                this.isWaiterActive = false;
            },
        };

        try {
            if (!sync.isNeedSync) {
                return;
            }

            sync.process();
        } catch (e) {
            console.log(e);
        }
    };
});
