/**
 * Table representation
 * @note data-flex is required for a row width identification
 *
 * @name Ui/Table
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Ui/Table'
    };

    // Load related styles
    Vaviorka.registry.css(self.name);

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
         * Init table
         * @param {jQuery} oElem
         */
        , init: function(oElem) {
            var grid = {}
            //    , elWidth = []
                , elHeight = [];

            var add = function(el, j, flex) {
                grid[j] = jQuery('<div class="el_ui_table_col"/>').outerWidth(flex + '%');
            //    elWidth.push(el);
            };

            oElem.find('.el_ui_table_row').each(function(i) {
                var isHeader = 'header' === this.tagName.toLowerCase();
                var o = jQuery(this).children();
                var j = 0;
                o.each(function() {
                    var el = jQuery(this);
                    el.data('height', '.ui-height-' + i);
                    el.data('width', '.ui-width-' + j);
                    el.data('curr', i);
                    el.addClass('ui-height-' + i + ' ui-width-' + j);
                    if (isHeader) {
                        el.addClass('el_ui_table_cell_header').html(jQuery('<strong/>').html(el.html()));
                    }
                    if (i && i%2 === 0) {
                        el.addClass('el_ui_table_cell_even');
                    }

                    var flex = el.data('flex');
                    if (grid[j]) {
                        flex = grid[j].children(0).data('flex');
                    }

                    if (!grid[j]) {
                        var row = 0, rowspan = parseInt(el.data('rowspan'));
                        do {
                            var el2 = el;
                            if (rowspan) {
                                el2 = el.clone(true);
                                el2.data('height', '.ui-height-' + i);
                                el2.data('width', '.ui-width-' + j);
                                el2.removeClass('ui-height-' + i).removeClass('ui-width-' + (j-row));
                                el2.addClass('ui-height-' + i + ' ui-width-' + j);
                                if (row) {
                                    el2.data('rowspan', 0);
                                    el2.children().css('visibility', 'hidden');
                                } else {
                                    el2.addClass('bg_form ' + String(flex).split(',').map(function(v,idx) {return 'ui-width-'+ (j+idx);}).join(' '));
                                    el2.css('position', 'absolute').css('box-shadow', 'none');
                                }
                            }
                            add(el2, j, String(flex).split(',')[row]);                            
                            grid[j].append(el2);   
                            elHeight.push(el2);                         
                            row++;
                            j++;
                        } while (rowspan > row);

                    } else {
                        el.on('mouseover', function() {
                            oElem.find('.active').removeClass('active');
                            for (var k in grid) {
                                grid[k].children().eq(jQuery(this).data('curr')).addClass('active');
                            }
                        });
                        grid[j].append(el);
                        if (!j) {
                            elHeight.push(el);
                        }
                        j++;
                    }
                });
            });

            // Show results
            for (var i in grid) {                    
                oElem.append(grid[i]);
            }
            // To avoid concurrent DOM changes
            setTimeout(function() {
                // Adapt visual aspects
                //for (var i = 0; i < elWidth.length; i++) {
                //    Vaviorka.registry.trigger('View/Size', 'same', [elWidth[i]]);
                //}
                for (var i = 0; i < elHeight.length; i++) {
                    Vaviorka.registry.trigger('View/Size', 'height', [elHeight[i]]);
                }
                oElem.find('.el_ui_table_cell[data-rowspan]').each(function() {
                    var o = jQuery(this);
                    if (o.data('rowspan')) {
                        o.next().css('margin-top', o.outerHeight());
                    }
                });
                // Clear previous
                oElem.find('.el_ui_table_row').remove();
            }, 0);
        }
    };

})(window.Vaviorka, window.Vaviorka.query));
