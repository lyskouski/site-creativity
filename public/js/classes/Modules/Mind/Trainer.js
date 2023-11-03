/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    var timer = Date.parse(new Date());
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Modules/Mind/Trainer'

        , getTime: function(el) {
            var t = timer - Date.parse(new Date());
            if (jQuery('.ui-target-time')) {
                t += 1000 * parseInt(jQuery('.ui-target-time').val(), 10);
            }

            var seconds = Math.floor((t/1000) % 60);
            if (seconds < 10) {
                seconds = '0' + seconds;
            }
            var minutes = Math.floor((t/1000/60) % 60);
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            el.html(minutes + ':' + seconds);

            if (t > 0) {
                Vaviorka.registry.trigger('View/Animate/Soft', 'animate', [self.getTime, el, [el]]);
            } else if (isNaN(t)) {
                el.parent().hide();
            }

            return el;
        }
    };

    /**
     * External functionality
     * @type {Object}
     */
    return {
        /**
         * Get object name
         * @returns {String}
         */
        getName: function() {
            return self.name;
        }

        /**
         *
         * @param {jQuery} oElem
         */
        , start: function(oElem) {
            oElem.bind('click', function(event) {
                var s = jQuery(this).data('data');
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [this, this.href.replace('.html', '.json'), JSON.parse(s.split("'").join('"')), function() {
                    jQuery('html,body').animate({scrollTop: 0}, 1000);
                    var tr = new Vaviorka.model.translate();

                    var el = jQuery('<span class="co_attention" />');
                    oElem.html(tr.tget('LB_GAME_TIME_LEFT'));
                    oElem.width(200);
                    oElem.append(self.getTime(el));

                    oElem.off('click');
                    oElem.removeClass('active');
                }]);
                Vaviorka.ui.stopPropagation(event);
                return false;
            });
        }
    };

})(window.Vaviorka.query, window.Vaviorka));