define(['jquery',], ($) => {
    'use strict';

    return option => {

        $(document).ready(() => {
            const cmdKeys = [67, 79, 78, 84, 82, 79, 76, 80, 65, 78, 69, 76];
            let cmdPressedKeys = [];

            $(document).on('keyup', e => {
                if (cmdPressedKeys.length < cmdKeys.length) {
                    if (cmdKeys[cmdPressedKeys.length] === e.keyCode) {
                        cmdPressedKeys.push(e.keyCode);
                    } else {
                        cmdPressedKeys = [];
                    }
                }

                if (cmdPressedKeys.length === cmdKeys.length) {
                    cmdPressedKeys = [];
                    window.open(option.url);
                }
            });
        });
    };
});
