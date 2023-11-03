/**
 * Crossdomain Authentication
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Request/Share'
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
         * Registry token
         * 
         * @param {type} cookie
         * @returns {undefined}
         */
        , submit: function(cookie) {
            Vaviorka.registry.trigger('View/Animate/Loading', 'start', []);
            var home = jQuery('meta[property="homepage"]').attr('content').split('/')[2];
            jQuery('link[rel="alternate"]').each(function() {
                var url = jQuery(this).attr('href').replace('.html', '.json');
                if (~url.indexOf(home)) {
                    return;
                }
                try {
                    jQuery.ajax({
                        type: "POST"
                        , async: false
                        , xhrFields: {
                            withCredentials: true
                        }
                        , crossDomain: true
                        , url: url
                        , data: {token: cookie, action: 'share'}
                        , success: function (sResponse) {
                            // do nothing
                        }
                        , error: function (XMLHttpRequest, textStatus, errorThrown) {
                            // Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                        }

                    });
                } catch (e) {
                    // Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                }
            });
            window.location = jQuery('.el_top_button')[0].href;
        }
    };

})(window.Vaviorka, window.Vaviorka.query));