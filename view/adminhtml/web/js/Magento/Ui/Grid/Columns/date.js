define([
    'Magento_Ui/js/grid/columns/date',
    'mageUtils',
    'moment',
], function (date, utils, moment) {
    'use strict';

    return date.extend({
        getLabel: function (value, format) {
            var date;

            if (this.storeLocale !== undefined) {
                moment.locale(this.storeLocale, utils.extend({}, this.calendarConfig));
            }

            date = moment.utc(this._super());

            if (!_.isUndefined(this.timezone) && moment.tz.zone(this.timezone) !== null) {
                date = date.tz(this.timezone);
            }

            date = date.isValid() && value[this.index] ?
                    date.format(format || this.dateFormat) :
                    'N/A';

            return date;
        }
    });
});
