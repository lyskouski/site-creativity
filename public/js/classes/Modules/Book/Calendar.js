/**
 * Prototype for all classes
 *
 * @name Modules/Book/Calendar
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    // Required modules
    Vaviorka.model.requireOne('basic');

    /**
     * Internal functionality
     * @type object
     */
    var self = {
        isPercent: false
        , cookiePercent: 'ui-percent'

        , isPlainList: false
        , cookieList: 'ui-list'

        , savePages: function (isbn, page, rng) {
            var data = {
                action: 'page',
                isbn: isbn,
                page: self.isPercent ? Math.round(rng.data('max') * page / 100) : page
            };
            var url = window.location.href;
            if (rng.data('url')) {
                url = rng.data('url');
            }
            if (rng.data('last') != data.page) {
                var iniTitle = document.title;
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, url, data, function() {
                    document.title = iniTitle;
                }]);
                rng.data('last', data.page);
            }
        }

        , radioSwitch: function(oElem, stateName, cookieName, callback) {
            var cookie = new Vaviorka.model.basic();

            var oLabel = oElem.find('label');
            var oInput = oElem.find('input');

            oInput.bind('change', function() {
                var label = [oInput.data('checked'), oInput.data('unchecked')];
                self[stateName] = !this.checked;
                cookie.setCookie(cookieName, this.checked ? '' : 1);

                if (this.checked) {
                    label = label.reverse();
                }

                if (typeof callback === 'function') {
                    callback(oElem, self[stateName], label);
                }

                oLabel.html(''+label[1]);
            });

            if (cookie.getCookie(cookieName)) {
                oInput.prop('checked', false).trigger('change');
            }
        }

        , isNotColumn: function() {
            return jQuery('.el_table_newline:eq(0)').css('display') === 'table-row';
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
            return 'Modules/Book/Calendar';
        }


        /**
         * Update description
         *
         * @param {jQuery} oElem
         */
        , note: function(oElem) {
            oElem.on('click', function() {
                var o = jQuery(this)
                    , el = jQuery('.ui-note').eq(o.data('note'));

                if (el.find('form').length) {
                    Vaviorka.registry.trigger('Modules/Book/Calendar', 'closeNote', [el.find('form')]);

                } else {
                    var text = jQuery('<input style="width:100%" type="text" name="content['+o.data('id')+']" />').val(el.html());
                    el.html(
                        jQuery('<form method="POST" action="'+o.data('href')+'" class="ui" data-class="Request/Form" data-actions="init" data-ignore-callback="Modules/Book/Calendar:closeNote" />')
                        .append(jQuery('<input type="hidden" name="action" value="update" />'))
                        .append(text)
                    );
                    text.trigger('focus');
                    Vaviorka.registry.final();
                }
            });
        }

        /**
         * Close Form with description
         *
         * @param {jQuery} oElem
         */
        , closeNote: function(oElem) {
            oElem.replaceWith(oElem.find('input[type="text"]').val());
        }

        /**
         *
         * @param {jQuery} oTarget
         * @param {jQuery} oElem
         */
        , add: function(oTarget, oElem) {
            if (!oElem.data('isbn')) {
                Vaviorka.registry.trigger('Response/Error', 'show', [null, 'ISBN is missing', 500]);
            } else {
                var data = {
                    action: 'move',
                    id: oElem.data('id'),
                    isbn: oElem.data('isbn'),
                    pos: oTarget.data('pos'),
                    type: oTarget.data('type'),
                    language: jQuery('input[name="language"]').val()
                };
                Vaviorka.registry.trigger('View/Animate/Loading', 'start', [oTarget]);
                Vaviorka.registry.trigger('Request/Pjax', 'submit', [oElem, window.location.href, data, function() {
                    Vaviorka.registry.trigger('View/Animate/Loading', 'stop', []);
                }]);
            }
        }

        /**
         * Status bar
         * @param {jQuery} oElem
         */
        , status: function(oElem) {
            var val = ~oElem.data('proc') / ~oElem.data('max');
            var state = 90 + 270 * val;
            oElem.find('img').css('background-image', "url('"+oElem.data('cover')+"')");
            if (val <= 0.5) {
                oElem.css('background-image', 'linear-gradient(90deg, #efefef 50%, transparent 50%, transparent), linear-gradient('+state+'deg, #7c8c9c 50%, #efefef 50%, #efefef)');
            } else {
                oElem.css('background-image', 'linear-gradient('+(state - 270)+'deg, #7c8c9c 50%, transparent 50%, transparent), linear-gradient(270deg, #7c8c9c 50%, #efefef 50%, #efefef)');
            }
        }

        /**
         * Pagination bar
         * @param {jQuery} oElem
         */
        , pagination: function (oElem) {
            var o = oElem,
                rng = o.find('input[type="range"]').eq(0),
                cnt = o.find('input[type="text"]').eq(0),
                height = 12,
                tm;

            o.removeClass('hidden');
            o.height(height);
            rng.width(rng.width() - cnt.parent().width());
            rng.height(height);
            o.find('progress')
                .width(rng.width())
                .height(height)
                .val(rng.val());

            rng.on('change mousemove', function() {
                cnt.val(this.value);
                cnt.trigger('change');
            })
            .css('position', 'absolute')
            .css('margin-left', '-'+rng.width()+'px');

            cnt.on('focus', function() {
                this.select();
                clearTimeout(tm);
            });
            rng.data('last', rng.val());
            cnt.on('change', function() {
                var page = this.value;

                var el = o.parent().find('.el_circle_cover').eq(0);
                el.data('proc', page);
                rng.val(page);
                o.find('progress').val(page);
                Vaviorka.registry.trigger('Modules/Book/Calendar', 'status', [el]);

                clearTimeout(tm);
                tm = setTimeout(function() {
                    self.savePages(o.parent().data('isbn'), page, rng);
                }, 1000);

                cnt.trigger('blur');
            });

            // Avoid popup with navigation
            cnt.bind('mouseup', function(event) {
                Vaviorka.ui.stopPropagation(event);
            });
            rng.bind('mouseup', function(event) {
                Vaviorka.ui.stopPropagation(event);
            });
        }

        /**
         * Add book to list
         * @param {jQuery} oElem
         */
        , push: function(oElem) {
            var form = oElem.closest('form');
            form.attr('action', oElem.val());
            form.trigger('submit');
        }

        /**
         * Extra menu functionality
         *
         * @param {jQuery} oElem
         */
        , popup: function(oElem, bFocus) {
            var tr = new Vaviorka.model.translate();
            var oSelect = jQuery(
                    '<select class="cr_move" style="opacity:0;position:absolute;left:0;top:0;width:52px;height:'+oElem.innerHeight()+'px">'
                    + '<optgroup label="'+tr.tget('LB_BOOK_CALENDAR_SELECT')+'"></optgroup>'
                    + '</select>'
            );
            var opt =  oSelect.children(0);
            var toDelete = '8;8';
            var toChangeList = '8;7';

            opt.append(jQuery('<option />').val('0;0').text(tr.tget('LB_BOOK_LIST_WISH')));
            opt.append(jQuery('<option />').val('1;0').text(tr.tget('LB_BOOK_LIST_READ')));
            opt.append(jQuery('<option />').val('9;0').text(tr.tget('LB_BOOK_LIST_FINISH')));
            opt.append(jQuery('<option />').val('8;0').text(tr.tget('LB_BOOK_LIST_DELETE')));
            opt.append(jQuery('<option />').val(toDelete).text(tr.tget('LB_BOOK_LIST_REMOVE')));
            opt.append(jQuery('<option />').val(toChangeList).text(tr.tget('LB_BOOK_LIST_CHANGE')));

            if (typeof oElem.data('type') !== 'undefined') {
                var up = oElem.data('pos') - 3
                    , down = 3 + oElem.data('pos')
                    , end = oElem.closest('.el_grid_top').data('pos');
                opt.append(jQuery('<option style="margin-top:12px" />').val(oElem.data('type') + ';0').text(tr.tget('LB_BOOK_CALENDAR_SELECT_FIRST')));
                opt.append(jQuery('<option />').val(oElem.data('type') + ';' + (up > 0 ? up : 0)).text(tr.tget('LB_BOOK_CALENDAR_SELECT_UP')));
                opt.append(jQuery('<option />').val(oElem.data('type') + ';' + (down > end ? end : down)).text(tr.tget('LB_BOOK_CALENDAR_SELECT_DOWN')));
                opt.append(jQuery('<option />').val(oElem.data('type') + ';' + end).text(tr.tget('LB_BOOK_CALENDAR_SELECT_END')));
            }

            // Selection behaviour
            oSelect.on('change', function() {
                var o = jQuery(this).parent();
                switch (this.value) {
                    case toChangeList:
                        Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, window.location.href, {action: 'change', id: o.data('isbn')}]);
                        break;

                    case toDelete:
                        o.remove();
                        Vaviorka.registry.trigger('Request/Pjax', 'submit', [null, window.location.href, {action: 'remove', id: o.data('isbn')}]);
                        break;

                    default:
                        var a = this.value.split(';');
                        if (a.length !== 2) {
                            break;
                        }
                        var target = null;
                        jQuery('.el_grid_top,.el_table,.el_border').each(function() {
                            var el = jQuery(this);
                            if (el.data('type') == a[0] && el.data('pos') == a[1]) {
                                target = el;
                            }
                        });
                        if (target === null) {
                            throw new Error(tr.tget('LB_ERROR_INTERNAL_ERROR'));
                        }
                        Vaviorka.registry.trigger('Modules/Book/Calendar', 'add', [target, o]);

                }
            });
            //oSelect.on('focus', function() {
            //    jQuery(this).width(jQuery(this).parent().width());
            //});
            oSelect.on('blur', function() {
                jQuery(this).width(32);
            });

            oElem.append(oSelect);
            oSelect[0].selectedIndex = -1;

            // iOS claims that draggable is in the element but doesn't allow drag and drop.
            var isMobile = self.isNotColumn();
            if (!isMobile && 'draggable' in document.createElement('span')) {
                oSelect.hide();

                oElem.bind('mouseup', function(event) {
                    oSelect.show();
                    try {
                        var mdown = document.createEvent("MouseEvents");
                        mdown.initMouseEvent("mousedown", true, true, window, 0, event.screenX, event.screenY, event.clientX, event.clientY, true, false, false, true, 0, null);
                        oSelect[0].dispatchEvent(mdown);
                    } catch (e) {
                        // ignore try
                    }
                });
                oSelect.bind('blur', function() {
                    oSelect.hide();
                });
            }

        }

        /**
         * DragDrop books functionality
         *
         * @param {jQuery} oElem
         */
        , move: function(oElem) {
            // Element's drab & drop ability
            Vaviorka.registry.trigger('View/DragDrop', 'drag', [oElem]);
            Vaviorka.registry.trigger('View/DragDrop', 'drop', [oElem]);
            // Menu
            Vaviorka.registry.trigger('Modules/Book/Calendar', 'popup', [oElem, true]);
        }

        /**
         * Change the column's order if it's a linear structure
         *
         * @param {jQuery} oElem
         */
        , order: function(oElem) {
            if (self.isNotColumn()) {
                oElem.children().addClass('el_table_newline');
                oElem.children().eq(1).prependTo(oElem);
                oElem.children().eq(2).find('header:eq(0)').appendTo(oElem.children().eq(2));
            }
        }

        /**
         * Change type of reading status
         *
         * @param {jQuery} oElem
         */
        , pcnt: function(oElem) {
            self.radioSwitch(oElem, 'isPercent', self.cookiePercent, function(oElem, isChecked, label) {
                jQuery('.ui-pagination').each(function(i, o) {
                    var max = jQuery(o).find('input[type="range"]:eq(0)');
                    var input = jQuery(o).find('input[type="text"]:eq(0)');
                    var val = [max.data('max'), Math.round(max.data('max') * max.val() / 100)];

                    if (!(max.val() || max.data('max'))) {
                        if (self.isPercent) {
                            val[0] = 100;
                        }
                        val[1] = 0;

                    } else if (self.isPercent) {
                        val = [100, Math.round(max.val() / max.data('max') * 100)];

                    }
                    jQuery(o).find('progress').attr('max', val[0]);
                    jQuery(o).parent().find('.el_circle_cover').data('proc', val[1]).data('max', val[0]);
                    max.attr('max', val[0]).val(val[1]);
                    input.attr('max', val[0]).val(val[1]).trigger('change');
                });
                jQuery('.ui-pagination span').html(''+ label[0]);
            });
        }

        /**
         * Change list representation
         *
         * @param {jQuery} oElem
         */
        , list: function(oElem) {
            if (self.isNotColumn()) {
                oElem.hide();
                return;
            }
            self.radioSwitch(oElem, 'isPlainList', self.cookieList, function(oElem, isChecked) {
                var list = jQuery('#read_list');
                if (isChecked) {
                    jQuery('.el_table_newline').css('display', 'table-row');
                    Vaviorka.registry.trigger('Modules/Book/Calendar', 'order', [list]);
                } else {
                    jQuery('.el_table_newline').css('display', '');
                    list.children().eq(1).prependTo(list);
                    list.children().eq(2).find('header:last').prependTo(list.children().eq(2));
                }
            });
        }

        /**
         * Define language hidden field for the form
         *
         * @param {jQuery} oElem
         */
        , setFormLanguage: function(oElem) {
            oElem.on('click', function() {
                var o = jQuery(this);
                var field = o.closest('form').find('input[name="language"]');
                o.closest('.select').find('strong:eq(0)').html(o.data('language'));
                o.closest('.select').find('.active').removeClass('active');
                o.addClass('active');
                if (field) {
                    field.val(o.data('language'));
                }
                return false;
            });
        }

        /**
         * Search by type
         *
         * @param {jQuery} oElem
         */
        , search: function(oElem) {
            oElem.on('click', function() {
                var o = jQuery(this);
                var field = jQuery('#search_area_type');
                field.val(o.text());
                field.closest('form').submit();
                field.val('');
                return false;
            });
        }


        /**
         * Autosearch by type
         *
         * @param {jQuery} oElem
         */
        , autoSearch: function(oElem) {
            var nextSearch = function () {
                var item = jQuery('#search_list');
                if (item.find('.el_notion').length) {
                    return;
                } else if (!item.find('.co_attention').length) {
                    setTimeout(nextSearch, 1000);
                } else {
                    activateSearch();
                }
            };
            var activateSearch = function () {
                var searchType = oElem.find('.ui-search-option');
                if (searchType.length) {
                    searchType.eq(0)
                        .focus()
                        .removeClass('ui-search-option')
                        .css('font-weight', 600);
                    setTimeout(function () {
                        searchType.eq(0).trigger('click');
                    }, 500);
                    setTimeout(nextSearch, 1000);
                }
            }
            activateSearch();
        }
    };

})(window.Vaviorka.query, window.Vaviorka));