"use strict";

/**
 * Web framework for internal usages on creativity.by
 * @copyright Copyright (c) 2015 creativity.by
 *
 * Support: IE6+, FF3+, Opera 9+, Safari, Chrome
 *
 * @note html-data parameters are used
 *     - Request/Form[data-extra, data-type]
 *     - class="ui"[data-class, data-actions]
 *
 * @since 2015-03-01
 * @author Viachaslau Lyskouski(deus@creativity.by)
 * @link http://creativity.by
 *
 * @type {Object} - web framework
 */
window.Vaviorka = (function (window, document) {
    /**
     * Internal functionality
     * @type {object}
     */
    var self = {
        pathPrefix: '/js/classes/'
        , pathSuffix: '?_v=' + (new Date).getTime()

        /**
         * List of an available classes
         *
         * @type {array}
         */
        , aList: {}
        /**
         * List of registered callbacks
         * @note waiting for a class uploading
         *
         * @type {array}
         */
        , aCallback: {}

        /**
         * Init request method
         *
         * @returns {ActiveXObject|XMLHttpRequest}
         */
        , request: function() {
            var oRequest;
            if (window.XMLHttpRequest) {
                var XHR = ('onload' in new XMLHttpRequest) ? XMLHttpRequest : XDomainRequest;
                oRequest = new XHR();
            } else if(window.ActiveXObject) {
                try {
                    oRequest = new ActiveXObject("Msxml2.XMLHTTP");
                } catch(e) {
                    try {
                        oRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) {
                        throw new Error((new Vaviorka.model.translate).tget('LB_ERROR_OLD_BROWSER'));
                    }
                }
            }

            return oRequest;
        }

        /**
         * Check file existance
         * @fixme: has to be checked, how it can be optimized
         *
         * @param {String} sUrl
         * @param {Callable} fOk
         * @param {Callable} fFalse
         */
        , check: function (sUrl, fOk, fFalse) {
            // @fixme: To avoid a delay
            return fOk();

            var oRequest = self.request();
            // oRequest.setRequestHeader('X-Auth', '123');
            // oRequest.getAllResponseHeaders()

            oRequest.open('HEAD', sUrl);// , false); - to disable async
            oRequest.onreadystatechange = function() {
                // HEADERS_RECEIVED{value=2} -> Unbind event
                if (oRequest.readyState === this.HEADERS_RECEIVED) {
                    oRequest.onreadystatechange = new Function();
                    if (~[200, 304].indexOf(this.status)) {
                        oRequest.abort();
                        fOk();
                    } else {
                        fFalse();
                    }
                }
            };
            // oRequest.onabort = fFalse;
            oRequest.onerror = fFalse;

            oRequest.send();
        }

        /**
         * Registry new requested callback
         * @note will be executed after the file uploading
         *
         * @param {String} sName - path to a framework's class
         * @param {String} sAction
         * @param {Array|Object|Null} mValues
         */
        , reg: function (sName, sAction, mValues) {
            if (typeof self.aCallback[ sName ] === 'undefined') {
                self.aCallback[ sName ] = [];
            }
            self.aCallback[ sName ].push(function (oClass) {
                if (typeof oClass[sAction] === 'undefined') {
                    throw Error(sName + '::' + sAction + '() - wrong implementation', 500);
                }
                oClass[sAction].apply(null, mValues);
            });
        }

        /**
         * Execute all required functions for a class after it has been loaded
         *
         * @param {String} sName
         */
        , flush: function (sName) {
            if (typeof self.aCallback[ sName ] !== 'undefined') {
                while (self.aCallback[ sName ].length) {
                    (self.aCallback[ sName ].shift())( self.aList[ sName ] );
                }
            }
        }

        /**
         * Load file if the required class is missing
         *
         * @param {String} sName
         */
        , load: function (sName) {
            if (!sName) {
                return;
            }
            var sFilePath = '';
            if (sName.indexOf('//') !== 0) {
                sFilePath += self.pathPrefix;
                if (sName.indexOf('lib/') === 0) {
                    sFilePath += '../';
                }
            }
            sFilePath += sName;
            if (!(~sName.indexOf('.js') || ~sName.indexOf('?'))) {
                sFilePath += '.js' + self.pathSuffix;
            }

            if (typeof this.aList[sName] === 'undefined') {
                self.check(
                    sFilePath,
                    function() {
                        var oClass = document.createElement('script');
                        oClass.setAttribute('type', 'text/javascript');
                        //if (~sFilePath.indexOf('Translate/')) {
                        //    oClass.setAttribute('async', false);
                        //}
                        //oClass.setAttribute('defer', false);
                        oClass.setAttribute('src', sFilePath);
                        document.body.appendChild(oClass);
                    },
                    function() {
                        throw Error(sName + ' is missing', 404);
                    }
                );
            }

        }

        /**
         * Trigger required class function
         *
         * @param {String} sName
         * @param {String} sAction
         * @param {Array|Object|Null} mValues
         */
        , trigger: function (sName, sAction, mValues) {
            self.reg(sName, sAction, mValues);
            if (typeof self.aList[sName] === 'undefined') {
                self.load(sName);
                self.aList[sName] = null;

            } else if (self.aList[sName] !== null) {
                self.flush(sName);
            }
        }
    };

    /**
     * Loaded models into application
     * @note blocking sync loading
     *
     * @sample var alertHi = new Vaviorka.model.sample.hi();
     *
     * @type {Object}
     */
    var model = {
        create: function (name, prefix) {
            // Check namespace
            if (typeof prefix === 'undefined') {
                prefix = '';
            } else if (prefix) {
                prefix += '/';
            }
            // Load class
            var req = self.request();
            req.open("GET", self.pathPrefix + 'model/'+ prefix + name +'.js' + self.pathSuffix, false);
            req.send(null);
            return (new Function('return ' + req.responseText))();
        }

        /**
         * Preload required model
         *
         * @param {String} name
         */
        , requireOne: function(name) {
            var namespace = name.split('.');
            var el = this
                , b;
            for (var i = 0; i < namespace.length; i++) {
                if (typeof el === 'function') {
                    var tmp = new el();
                    b = typeof tmp[namespace[i]] === 'undefined';
                } else {
                    b = typeof el[namespace[i]] === 'undefined';
                }
                if (b) {
                    el[namespace[i]] = model.create(namespace[i], namespace.splice(0, i).join('/'));
                }
                var el = el[namespace[i]];
            }
        }

        /**
         * Preload required models
         * @note to avoid errors for deprecated browsers
         *
         * @param {Array} nameList
         */
        , require: function(nameList) {
            for (var i = 0; i < nameList.length; i++) {
                model.requireOne(nameList[i]);
            }
        }

        , bind: function(data, prefix, namespace) {
            var trg = model;
            if (namespace) {
                var nm = namespace.split('.');
                while (nm.length) {
                    var key = nm.pop();
                    trg = trg[key];
                }
            }
            if (typeof trg[prefix] !== 'undefined') {
                trg = trg[prefix];
            }
            // Init proxy
            trg = new Proxy(data, {
                /**
                 * Magic call to cover missing functionality
                 *
                 * @param {Object} target
                 * @param {String} name
                 * @throws {Error} Your browser does not support needed WebGL functionality
                 * @returns {Mixed}
                 */
                get: function (target, name) {
                    // Basic options
                    if (name in []) {
                        return null;
                    // Get source
                    } else if (name === 'getSourceLink') {
                        return target;
                    // Get element
                    } else if (!(name in target)) {
                        target[name] = model.create(name, prefix);
                    }
                    return target[name];
                }

                /**
                 * It's should not be possible to override properties
                 * @note modules are protected and can be used in RO mode
                 *
                 * @param {Object} target
                 * @param {String} prop
                 * @param {Mixed} value
                 */
                , set: function (target, prop, value) {
                    if (!(prop in target)) {
                        target[prop] = value;
                    }// else {
                    //    throw Error('Model `'+prop+'` update cannot be done', 500);
                    //}
                    return value;
                }
            });
            return trg;
        }
    };

    /**
     * External functionality to operate with classes
     * @type object
     */
    var external = {
        /**
         * Operate with classes
         */
        registry: {
            /**
            * Vaviorka Framework Version
            *
            * @param {String} prefix
            * @param {String} suffix
            */
            target: function (prefix, suffix) {
                self.pathPrefix = prefix;
                if (suffix) {
                    self.pathSuffix = '?_v=' + suffix;
                }
            }
            /**
             * Initially load classes
             *
             * @param {String|Array} mName
             */
            , init: function (mName) {
                if (typeof mName !== 'string') {
                    for (var i = 0; i < mName.length; i++) {
                        self.load(mName[i]);
                    }
                } else {
                    self.load(mName);
                }
            }

            /**
             * Basic functionality that has to be triggered after each time of async execution
             * @sample <li class="ui" data-class="View/DragDrop" data-actions="drag,drop">...</li>
             */
            , final: function () {
                self.trigger('View/Height', 'resize', []);
                self.trigger('View/Animate/Loading', 'stop', []);
                self.trigger('View/Popup', 'title', []);
                // init all required components
                jQuery('.ui').each(function() {
                    var o = jQuery(this),
                        a = o.data('actions').split(',');
                    for (var i = 0; i < a.length; i++) {
                        self.trigger(o.data('class'), a[i], [o]);
                        o.removeClass('ui');
                        o.removeAttr('data-class');
                        o.removeAttr('data-actions');
                    }
                });
            }

            /**
             * Trigger required class function
             *
             * @param {string} sName
             * @param {string} sAction
             * @param {mixed} mValues
             */
            , trigger: function (sName, sAction, mValues) {
                self.trigger(sName, sAction, mValues);
            }

            /**
             * Init object from other classes
             * @sample Vaviorka.registry.include( (function(){ ... })() );
             *
             * @param {object} oClass
             */
            , include: function (oClass) {
                self.aList[oClass.getName()] = oClass;
                self.flush(oClass.getName());
            }

            , css: function(sName) {
                if (~self.pathPrefix.indexOf('.min')) {
                    return;
                }
                var link = document.createElement('link');
                link.setAttribute('media', 'all');
                link.setAttribute('rel', 'stylesheet');
                link.setAttribute('href', self.pathPrefix.replace('/js', '/css') + sName + '.css');
                if (!Vaviorka.query('link[href="' + link.href + '"]').length) {
                    link.href += self.pathSuffix;
                    document.body.appendChild(link);
                }
            }
        }

        , model: model.bind(model, '')

        /**
         * Operate with values
         */
        , ui: {
            /**
             * Triggered from a history part
             * @var boolean
             */
            history: false

            /**
             * Disable cascade event actions
             *
             * @param {window.event} event
             */
            , stopPropagation: function(event) {
                if (!event) {
                    var event = window.event;
                }
                event.cancelBubble = true;
                if (event.stopPropagation) {
                    event.stopPropagation();
                }
            }

            /**
             * Get cursor position
             *
             * @param {event} event
             * @returns {Object} - {x:___, y:___}
             */
            , getCursor: function (event) {
                var cursorX, cursorY;
                if (document.all) {
                    cursorX = event.clientX + document.body.scrollLeft;
                    cursorY = event.clientY + document.body.scrollTop;
                } else {
                    cursorX = (window.Event) ? event.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
                    cursorY = (window.Event) ? event.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
                }
                return {x: cursorX, y: cursorY};
            }

            , getLanguage: function() {
                return Vaviorka.query('head > meta[http-equiv="Content-Language"]').attr('content');
            }

            , getBasicUrl: function() {
                return  Vaviorka.query('head > meta[property="homepage"]').attr('content');
            }

            /**
             * Check if failed or not
             *
             * @param {mixed} value
             * @param {String|undefined} code
             * @returns {Boolean}
             */
            , isFailed: function(value, code) {
                if (typeof code === 'undefined') {
                    code = '2';
                }
                return String(value)[0] !== code;
            }

            , clone: function(elem) {
                switch (Object.prototype.toString.call(elem)) {
                    case '[object Array]':
                        var o = [];
                        for (var i = 0; i < elem.length; i++) {
                            o[i] = Vaviorka.ui.clone(elem[i]);
                        }
                        break;
                    case '[object Object]':
                        var o = {};
                        for (var i in elem) {
                            o[i] = Vaviorka.ui.clone(elem[i]);
                        }
                        break;
                    default:
                        var o = elem;
                }
                return o;
            }

            /**
             * Revert string
             *
             * @param {string} value
             * @returns {String}
             */
            , reverseChars: function (value) {
                return ('' + value).split("").reverse().join("");
            }

            /**
             * Number format functionality
             *
             * @param {float} value
             * @param {integer} decimals
             * @param {string} dec_point
             * @param {string} thousands_sep
             * @returns {Number}
             */
            , numberFormat: function (value, decimals, dec_point, thousands_sep) {
                // NaN value
                if (Number(value) !== Number(value)) {
                    return 0;
                }

                //decimals = (typeof decimals === 'undefined') ? 0 : decimals;
                //dec_point = (typeof dec_point === 'undefined') ? '.' : dec_point;
                //thousands_sep = (typeof thousands_sep === 'undefined') ? '`' : thousands_sep;

                var mResult = external.ui.reverseChars(
                    external.ui.reverseChars( '' + value ).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep)
                );
                if (decimals) {
                    mResult += dec_point + (value - Number.parseInt(value)).toFixed(decimals).slice(2);
                }
                return mResult;
            }

            /**
             * Check value in array
             *
             * @param {string} sKey
             * @param {array} aList
             * @returns {Boolean}
             */
            , inArray: function (sKey, aList) {
                if (aList.length) {
                    return ~aList.indexOf( sKey );
                } else {
                    for (var i in aList) {
                        if (sKey === aList[i]) {
                            return true;
                        }
                    }
                }
                return false;
            }

            /**
             * Get text selection
             * @returns {String}
             */
            , getSelected: function () {
                var txt = '';
                if (window.getSelection) {
                    txt = window.getSelection();
                } else if (document.getSelection) {
                    txt = document.getSelection();
                } else if (document.selection) {
                    txt = document.selection.createRange().text;
                }
                return txt;
            }
        }

        /**
         * @todo Operate with DOM elements
         */
        , query: jQuery
        //, query: {
        //
        //}

    };

    return external;

})(window, document);