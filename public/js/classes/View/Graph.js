/**
 * Prototype for all classes
 *
 * @name Proto
 */
window.Vaviorka.registry.include((function (Vaviorka) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        iNum: 0,

        languages: function () {
            var tr = new Vaviorka.model.translate();
            Highcharts.setOptions({
                lang: {
                    months: [
                        tr.tget('LB_MONTH_01')
                        , tr.tget('LB_MONTH_02')
                        , tr.tget('LB_MONTH_03')
                        , tr.tget('LB_MONTH_04')
                        , tr.tget('LB_MONTH_05')
                        , tr.tget('LB_MONTH_06')
                        , tr.tget('LB_MONTH_07')
                        , tr.tget('LB_MONTH_08')
                        , tr.tget('LB_MONTH_09')
                        , tr.tget('LB_MONTH_10')
                        , tr.tget('LB_MONTH_11')
                        , tr.tget('LB_MONTH_12')
                    ],
                    shortMonths: [
                        tr.tget('LB_MONTH_01_SHORT')
                        , tr.tget('LB_MONTH_02_SHORT')
                        , tr.tget('LB_MONTH_03_SHORT')
                        , tr.tget('LB_MONTH_04_SHORT')
                        , tr.tget('LB_MONTH_05_SHORT')
                        , tr.tget('LB_MONTH_06_SHORT')
                        , tr.tget('LB_MONTH_07_SHORT')
                        , tr.tget('LB_MONTH_08_SHORT')
                        , tr.tget('LB_MONTH_09_SHORT')
                        , tr.tget('LB_MONTH_10_SHORT')
                        , tr.tget('LB_MONTH_11_SHORT')
                        , tr.tget('LB_MONTH_12_SHORT')
                    ],
                    weekdays: [
                        tr.tget('LB_WEEK_01')
                        , tr.tget('LB_WEEK_02')
                        , tr.tget('LB_WEEK_03')
                        , tr.tget('LB_WEEK_04')
                        , tr.tget('LB_WEEK_05')
                        , tr.tget('LB_WEEK_06')
                        , tr.tget('LB_WEEK_07')
                    ]
                }
            });
        },

        config: {
            summary: {
                chart: {
                    type: 'column',
                    backgroundColor: 'rgba(0,0,0,0)'
                },
                title: {
                    text: ''
                },
                tooltip: {

                },
                credits: {
                    enabled: false
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    plotLines: []
                },
                xAxis: {
                    type: 'datetime'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        lineColor: '#666666',
                        lineWidth: 1,
                        marker: {
                            enabled: false
                        }
                    }
                },
                series: []
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
            return 'View/Graph';
        }

        /**
         * Graph with summary list
         * @param {jQuery} oElem
         */
        , summary: function (oElem) {
            var chart = self.config.summary;
            chart.yAxis.title.text = oElem.data('title');

            var data = JSON.parse(oElem.children(0).html());
            for (var i in data) {
                // Check horizontal line
                if (i < 0) {
                    chart.yAxis.plotLines.push(data[i]);
                // Add data
                } else {
                    chart.series.push(data[i]);
                }
            }

            self.languages();
            oElem.highcharts(chart);
        }
    };

})(window.Vaviorka));