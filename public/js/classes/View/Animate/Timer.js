/**
 * Timer animation
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        value: function(val) {
            return val < 10 ? '0'+val : val;
        }
        ,run: function(oElem) {
            var i = 1 + oElem.data('value'),
                h = Math.floor(i / 3600),
                m = Math.floor((i - h*3600) / 60);

            var tmp = i - h*3600 - m*60;
            oElem.html(self.value(h) + ':' + self.value(m) + ':<span class="blink">'+self.value(tmp)+'</span>');
            oElem.data('value', i);
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
            return 'View/Animate/Timer';
        }

        /**
         * Create timer
         * @param {jQuery} oElem
         */
        , init: function(oElem) {
            setInterval(function(){self.run(oElem)}, 1000);
        }
    }

})(jQuery));