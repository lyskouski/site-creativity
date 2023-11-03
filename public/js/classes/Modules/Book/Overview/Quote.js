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
        name: 'Modules/Book/Overview/Quote'

        , changeQuote: function () {
            var oElem = jQuery(this);
            var trg = oElem.find('.ui-target');
            var date = jQuery('<textarea name="content" />').val(trg.text()).css('width', trg.width() - 2);
            date.insertAfter(trg);
            trg.remove();

            date.focus();

            oElem.unbind('click', self.changeQuote);
            oElem.find('.ui-submit').bind('click', function () {
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [
                    date,
                    Vaviorka.ui.getBasicUrl() + '/book/recite/import.json',
                    {
                        id: oElem.data('id'),
                        action: 'change',
                        quote: date.val(),
                        forward: window.location.href
                    }
                ]);
            });
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
        , quote: function (oElem) {
            oElem.bind('click', self.changeQuote);
        }
    };

})(window.Vaviorka, window.Vaviorka.query));