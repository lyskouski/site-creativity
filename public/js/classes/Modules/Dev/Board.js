/**
 * Prototype for all classes
 *
 * @name Modules/Book/Calendar
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
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
            return 'Modules/Dev/Board';
        }

        /**
         *
         * @param {jQuery} oTarget
         * @param {jQuery} oElem
         */
        , add: function (oTarget, oElem) {
            var data = {
                action: 'move',
                pattern: oElem.data('pattern'),
                type: oTarget.data('type')
            };
            Vaviorka.registry.trigger('View/Animate/Loading', 'start', [oTarget]);
            Vaviorka.registry.trigger('Request/Pjax', 'submit', [oElem, window.location.href, data, function () {
                Vaviorka.registry.trigger('View/Animate/Loading', 'stop', []);
            }]);
        }
        
        
        /**
         * Add book to list
         * @param {jQuery} oElem
         */
        , state: function(oElem) {
            var data = {action:'subtask'};
            data[oElem[0].name] = oElem[0].value;
            Vaviorka.registry.trigger('Request/Pjax', 'submit', [oElem, window.location.href, data, function () {
                Vaviorka.registry.trigger('View/Animate/Loading', 'stop', []);
            }]);
        }

        /**
         * Add book to list
         * @param {jQuery} oElem
         */
        , push: function(oElem) {
            var form = oElem.closest('form');
            form.attr('action', oElem.val());
            form.trigger('submit');
        }
    };

})(window.Vaviorka.query, window.Vaviorka));