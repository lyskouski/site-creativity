/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        uid: 0
        , draw: function () {
            // has to be inited
        }
        , text: ['s:#9ac0cc', 'f:#9ac0cc', 'w:2',
            /*c*/ 'b:28,20', ':2,20', ':2,90', ':51,90', ':51,77', ':13,77', ':13,31', ':28,31', 'c:1,1',
            /*r*/ 'b:34,20', ':34,31', ':38,31', ':38,37', ':21,37', ':19,40', ':19,71', ':28,71', ':28,48',
            ':44,48', ':48,43', ':48,20', 'c:1,1', 'b:41,64', 'a:41,64,6,0,Math.PI*2,0', 'c:1,1',
            /*e*/ 'b:60,20', ':60,31', ':70,31', ':51,54', ':66,90', ':76,90', ':76,80', ':64,56', ':86,29', ':86,20', 'c:1,1',
            /*a*/
            /*t*/ 'b:98,20', ':98,31', ':117,31', ':117,50', ':129,62', ':129,31', ':150,31', ':150,20', 'c:1,1',
            /*i*/ 'b:150,38', ':150,83', ':138,83', ':138,38', 'c:1,1', 'b:144,8', 'a:144,8,6,0,Math.PI*2,0', 'c:1,1',
            /*v*/ 'b:162,20', ':162,44', ':178,83', ':192,83', ':208,44', ':208,20', ':197,20', ':197,44', ':187,70',
            ':183,70', ':173,44', ':173,20', 'c:1,1',
            /*i*/ 'b:220,20', ':220,65', ':232,65', ':232,20', 'c:1,1', 'b:226,77', 'a:226,77,6,0,Math.PI*2,0', 'c:1,1',
            /*t*/ 'b:244,20', ':244,31', ':263,31', ':263,83', ':275,83', ':275,31', ':296,31', ':296,20', 'c:1,1',
            /*y*/ 'b:285,38', ':285,49', ':305,49', 'v:305,49,305,71,285,71', ':285,83', 'v:305,83,316,66,316,49',
            ':316,20', ':305,20', ':305,38', 'c:1,1',
            /*a-silver-fone*/ 'f:#bbbbbb', 's:#bbbbbb', 'w:3',
            'b:78,51', 'v:74,56,73,63,78,70', ':101,99', ':107,93', ':107,87', ':103,82', ':112,74', ':120,82',
            ':126,76', ':126,70', ':97,44', 'm:88,53', 'v:91,51,94,56,94,56', ':103,65', ':95,73', ':82,58', 'c:1,0',
            /*a-fone*/ 'f:#cc9a9a', 's:#f6f5f2', 'w:2',
            'b:77,50', 'v:73,55,73,63,78,70', ':101,99', ':107,93', ':107,87', ':103,82', ':112,74', ':120,82',
            ':126,76', ':126,70', ':97,44', 'm:88,53', 'v:91,51,94,56,94,56', ':103,65', ':95,73', ':82,58', 'c:1,0',
            /*a-lines*/ 'w:2', 'b:101,98', ':101,90', 'c:1,0',
            'b:120,83', ':120,75', 'c:1,0',
            'b:112,68', ':112,76', 'c:1,0',
            /*a*/ 'f:#cc9a9a', 'w:4', 'b:77,50', 'v:73,55,76,60,81,68', ':101,93', ':107,87', ':100,78', ':112,68',
            ':120,76', ':126,70', ':103,44', 'v:95,37,90,36,86,41', ':77,50', 'm:84,57', 'v:91,49,94,48,97,52',
            ':107,63', ':96,72', 'c:1,1'
        ]
        , water: 0
        , bubble: {
            pos: [1, 1, 1]
            , speed: [1.1, 1.15, 1.2]
            , size: [0.3, 0.3, 0.3]
                    /**
                     * Calculate the bubble speed
                     *
                     * @param {Number} nValue
                     * @param {Number} iSpeed
                     * @returns {Number}
                     */
            , getBoost: function (nValue, iSpeed) {
                return nValue * iSpeed;
            }

            , moveBubble: function (i, iEnd, iSpeed) {
                this.pos[i] = this.getBoost(this.pos[i], this.speed[i]);
                if (this.pos[i] > iEnd) {
                    this.pos[i] = 5 + 4 * (0.5 - Math.random());
                    this.speed[i] = iSpeed + 0.1 * Math.random();
                    this.size[i] = 0.1 + 0.3 * Math.random();
                }
            }
        }

        /**
         * Animate bubbles
         * @param {String} id
         */
        , fbubble: function (id, internal) {
            var el = jQuery('#' + id);
            var o = el[0].getContext('2d');
            internal.water++;

            var iDelay = 20;
            var iWave = 7 * Math.sin(internal.water / iDelay);
            var iEnd = 20 + 5 * Math.random();

            internal.bubble.moveBubble(0, iEnd, 1.02);
            internal.bubble.moveBubble(1, 4 + iEnd, 1.02);
            internal.bubble.moveBubble(2, 15 + iEnd, 1.03);

            var a = [
                'w:1', 'f:#cc9a9a', 's:#ffffff',
                '-:175.5,0,19,44', '-:179,40,13,10', '-:181,35,9,19', '-:182.5,40,6,19', '-:183.5,45,3.5,18',
                't:destination-over',
                'b:172,70', ':170,30', 'v:' + (180 + iWave) + ',' + (30 + iWave) + ',' + (190 + iWave) + ',' + (30 - iWave) + '3,198,30', ':198,70', 'c:1,1',
                't:source-over',
                's:#f1f1ef', 'f:#f1f1ef', //'w:0', 't:lighter',
                'b:', 'a:' + (182 + 0.5 * Math.random()) + ',' + (40 - internal.bubble.pos[0]) + ',' + (internal.bubble.size[0] + Math.log(internal.bubble.pos[0])) + ',0,Math.PI*2,1', 'c:1,1',
                'b:', 'a:' + (189 + 0.5 * Math.random()) + ',' + (44 - internal.bubble.pos[1]) + ',' + (internal.bubble.size[1] + Math.log(internal.bubble.pos[1])) + ',0,Math.PI*2,1', 'c:1,1',
                'b:', 'a:' + (185 + 0.5 * Math.random()) + ',' + (55 - internal.bubble.pos[2]) + ',' + (internal.bubble.size[2] + Math.log(internal.bubble.pos[2])) + ',0,Math.PI*2,1', 'c:1,1'
            ];
            for (var i = 0; i < a.length; i++) {
                self.draw(o, a[i].split(':'));
            }
            if (internal.water / iDelay > 90) {
                internal.water = 0;
            }

            if (el.data('animation')) {
                Vaviorka.registry.trigger('View/Animate/Soft', 'animate', [
                    internal.fbubble,
                    el[0],
                    [id, internal]
                ]);
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
            return 'Modules/Index/Logo';
        }

        /**
         * Build a logotype and animate it
         *
         * @param {jQuery} oElem
         * @param {Function} fDraw
         */
        , init: function (oElem, fDraw) {
            self.draw = fDraw;
            if (typeof oElem[0].id === 'undefined' || !oElem[0].id) {
                oElem[0].id = 'ui-logo' + ++self.uid;
            }
            // Cursor + go home
            oElem.addClass('cr_pointer');
            oElem.data('animation', 1);
            oElem.bind('mouseover', function () {
                oElem.data('animation', 0);
            });
            jQuery('body').bind('click', function() {
                oElem.data('animation', 0);
            }).bind('touchstart', function() {
                oElem.data('animation', 0);
            });
            jQuery('input,textarea').bind('focus', function() {
                oElem.data('animation', 0);
            });
            oElem.bind('click', function () {
                window.location.href = Vaviorka.ui.getBasicUrl() + '/index.html';
            });
            // Draw canvas logo
            var o = oElem[0].getContext('2d');
            // silver border
            var a = Vaviorka.ui.clone(self.text);
            a[0] = 's:#bbbbbb';//3f4e53
            a[2] = 'w:5';
            for (var i = 0; i < a.length; i++) {
                self.draw(o, a[i].split(':'));
            }
            // silver border
            var a = Vaviorka.ui.clone(self.text);
            a[0] = 's:#ffffff';
            a[2] = 'w:4';
            for (var i = 0; i < a.length; i++) {
                self.draw(o, a[i].split(':'));
            }
            // original
            for (var i = 0; i < self.text.length; i++) {
                self.draw(o, self.text[i].split(':'));
            }
            // animate bubble
            self.fbubble(oElem[0].id, Vaviorka.ui.clone(self));
        }
    }

})(window.Vaviorka, window.Vaviorka.query));