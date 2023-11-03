/**
 * Change ajax request with changes in href bar
 *
 * @name Request/Pjax
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        pageInput: []

        /**
         * Update URL to take JSON from request
         *
         * @param {Element} el
         * @returns {String}
         */
        , getLink: function (el) {
            var sLink = (el.dataset && el.dataset.href ? el.dataset.href : el.href).replace('#!', '').replace('.html', '');
            if (!~sLink.indexOf('.json')) {
                sLink += '.json';
            }
            return sLink;
        }

        /**
         * Check if element is visible
         *
         * @param {jQuery} el
         * @param {callable} callback
         * @returns {Function}
         */
        , scrollHandler: function (el, callback) {
            return function () {
                var rect = el[0].getBoundingClientRect();
                var visible = Boolean(
                    rect.top >= 0
                    && rect.left >= 0
                    && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
                    && rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );

                // Prevent event if it's a history loop
                if (Vaviorka.ui.history) {
                    visible = false;
                }
                // Unbind deleted elements
                if (!jQuery.contains(document, el[0])) {
                    visible = false;
                    jQuery(window).unbind('DOMContentLoaded load resize scroll');
                }

                if (visible) {
                    jQuery(window).unbind('DOMContentLoaded load resize scroll');
                    Vaviorka.registry.trigger('Request/Pjax', 'submit', [el, self.getLink(el[0]), {inf:1}, function(aResponse) {
                        var o = jQuery('#ui-next_page') || el.parent();
                        jQuery(aResponse.data[1]).insertAfter(o);
                        o.replaceWith(aResponse.data[0]);
                        setTimeout(function() { window.Vaviorka.registry.final() }, 500);
                    }]);
                }
            };
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function () {
            return 'Request/Pjax';
        }

        /**
         * Init ajax requests
         *
         * @note when comparing attributes, jQuery offers several operators for comparisions:
         *   = Is Equal
         *  != Is Not Equal
         *  ^= Starts With
         *  $= Ends With
         *  *= Contains
         *
         * @param {jQuery} oElem
         */
        , init: function (oElem) {
            var sPatternSearch = oElem.prop("tagName").toLowerCase() + '[href*="#!"]';
            oElem.off('click').on('click', function (event) {
                // Check if it was aready triggered
                if (~this.className.indexOf('active')) {
                    return false;
                }
                // Form extra data
                var aData = {};
                var s = jQuery(this).data('data');
                if (s) {
                    if (typeof s === 'string') {
                        s = JSON.parse(s.split("'").join('"'));
                    }
                    aData = s;
                // Use search for for pagination
                } else {
                    jQuery('input[name="action"]').each(function() {
                        if (this.value === 'search') {
                            var dataList = jQuery(this).closest('form').serializeArray();
                            for (var i = 0; i < dataList.length; i++) {
                                aData[dataList[i].name] = dataList[i].value;
                            }
                        }
                    });
                }

                Vaviorka.registry.trigger('Request/Pjax', 'submit', [this, self.getLink(this), aData, function() {
                    jQuery('html,body').animate({scrollTop: 0}, 1000);
                }]);
                Vaviorka.ui.stopPropagation(event);
                return false;
            });
            // bind history events
            Vaviorka.registry.trigger('View/Href', 'init', [sPatternSearch]);
        }

        , push: function (oElem) {
            oElem.bind('click', function () {
                jQuery(this).addClass('bg_accepted');
                jQuery.ajax({
                    type: "POST"
                    , url: self.getLink(this)
                    , data: JSON.parse(jQuery(this).data('data').split("'").join('"'))
                    , error: function (XMLHttpRequest, textStatus, errorThrown) {
                        Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                    }
                });
            });
        }

        /**
         * Goto page
         * @param {jQuery} oElem
         */
        , page: function (oElem) {
            var sLink = oElem.data('url');
            self.pageInput.push(oElem);
            var fGoto = function(event) {
                var i = (this.value-1);
                if (i > this.max || i < this.min) {
                    if(event.type === 'blur') {
                        return;
                    }
                    throw new Error('Out from the pagination list');
                }
                Vaviorka.registry.trigger( 'Request/Pjax', 'submit', [null, sLink + (i ? '/' + i : '') + '.json']);
            };
            oElem.bind('change', fGoto).bind('blur', fGoto).bind('focus', function() {
                jQuery(this).select();
            });
        }

        /**
         * Submit async request
         *
         * @param DOMElement oLink
         * @param string sLink
         */
        , submit: function (oLink, sLink, aData, fCallback) {
            Vaviorka.registry.trigger('View/Animate/Loading', 'start', []);

            var oRequest = jQuery.ajax({
                type: "POST"
                , url: sLink.replace('.html', '.json')
                , data: typeof aData !== 'undefined' ? aData : []
                , success: function (sResponse) {
                    Vaviorka.registry.trigger('Response/Json', 'init', [sResponse, function(aResponse) {
                        if (Vaviorka.ui.isFailed(aResponse.success, '3') && Vaviorka.ui.isFailed(aResponse.success, '2')) {
                            return;
                        }
                        if (oLink && jQuery(oLink).length) {
                            var sAttr = jQuery(oLink).prop("tagName").toLowerCase();
                            jQuery(oLink).parent().find(sAttr + '[href*="#!"]').removeClass('active');
                            jQuery(oLink).parent().find(sAttr + '[data-href*="#!"]').removeClass('active');
                            jQuery(oLink).addClass('active');
                            Vaviorka.registry.trigger('View/Href', 'set', [oLink, aResponse.title, sLink.replace('.json', '.html')]);
                        }
                        if (typeof fCallback !== 'undefined') {
                            fCallback(aResponse);
                        }
                    }]);
                }
                , error: function (XMLHttpRequest, textStatus, errorThrown) {
                    Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                }

            });
            oRequest.always(function() {
                Vaviorka.registry.final();
            });
        }

        /**
         * Autoload page
         * @param {jQuery} oElem
         */
        , infiniteScroll: function (oElem) {
            jQuery(window).bind('DOMContentLoaded load resize scroll', self.scrollHandler(oElem));
        }
    }

})(window.Vaviorka, window.Vaviorka.query));