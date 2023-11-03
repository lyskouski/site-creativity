/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Modules/Person/Work/Book/Series'

        , getState: function () {
            var input = jQuery('input[name="content#0"]');
            var res = [];
            if (input.val()) {
                res = input.val().split(',').filter(function(value, index, self) {
                    return self.indexOf(value) === index;
                });
            }
            return res;
        }

        , delState: function (isbn) {
            var content = self.getState();
            var key = content.indexOf(''+isbn);
            if (~key) {
                content.splice(key, 1);
            }
            return content;
        }

        , saveState: function(content) {
            var data = content.join(',');
            jQuery('input[name="content#0"]').val(data);

            Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, window.location.href, {
                action: 'clear'
            }, function() {
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, window.location.href, {
                    num: 0,
                    content: data,
                    action: 'save'
                }]);
            }]);
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
         * Change the series description in accordance with specified read list
         *
         * @param {jQuery} oElem
         */
        , setByPattern: function(oElem) {
            Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, oElem.closest('form').attr('action'), {pattern: oElem.val(), action: 'pattern'}]);
        }

        /**
         * Add book into the list
         *
         * @param {jQuery} oTarget
         * @param {jQuery} oElem
         */
        , add: function(oTarget, oElem) {
            var content = self.delState(oElem.data('isbn'));
            if (!content.length || oTarget.data('pos') > content.length) {
                content.push(oElem.data('isbn'));
            } else {
                content.splice(parseInt(oTarget.data('pos')), 0, oElem.data('isbn'));
            }
            self.saveState(content);
        }

        /**
         * Add book into the list
         *
         * @param {jQuery} oElem
         */
        , remove: function(oElem) {
            oElem.bind('click', function() {
                var content = self.delState(jQuery(this).data('isbn'));
                self.saveState(content);
            });
        }
    };

})(window.Vaviorka.query, window.Vaviorka));