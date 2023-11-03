/*jslint browser: true */

/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery) {
    'use strict';
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Ui/Editor/Panel/Html'
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
         * Editor buttons
         *
         * @param {jQuery} oElem
         */
        , init: function (oElem) {
            console.log('todo: editor panel');
        }
    };

})(window.Vaviorka.query));