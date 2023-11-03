/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        sDropName: 'ui-dragenter'
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
        getName: function () {
            return 'View/DragDrop';
        }

        /**
         * Prevent any event for internal inputs
         *
         * @param {jQuery} oElem
         */
        , prevent: function (oElem) {
            oElem.find('input,textarea').on('focus', function (event) {
                Vaviorka.ui.stopPropagation(event);
                oElem.off('dragstart');
                oElem.removeAttr('draggable');
            });
            oElem.find('input,textarea').on('blur', function (event) {
                oElem.on('dragstart');
                oElem.attr('draggable', 'true');
            });
        }

        /**
         * Find all dragable elements
         *
         * @param {jQuery} oElem
         */
        , drag: function (oElem) {
            oElem.attr('draggable', 'true');
            //oElem.addClass('resizable');
            // oElem.css('resize', 'both');
            oElem.bind('dragstart', Vaviorka.ui.stopPropagation);
            oElem.bind('dragend', function (event) {
                var oTarget = jQuery('.' + self.sDropName);
                oTarget.eq(0).append(this);
                oTarget.removeClass(self.sDropName);
                Vaviorka.ui.stopPropagation(event);
                if (oTarget.data('callback')) {
                    var a = oTarget.data('callback').split(':');
                    Vaviorka.registry.trigger(a[0], a[1], [oTarget, jQuery(this)]);
                }
                oTarget.trigger('change');
            });
        }

        /**
         * Find all target elements (that a able to catch dragable elements)
         *
         * @param {jQuery} oElem
         */
        , drop: function (oElem) {
            oElem.bind('dragenter', function (event) {
                jQuery('.' + self.sDropName).removeClass(self.sDropName);
                jQuery(this).addClass(self.sDropName);
                Vaviorka.ui.stopPropagation(event);
                return false;
            });
        }

        /**
         * Find all target elements (that a able to catch dragable elements)
         *
         * @param {jQuery} oElem
         */
        , replace: function (oElem) {
            oElem.bind('change', function () {
                jQuery(this).replaceWith(jQuery(this).children());
            });
        }
    };

})(window.Vaviorka.query, window.Vaviorka));