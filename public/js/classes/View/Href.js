/**
 * Change HREF bar
 *
 * @name View/Href
 */
window.Vaviorka.registry.include((function (Vaviorka, history, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        /**
         * Actualy used history index
         * @note to avoid multiple triggering from a history state
         *
         * @var int
         */
        iHistoryIndex: -1

        /**
         * If actual link is missing
         * @note to avoid multiple triggering from a history state
         *
         * @var string
         */
        , sHistoryUrl: ''

        /**
         * Pattern search option
         * @var string
         */
        , sPatternSearch: ''

        /**
         * History popstate event
         */
        , popstate: function () {
            Vaviorka.ui.history = true;
            jQuery('article').each(function(i,o) {
                i && jQuery(o).remove();
            });
            jQuery('html,body').scrollTop(0);

            if (history.state !== null) {
                var link = null,
                    url = self.sHistoryUrl;

                if (history.state.index === -1 && self.sHistoryUrl !== history.state.origin) {
                    url = history.state.origin;
                    self.iHistoryIndex = -1;

                } else if (self.iHistoryIndex !== history.state.index) {
                    self.iHistoryIndex = history.state.index;
                    url = history.state.link;
                    link = jQuery(history.state.pattern + ':eq('+history.state.index + ')');

                } else {
                    url = history.state.link;
                }

                if (self.sHistoryUrl !== url) {
                    self.sHistoryUrl = url;
                    Vaviorka.registry.trigger('Request/Pjax', 'submit', [link, self.sHistoryUrl, {}, function () {
                        jQuery('html,body').scrollTop(0);
                    }]);
                }

            } else {
                var url = window.location.href;
                if (url !== history.location.href) {
                    url = history.location.href;
                }

                jQuery.ajax({type: 'GET', url: url
                    , success: function (newContent) {
                        document.open();
                        document.write(newContent);
                        jQuery('html,body').scrollTop(0);
                        window.Vaviorka.ui.history = false;
                        document.close();
                    }
                    , error: function (XMLHttpRequest, textStatus, errorThrown) {
                        Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                    }

                });
            }
        }

        /**
         * Click on `oElem` will trigger a click-event for `oTarget`
         *
         * @param {jQuery} oTarget
         * @param {jQuery} oElem
         */
        , bindClick: function(oTarget, oElem) {
            if (typeof oTarget[0] === 'undefined') {
                return;
            }
            oElem.bind('click', function() {
                oTarget[0].click();
            });
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function() {
            return 'View/Href';
        }

        /**
         * Add event listner
         *
         * @param {string} sPatternSearch
         */
        , init: function (sPatternSearch) {
            self.sPatternSearch = sPatternSearch;
            jQuery(window).unbind('popstate', self.popstate).bind('popstate', self.popstate);
        }

        /**
         * Disable default action
         *
         * @param {jQuery} oElem
         */
        , preventDefault: function (oElem) {
            oElem.bind('click', function (event) {
                event.preventDefault();
            });
        }

        /**
         * Disable default action
         *
         * @param {jQuery} oElem
         */
        , stopPropagation: function (oElem) {
            oElem.bind('click', function (event) {
                Vaviorka.ui.stopPropagation(event);
            });
        }

        /**
         * Add event "onclick" for a target object to trigger current object
         * @param {jQuery} oElem
         * @param {Boolean} bInside
         */
        , target: function (oElem, bInside) {
            var s = oElem.data('target');
            if (typeof bInside === 'undefined' || bInside) {
                self.bindClick(oElem.find(s), oElem);
            } else {
                self.bindClick(oElem, oElem.closest(s));
            }

        }

        /**
         * Add history event
         *
         * @param {DOMElement} oLink
         * @param {String} sTitle
         * @param {String} sHref
         */
        , set: function (oLink, sTitle, sHref) {
            // Check if it was a history event
            if (Vaviorka.ui.history) {
                Vaviorka.ui.history = false;
                // Add new history line
            } else if (document.location.href !== sHref) {
                var iIndex = jQuery(self.sPatternSearch).index(oLink);
                history.pushState({link: sHref.replace('.html', '.json'), pattern: self.sPatternSearch, index: iIndex, origin: sHref}, sTitle, sHref);
            }
        }
    };

})(window.Vaviorka, history, window.Vaviorka.query));