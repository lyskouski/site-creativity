/**
 * Change ajax request with changes in href bar
 *
 * @name Request/Translation
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        key: null

        , errors: {
            200: 'Operation completed successfully'
            , 206: 'Taken a Partial Content'
            , 401: 'Invalid API key'
            , 402: 'Blocked API key'
            , 403: 'Exceeded the daily limit on the number of requests'
            , 404: 'Exceeded the daily limit on the amount of translated text'
            , 413: 'Exceeded the maximum text size'
            , 422: 'The text cannot be translated'
            , 501: 'The specified translation direction is not supported'
        }

        , yaGet: function (sText, sFrom, sTo, callback) {
            if ('ru' !== sFrom && 'ru' !== sTo) {
                return self.yaGet(sText, sFrom, 'ru', function (data) {
                    return self.yaGet(data.text.join(' '), 'ru', sTo, callback);
                });
            }

            jQuery.get(
                'https://translate.yandex.net/api/v1.5/tr.json/translate',
                {
                    key: self.key,
                    text: sText,
                    lang: (sFrom + '-' + sTo).replace('ua', 'uk') // hack for Ukraine language
                },
                typeof callback !== 'undefined' ? callback : function (data) {
                    if (Vaviorka.ui.isFailed(data.code)) {
                        Vaviorka.registry.trigger('Response/Error', 'show', [null, data.code+'!', self.errors[data.code]]);
                    } else {
                        Vaviorka.registry.trigger('View/Popup', 'prompt', [
                            '<div class="el_panel">' + data.text.join(' ') + '</div>',
                            '<button class="right bg_note" style="margin-top:-24px" data-type="cancel">ok</button>'
                        ]);
                    }
                }
            );
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function() {
            return 'Request/Translation';
        }


        /**
         * Mark field as changed
         *
         * @param {jQuery} oElem
         */
        , init: function(oElem) {
            oElem.bind('change', function() {
                this.name = jQuery(this).data('name');
                jQuery(this).unbind('change');
            });
        }

        /**
         * Get transaltion from yandex
         *
         * @param {jQuery} oElem
         */
        , yandex: function(oElem) {
            oElem.bind('click', function() {
                var o = jQuery(this);
                self.yaGet(o.data('content'), o.data('from'), o.data('to'));
                return false;
            });
            if (self.key === null) {
                self.key = '';
                var t = (new Date).getTime();
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [
                    null,
                    '/ru/dev/tasks/translation/yandex.json',
                    {token: t, getdata: 1},
                    function(aResponse) {
                        self.key = aResponse.params[0].token;
                    }
                ]);
            }
        }

        , google: function(oElem) {
            oElem.bind('click', function() {
                var o = jQuery(this);
                var oRequest = jQuery.ajax({
                    type: "GET"
                    , url: 'https://translate.google.com/translate_a/single'
                    , data: {
                        client: 't'
                        , sl: o.data('from')
                        , tl: o.data('to')
                        , hl: o.data('from')
                        , ie: 'UTF-8'
                        , oe: 'UTF-8'
                        , q: encodeURIComponent( o.data('content') )
                    }
                    , success: function (data) {
                        // @todo: change to a modal window
                    }
                });
            });
        }
    };
})(window.Vaviorka, window.Vaviorka.query));