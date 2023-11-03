/**
 * Module access rules
 *
 * @name Modules/Dev/Access
 */
window.Vaviorka.registry.include((function (jQuery, Vaviorka) {
    /**
     * Internal functionality
     *
     * @type {object}
     */
    var self = {
        /**
         * Access identification
         * @var {integer}
         */
        iTarget: -1

        , iNew: -1

        , aNewNames: []

        , aData: {}

        , aChanges: {
            action: 'save'
            , access_action: {}
            , access: {}
        }

        /**
         *
         */
        , checkStatus: function () {
            if (self.iTarget === -1) {
                throw Error('Profile is missing!');
            }

            var el = jQuery(this).closest('table'),
                    bAllow = this.checked,
                    bDeny = this.checked;

            if (this.name === 'deny') {
                bAllow = false;
            } else {
                bDeny = false;
            }

            el.removeClass('inactive');

            var eOpposite = el.find('input[name="' + (this.name === 'allow' ? 'deny' : 'allow') + '"]');
            if (eOpposite.prop('checked')) {
                eOpposite.prop('checked', !this.checked);

            } else if (!bAllow && !bDeny) {
                delete self.aData[ el.data('id') ];

            } else {
                self.aData[ el.data('id') ] = [bAllow, bDeny, self.iTarget];

            }
        }

        /**
         * Apply parent rules if exist
         * @param {jQuery} o
         * @param {string} sClassName
         * @returns {jQuery}
         */
        , getParent: function(o, sClassName) {
            var prn = o.parent().closest('.' + sClassName);
            return (prn.length) ? prn : false;
        }

        /**
         * Apply parent rules if exist
         * @param {jQuery} o
         * @param {array} aList
         */
        , checkParent: function (o, aList) {
            var prn = self.getParent(o, 'db-access');
            if (prn) {
                self.checkParent(prn, aList);
                self.updateList(prn, aList, false);
            }
        }

        , updateList: function (o, aList, bMain) {
            var a = JSON.parse(o.find('.db-access_action:eq(0)').text());
            for (var i in a) {
                var row = aList.find('[data-id="' + i + '"]').eq(0);
                if (bMain) {
                    self.aData = a;
                    row.removeClass('inactive');
                    row.find('input[name="allow"]').prop('checked', a[i][0]);
                    row.find('input[name="deny"]').prop('checked', a[i][1]);
                } else {
                    row.addClass('bg_highlight');
                    row.find('input[name="allow"]').prop('checked', a[i][0]).css('visibility', a[i][1] ? 'hidden' : 'visible').prop('disabled', a[i][0]);
                    row.find('input[name="deny"]').prop('checked', a[i][1]).prop('disabled', !a[i][0]).css('visibility', 'visible');
                }
            }
        }

        , saveUpdates: function () {
            if (self.iTarget !== -1) {
                var o = jQuery('.db-access[data-id="' + self.iTarget + '"] .db-access_action:eq(0)');
                var s = JSON.stringify(self.aData);
                if (o.html() !== s) {
                    for (var i in self.aData) {
                        if (typeof self.aChanges.access_action[ self.aData[i][2]] === 'undefined') {
                            self.aChanges.access_action[ self.aData[i][2] ] = {};
                        }
                        self.aChanges.access_action[ self.aData[i][2] ][i] = self.aData[i][0];
                    }
                }
                o.html(s);
            }

        }

        /**
         * Check full hierarchy of access groups
         */
        , saveHierarchy: function() {
            jQuery('.el_grid .db-access').each(function() {
                var o = jQuery(this);
                var oPrn = self.getParent(o, 'db-access');
                self.aChanges.access[o.data('id')] = {
                    title: o.data('title'),
                    access: oPrn ? oPrn.data('id') : null
                };
            });
        }
    };

    /**
     * External functionality
     *
     * @type {object}
     */
    return {
        /**
         * Get object name
         *
         * @returns {string}
         */
        getName: function () {
            return 'Modules/Dev/Access';
        }

        /**
         * Initiate form editor
         *
         * @returns {jQuery} oElem
         */
        , init: function (oElem) {
            jQuery('.db-access').each(function () {
                var o = jQuery(this);
                o.children().find('[data-type="edit"]').unbind('click').bind('click', function (event) {
                    Vaviorka.ui.stopPropagation(event);

                    if (self.iTarget !== -1) {
                        self.saveUpdates();
                    }

                    self.aData = {};
                    self.iTarget = o.data('id');
                    var aList = jQuery('.db-action');
                    // Style options
                    jQuery(this).closest('.el_grid').children().removeClass('hidden').trigger('change');
                    oElem.find('.active').removeClass('active');
                    jQuery('#access_name').html(jQuery(this).parent().addClass('active').find('b').text());
                    // List style
                    aList.find('.bg_highlight').removeClass('bg_highlight');
                    aList.find('[data-id]').addClass('inactive'); //.removeClass('bg_button');
                    aList.find('input[type="checkbox"]')
                            .prop('checked', false).prop('disabled', false)
                            .css('visibility', 'visible')
                            .bind('change', self.checkStatus);
                    aList.find('input[name="deny"]').css('visibility', 'hidden');
                    // Update list - {"1":[true,false,1],"2":[true,false,2]}
                    self.updateList(o, aList, true);
                    self.checkParent(o, aList);
                    return false;
                });
            });

            jQuery('.db-add').unbind('click').bind('click', function () {
                jQuery(this).closest('.list').append(jQuery('#add_new').html().split('ui-delay').join('ui'));
                jQuery(this).closest('.list').find('input:eq(0)').focus();
                Vaviorka.registry.final();

            });

            jQuery('.db-submit').unbind('click').bind('click', function () {
                self.saveUpdates();
                self.saveHierarchy();
                Vaviorka.registry.trigger('View/Animate/Loading', 'start', [jQuery(this)]);

                var oRequest = jQuery.ajax({
                    type: 'POST'
                    , url: window.location.href.split('#')[0].replace('.html', '') + '.json'
                    , data: self.aChanges
                    , success: function (sResponse) {
                        Vaviorka.registry.trigger('Response/Json', 'init', [sResponse]);
                    }
                    , error: function (XMLHttpRequest, textStatus, errorThrown) {
                        Vaviorka.registry.trigger('Response/Error', 'show', [XMLHttpRequest, textStatus, errorThrown]);
                    }
                });
                oRequest.always(function () {
                    Vaviorka.registry.final();
                });
            });
        }

        /**
         * Initiate line for a new access-title
         *
         * @returns {jQuery} oElem
         */
        , initNew: function (oElem) {
            oElem.bind('keypress', function(event) {
                if (event.which === 13) {
                    var sTitle = this.value.toUpperCase();
                    // Validate name
                    var a = (this.value).split(/\w/gi);
                    for (var i = 0; i < a.length; i++) {
                        if (a[i]) {
                            throw Error( 'Forbidden symbol(s): "' + s + '"' );
                        }
                    }
                    // Validate uniq
                    if (~self.aNewNames.indexOf(sTitle)) {
                        throw Error( 'Already existing identificator!' );
                    }
                    self.aNewNames.push(sTitle);
                    // Create new element
                    Vaviorka.registry.trigger('Modules/Dev/Access', 'create', [jQuery(this).closest('li'), sTitle]);
                }
            });
        }

        /**
         * Add new element into the list
         *
         * @param {jQuery} oElem
         * @param {string} sTitle
         */
        , create: function(oElem, sTitle) {
            var sName = 'LB_ACCESS_NEW_'+sTitle;
            self.iNew--;
            //self.aChanges.access[self.iNew] = {title: sName, access: oElem.closest('.db-access').data('id')};
            oElem.html(
                jQuery('#elem_new').html()
                    .split('ui-delay').join('ui')
                    .split('{ID}').join(self.iNew)
                    .split('{TITLE}').join(sName)
            );
            Vaviorka.registry.final();
            Vaviorka.registry.trigger('Modules/Dev/Access', 'init', [oElem.closest('section')]);

        }
    }

})(window.Vaviorka.query, window.Vaviorka));