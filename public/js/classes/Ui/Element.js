/**
 * UI Design customizations
 *
 * @name Ui/Element
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    'use strict';
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        name: 'Ui/Element'

        , globalMouse: false

        , patternWidth: '.ui-select-width'

        , mouseup: function () {
            if (!self.globalMouse) {
                jQuery(document).bind('mouseup', function() {
                    jQuery('.select').removeClass('active');
                });
                self.globalMouse = true;
            }
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
        getName: function () {
            return self.name;
        }

        /**
         * selector customization
         * @param {jQuery} oElem
         */
        , select: function (oElem) {
            var o = jQuery('<div class="inline select" />'),
                width = 0,
                widthEl = null,
                val = '';

            oElem.hide();
            self.mouseup();

            oElem.children().each(function() {
                var link = jQuery('<a/>').html('<span class="indent">' + this.innerHTML + '</span>');
                if (jQuery(this).data('image')) {
                    link.prepend( '<img align="absmiddle" class="indent_neg_right" src="' + jQuery(this).data('image') + '" />');
                }
                link.data('value', this.value);
                var sort = oElem.closest('form').find('input[name="ui-sort-type"]');
                // Check if direction icon is needed
                var sortIcon = '';
                if (sort && oElem.data('direction')) {
                    sortIcon = parseInt(sort.val()) ? '&uarr;' : '&darr;';
                }
                if (!val || this.selected) {
                    val = sortIcon + link.html();
                }

                if (this.innerHTML.length > width) {
                    width = this.innerHTML.length;
                    widthEl = link;
                }
                link.on('click', function() {
                    var curr = jQuery(this).data('value');
                    var el = oElem;
                    // Check sort direction
                    if (sort && oElem.data('direction')) {
                        if (el.val() === curr) {
                            sort.val(parseInt(sort.val()) ? 0 : 1);
                        } else {
                            sort.val(0);
                        }
                    }
                    el.val(curr);
                    el.find('option').filter(function() {
                        return this.value == curr;
                    }).prop('selected', true);
                    o.find('span:eq(0)').html(this.innerHTML);
                    o.trigger('change');
                });
                link.appendTo(o);
            });

            var evnt = oElem.data('callback');
            if (evnt) {
                o.bind('change', function () {
                    var a = evnt.split(':');
                    var el = oElem;
                    o.removeClass('active');
                    Vaviorka.registry.trigger(a[0], a[1], [el]);
                });
            } else {
                o.bind('change', function () {
                    o.removeClass('active');
                });
            }

            if (oElem.data('autosubmit')) {
                o.bind('change', function () {
                    var fm = jQuery(this).closest('form');
                    if (fm) {
                        fm.find('input[type="submit"]').focus().trigger('click');
                    }
                });
            }

            // Calculate width
            var nearby = oElem.closest(self.patternWidth);
            if (nearby.length) {
                nearby.width(nearby.data('width'));
                width = nearby.data('width') - nearby.find(self.patternWidth).width();
            // Set width by pattern
            } else if (jQuery(self.patternWidth).length) {
                width = jQuery(self.patternWidth).width() - 10;
            // Get max width by element inside
            } else if (jQuery(widthEl).innerWidth()) {
                width = jQuery(widthEl).innerWidth();
            // Otherwise calculate width
            } else {
                width *= 7;
            }

            var active = jQuery('<strong style="display:block;margin-left:'+(width - 32)+'px"><span class="nowrap" style="display:block;margin-left:'+(32 - width)+'px;padding:4px 0;text-align:left;overflow:hidden;width:'+width+'px;z-index:1">'+val+'<span></strong>');
            active.bind('click', function (event) {
                var el = oElem;
                var flow = 'visible';
                if (o.hasClass('active')) {
                    o.removeClass('active');
                    flow = '';
                } else {
                    o.addClass('active');
                }
                // Clear visual state
                jQuery('.nowrap').css('overflow', '');
                el = o.closest('.nowrap');
                while (el.length) {
                    el.css('overflow', flow);
                    el = el.parent().closest('.nowrap');
                }
                Vaviorka.ui.stopPropagation(event);
                event.preventDefault();
            });

            o.prepend(active);
            o.width(width);
            o.insertAfter(oElem);
        }

        /**
         * selector customization
         * @param {jQuery} oElem
         */
        , selectEvent: function (oElem) {
            self.mouseup();
            oElem.bind('click', function (event) {
                var o = jQuery(this).parent();
                if (~o[0].className.indexOf('active')) {
                    o.removeClass('active');
                } else {
                    o.addClass('active');
                }
                Vaviorka.ui.stopPropagation(event);
                event.preventDefault();
            });
        }

        /**
         * Harmonic highlight
         * @param {jQuery} oElem
         */
        , harmonic: function(oElem) {
            oElem.children().bind('mouseover', function() {
                var el = jQuery(this);
                if (!el.hasClass('active')) {
                    el.parent().find('.active').each(function() {
                        var o = jQuery(this);
                        o.find('p').slideUp('slow', function() {
                            o.removeClass('active');
                            if (this.style) {
                                jQuery(this).removeClass('el_hidden');
                                this.style = '';
                            }
                        });
                    });
                    el.find('p').hide();
                    el.addClass('active');
                    el.find('p').slideDown('slow');
                }
            });
        }

        /**
         * Rotate element
         * @param {jQuery} oElem
         */
        , menu: function (oElem) {
            var iOffset = 0;
            var indent =  /* Math.round(oElem.parent().width() / 2) - 7 */ 13;

            // Load related styles
            Vaviorka.registry.css(self.name + '/Menu');

            if (oElem.data('rotate') === 'rf') {
                var cnt = jQuery('.el_content');
                cnt.width(cnt.width() - 3 * indent);
            }

            oElem.children().each(function (pos) {
                var o = jQuery(this),
                    h = o.innerHeight(), w = o.innerWidth(),
                    i = Math.round((w - h) / 2);

                if (oElem.data('rotate') === 'rf') {
                    if (o.hasClass('active')) {
                        o.css('margin-left', '-4px');
                    }
                } else { //if (oElem.data('rotate') === 'lf') {
                    oElem.css('left', '-' + o.width() + 'px');
                    o.css('left', (i + indent) + 'px');
                }
                o.addClass('el_rotate_' + oElem.data('rotate'));
                o.css('margin-top', (iOffset + i) + 'px');
                o.css('margin-bottom', Math.round(i / 2) + 'px');
                o.css('position', 'relative');
                iOffset = parseInt(i);
            });

            if (jQuery('.el_content').length) {
                jQuery('.el_content').css('min-height', oElem.outerHeight() + iOffset);
            }
        }
    }

})(window.Vaviorka.query, window.Vaviorka));