/**
 * Response error popup
 *
 * @name Response/Error
 */
window.Vaviorka.registry.include((function ( Vaviorka ) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {

    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function() {
            return 'Response/Error';
        }

        /**
         * Show error that was taken
         *
         * @param object XMLHttpRequest
         * @param integer textStatus
         * @param string errorThrown
         */
        , show: function (XMLHttpRequest, textStatus, errorThrown) {
            if (textStatus == 206) {
                return;
            }
            var b = Vaviorka.ui.isFailed(textStatus, '3') && Vaviorka.ui.isFailed(textStatus, '2');
            Vaviorka.registry.trigger('View/Popup', 'prompt', [
                '<div class="'+(b ? 'bg_attention' : 'bg_normal')+' el_error el_panel"><span>&cross;</span>[' + textStatus + '] ' + errorThrown + '</div>',
                '<button class="right bg_note" style="margin-top:-24px" data-type="cancel">ok</button>'
            ]);
            // @fixme: delete afterwards
            console.log('request: ', XMLHttpRequest);
            console.log('error:', errorThrown);
            console.log('code:', textStatus);
            console.log('---');
        }
    };

})( window.Vaviorka ));
