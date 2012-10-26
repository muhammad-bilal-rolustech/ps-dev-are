/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: customSugarCharts.js 2010-12-01 23:11:36Z lhuynh $

function loadSugarChart (chartId, jsonFilename, css, chartConfig, params, callback) {
    this.chartObject = "";

    //Bug#45831
    if(document.getElementById(chartId) == null) {
        return false;
    }

    var labelType = 'Native',
        useGradients = false,
        animate = false,
        that = this,
        /**
         * the main container to render chart
         */
        contentEl = 'content',
        /**
         * with of one column to render bars
         */
        minColumnWidth = 40;

    params = params ? params : {};

    contentEl = params.contentEl || contentEl;
    minColumnWidth = params.minColumnWidth || minColumnWidth;

			switch(chartConfig["chartType"]) {
			case "barChart":
                SUGAR.charts.get(jsonFilename, params, function(data) {
                    if(SUGAR.charts.isDataEmpty(data)){
                        var json = data;
                        var properties = $jit.util.splat(data.properties)[0];
                        var marginBottom = (chartConfig["orientation"] == 'vertical' && data.values.length > 8) ? 20*4 : 20;

                        // Bug #49732 : Bars in charts overlapping
                        // if to many data to display fix canvas width and set up width to container to allow overflow
                        if ( chartConfig["orientation"] == 'vertical' )
                        {
                            function fixChartContainer(event, itemsCount)
                            {
                                var chartCanvas = YAHOO.util.Dom.getElementsByClassName('chartCanvas', 'div');
                                var chartContainer = YAHOO.util.Dom.getElementsByClassName('chartContainer', 'div');
                                var region = YAHOO.util.Dom.getRegion(contentEl);
                                if ( chartContainer.length > 0 && chartCanvas.length > 0 )
                                {
                                    if ( region && region.width )
                                    {
                                        // one bar needs about minColumnWidth px to correct display data and labels
                                        var realWidth = itemsCount * parseInt(minColumnWidth, 10);
                                        chartContainer = YAHOO.util.Dom.get(chartContainer[0]);
                                        chartCanvas = YAHOO.util.Dom.get(chartCanvas[0]);
                                        if ( realWidth > region.width )
                                        {
                                            YAHOO.util.Dom.setStyle(chartContainer, 'width', region.width+'px')
                                            YAHOO.util.Dom.setStyle(chartCanvas, 'width', realWidth+'px');
                                        }
                                        else
                                        {
                                            YAHOO.util.Dom.setStyle(chartContainer, 'width', region.width+'px')
                                            YAHOO.util.Dom.setStyle(chartCanvas, 'width', region.width+'px');
                                        }
                                    }
                                }
                                if (!event)
                                {
                                    YAHOO.util.Event.addListener(window, "resize", fixChartContainer, json.values.length);
                                }
                            }
                            fixChartContainer(null, json.values.length);
                        }

                        //init BarChart
                        var barChart = new $jit.BarChart({
                            //id of the visualization container
                            injectInto: chartId,
                            //whether to add animations
                            animate: animate,
                            nodeCount: data.values.length,
                            renderBackground: chartConfig['imageExportType'] == "jpg" ? true: false,
                            dataPointSize: chartConfig["dataPointSize"],
                            backgroundColor: 'rgb(255,255,255)',
                            colorStop1: 'rgba(255,255,255,.8)',
                            colorStop2: 'rgba(255,255,255,0)',
                            shadow: {
                                enable: false,
                                size: 2
                            },
                            //horizontal or vertical barcharts
                            orientation: chartConfig["orientation"],
                            hoveredColor: false,
                            Title: {
                                text: properties['title'],
                                size: 16,
                                color: '#444444',
                                offset: 20
                            },
                            Subtitle: {
                                text: properties['subtitle'],
                                size: 11,
                                color: css["color"],
                                offset: 20
                            },
                            Ticks: {
                                enable: true,
                                color: css["gridLineColor"]
                            },
                            //bars separation
                            barsOffset: (chartConfig["orientation"] == "vertical") ? 20 : 20,
                            //visualization offset
                            Margin: {
                                top:20,
                                left: 30,
                                right: 20,
                                bottom: marginBottom
                            },
                            ScrollNote: {
                                text: (chartConfig["scroll"] && $jit.util.isTouchScreen()) ? "Use two fingers to scroll" : "",
                                size: 12
                            },
                            Events: {
                                enable: true,
                                onClick: function(node) {
                                    if(!node || $jit.util.isTouchScreen()) return;
                                    if(node.link == undefined || node.link == '') return;
                                    window.location.href=node.link;
                                }
                            },
                            //labels offset position
                            labelOffset: 5,
                            //bars style
                            type: useGradients? chartConfig["barType"]+':gradient' : chartConfig["barType"],
                            //whether to show the aggregation of the values
                            showAggregates: (chartConfig["showAggregates"] != undefined) ? chartConfig["showAggregates"] : true,
                            showNodeLabels: (chartConfig["showNodeLabels"] != undefined) ? chartConfig["showNodeLabels"] : true,
                            segmentStacked: (chartConfig["segmentStacked"] != undefined) ? chartConfig["segmentStacked"] : false,
                            //whether to show the labels for the bars
                            showLabels:true,
                            //labels style
                            Label: {
                                type: labelType, //Native or HTML
                                size: 12,
                                family: css["font-family"],
                                color: css["color"],
                                colorAlt: "#ffffff"
                            },
                            //add tooltips
                            Tips: {
                                enable: true,
                                onShow: function(tip, elem) {

                                    if(elem.type == 'marker') {
                                        tip.innerHTML = '<b>' + elem.name + '</b>: ' + elem.valuelabel ;
                                    } else {
                                        if(elem.link != 'undefined' && elem.link != '') {
                                            drillDown = ($jit.util.isTouchScreen()) ? "<br><a href='"+ elem.link +"'>Click to drilldown</a>" : "<br>Click to drilldown";
                                        } else {
                                            drillDown = "";
                                        }

                                        if(elem.valuelabel != 'undefined' && elem.valuelabel != undefined && elem.valuelabel != '') {
                                            value = "elem.valuelabel";
                                        } else {
                                            value = "elem.value";
                                        }

                                        if(properties.label_name != "undefined" && properties.label_name != "") {
                                            eval("tip.innerHTML = properties.label_name + ': <b>' + elem."+chartConfig["tip"]+" + '</b><br> '+properties.value_name+': <b>' + "+value+" + '</b>' + drillDown");
                                        } else {
                                            eval("tip.innerHTML = '<b>' + elem."+chartConfig["tip"]+" + '</b>: ' + "+value+" + ' - ' + elem.percentage + '%' + drillDown");
                                        }
                                    }
                                }
                            }
                        });
                        //load JSON data.
                        barChart.loadJSON(data);

                        var list = SUGAR.charts.generateLegend(barChart, chartId);

                        //save canvas to image for pdf consumption
                        $jit.util.saveImageTest(chartId,jsonFilename,chartConfig["imageExportType"],chartConfig['saveImageTo']);

                        SUGAR.charts.trackWindowResize(barChart, chartId, data);
                        barChart.json = json;
                        that.chartObject = barChart;

                    }
                    SUGAR.charts.callback(callback);
                });

				break;
				
			case "lineChart":
                SUGAR.charts.get(jsonFilename, params, function(data) {
                    if(SUGAR.charts.isDataEmpty(data)){
                        var properties = $jit.util.splat(data.properties)[0];
                        //init Linecahrt
                        var lineChart = new $jit.LineChart({
                            //id of the visualization container
                            injectInto: chartId,
                            //whether to add animations
                            animate: animate,
                            renderBackground: chartConfig['imageExportType'] == "jpg" ? true: false,
                            backgroundColor: 'rgb(255,255,255)',
                            colorStop1: 'rgba(255,255,255,.8)',
                            colorStop2: 'rgba(255,255,255,0)',
                            selectOnHover: false,
                            Title: {
                                text: properties['title'],
                                size: 16,
                                color: '#444444',
                                offset: 20
                            },
                            Subtitle: {
                                text: properties['subtitle'],
                                size: 11,
                                color: css["color"],
                                offset: 20
                            },
                            Ticks: {
                                enable: true,
                                color: css["gridLineColor"]
                            },
                            //visualization offset
                            Margin: {
                                top:20,
                                left: 40,
                                right: 40,
                                bottom: 20
                            },
                            Events: {
                                enable: true,
                                onClick: function(node) {
                                    if(!node || $jit.util.isTouchScreen()) return;
                                    if(node.link == 'undefined' || node.link == '') return;
                                    window.location.href=node.link;
                                }
                            },
                            //labels offset position
                            labelOffset: 5,
                            //bars style
                            type: useGradients? chartConfig["lineType"]+':gradient' : chartConfig["lineType"],
                            //whether to show the aggregation of the values
                            showAggregates:true,
                            //whether to show the labels for the bars
                            showLabels:true,
                            //labels style
                            Label: {
                                type: labelType, //Native or HTML
                                size: 12,
                                family: css["font-family"],
                                color: css["color"],
                                colorAlt: "#ffffff"
                            },
                            //add tooltips
                            Tips: {
                                enable: true,
                                onShow: function(tip, elem) {
                                    if(elem.link != 'undefined' && elem.link != '') {
                                        drillDown = ($jit.util.isTouchScreen()) ? "<br><a href='"+ elem.link +"'>Click to drilldown</a>" : "<br>Click to drilldown";
                                    } else {
                                        drillDown = "";
                                    }

                                    if(elem.valuelabel != 'undefined' && elem.valuelabel != undefined && elem.valuelabel != '') {
                                        var value = "elem.valuelabel";
                                    } else {
                                        var value = "elem.value";
                                    }

                                    if(elem.collision) {
                                        eval("var name = elem."+chartConfig["tip"]+";");
                                        var content = '<table>';

                                        for(var i=0; i<name.length; i++) {
                                            content += '<tr><td><b>' + name[i] + '</b>:</td><td> ' + elem.value[i] + ' - ' + elem.percentage[i] + '%' + '</td></tr>';
                                        }
                                        content += '</table>';
                                        tip.innerHTML = content;
                                    } else {
                                        eval("tip.innerHTML = '<b>' + elem."+chartConfig["tip"]+" + '</b>: ' + "+value+" + ' - ' + elem.percentage + '%' + drillDown");
                                    }
                                }
                            }
                        });
                        //load JSON data.
                        lineChart.loadJSON(data);
                        //end

                        /*
                         var list = $jit.id('id-list'),
                         button = $jit.id('update'),
                         orn = $jit.id('switch-orientation');
                         //update json on click 'Update Data'
                         $jit.util.addEvent(button, 'click', function() {
                         var util = $jit.util;
                         if(util.hasClass(button, 'gray')) return;
                         util.removeClass(button, 'white');
                         util.addClass(button, 'gray');
                         barChart.updateJSON(json2);
                         });
                         */
                        //dynamically add legend to list

                        var list = SUGAR.charts.generateLegend(lineChart, chartId);


                        //save canvas to image for pdf consumption
                        $jit.util.saveImageTest(chartId,jsonFilename,chartConfig["imageExportType"]);

                        SUGAR.charts.trackWindowResize(lineChart, chartId, data);
                        that.chartObject = lineChart;
                    }
                    SUGAR.charts.callback(callback);
                });

                break;

			case "pieChart":
                SUGAR.charts.get(jsonFilename, params, function(data) {
                    if(SUGAR.charts.isDataEmpty(data)){
                        var properties = $jit.util.splat(data.properties)[0];

                        //init BarChart
                        var pieChart = new $jit.PieChart({
                            //id of the visualization container
                            injectInto: chartId,
                            //whether to add animations
                            animate: animate,
                            renderBackground: chartConfig['imageExportType'] == "jpg" ? true: false,
                            backgroundColor: 'rgb(255,255,255)',
                            colorStop1: 'rgba(255,255,255,.8)',
                            colorStop2: 'rgba(255,255,255,0)',
                            labelType: properties['labels'],
                            hoveredColor: false,
                            //offsets
                            offset: 50,
                            sliceOffset: 0,
                            labelOffset: 30,
                            //slice style
                            type: useGradients? chartConfig["pieType"]+':gradient' : chartConfig["pieType"],
                            //whether to show the labels for the slices
                            showLabels:true,
                            Title: {
                                text: properties['title'],
                                size: 16,
                                color: '#444444',
                                offset: 20
                            },
                            Subtitle: {
                                text: properties['subtitle'],
                                size: 11,
                                color: css["color"],
                                offset: 20
                            },
                            Margin: {
                                top:20,
                                left: 20,
                                right: 20,
                                bottom: 20
                            },
                            Events: {
                                enable: true,
                                onClick: function(node) {
                                    if(!node || $jit.util.isTouchScreen()) return;
                                    if(node.link == 'undefined' || node.link == '') return;
                                    window.location.href=node.link;
                                }
                            },
                            //label styling
                            Label: {
                                type: labelType, //Native or HTML
                                size: 12,
                                family: css["font-family"],
                                color: css["color"]
                            },
                            //enable tips
                            Tips: {
                                enable: true,
                                onShow: function(tip, elem) {
                                    if(elem.link != 'undefined' && elem.link != '') {
                                        drillDown = ($jit.util.isTouchScreen()) ? "<br><a href='"+ elem.link +"'>Click to drilldown</a>" : "<br>Click to drilldown";
                                    } else {
                                        drillDown = "";
                                    }

                                    if(elem.valuelabel != 'undefined' && elem.valuelabel != undefined && elem.valuelabel != '') {
                                        value = "elem.valuelabel";
                                    } else {
                                        value = "elem.value";
                                    }
                                    eval("tip.innerHTML = '<b>' + elem.label + '</b>: ' + "+ value +" + ' - ' + elem.percentage + '%' + drillDown");
                                }
                            }
                        });
                        //load JSON data.
                        pieChart.loadJSON(data);
                        //end
                        //dynamically add legend to list
                        var list = SUGAR.charts.generateLegend(pieChart, chartId);


                        //save canvas to image for pdf consumption
                        $jit.util.saveImageTest(chartId,jsonFilename,chartConfig["imageExportType"]);

                        SUGAR.charts.trackWindowResize(pieChart, chartId, data);
                        that.chartObject = pieChart;
                    }
                    SUGAR.charts.callback(callback);
                });

				break;

			case "funnelChart":
                SUGAR.charts.get(jsonFilename, params, function(data) {
                    if(SUGAR.charts.isDataEmpty(data)){
                        var properties = $jit.util.splat(data.properties)[0];

                        //init Funnel Chart
                        var funnelChart = new $jit.FunnelChart({
                            //id of the visualization container
                            injectInto: chartId,
                            //whether to add animations
                            animate: animate,
                            renderBackground: chartConfig['imageExportType'] == "jpg" ? true: false,
                            backgroundColor: 'rgb(255,255,255)',
                            colorStop1: 'rgba(255,255,255,.8)',
                            colorStop2: 'rgba(255,255,255,0)',
                            //orientation setting should not be changed
                            orientation: "vertical",
                            hoveredColor: false,
                            Title: {
                                text: properties['title'],
                                size: 16,
                                color: '#444444',
                                offset: 20
                            },
                            Subtitle: {
                                text: properties['subtitle'],
                                size: 11,
                                color: css["color"],
                                offset: 20
                            },
                            //segment separation
                            segmentOffset: 20,
                            //visualization offset
                            Margin: {
                                top:20,
                                left: 20,
                                right: 20,
                                bottom: 20
                            },
                            Events: {
                                enable: true,
                                onClick: function(node) {
                                    if(!node || $jit.util.isTouchScreen()) return;
                                    if(node.link == 'undefined' || node.link == '') return;
                                    window.location.href=node.link;
                                }
                            },
                            //labels offset position
                            labelOffset: 10,
                            //bars style
                            type: useGradients? chartConfig["funnelType"]+':gradient' : chartConfig["funnelType"],
                            //whether to show the aggregation of the values
                            showAggregates:true,
                            //whether to show the labels for the bars
                            showLabels:true,
                            //labels style
                            Label: {
                                type: labelType, //Native or HTML
                                size: 12,
                                family: css["font-family"],
                                color: css["color"],
                                colorAlt: "#ffffff"
                            },
                            //add tooltips
                            Tips: {
                                enable: true,
                                onShow: function(tip, elem) {
                                    if(elem.link != 'undefined' && elem.link != '') {
                                        drillDown = ($jit.util.isTouchScreen()) ? "<br><a href='"+ elem.link +"'>Click to drilldown</a>" : "<br>Click to drilldown";
                                    } else {
                                        drillDown = "";
                                    }

                                    if(elem.valuelabel != 'undefined' && elem.valuelabel != undefined && elem.valuelabel != '') {
                                        value = "elem.valuelabel";
                                    } else {
                                        value = "elem.value";
                                    }
                                    eval("tip.innerHTML = '<b>' + elem."+chartConfig["tip"]+" + '</b>: ' + "+value+"  + ' - ' + elem.percentage + '%' +  drillDown");
                                }
                            }
                        });
                        //load JSON data.
                        funnelChart.loadJSON(data);
                        //end

                        /*
                         var list = $jit.id('id-list'),
                         button = $jit.id('update'),
                         orn = $jit.id('switch-orientation');
                         //update json on click 'Update Data'
                         $jit.util.addEvent(button, 'click', function() {
                         var util = $jit.util;
                         if(util.hasClass(button, 'gray')) return;
                         util.removeClass(button, 'white');
                         util.addClass(button, 'gray');
                         barChart.updateJSON(json2);
                         });
                         */
                        //dynamically add legend to list
                        var list = SUGAR.charts.generateLegend(funnelChart, chartId);

                        //save canvas to image for pdf consumption
                        $jit.util.saveImageTest(chartId,jsonFilename,chartConfig["imageExportType"]);

                        SUGAR.charts.trackWindowResize(funnelChart, chartId, data);
                        that.chartObject = funnelChart;
                    }
                    SUGAR.charts.callback(callback);
                });

				break;

			case "gaugeChart":
                SUGAR.charts.get(jsonFilename, params, function(data) {
                    if(SUGAR.charts.isDataEmpty(data)){
                        var properties = $jit.util.splat(data.properties)[0];

                        //init Gauge Chart
                        var gaugeChart = new $jit.GaugeChart({
                            //id of the visualization container
                            injectInto: chartId,
                            //whether to add animations
                            animate: animate,
                            renderBackground: chartConfig['imageExportType'] == "jpg" ? true: false,
                            backgroundColor: 'rgb(255,255,255)',
                            colorStop1: 'rgba(255,255,255,.8)',
                            colorStop2: 'rgba(255,255,255,0)',
                            labelType: properties['labels'],
                            hoveredColor: false,
                            Title: {
                                text: properties['title'],
                                size: 16,
                                color: '#444444',
                                offset: 20
                            },
                            Subtitle: {
                                text: properties['subtitle'],
                                size: 11,
                                color: css["color"],
                                offset: 5
                            },
                            //offsets
                            offset: 20,
                            gaugeStyle: {
                                backgroundColor: '#aaaaaa',
                                borderColor: '#999999',
                                needleColor: 'rgba(255,0,0,.8)',
                                borderSize: 4,
                                positionFontSize: 24,
                                positionOffset: 2
                            },
                            //slice style
                            type: useGradients? chartConfig["gaugeType"]+':gradient' : chartConfig["gaugeType"],
                            //whether to show the labels for the slices
                            showLabels:true,
                            Events: {
                                enable: true,
                                onClick: function(node) {
                                    if(!node || $jit.util.isTouchScreen()) return;
                                    if(node.link == 'undefined' || node.link == '') return;
                                    window.location.href=node.link;
                                }
                            },
                            //label styling
                            Label: {
                                type: labelType, //Native or HTML
                                size: 12,
                                family: css["font-family"],
                                color: css["color"]
                            },
                            //enable tips
                            Tips: {
                                enable: true,
                                onShow: function(tip, elem) {
                                    if(elem.link != 'undefined' && elem.link != '') {
                                        drillDown = ($jit.util.isTouchScreen()) ? "<br><a href='"+ elem.link +"'>Click to drilldown</a>" : "<br>Click to drilldown";
                                    } else {
                                        drillDown = "";
                                    }
                                    if(elem.valuelabel != 'undefined' && elem.valuelabel != undefined && elem.valuelabel != '') {
                                        value = "elem.valuelabel";
                                    } else {
                                        value = "elem.value";
                                    }
                                    eval("tip.innerHTML = '<b>' + elem.label + '</b>: ' + "+ value +" + drillDown");
                                }
                            }
                        });
                        //load JSON data.
                        gaugeChart.loadJSON(data);

                        var list = SUGAR.charts.generateLegend(gaugeChart, chartId);

                        //save canvas to image for pdf consumption
                        $jit.util.saveImageTest(chartId,jsonFilename,chartConfig["imageExportType"]);

                        SUGAR.charts.trackWindowResize(gaugeChart, chartId, data);
                        that.chartObject = gaugeChart;
                    }
                    SUGAR.charts.callback(callback);
                });

				break;

			}
		}

function updateChart(jsonFilename, chart, params) {
    params = params ? params : {};
    SUGAR.charts.get(jsonFilename, params, function(data) {
        if(SUGAR.charts.isDataEmpty(data)){
            chart.busy = false;
            chart.updateJSON(data);
        }
    });
}

function swapChart(chartId,jsonFilename,css,chartConfig){
    $("#"+chartId).empty();
    $("#legend"+chartId).empty();
    $("#tiptip_holder").empty();
    var chart = new loadSugarChart(chartId,jsonFilename,css,chartConfig);
    return chart;

}

/**
 * As you touch the code above, migrate the code to use the pattern below.
 */
(function($) {

    if (typeof SUGAR == "undefined" || !SUGAR) {
        SUGAR = {};
    }
    SUGAR.charts = {

        chart : null,
        /**
         * Execute callback function if specified
         *
         * @param callback
         */
        callback: function(callback) {
            if (callback) {
                // if the call back is fired, include the chart as the only param
                callback(this.chart);
            }
        },

        /**
         * Handle the Legend Generation
         *
         * @param chart
         * @param chartId
         * @return {*}
         */
        generateLegend: function(chart, chartId) {
            var list = $jit.id('legend'+chartId);
            var legend = chart.getLegend(),
                table = "<div class='col'>";
            for(var i=0;i<legend['name'].length;i++) {
                if(legend["name"][i] != undefined) {
                    table += "<div class='legendGroup'>";
                    table += '<div class=\'query-color\' style=\'background-color:'
                        + legend["color"][i] +'\'></div>';
                    table += '<div class=\'label\'>';
                    table += legend["name"][i];
                    table += '</div>';
                    table += "</div>";
                }
            }

            table += "</div>";


            if(legend['wmlegend'] != "undefined") {

                table += "<div class='col2'>";
                for(var i=0;i<legend['wmlegend']['name'].length;i++) {
                    table += "<div class='legendGroup'>";
                    table += "<div class='waterMark  "+ legend["wmlegend"]['type'][i] +"' style='background-color: "+ legend["wmlegend"]['color'][i] +";'></div>";
                    table += "<div class='label'>"+ legend["wmlegend"]['name'][i] +"</div>";
                    table += "</div>";
                }
                table += "</div>";

            }

            list.innerHTML = table;


            //adjust legend width to chart width
            jQuery('#legend'+chartId).ready(function() {
                var chartWidth = jQuery('#'+chartId).width();
                chartWidth = chartWidth - 20;
                $('#legend'+chartId).width(chartWidth);
                var legendGroupWidth = new Array();
                $('.col .legendGroup').each(function(index) {
                    legendGroupWidth[index] = $(this).width();
                });
                var largest = Math.max.apply(Math, legendGroupWidth);
                $('.col .legendGroup').width(largest+2);
            });



            return list;
        },

        /**
         * Calls the server to retrieve chart data
         *
         * @param url - target url
         * @param param - object of parameters to pass to the server
         * @param success - callback function to be executed after a successful call
         */
        get: function(url, params, success) {
            var data = {
                r: new Date().getTime()
            };
            $.extend(data, params);

            $.ajax({
                url: url,
                data: data,
                dataType: 'json',
                async: false,
                success: success
            });
        },

        /**
         * Is data returned from the server empty?
         *
         * @param data
         * @return {Boolean}
         */
        isDataEmpty: function(data) {
            if (data !== undefined && data !== "No Data" && data !== "") {
                return true;
            } else {
                return false;
            }
        },

        /**
         * Resize graph on window resize
         *
         * @param chart
         * @param chartId
         * @param json
         */
        trackWindowResize: function(chart, chartId, json) {
            var timeout,
                delay = 500,
                origWindowWidth = document.documentElement.scrollWidth,
                container = document.getElementById(chartId),
                widget = document.getElementById(chartId + "-canvaswidget");

            // refresh graph on window resize
            $(window).resize(function() {
                if (timeout) {
                    clearTimeout(timeout);
                }

                timeout = setTimeout(function() {
                    var newWindowWidth = document.documentElement.scrollWidth;

                    // if window width has changed during resize
                    if (newWindowWidth != origWindowWidth) {
                        // hide widget in order to let it's container have
                        // width corresponding to current window size,
                        // not it's contents
                        widget.style.display = "none";

                        // add one more timeout in order to let all widgets
                        // on the page hide
                        setTimeout(function() {
                            // measure container width
                            var width = container.offsetWidth;
                            var chartWidth = width - 20;
                            $('#legend'+chartId).width(chartWidth);

                            // display widget before resize, otherwise
                            // it will be rendered incorrectly in IE
                            widget.style.display = "";

                            chart.resizeGraph(json, width);
                            origWindowWidth = newWindowWidth;
                        }, 0);
                    }
                }, delay);
            });
        },

        /**
         * Update chart with new data from server
         *
         * @param chart
         * @param url
         * @param params
         * @param callback
         */
        update: function(chart, url, params, callback) {
            var self = this;
            params = params ? params : {};
            self.chart = chart;
            this.get(url, params, function(data) {
                if(self.isDataEmpty(data)){
                    self.chart.busy = false;
                    self.chart.updateJSON(data);
                    self.callback(callback);
                }
            });
        }
    }
})(jQuery);
