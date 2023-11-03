/**
 * Popup element
 *
 * @name View/Popup
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     *
     * @type {object}
     */
    var self = {
        oMask: null,

        mask: function(bOutFocus) {
            var o = jQuery(document), ob = jQuery(document.body);
            self.clear();
            self.oMask = jQuery('<div class="el_mask bg_mask"></div>')
                .css('width', o.outerWidth())
                .css('height', o.outerHeight());
            ob.children().each(function() {
                jQuery(this).addClass('blur');
            });
            if (bOutFocus) {
                self.oMask.bind('click', function(event) {
                    self.clear();
                });
            }
            ob.append(self.oMask);
        }

        , clear: function() {
            if (self.oMask) {
                jQuery('.blur').removeClass('blur');
                self.oMask.remove();
            }
        }
    };

    /**
     * External functionality
     *
     * @type {object}
     */
    return {
        /**
         * Get object name
         *
         * @returns {string}
         */
        getName: function () {
            return 'View/Popup';
        }

        /**
         * Show mask
         * @param {jQuery} oPopup
         */
        , mask: function(oPopup) {
            self.mask(false);
            self.oMask.append(oPopup);
        }

        /**
         * Hide mask
         */
        , unmask: function() {
            self.clear();
        }

        /**
         * Customized prompt popup
         *
         * @param {string} sTemplate
         * @param {string} sButtons
         * @param {function} fCallback
         */
        , prompt: function( sTemplate, sButtons, fCallback ) {
            var oPopup = jQuery('<form class="a_scale" />').html(sTemplate);
            oPopup.css('margin-top', parseInt(jQuery(document).scrollTop()) + 32);
            oPopup.append(jQuery('<footer/>').html(sButtons));

            oPopup.find('[data-type="cancel"]').bind('click', function(event) {
                event.preventDefault();
                Vaviorka.ui.stopPropagation(event);
                self.clear();
                return false;
            });

            oPopup.bind('submit', function(event) {
                event.preventDefault();
                Vaviorka.ui.stopPropagation(event);
                fCallback( oPopup );
                self.clear();
                return false;
            });
            oPopup.bind('click', Vaviorka.ui.stopPropagation);
            self.mask(true);
            self.oMask.append( oPopup );
        }

        /**
         * Convert all title into popups
         */
        , title: function() {
            var timer = null;

            jQuery('[title]').each(function() {
                var o = jQuery(this);
                o.data('title', this.title);
                o.removeAttr('title');
                o.bind('mouseover', function (event) {
                    var cursor = Vaviorka.ui.getCursor(event);
                    var o = jQuery(this);
                    var width = jQuery(window).width();
                    var offset = o.offset();
                    var x = cursor.x - offset.left;
                    var y = cursor.y - offset.top;

                    jQuery('#ui-popup').remove();
                    var o = jQuery(
                        '<div id="ui-popup">'
                            + '<div class="el_notion" style="position:absolute;height:auto;margin:0;padding: 2px 6px;top:'
                            + (offset.top + o.height() + 24) + 'px;'
                            + (offset.left < width/2 ? 'left:'+ (offset.left + o.width()) : 'right:'+ (width - offset.left) ) + 'px">'
                                + o.data('title')
                            + '</div>'
                        +'</div>'
                    );
                    jQuery('body').append(o);
                    clearTimeout(timer);
                    timer = setTimeout(function(){
                        jQuery('#ui-popup').remove();
                    }, 2000);
                }).bind('mouseout', function () {
                    clearTimeout(timer);
                    jQuery('#ui-popup').remove();
                });
            });
        }
    };
})(window.Vaviorka.query, window.Vaviorka));