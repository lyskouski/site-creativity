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
        bActive: true

        , save: function (oElem, fCallback) {
            var aData = {};
            // Check fields by unique names
            oElem.find('[data-type]').each(function () {
                var o = jQuery(this);
                var tp = o.data('type');
                var i = 0;
                if (typeof aData[tp] === 'undefined') {
                    aData[tp] = '';
                } else {
                    while (typeof aData[tp + '#' + i] !== 'undefined') {
                        i++;
                    }
                    aData[tp + '#' + i] = '';
                    o.data('type', tp + '#' + i);
                }
            });
            // Form data request
            oElem.find('[data-type]').each(function () {
                var o = jQuery(this);
                var m = this.value ? this.value : o.data('value');
                var tp = o.data('type');
                // Special case for a grid and sked elements
                if (~['grid', 'sked'].indexOf(tp.split('#')[0])) {
                    var a = [];
                    o.children(':last').children('[data-type]').each(function () {
                        a.push(jQuery(this).data('type'));
                    });
                    m = a.join(',');
                }
                aData[tp] = m;
            });
            Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, window.location.href, {data: aData, action: 'save'}, fCallback]);
        }

        /**
         * Add events
         *
         * @param {jQuery} oElem
         */
        , events: function (oElem) {
            oElem.bind('mouseover', function (event) {
                if (self.bActive) {
                    var iWidth = oElem.innerWidth();
                    var aNavElem = jQuery('#ui-navigation').children();
                    jQuery('#ui-navigation')
                            .css('margin', '-6px -12px 4px');
                    aNavElem.each(function (i, o) {
                        var iElWidth = Math.floor(iWidth / (2 * aNavElem.length - 1) - aNavElem.length * 12);
                        jQuery(o)
                            .css('margin', 0)
                            .css('min-width', iElWidth)
                            .css('max-width', iElWidth)
                        .bind('mousedown', function () {
                            self.bActive = false;
                                setTimeout(function () {
                                    self.bActive = true;
                                }, 1000);
                        })
                        .off('click').on('click', function () {
                            var el = jQuery(this).closest('.el_panel');
                            // Edit
                            if (i === 0 && el.data('type')) {
                                Vaviorka.registry.trigger('Modules/Person', 'action', [el, el.data('type')]);
                                // Delete
                            } else if (i === 2) {
                                self.bActive = false;
                                jQuery('#ui-elememts').append(jQuery('#ui-navigation'));
                                if (el.data('type') && ~el.data('type').indexOf('og:')) {
                                    var s = jQuery('#ui-error').text();
                                    throw Error(s);
                                } else {
                                    el.slideUp('slow', function () {
                                        el.remove();
                                        self.bActive = true;
                                    });
                                }
                            }
                            return false;
                        });

                    });

                    oElem.prepend(jQuery('#ui-navigation'));
                    Vaviorka.ui.stopPropagation(event);
                }
            });

        }

        /**
         * List of operations
         * @var array
         */
        , action: {
            /**
             * image action
             *
             * @param {jQuery} oElem
             */
            image: function (oElem) {
                Vaviorka.registry.trigger('View/Popup', 'prompt', [
                    jQuery('#ui-sample-image').html().replace('{SRC}', oElem.data('value')),
                    jQuery('#ui-buttons-prompt').html(),
                    function (oSubmit, data) {
                        Vaviorka.registry.trigger('Request/Image', 'load', [
                            oSubmit.find('[type="file"]')[0],
                            oSubmit.find('[name="width"]:eq(0)').val(),
                            oSubmit.find('[name="height"]:eq(0)').val(),
                            function (value) {
                                if (oElem.find('img').length) {
                                    oElem.find('img')[0].src = value;
                                } else {
                                    oElem.contents().filter(function () {
                                        return this.nodeType == 3; //Node.TEXT_NODE
                                    }).remove();
                                    oElem.append(jQuery('<img src="' + value + '" />'));
                                }
                                oElem.data('value', value);
                                oElem.trigger('change');
                            }
                        ]);
                    }
                ]);
            }
            /**
             * Alias of `image`-action
             * @param {jQuery} oElem
             */
            , 'og:image': function (oElem) {
                self.action.image(oElem);
            }

            /**
             * Text element
             * @param {jQuery} oElem
             */
            , text: function (oElem) {
                var oTarget = oElem.children('.ui-target');
                if (oTarget.prop('tagName').toLowerCase() === 'span') {
                    var o = jQuery('<div class="ui-target indent el_border bg_highlight"/>')
                        .attr('contenteditable', 'true')
                        .attr('spellcheck', true)
                        .html(oElem.data('value'));
                    oTarget.replaceWith(o);
                    o.trigger('focus');
                    o.bind('blur', function () {
                        self.action.text(oElem);
                    });
                } else {
                    var s = oTarget.html();
                    oTarget.replaceWith(jQuery('<span class="ui-target" />').html(s));
                    oElem.data('value', s);
                }
            }
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
            return 'Modules/Person';
        }

        /**
         * Initalization
         *
         * @param {jQuery} oElem
         */
        , init: function (oElem) {
            oElem.find('.el_border_dashed').each(function (i, o) {
                // Skip for the first and second elements
                if (i === 0 || i === 2) {
                    return;
                }
                self.events(jQuery(o));
            });

            jQuery(document).bind('mouseup', function () {
                self.bActive = true;
            });
            // Save changes
            jQuery('#ui-save').bind('click', function () {
                self.save(oElem);
                return false;
            });
            jQuery('#ui-publicate').bind('click', function () {
                self.save(oElem, function () {
                    Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, window.location.href, {action: 'publicate'}]);
                });
                return false;
            });
        }

        /**
         * Operate with data for element
         *
         * @param {jQuery} oElem
         * @param {string} sType
         */
        , action: function (oElem, sType) {
            self.action[ sType.split('#')[0] ](oElem);
        }

        /**
         * Add logic block into the grid
         *
         * @param {jQuery} oElem
         */
        , add: function (oElem) {
            oElem.bind('click', function () {
                var o = jQuery(this);
                o.removeClass('bg_attention').addClass('bg_form');
                o.html(jQuery('#ui-list').html().split('ui-delay').join('ui'));
                o.children('section').each(function () {
                    jQuery(this).bind('click', function (event) {
                        // Add gaps for moving
                        jQuery('#ui-trap').clone().removeAttr('id').addClass('ui ui-trap').insertBefore(o);
                        // Add element
                        var oNew = jQuery(this).clone();
                        oNew.insertBefore(o);
                        Vaviorka.registry.final();
                        // Add basic event
                        self.events(oNew);
                        oNew.find('.el_border_dashed').each(function () {
                            self.events(jQuery(this));
                        });
                        // Inital stage
                        o.find('.button:eq(0)').trigger('click');
                        Vaviorka.ui.stopPropagation(event);
                        return false;
                    });
                });
                o.find('.button:eq(0)').unbind('click').bind('click', function (event) {
                    o.slideUp('slow', function () {
                        o.removeClass('bg_form').addClass('bg_attention');
                        o.html(jQuery(this).find('.hidden').html());
                        o.slideDown('slow');
                    });
                    Vaviorka.ui.stopPropagation(event);
                });
            });


        }

        /**
         * Revalidate trap positions
         *
         * @param {jQuery} oElem
         */
        , trap: function (oElem) {
            oElem.replaceWith(oElem.children());
            jQuery('.ui-trap').remove();
            jQuery('.el_content .el_border_dashed').each(function (i, o) {
                // Skip for the first trhee elements
                if (i < 3) {
                    return;
                }
                // Add traps
                jQuery('#ui-trap').clone().removeAttr('id').addClass('ui ui-trap').insertBefore(o);
            });
            Vaviorka.registry.final();
        }

    };

})(window.Vaviorka.query, window.Vaviorka));