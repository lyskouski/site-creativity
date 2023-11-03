/**
 * Canvas plotting module
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (Vaviorka, jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {

        /**
         *
         * @param {object} o - element.getContext('2d')
         * @param {array} a
         * @returns {undefined}
         */
        draw: function(o, a) {
            if (Vaviorka.ui.inArray(a[0], ['b','','c', 'a','v','m','-','t','r','d','x','o','[]'])) {
                a[1] = a[1].split(',');
            }
            switch(a[0]) {
                case 's':
                    o.strokeStyle = a[1];
                    break;
                case 'f':
                    o.fillStyle = a[1];
                    break;
                case 'w':
                    o.lineWidth = a[1];
                    break;
                case 'b':
                    o.beginPath();
                    if (a[1][0]) {
                        o.moveTo(a[1][0], a[1][1]);
                    }
                    break;
                case 'm':
                    o.moveTo(a[1][0],a[1][1]);
                    break;
                case '' :
                    o.lineTo(a[1][0],a[1][1]);
                    break;
                case 'c':
                    if(!a[1][2])o.closePath();
                    if(a[1][0]) o.stroke();
                    if(a[1][1]) o.fill();
                    break;
                case 'st':
                    o.stroke();
                    break;
                case 'a':
                    o.arc(a[1][0],a[1][1],a[1][2],a[1][3],eval(a[1][4]),a[1][5]);
                    break;
                case '[]':
                    o.beginPath();
                    o.rect(a[1][0],a[1][1],a[1][2],a[1][3]);
                    o.stroke();
                    o.fill();
                    break;
                case 'v':
                    o.bezierCurveTo(a[1][0],a[1][1],a[1][2],a[1][3],a[1][4],a[1][5]);
                    break;
                case '-':
                    o.clearRect(a[1][0],a[1][1],a[1][2],a[1][3],a[1][4]);
                    break;
                case 't':
                    o.globalCompositeOperation = a[1];
                    break;
                case 'r':
                    o.translate(a[1][0],a[1][1]);
                    o.rotate(a[1][2].degree());
                    o.translate(-a[1][0],-a[1][1]);
                    break;
                case 'd':
                    o.translate(a[1][0],a[1][1]);
                    break;
                case '>':
                    o.save();
                    break;
                case '<':
                    o.restore();
                    break;
                case 'x':
                    o.fillText(a[1][0], a[1][1], a[1][2]);
                    break;
                case 'o':
                    o.translate(a[1][0],a[1][1]);
                    o.scale(a[1][2],a[1][3]);
                    o.translate(-a[1][0],-a[1][1]);
                    break;
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
            return 'View/Graphics/Canvas';
        }

        /**
         * Resize image
         * @param {jQuery} oElem
         * @param {String} sParams - default '-16,28,1.4,1.4'
         */
        , resize: function(oElem, sParams) {
            var o = oElem[0].getContext('2d');
            if (typeof sParams === 'undefined') {
                sParams = '-16,28,1.4,1.4';
            }
            self.draw(o, ['o', sParams]);
        }

        /**
         * Draw the logo
         * @param {jQuery} oElem
         */
        , logo: function(oElem) {
            Vaviorka.registry.trigger('Modules/Index/Logo', 'init', [oElem, self.draw]);
        }
    };

})(window.Vaviorka, window.Vaviorka.query));