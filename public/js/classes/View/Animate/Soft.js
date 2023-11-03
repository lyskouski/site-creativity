/**
 * Soft animation in the browser (CPU/GPU/RAM health)
 *
 * @name View/Animate/Soft
 */
window.Vaviorka.registry.include((function () {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        /**
         * Soft animation
         * @note check availability of an animation functionality in a browser
         *
         * @param function callback
         * @param object<DOMElement> element
         */
        requestAnimFrame: function (callback, element) {
            var event;
            switch (true) {
                case typeof window.requestAnimationFrame !== 'undefined':
                    event = window.requestAnimationFrame;
                    break;

                case typeof window.webkitRequestAnimationFrame !== 'undefined':
                    event = window.webkitRequestAnimationFrame;
                    break;

                case typeof window.mozRequestAnimationFrame !== 'undefined':
                    event = window.mozRequestAnimationFrame;
                    break;

                case typeof window.oRequestAnimationFrame !== 'undefined':
                    event = window.oRequestAnimationFrame;
                    break;

                case typeof window.msRequestAnimationFrame !== 'undefined':
                    event = window.msRequestAnimationFrame;
                    break;

                default:
                    event = function (callback, element) {
                        window.setTimeout(callback, 1000 / 60);
                    };
            }
            return event(callback, element);
        }
    };

    /**
     * External functionality
     * @type {Object}
     */
    return {
        getName: function () {
            return 'View/Animate/Soft';
        }
        /**
         * Soft animation
         *
         * @param {Function} callback
         * @param {DOMElement} element
         * @param {Array} params
         */
        , animate: function (callback, element, params) {
            self.requestAnimFrame(function () {
                callback.apply(this, params);
            }, element);
        }
    };

})(  ));