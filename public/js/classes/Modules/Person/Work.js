/**
 * Operate with new content
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        /**
         * @var integer - symbol limitation for one page
         */
        limit: 5000
        /**
         * @var boolean - source code is shown
         */
        , source: false

        , imagePanel: null

        /**
         * Add new page
         * @returns {jQuery}
         */
        , page: function(pg) {
            if (typeof pg === 'undefined') {
                var elem = jQuery('<section class="indent el_border bg_highlight el_A4" contenteditable="true" spellcheck="true" data-name="content[]"></section>');
                pg = elem.eq(0);
            }

            if (!self.imagePanel) {
                self.imagePanel = jQuery('#ui-sample-image').html();
                jQuery('#ui-sample-image').remove();
            }

            pg.bind('keyup', function(event) {
                var curr = jQuery(this);
                if (curr.text().length > self.limit && !curr.data('addedPage')) {
                    curr.data('addedPage', 1);
                    var nxt = curr.next();
                    if (!nxt.hasClass('el_A4')) {
                        var nPage = self.page();
                        nPage.insertAfter(curr);
                        nxt = nPage.eq(0);
                    }
                    nxt.trigger('focus');
                    event.preventDefault();
                    return false;
                }
                Vaviorka.registry.trigger('View/Popup/EditPanel', 'hide', []);
            });

            pg.bind('mouseup', function(event) {
                var selectedText = Vaviorka.ui.getSelected();
                if (jQuery.trim(selectedText) !== '') {
                    Vaviorka.registry.trigger('View/Popup/EditPanel', 'show', [event, jQuery(this), selectedText]);
                } else {
                    Vaviorka.registry.trigger('View/Popup/EditPanel', 'hide', []);
                }
            });

            // Catch images from a drag and drop events
            pg.bind('dragenter', function() {
                var element = jQuery(this);
                setTimeout(function() {
                    element.find('img[class!="ui-image"]').each(function() {
                        var img = jQuery(this);
                        img.addClass('ui-image');
                        img.bind('dblclick', function() {
                            var curr = jQuery(this);
                            Vaviorka.registry.trigger('View/Popup', 'prompt', [
                                self.imagePanel,
                                jQuery('#ui-buttons-prompt').html(),
                                function (oSubmit) {
                                    self.updateImage(curr, oSubmit);
                                }
                            ]);
                        });
                    });
                }, 1000);
            });

            return pg;
        }

        /**
         * Convert page to cource code textarea
         */
        , showSource: function() {
            // Revert source back
            if (self.source) {
                jQuery('textarea').each(function() {
                    var o = self.page();
                    o[0].innerHTML = this.value;
                    jQuery(this).parent().css('padding-left', 0);
                    jQuery(this).replaceWith(o);
                    jQuery('.ui-move').remove();
                });
            // Show source code
            } else {
                jQuery('.el_A4').each(function() {
                    var o = jQuery('<div style="padding-left:12px"><img style="position:absolute;margin-left:-12px;" class="ui-move cr_move" src="/img/icon/move.svg" /><textarea name="content[]"/></div>');
                    o.find('textarea').val(this.innerHTML);
                    jQuery(this).replaceWith(o);
                    Vaviorka.registry.trigger('View/DragDrop', 'drag', [o]);
                    Vaviorka.registry.trigger('View/DragDrop', 'drop', [o]);
                    Vaviorka.registry.trigger('View/DragDrop', 'prevent', [o]);
                });
            }
            self.source = !self.source;
        }

        /**
         * Update current image in accordance with form data
         * @param {jQuery} image
         * @param {jQuery} form
         */
        , updateImage: function(image, form) {
            image.attr('class', 'ui-image');
            // Change position
            switch (form[0].position.value) {
                case 'left': image.addClass('left indent'); break;
                case 'right': image.addClass('right indent'); break;
                case 'center': image.addClass('center'); break;
            }
            // Change text covering
            switch (form[0].cover.value) {
                case 'clear': image.addClass('clear'); break;
            //    case 'text': image.addClass('right'); break;
            }
            if (form[0].height.value !== 'auto') {
                image.attr('height', form[0].height.value + 'px');
            } else {
                image.attr('height', (image.height() * form[0].width.value / image.width()) + 'px');
            }
            // Change width
            image.attr('width', form[0].width.value + 'px');
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
            return 'Modules/Person/Work';
        }

        /**
         * Init form
         * @todo saving mechanizm
         *
         * @param {jQuery} oElem
         */
        , init: function (oElem) {
            oElem.find('[data-type="submit"]').bind('click', function () {
                Vaviorka.registry.trigger('View/Animate/Loading', 'start', [jQuery(this)]);
                oElem.trigger('submit');
                return false;
            });


            oElem.bind('submit', function (event) {
                event.preventDefault();
                Vaviorka.ui.stopPropagation(event);

                if (!self.source) {
                    self.showSource();
                }
                var aFullData = jQuery(this).serializeArray();

                // Show mask and status bar
                Vaviorka.registry.trigger('View/Animate/Loading', 'start', [jQuery(":focus")]);
                Vaviorka.registry.trigger('View/Popup', 'mask', [jQuery('#ui-mask').clone()]);
                jQuery('.ui-mask-status').attr('max', aFullData.length).val(i);

                var Form = jQuery(this);
                var sError = '';
                // Clear previous content
                var oPromise = jQuery.when(
                    jQuery.ajax({
                        type: Form.attr('method')
                        , async: false
                        , delay: 1
                        , url: Form.attr('action')
                        , data: {
                            action: 'clear'
                        }
                    })
                );
                // Send page by page
                for (var i = 0; i < aFullData.length; i++) {
                    (function(i) {
                    oPromise.then(
                        jQuery.ajax({
                            type: Form.attr('method')
                            , async: false
                            , delay: 1
                            , url: Form.attr('action')
                            , data: {
                                action: 'save'
                                , num: i
                                , content: aFullData[i].value
                            }
                            , success: function() {
                                jQuery('.ui-mask-status').attr('max', aFullData.length).val(i);
                            }
                            , error: function () {
                                self.showSource();
                                Vaviorka.registry.trigger('View/Popup', 'unmask', []);
                                sError += 'Taken errors for the page #' + i + '<br />';
                                throw new Error(sError);
                            }
                        })
                    );
                    })(i);
                }
                oPromise.done(function() {
                    Vaviorka.registry.final();
                    if (!sError) {
                        self.showSource();
                        Vaviorka.registry.trigger('View/Popup', 'unmask', []);
                        Vaviorka.registry.trigger('Response/Error', 'show', [null, 200, 'Content has been saved']);
                    }
                });

                return false;
            });
        }

        /**
         * Init image upload
         * @param {jQuery} oElem
         */
        , image: function (oElem) {
            oElem.bind('click', function () {
                Vaviorka.registry.trigger('Modules/Person', 'action', [jQuery(this), 'image']);
            }).bind('change', function() {
                var el = jQuery('input[name="og:image"]');
                if (!el.lenght) {
                    el = jQuery(this).parent().find('input[type="hidden"]');
                }
                el.val(jQuery(this).data('value'));
            });
        }

        /**
         * Add page
         * @param {jQuery} oElem
         * @param {String} sContent
         */
        , page: function(oElem, sContent) {
            var pg = self.page();
            pg.html(''+sContent);
            pg.insertBefore(oElem.next());
        }

        /**
         * Add new page
         * @param {jQuery} oElem
         */
        , addPage: function(oElem) {
            oElem.bind('click', function() {
                self.page().insertBefore(jQuery(this).parent());
            });
        }

        /**
         * Update page events
         * @param {jQuery} oElem
         */
        , bindPage: function(oElem) {
            self.page(oElem);
        }

        /**
         * Add new page
         * @param {jQuery} oElem
         */
        , addFirstPage: function(oElem) {
            var o = self.page();
            oElem.prepend(o);
            o.eq(0).trigger('focus');
        }

        /**
         * Promt before submit
         * @param {jQuery} oElem
         */
        , before: function(oElem) {
            oElem.bind('click', function(event) {
                Vaviorka.registry.trigger('View/Popup', 'prompt', [
                    '<p class="indent">Send for approval? Not saved data will be losted...</p>&nbsp;',
                    '<a href="'+this.href+'" class="right button bg_note" style="margin-top:-24px" data-type="ok">ok</a>'
                    + '<a class="right button bg_attention" style="margin-top:-24px" data-type="cancel">cancel</a>'
                ]);
                return false;
            });
        }

        /**
         * Show source code
         * @param {jQuery} oElem
         */
        , source: function(oElem) {
            oElem.bind('click', self.showSource);
        }

        , update: function(oElem) {
            oElem.bind('dblclick', function() {
                var o = jQuery(this);
                if (!o.attr('contenteditable')) {
                    jQuery(this)
                        .attr('contenteditable', true)
                        .attr('spellcheck', true)
                        .addClass('indent el_border bg_highlight el_A4');
                }
            });

            if (jQuery('#ui-editPanel-bm').length) {
                oElem.bind('mouseup', function(event) {
                    var selectedText = Vaviorka.ui.getSelected();
                    if (jQuery.trim(selectedText) !== '') {
                        Vaviorka.registry.trigger('View/Popup/EditPanel', 'show', [event, jQuery(this), selectedText]);
                    } else {
                        Vaviorka.registry.trigger('View/Popup/EditPanel', 'hide', []);
                    }
                });
            }

            oElem.bind('keyup', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    jQuery(this).trigger('change');
                }
            });

            oElem.bind('change', function() {
                var o = jQuery(this);
                jQuery.ajax({
                    type: 'POST'
                    , async: false
                    , url: Vaviorka.ui.getBasicUrl() + '/dev/tasks/auditor.json'
                    , data: {
                        action: 'update',
                        id: o.data('id'),
                        content: o.html()
                    }
                    , success: function (sResponse) {
                        var response = JSON.parse(sResponse);
                        Vaviorka.registry.trigger('Response/Error', 'show', [null, response.success, response.message]);
                    }
                    , error: function (XMLHttpRequest, textStatus, errorThrown) {
                        Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                    }
                });
            });
        }
    };

})(window.Vaviorka.query, window.Vaviorka));