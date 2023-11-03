/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {

    };

    /**
     * External functionality
     * @type object
     */
    return {
        /**
         * Get object name
         * @returns string
         */
        getName: function() {
            return 'View/Graphics/Svg';
        }

        /**
         *
         * @param {jQuery} oElem
         */
        , initProposition: function(oElem) {

        }
    };

})(window.Vaviorka.query));