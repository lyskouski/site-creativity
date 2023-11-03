/**
 * Change in window a content height
 *
 * @name View/Height
 */
window.Vaviorka.registry.include((function (jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        getMaxHeight: function (el) {
            return Math.max(
                el.scrollHeight,
                el.offsetHeight,
                el.clientHeight
            );
        }
        , getBodyHeight: function () {
            return Math.max(
                document.body.scrollHeight,
                document.documentElement.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.offsetHeight,
                document.body.clientHeight,
                document.documentElement.clientHeight
            );
        }
        /**
         * Check if it was targeted another element
         * @param {jQuery} o
         * @returns {jQuery}
         */
        , checkTarget: function (o) {
            var el = o;
            if (o.data('target')) {
                var s = o.data('target');
                var a = s.split(/(closest|sibling)\:(.*?)\:(.*?)/gi);
                if (a.length === 5) {
                    switch (a[1]) {
                        case 'closest':
                            el = el.closest(a[2]);
                            break;
                        case 'sibling':
                            el = el.siblings(a[2]);
                            break;
                    }
                    s = a[4];
                }
                if (s) {
                    el = el.find(s);
                }
            }
            return el;
        }

        /**
         * Operate with a visibility
         * @param {jQuery} o
         * @param {boolean} status
         * @param {string} timer
         */
        , spoiler: function (o, status, timer) {
            var html = o.html(),
                el = self.checkTarget(o);

            el.find('.el_hidden').removeClass('el_hidden');
            el.removeClass('el_hidden');

            if (status) {
                timer && el.slideUp(timer) || el.hide();
            } else {
                timer && el.slideDown(timer) || el.show();
            }
            o.html(o.data('invert')).data('invert', html);
            o.data('status', !status);
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function () {
            return 'View/Height';
        }

        /**
         * Hide or show spoiler
         *
         * @param {jQuery} oElem
         */
        , spoiler: function (oElem) {
            oElem.bind('click', function () {
                var o = jQuery(this);
                self.spoiler(o, o.data('status'), 'slow');
                if (o.data('callback')) {
                    var a = o.data('callback').split(':');
                    Vaviorka.registry.trigger(a[0], a[1], [oElem, o]);
                }
            });
        }

        , spoilerAction: function (obj, status) {
            self.spoiler(obj, !status, '');
        }

        /**
         * Hide element
         *
         * @param {jQuery} oElem
         */
        , addHiddenClass: function (oElem) {
            oElem.addClass('hidden');
        }

        /**
         * Hide or show spoiler
         *
         * @param {jQuery} oElem
         */
        , remove: function (oElem) {
            oElem.bind('click', function () {
                self.checkTarget(jQuery(this)).remove();
                return false;
            });
        }

        /**
         * Hide or show spoiler
         *
         * @param {jQuery} oElem
         */
        , removeAfterHide: function (oElem) {
            oElem.delay(3000).hide('slow', function () {
                jQuery(this).trigger('click');
            });
        }

        /**
         * Rescale article width to a fullscreen
         */
        , resize: function () {
            var iHeight = self.getBodyHeight(),
                iOtherHeight = 0;

            jQuery('body > *').each(function () {
                if (this.nodeName.toLowerCase() !== 'article') {
                    iOtherHeight += self.getMaxHeight(this);
                }
            });

            if (iHeight < window.screen.availHeight) {
                jQuery('article:eq(0)').css('min-height', jQuery(document).height() - iOtherHeight);
                jQuery('article > section.el_content:eq(0)').css('min-height', jQuery(document).height() - 18);
            }
        }

    };

})(window.Vaviorka.query));