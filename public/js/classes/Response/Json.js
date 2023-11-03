/**
 * Process response data
 *
 * @name Response/Json
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        aParams: []

        /**
         * Process response json-data
         *
         * @param array aResponse
         */
        , process: function (aResponse) {
            var status = aResponse.success;
            if (status == 206) {
                return true;
            }

            if (Vaviorka.ui.isFailed(status, '3') && Vaviorka.ui.isFailed(status, '2')) {
                Vaviorka.registry.trigger('Response/Error', 'show', [null, aResponse.success, aResponse.message]);
            }
            for (var s in aResponse) {
                if (!self.exist(aResponse, s)) {
                    continue;
                }
                switch (s) {
                    case 'script':
                        var a = aResponse[s];
                        for (var i = 0; i < a.length; i++) {
                            if (!jQuery.find('script[src="'+a[i]+'"]').length) {
                                Vaviorka.registry.init(a[i]);
                            }
                        }
                        break;

                    case 'style':
                        jQuery('#ui-page-style').remove();
                        jQuery('<style id="ui-page-style" type="text/css">').html(aResponse[s]).appendTo(document.body);
                        break;

                    case 'data':
                        self.updateContent(aResponse[s]);
                        break;

                    case 'runsrc':
                        for (var i = 0; i < aResponse[s].length; i++) {
                            (new Function(aResponse[s]))();
                        }
                        break;

                    case 'title':
                        jQuery('title').html(aResponse[s]);
                        break;

                    case 'params':
                        for (var i = 0; i < aResponse[s].length; i++) {
                            self.aParams.push(aResponse[s]);
                        }
                        break;
                }
            }
        }

        , exist: function (aResponse, sParameter) {
            var bResult = false;
            switch (Object.prototype.toString.call(aResponse[ sParameter ])) {
                case '[object Undefined]':
                    bResult = false;
                    break;

                case '[object Array]':
                case '[object String]':
                    bResult = aResponse[ sParameter ].length > 0;
                    break;

                case '[object Object]':
                    for (var i in aResponse[ sParameter ]) {
                        bResult = true;
                        break;
                    }
                    break;

                default:
                    bResult = Boolean(aResponse[ sParameter ]);
            }
            return bResult;
        }

        , trim: function (str) {
            return str.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
        }

        , articleContent: function (mData) {
            var oContent = jQuery('article');
            var sData = '';
            if (typeof mData === 'object') {
                for (var i = 0; i < mData.length; i++) {
                    sData += self.trim(mData[i]).split('\\n').join("\n");
                }
            } else {
                sData = mData;
            }

            var o = jQuery(' ' + sData);
            // Update custom content
            if (o.find('search').length) {
                o.find('search').each(function () {
                    jQuery('#' + this.id).html(jQuery(this).html());
                });
                // Update article
            } else {
                jQuery('body > .el_crumbs').remove();
                jQuery('.article').remove();
                if (typeof mData === 'object') {
                    o.insertAfter(oContent);
                    oContent.remove();
                } else {
                    oContent.html(mData);
                }
            }
            return sData;
        }

        /**
         * Change the page content (article-section) if needed
         *
         * @param array|string mData
         */
        , updateContent: function (mData) {
            // Check navigation state
            var aNavState = {};
            jQuery('.el_catalog').each(function () {
                var a = {};
                jQuery(this).find('[data-status]').each(function (i, o) {
                    a[i] = jQuery(o).data('status');
                });
                aNavState[this.id] = a;
            });
            // Update content
            var sData = self.articleContent(mData);
            // Update navigation state
            jQuery('.el_catalog').each(function () {
                if (typeof aNavState[this.id] !== 'undefined') {
                    var a = aNavState[this.id];
                    jQuery(this).find('[data-status]').each(function (i, o) {
                        var el = jQuery(o);
                        if (!a[i] && el.data('status') != a[i]) {
                            Vaviorka.registry.trigger('View/Height', 'spoilerAction', [jQuery(o), a[i]]);
                        }
                    });
                }
            });
            // check if some elements require autofocus
            if (~sData.indexOf('autofocus="true"')) {
                jQuery('[autofocus="true"]').focus();
            }
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function () {
            return 'Response/Json';
        }

        /**
         * Get latest params from request
         *
         * @returns array
         */
        , getParams: function () {
            return self.aParams.pop();
        }

        /**
         * Process response and operate with its data
         *
         * @param {String} sResponse
         * @param {Function} fCallback
         */
        , init: function (sResponse, fCallback) {
            var aResponse = {success: 500, message: 'Internal application error'};
            try {
                aResponse = JSON.parse(sResponse);
            } catch (oError) {
                aResponse = {success: oError.code, message: oError.name};
            }

            self.process(aResponse);
            Vaviorka.registry.final();

            if (typeof fCallback === 'function') {
                fCallback(aResponse);
            }
        }
    };

})(window.Vaviorka, window.Vaviorka.query));