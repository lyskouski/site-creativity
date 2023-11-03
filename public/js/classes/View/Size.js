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
        /**
         * Same width
         * @param {jQuery} oElem
         * @param {String} sTarget
         */
        width: function (oElem, sTarget) {
            var w = 0
                , target = jQuery(sTarget);
            // Find max width
            target.each(function () {
                var o = jQuery(this);
                if (o.width() > w && !o.data('rowspan')) {
                    w = o.width();
                }
                o.width(0);
            });
            // Update width
            target.each(function () {
                var o = jQuery(this);
                if (o.data('rowspan')) {
                    o.width(w + o.width());
                } else {
                    o.width(w);
                }
            });
        }

        /**
         * Same height
         * @param {jQuery} oElem
         * @param {String} sTarget
         */
        , height: function (oElem, sTarget) {
            var h = 0
                , target = jQuery(sTarget);
            // Find max height
            target.each(function () {
                var o = jQuery(this);
                if (o.height() > h && !o.data('colspan')) {
                    h = o.height();
                }
            });
            // Update height
            target.each(function () {
                var o = jQuery(this);
                if (o.data('colspan')) {
                    o.height(h + o.height());
                } else {
                    o.height(h);
                }
                if (o.data('rowspan')) {
                    o.siblings().eq(0).css('margin-top', o.outerHeight());
                }
            });
        }
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
            return 'View/Size';
        }

        /**
         * Update height and width
         *
         * @param {jQuery} oElem
         */
        , same: function(oElem) {
            self.width(oElem, oElem.data('width'));
            self.height(oElem, oElem.data('height'));
        }

        /**
         * Update height
         *
         * @param {jQuery} oElem
         */
        , height: function(oElem) {
            self.height(oElem, oElem.data('height'));
        }
    };

})(window.Vaviorka.query));