/*jslint browser: true */

/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    'use strict';
    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Games/Accordion'
        , translate: new Vaviorka.model.translate()
        , store: {}
        , errorCount: 0
        , seriesCount: 1
        , goodSeries: 0

        /**
         * Initialize the list for the game
         * @param {jQuery} oElem
         */
        , initGame: function(oElem) {
            var store = jQuery('<textarea class="ui-store" style="width:100%;height:240px" />');
            oElem.find('.ui-content').html(store);
            var container = oElem.siblings('.ui-container');
            if (container.length) {
                store.val(container.eq(0).text());
            }
            var input = oElem.find('input.bg_attention');
            input.val(self.translate.tget("LB_BUTTON_RUN"));
            input.off('click').on('click', function () {
                var storeList = store.val().split('\n');
                var storeArray = [];
                var i, j, temp;
                for(i = 0; i < storeList.length; i++) {
                    var pair = storeList[i].split(/[.:;,]/);
                    if (pair.length > 1) {
                        storeArray.push([jQuery.trim(pair[0]), jQuery.trim(pair[1])]);
                    }
                }
                // shuffle
                for (i = storeArray.length - 1; i > 0; i--) {
                    j = Math.floor(Math.random() * (i + 1));
                    temp = storeArray[i];
                    storeArray[i] = storeArray[j];
                    storeArray[j] = temp;
                }
                // put into object
                for (i = 0; i < storeArray.length; i++) {
                    self.store[storeArray[i][0]] = storeArray[i][1];
                }

                self.startGame(oElem);
                return false;
            });
        }

        /**
         * Run the game
         * @param {jQuery} oElem
         */
        , startGame: function(oElem) {
            var content = oElem.find('.ui-content');
            content.html('');
            content.append(
                '<footer class="el_footer"><div class="menu indent">'
                    + '<a href="#" class="right">' + self.translate.tget("LB_GAME_ACCORDION_WELL") + '<span>' + self.goodSeries + '</span></a>'
                    + '<a href="#" class="right">' + self.translate.tget("LB_GAME_ACCORDION_SERIA") + '<span>' + self.seriesCount + '</span></a>'
                + '</div></footer>'
            );
            var limit = 20;
            for (var key in self.store) {
                if (!limit) {
                    break;
                }
                content.append(
                    '<div class="ui-check indent el_grid el_grid_third" style="width:calc(100% - 40px)" data-id="' + key + '">'
                        + '<span class="el_border" style="text-align:right">' + key + '</span>'
                        + '<input type="text" />'
                    + '</div>'
                );
                limit--;
            }
            var input = oElem.find('input.bg_attention');
            input.val(self.translate.tget("LB_BUTTON_VALIDATE"));
            input.off('click').on('click', function () {
                var result = content.find('.ui-check');
                self.errorCount = 0;
                for (var i = 0; i < result.length; i++) {
                    var key = result.eq(i).data('id');
                    var inputCheck = result.eq(i).find('input');
                    var value = inputCheck.val();
                    var valueResult = '<ins class="indent" style="text-align:left">&radic; ' + value + '</ins>';
                    if (value !== self.store[key]) {
                        valueResult = '<del class="indent" style="text-align:left">&times; <s>' + value + '</s> <ins>' + self.store[key] + '</ins></del>';
                        self.errorCount++;
                    }
                    var valueInput = jQuery(valueResult);
                    inputCheck.parent().append(valueInput);
                    inputCheck.remove();
                }
                self.goodSeries = self.errorCount ? 0 : self.goodSeries + 1;
                self.seriesCount++;

                self.continueGame(oElem);
                return false;
            });
        }

        /**
         * Run the game
         * @param {jQuery} oElem
         */
        , continueGame: function(oElem) {
            var input = oElem.find('input.bg_attention');
            input.val(self.translate.tget("LB_BUTTON_NEXT"));
            input.off('click').on('click', function () {
                var storeArray = [];
                var i, j, temp;
                for (var key in self.store) {
                    storeArray.push([key, self.store[key]]);
                }
                // shuffle
                for (i = storeArray.length - 1; i > 0; i--) {
                    j = Math.floor(Math.random() * (i + 1));
                    temp = storeArray[i];
                    storeArray[i] = storeArray[j];
                    storeArray[j] = temp;
                }
                // Revert store data
                self.store = {};
                for (i = 0; i < storeArray.length; i++) {
                    self.store[storeArray[i][1]] = storeArray[i][0];
                }

                self.startGame(oElem);
                return false;
            });
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
        getName: function () {
            return self.name;
        }

        /**
         * Initialize game
         * @param {jQuery} oElem
         */
        , init: function (oElem) {
            var form = jQuery('<form class="indent_neg center el_form" />');
            oElem.append(form.html('<p class="ui-content" />'));
            var input = jQuery('<input type="submit" class="button center bg_attention" />').val(self.translate.tget("LB_BUTTON_START"));
            form.append(input);
            input.on('click', function() {
                self.initGame(oElem);
                return false;
            })
        }
    };

})(window.Vaviorka.query, window.Vaviorka));