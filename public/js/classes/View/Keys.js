/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        bindList: function (o, a, url) {
            for (var i = 0; i < a.length; i++) {
                if (~a[i].indexOf(':')) {
                    a[i] = a[i].split(':');
                    var tmpUrl = a[i][0].trim();
                    if (tmpUrl[0] !== '/') {
                    //    var el = o.find('a:last');
                    //    el.attr('href', el.html() + ' ' + a[i][0] + '.html');
                    //    var tmpList = el.html().split('/');
                    //    el.html(tmpList[tmpList.length - 1] + ' ' + a[i][0]);
                    } else {
                        delete a[i][0];
                        o.append(jQuery('<a class="bg_attention" href="' + Vaviorka.ui.getBasicUrl() + tmpUrl  + '.html">' + a[i].join(':').slice(1) + '</a>'));
                    }
                } else if (a[i][0] !== '/') {
                    o.append(jQuery('<a href="' + (url ? url + '/search/' + a[i].trim() + '.html' : '#') + '">' + a[i] + '</a>'));                   
                }
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
            return 'View/Keys';
        }

        /**
         *
         * @param {jQuery} oElem
         */
        , init: function(oElem) {
            var a = oElem.html().split(',');
            var o = jQuery('<aside class="left" />');
            oElem.html(o);
            self.bindList(o, a, oElem.data('url'));
        }

        /**
         *
         * @param {jQuery} oElem
         */
        , tags: function(oElem) {
            var a = oElem.html().split(',').filter(function (value, index, self) {
                return self.indexOf(value) === index;
            });
            var o = jQuery('<div class="left indent" />');
            oElem.html(o);
            self.bindList(o, a, oElem.data('url'));
            o.find('a').each(function() {
                var f = function(val, d) {
                    var delta = val / 100 * d;
                    return parseInt(val + Math.random() * delta - Math.random() * delta);
                };
                this.style.background = 'none';
                //this.style.background = 'rgba(' + [f(204, 30), f(154, 30), f(154, 30), f(100, 100) / 1000].join(',') + ')';
                var opacity = 2 * f(100, 100) / 100;
                if (opacity < 0.4) {
                    opacity += 0.4;
                }
                if (opacity > 1) {
                    opacity = 1;
                }
                this.style.color = 'rgba(' + [f(204, 30), f(154, 30), f(154, 30), opacity].join(',') + ')';
                this.style.fontSize = (1 * Math.random() + 0.5) + 'rem';
                this.style.margin = f(-6, 200) + 'px 12px ' + f(6, 200) + 'px 6px';
                this.style.float = 'left';
                this.style.textShadow = '-1px -1px 1px #f6f5f2, 1px 1px 1px #f6f5f2';
                this.title = this.innerHTML;
                jQuery(this).data('color', this.style.color);
            }).on('mouseover', function() {
                this.style.color = 'black';
            }).on('mouseout', function() {
                this.style.color = jQuery(this).data('color');
            });
        }
    };

})(window.Vaviorka.query));