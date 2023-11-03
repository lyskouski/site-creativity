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
        name: 'Modules/Mind/Trainer/Gibberish'

        , result: []

        , speed: jQuery('.ui-target-level').text() / 10000

        , changeBgColor: function (oElem, opacity) {
            opacity -= self.speed;
            if (opacity < 0) {
                opacity = 0;
            }
            oElem.css('background', 'rgba(154, 192, 204,' + opacity + ')');
            if (opacity && self.speed) {
                Vaviorka.registry.trigger('View/Animate/Soft', 'animate', [self.changeBgColor, oElem, [oElem, opacity]]);
            }
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
        , push: function(oElem) {
            oElem.bind('click', function(event) {
                self.changeBgColor(oElem, 1);
                self.result.push(jQuery(this).data('id'));
                jQuery('.ui-target-element').val(self.result.join('|'));
            });
        }
    };

})(window.Vaviorka.query, window.Vaviorka));