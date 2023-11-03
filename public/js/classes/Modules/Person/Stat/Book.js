/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Modules/Person/Stat/Book'

        , changeDate: function () {
            var oElem = jQuery(this);
            var date = jQuery('<input class="right" type="text" placeholder="YYYY-MM-DD" />').val(oElem.data('date'));
            date.bind('change', function () {
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [date, window.location.href, {id: oElem.data('id'), action: 'date', date: this.value}]);
            });
            date.insertAfter(this);
            date.focus();

            oElem.unbind('click', self.changeDate);
        }

        , changeMark: function () {
            var oElem = jQuery(this);
            var date = jQuery('<input class="right" type="number" min="0" max="10" placeholder="0 .. 10" />').val(oElem.data('mark'));
            date.bind('change', function () {
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [date, window.location.href, {
                        id: oElem.data('id'),
                        action: 'mark',
                        mark: this.value
                }]);
            });
            date.insertAfter(this);
            date.focus();

            oElem.unbind('click', self.changeMark);
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
        getName: function() {
            return self.name;
        }

        /**
         *
         * @param {jQuery} oElem
         */
        , date: function (oElem) {
            oElem.bind('click', self.changeDate);
        }

        /**
         *
         * @param {jQuery} oElem
         */
        , mark: function (oElem) {
            oElem.bind('click', self.changeMark);
        }
    };

})(window.Vaviorka, window.Vaviorka.query));