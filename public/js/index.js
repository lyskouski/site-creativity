/**
 * Vaviorka initialisation
 *
 * @copyright Viachaslau Lyskouski, 2015 - now
 */

// pjax workflow
if (~window.location.hash.indexOf('!/')) {
    window.location.replace(window.location.href.replace('#!', ''));
} else {
    window.Vaviorka.registry.final();
}

// Trigger Errors if something has happened
window.onerror = function (message /*, url, lineNumber */) {
    window.Vaviorka.registry.trigger('Response/Error', 'show', [null, 500, message]);
};

// UI fixes
jQuery(document).ready(function () {
    // Scroll behaviour
    jQuery('.el_scroll').each(function () {
        var fHeight = function (oElem) {
            var i = 0;
            oElem.css('max-height', 0);
            oElem.siblings().each(function () {
                i += jQuery(this).outerHeight();
            });
            oElem.css('max-height', oElem.parent().height() - i);
        };

        var o = jQuery(this);
        o.parent().bind('change', function() {
            fHeight( o );
        });
        fHeight( o );
    });

    // Mark all titles and scroll if cache exists
    jQuery('.bg_headers :header').each(function() {
        var o = jQuery(this);
        jQuery('<a class="hidden" name="'+o.text()+'" />').insertBefore(o);
        if (decodeURI(location.hash.substr(1)).split(' ').join('') === o.text().split('\n').join('').split(' ').join('')) {
            jQuery('html,body').animate({scrollTop: o.offset().top - jQuery('.el_header_top:eq(0)').height()}, 1000);
        }
    });

    window.Vaviorka.registry.trigger('Ui/Element', 'selectEvent', [jQuery('.select > strong')]);

    jQuery('.select > strong').bind('click', function (event) {
        var o = jQuery(this).parent();
        if (~o[0].className.indexOf('active')) {
            o.removeClass('active');
        } else {
            o.addClass('active');
        }
        event.stopPropagation();
        event.preventDefault();
    });

    // Catalog margin bottom
    var fCatalog = function() {
        jQuery('.el_catalog').each(function() {
            var el = jQuery(this),
                o = el.find('.el_grid_top:eq(0)'),
                e = jQuery('.el_notion:eq(0)'),
                m = parseInt(e.css('margin-left')),
                h = parseInt(e.height()) + m,
                ht = o.outerHeight(),
                hi = ht,
                i = 0,
                im = 0;
            while (hi > h) {
                hi -= h;
                i++;
            }
            if (o.css('display') !== 'none') {
                im = h - hi + 9 + i*2;
            }
            /* el.animate({ 'margin-bottom': (im > 0 ? '+' : '-') + '=' + im }, "slow" ); */
            el.css('margin-bottom', im + 'px');
        });
        setTimeout(fCatalog, 1000);
    };
    setTimeout(fCatalog, 1000);

    // Resize main panel to a fullsize
    jQuery(window).resize(function () {
        jQuery('.el_header .menu').each(function () {
            var o = jQuery(this),
                    i = -1,
                    iMax = 400,
                    iWidth = 75 + 2 * parseInt(o.css('margin-left'));

            if (jQuery('#el_header-menu-popup').length) {
                var list = jQuery('#el_header-menu-popup').find('a');
                for (var k = 0; k < list.length; k++) {
                    list.eq(k).removeAttr('style');
                }
                o.append(list);
                jQuery('#el_header-menu-popup').remove();
            }

            if (o.height() >= 2 * o.children().eq(0).outerHeight()) {
                var a = o.children();
                while (i++, (o.width() > iWidth) && (o.width() > iMax)) {
                    iWidth += parseInt(a.eq(i).outerWidth());
                }
                i--;
                var oBlock = jQuery('<div id="el_header-menu-popup">' + (o.width() <= iMax ? '&equiv;' : '&ctdot;') + '</div>');
                oBlock.bind('click', function() {
                    if (this.className === '') {
                        this.className = 'active';
                    } else {
                        this.className = '';
                    }
                });
                o.append(oBlock);
                if (i <= 0) {
                    i = 0;
                    oBlock.css('margin-left', 0).css('left', 72);
                }
                // Calculate max width
                var maxW = 0, w = 0;
                for (var k = 0; k < a.length; k++) {
                    w = jQuery(a[k]).outerWidth();
                    if (maxW < w) {
                        maxW = w;
                    }
                }
                // Add into popup
                k = 0;
                for (var iSize = a.length; i < iSize; i++) {
                    a[i].style = 'width:'+maxW+'px;display:block;margin-top:'+(k ? 4 : 12)+'px;text-align: left';
                    oBlock.append(a[i]);
                    k++;
                }
            }
        });
    });

    jQuery(window).trigger('resize');
});

// NOTES:
// - for a text wrapping: https://developer.mozilla.org/en-US/docs/Web/API/Element/getClientRects
// - visual aspects: https://developer.mozilla.org/ru/docs/Web/CSS/text-transform
// - DOMSubtreeModified
// -- MutationObserver
