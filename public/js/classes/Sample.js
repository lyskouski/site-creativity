/*jslint browser: true */

/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function ( /* input values */ ) {
    'use strict';
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Folder/Name'
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
    };

})( /* input values */ ));