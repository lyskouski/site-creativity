/**
 * Module access rules
 *
 * @name Modules/Person
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     *
     * @type {object}
     */
    var self = {
        oReader: null

        , check: function (oElem) {
            if (typeof window.FileReader !== 'function') {
                throw new Error('Your browser is too old for that action.');
            }
            if (!oElem) {
                throw new Error('Image file is missing.');
            } else if (!oElem.files) {
                throw new Error('This browser does not support this action.');
            } else if (!oElem.files[0]) {
                throw new Error('File was not selected');
            }
        }

        , createImage: function (iWidth, iHeight, fCallback) {
            var oImg = new Image();
            oImg.onload = function() {
                self.imageLoaded(oImg, iWidth, iHeight, fCallback);
            };
            oImg.src = self.oReader.result;
        }

        , imageLoaded: function (oImg, iWidth, iHeight, fCallback) {
            if (iWidth === 'auto') {
                iWidth = oImg.width;
            }
            if (iHeight === 'auto') {
                iHeight = (iWidth/oImg.width) * oImg.height;
            }
            var w = parseInt(iWidth)
                , h = parseInt(iHeight)
                , o = jQuery('<canvas class=hidden width="'+w+'px" height="'+h+'px" />')
                , ctx = o[0].getContext("2d");

            ctx.drawImage(oImg, 0, 0, w, h);
            fCallback(o[0].toDataURL("image/png"), w, h);
            o.remove();
            Vaviorka.registry.trigger('View/Animate/Loading', 'stop', []);
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
            return 'Request/Image';
        }

        /**
         * Load image
         * @param {jQuery} oElem
         * @param {Number} iWidth
         * @param {Number} iHeight
         * @param {Callable} fCallback
         */
        , load: function (oElem, iWidth, iHeight, fCallback) {
            Vaviorka.registry.trigger('View/Animate/Loading', 'start', []);
            self.check(oElem);
            self.oReader = new FileReader();
            self.oReader.onload = function () {
                self.createImage(iWidth, iHeight, fCallback);
            };
            self.oReader.readAsDataURL(oElem.files[0]);
        }

    };

})(window.Vaviorka.query, window.Vaviorka));