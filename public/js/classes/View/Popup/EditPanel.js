/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        textSelection: ''

        , command: {
            clear: function(selectedText) {
                self.update((''+selectedText).replace(/<^br|\/?[^>]+>/g, ''));
            }
            , split: function(selectedText) {
                Vaviorka.registry.trigger('Modules/Person/Work', 'page', [jQuery(document.activeElement).closest('.el_A4'), selectedText]);
                self.update('');
            }
            , join: function() {
                var curr = jQuery(document.activeElement).closest('.el_A4');
                if (curr.next() && ~curr.next().attr('class').indexOf('el_A4')) {
                    curr.append(curr.next().html());
                    curr.next().remove();
                }
            }
            , accent: function(selectedText) {
                self.update(selectedText + '&#769;');
            }
            , bold: function(selectedText) {
                self.tag('strong', selectedText);
            }
            , italic: function(selectedText) {
                self.tag('em', selectedText);
            }
            , underline: function(selectedText) {
                self.tag('u', selectedText);
            }
            , crossed: function(selectedText) {
                self.tag('s', selectedText);
            }
            , sup: function(selectedText) {
                self.tag('sup', selectedText);
            }
            , sub: function(selectedText) {
                self.tag('sub', selectedText);
            }
        }

        , tag: function(tag, txt) {
            self.update('<'+tag+'>' + txt + '</'+tag+'>');
        }

        , update: function (str) {
            if (typeof document.selection !== 'undefined') {
                var range = document.selection.createRange();
                range.pasteHTML(str);

            } else {
                var sel = window.getSelection(),
                    range = sel.getRangeAt(0),
                    documentFragment = false;

                range.deleteContents();
                if (typeof document.createRange !== 'undefined') {
                    var rangeObj = document.createRange();
                    documentFragment = rangeObj.createContextualFragment(str);
                } else {
                    var ghost = document.createElement('div');
                    ghost.innerHTML = str;
                    documentFragment = document.createDocumentFragment();
                    while (ghost.firstChild) {
                        documentFragment.appendChild(ghost.firstChild);
                    };
                }
                range.collapse(false);
                range.insertNode(documentFragment);
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
        getName: function() {
            return 'View/Popup/EditPanel';
        }

        , init: function() {
            jQuery('<div id="ui-editPanel" style="display:hidden" class="el_content"/>')
                    .html(jQuery('#ui-editPanel-bm').contents())
                    .appendTo(document.body);
        }

        , hide: function() {
            jQuery('#ui-editPanel').hide();
        }

        /**
         * Init Editor Panel
         *
         * @param {Event} event
         * @param {jQuery} oElem
         */
        , show: function(event, oElem) {
            var cursor = Vaviorka.ui.getCursor(event);
            var offset = oElem.offset();
            // @todo
            if (!jQuery('#ui-editPanel').length) {
                Vaviorka.registry.trigger('View/Popup/EditPanel', 'init', []);
            }
            jQuery('#ui-editPanel')
                .attr('style', 'position:absolute;min-height: 40px;top:'+cursor.y+'px;left:'+offset.left+'px;width:'+oElem.innerWidth()+'px');

        }

        /**
         * Init Editor Panel
         *
         * @param {jQuery} oElem
         */
        , apply: function(oElem) {
            oElem.bind('mousedown', function() {
               var selectedText = Vaviorka.ui.getSelected();
               self.command[jQuery(this).data('command')](selectedText);
               return false;
            });
        }
    };

})(window.Vaviorka.query, window.Vaviorka));