Vaviorka.model.basic = (function () {

    if (!navigator.cookieEnabled) {
        var tr = new Vaviorka.model.translate();
        window.Vaviorka.registry.trigger('Response/Error', 'show', [null, 500, tr.tget('LB_ERROR_COOKIE_OFF')]);
        // throw Error(tr.tget('LB_ERROR_COOKIE_OFF'))
    }

    var data = {
        setCookie: function (name, value, options) {
            options = options || {};
            var expires = options.expires;

            if (typeof expires == "number" && expires) {
                var d = new Date();
                d.setTime(d.getTime() + expires * 1000);
                expires = options.expires = d;
            }
            if (expires && expires.toUTCString) {
                options.expires = expires.toUTCString();
            }

            value = encodeURIComponent(value);

            var updatedCookie = name + "=" + value;

            for (var propName in options) {
                updatedCookie += "; " + propName;
                var propValue = options[propName];
                if (propValue !== true) {
                    updatedCookie += "=" + propValue;
                }
            }

            document.cookie = updatedCookie;
            return this;
        }
        , getCookie: function (name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            if (matches) {
                return decodeURIComponent(matches[1]);
            }
        }
        , clearCookie: function (name) {
            data.setCookie(name, "", {expires: -1});
        }
    };

    return Vaviorka.model.bind(data, 'cookie');

});