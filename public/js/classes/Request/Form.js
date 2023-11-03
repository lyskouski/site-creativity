/**
 * Class to operate with async (.json) forms
 *
 * @name Request/Form
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        code: false

        /**
         * Get encoded line
         *
         * @param {Object} value
         * @returns {String}
         */
        , getAES: function (value) {
            if (self.code) {
                value = window.CryptoJS.AES.encrypt(JSON.stringify(value), self.code, {format: window.CryptoJSAesJson}).toString();
            }
            return value;
        }

        /**
         * Convert URL param' string to array
         *
         * @param {String} str
         * @returns {Array}
         */
        , strToArr: function (str) {
            var res = {};
            var a = str.split('&');
            for (var i = 0; i < a.length; i++) {
                var tmp = a[i].split('=');
                res[tmp[0]] = tmp.splice(1).join('=');
            }
            return res;
        }

        /**
         * Prepare form
         *
         * @param {String} extra
         * @param {jQuery} form
         * @returns {FormData}
         */
        , prepare: function(extra, form) {
            var fd = new FormData;

            form.find('input').each(function() {
                if (this.name) {
                    if (this.type === 'file') {
                        fd.append(this.name, jQuery(this).prop('files')[0]);
                    } else if (this.type === 'radio' && this.checked || this.type !== 'radio') {
                        fd.append(this.name, this.value);
                    }
                }
            });

            var attr = self.strToArr(extra);
            for (var s in attr) {
                if (s) {
                    fd.append(s, attr[s]);
                }
            }

            return fd;
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function () {
            return 'Request/Form';
        }

        /**
         * Disable submit action
         *
         * @param {jQuery} oForm
         */
        , disable: function (oForm) {
            oForm.bind('submit', function () {
               return false;
            });
        }

        /**
         * Disable submit action
         *
         * @param {oElem} oForm
         */
        , change: function (oElem) {
            oElem.bind('change', function () {
                var o = jQuery(this);
                var a = o.data('callback').split(':');
                Vaviorka.registry.trigger(a[0], a[1], [o]);
            });
            oElem.bind('click', function (e) {
                Vaviorka.ui.stopPropagation(e);
            });
        }

        /**
         * Init async loading
         *
         * @param {jQuery} oForm
         */
        , init: function (oForm, fCallBefore) {
            var sData = '';

            oForm.bind('keyup', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    jQuery(this).trigger('submit');
                }
            });

            oForm.find('[data-extra]').bind('click', function () {
                sData += jQuery(this).data('extra') + '&';
            });
            oForm.find('[data-type="submit"]').bind('click', function () {
                Vaviorka.registry.trigger('View/Animate/Loading', 'start', [jQuery(this)]);
                oForm.trigger('submit');
                return false;
            });
            oForm.find('[data-aes]').each(function () {
                var o = jQuery(this);
                self.code = o.data('aes');
                o.removeAttr('data-aes');
            });
            oForm.find('input[type="password"]').bind('click', function () {
                this.value = '';
            });
            oForm.bind('submit', function () {
                if (typeof fCallBefore !== 'undefined') {
                    fCallBefore(oForm);
                }
                oForm.find('input[type="password"]').each(function() {
                    this.value = this.value ? self.getAES(this.value) : '';
                });
                Vaviorka.registry.trigger('View/Animate/Loading', 'start', [jQuery(":focus")]);

                var params = {
                    type: jQuery(this).attr('method')
                    , url: jQuery(this).attr('action')
                    , success: function (sResponse) {
                        var ic = oForm.data('ignore-callback');
                        if (ic) {
                            Vaviorka.registry.trigger(ic.split(':')[0], ic.split(':')[1], [oForm]);
                        } else {
                            Vaviorka.registry.trigger('Response/Json', 'init', [sResponse]);
                        }
                    }
                    , error: function (XMLHttpRequest, textStatus, errorThrown) {
                        Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                    }
                };

                // Check FormData availablitity to send files
                if (typeof FormData !== 'undefined' && oForm.data('stream')) {
                    params.data = self.prepare(sData, oForm);
                    params.processData = false;
                    params.contentType = false;
                } else {
                    params.data = sData + oForm.serialize();
                }

                var oRequest = jQuery.ajax(params);
                oRequest.always(function () {
                    Vaviorka.registry.final();
                    sData = '';
                });

                return false;
            });
        }
    };

})(window.Vaviorka, window.Vaviorka.query));