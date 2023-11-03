Vaviorka.model.translate = (function () {

    var defLang = Vaviorka.ui.getLanguage();

    var data = {
        tget: function (key, lang) {
            if (typeof lang === 'undefined') {
                lang = defLang;
            }
            // Autoload required sub-module
            if (typeof Vaviorka.model.translate[lang] === 'undefined') {
                new Vaviorka.model.translate()[lang];
            }
            var list = new Vaviorka.model.translate[lang]();
            // Get translation
            if (key in list) {
                key = list[key];
            }
            return key;
        }
    };

    return Vaviorka.model.bind(data, 'translate');

});