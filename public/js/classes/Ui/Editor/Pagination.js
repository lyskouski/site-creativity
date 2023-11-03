/*jslint browser: true */

/**
 * Page list representation
 * - drag&drop
 * - add new
 * - remove
 *
 * @name Ui/Editor/Pagination
 *
 * @since 2017-04-26
 * @author Viachaslau Lyskouski
 */
window.Vaviorka.registry.include((function (jQuery) {
    'use strict';
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Ui/Editor/Pagination'

        /**
         * Add small page representation
         *
         * @param {String} data
         * @returns {jQuery}
         */
        , pageCorner: function (data) {
            var bm = jQuery('<div class="fa fa-eye" />');

            return bm;
        }

        /**
         * Marker to create new page
         *
         * @param {String} data
         * @returns {jQuery}
         */
        , newPage: function () {
            var bm = jQuery('<div class="fa fa-plus-square" />');

            return bm;
        }
    };

    /**
     * External functionality
     * @type {Object}
     */
    return {
        /**
         * Get object name
         * @returns {String}
         */
        getName: function () {
            return self.name;
        }

        /**
         * Add page navigational buttons
         *
         * @param {jQuery} oElem
         */
        , init: function (oElem) {
            var data = JSON.parse(jQuery('#ui-editor-data').text()).content;
            oElem.width(jQuery('#ui-editor-page').offset().left - 40);
            //jQuery('#ui-editor-data').bind('change', self.revalidatePagination);
            data.forEach(function (page, i) {
                oElem.append(self.pageCorner(page, i));
            });
            if (!data.length) {
                oElem.append(self.pageCorner('', 0));
            }
            oElem.append(self.newPage());
        }
    };

})(window.Vaviorka.query));