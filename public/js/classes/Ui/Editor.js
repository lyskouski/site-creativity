/*jslint browser: true */

/**
 * Editor
 * @name Ui/Editor
 *
 * @since 2017-04-26
 * @author Viachaslau Lyskouski
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    'use strict';

    /**
     * Internal functionality
     * @type {Object}
     */
    var self = {
        name: 'Ui/Editor',

        type: [
            'plain',
            'html',
            'latex',
            'wiki'
        ],

        /**
         * Get list of editors
         * @returns {jQuery}
         */
        getEditorTypes: function () {
            var tab = jQuery('<div class="menu ui-editor-tab ui" data-class="Ui/Element" data-actions="menu" data-rotate="rf" />');
            self.type.forEach(function (type) {
                tab.append(jQuery('<a class="button bg_normal ui" href="#html" data-type="' + type + '" data-class="' + self.name + '" data-actions="editor">').html(type));
            });
            return jQuery('<div style="margin:0 10px 0 -10px" class="el_grid_top right el_vertical" />').append(tab);
        }
    };

    // Load related styles
    // Vaviorka.registry.css(self.name);

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
        },

        /**
         * Init Editor variant
         *
         * @param {jQuery} elem
         */
        init: function (elem) {
            self.getEditorTypes().insertBefore(elem);
            elem.append('<div id="ui-editor" class="ui" data-class="Ui/Editor/Panel/Html" data-actions="init" />');
            elem.append('<div id="ui-editor-pagination" class="ui" data-class="Ui/Editor/Pagination" data-actions="init" />');
            elem.append('<div id="ui-editor-page" class="indent el_border bg_highlight el_A4" spellcheck="true" contenteditable="true" />');
        },

        /**
         * Switch editor
         *
         * @param {jQuery} elem
         */
        editor: function (elem) {
            elem.bind('click', function () {
                if (!elem.hasClass('active')) {
                    Vaviorka.registry.trigger(self.name, elem.data('type') + 'Editor', []);
                }
                return false;
            });
        },

        /**
         * Init HTML Editor
         */
        plainEditor: function () {
            jQuery('.ui-editor-tab .button').removeClass('active');
            jQuery('.ui-editor-tab .button[data-type="plain"]').addClass('active');
            Vaviorka.registry.final();
        },

        /**
         * Init HTML Editor
         */
        htmlEditor: function () {
            jQuery('.ui-editor-tab .button').removeClass('active');
            jQuery('.ui-editor-tab .button[data-type="html"]').addClass('active');
            Vaviorka.registry.final();
        },

        /**
         * Init HTML Editor
         *
         * @param {jQuery} elem
         */
        latexEditor: function (elem) {
            jQuery('.ui-editor-tab .button').removeClass('active');
            jQuery('.ui-editor-tab .button[data-type="latex"]').addClass('active');
            Vaviorka.registry.final();
        },

        /**
         * Init HTML Editor
         *
         * @param {jQuery} elem
         */
        wikiEditor: function (elem) {
            jQuery('.ui-editor-tab .button').removeClass('active');
            jQuery('.ui-editor-tab .button[data-type="wiki"]').addClass('active');
            Vaviorka.registry.final();
        }
    };

})(window.Vaviorka.query, window.Vaviorka));