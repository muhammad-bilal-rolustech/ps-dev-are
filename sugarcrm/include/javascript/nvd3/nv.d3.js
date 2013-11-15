(function(){

var nv = window.nv || {};

nv.version = '0.0.1a';
nv.dev = false; //set false when in production

window.nv = nv;

nv.tooltip = {}; // For the tooltip system
nv.utils = {}; // Utility subsystem
nv.models = {}; //stores all the possible models/components
nv.charts = {}; //stores all the ready to use charts
nv.graphs = []; //stores all the graphs currently on the page
nv.logs = {}; //stores some statistics and potential error messages

nv.dispatch = d3.dispatch('render_start', 'render_end');

// *************************************************************************
//  Development render timers - disabled if dev = false

if (nv.dev) {
  nv.dispatch.on('render_start', function(e) {
    nv.logs.startTime = +new Date();
  });

  nv.dispatch.on('render_end', function(e) {
    nv.logs.endTime = +new Date();
    nv.logs.totalTime = nv.logs.endTime - nv.logs.startTime;
    nv.log('total', nv.logs.totalTime); // used for development, to keep track of graph generation times
  });
}

// ********************************************
//  Public Core NV functions

// Logs all arguments, and returns the last so you can test things in place
nv.log = function() {
  if (nv.dev && console.log && console.log.apply)
    console.log.apply(console, arguments)
  else if (nv.dev && console.log && Function.prototype.bind) {
    var log = Function.prototype.bind.call(console.log, console);
    log.apply(console, arguments);
  }
  return arguments[arguments.length - 1];
};


nv.render = function render(step) {
  step = step || 1; // number of graphs to generate in each timeout loop

  nv.render.active = true;
  nv.dispatch.render_start();

  setTimeout(function() {
    var chart, graph;

    for (var i = 0; i < step && (graph = nv.render.queue[i]); i++) {
      chart = graph.generate();
      if (typeof graph.callback == typeof(Function)) graph.callback(chart);
      nv.graphs.push(chart);
    }

    nv.render.queue.splice(0, i);

    if (nv.render.queue.length) setTimeout(arguments.callee, 0);
    else { nv.render.active = false; nv.dispatch.render_end(); }
  }, 0);
};

nv.render.active = false;
nv.render.queue = [];

nv.addGraph = function(obj) {
  if (typeof arguments[0] === typeof(Function))
    obj = {generate: arguments[0], callback: arguments[1]};

  nv.render.queue.push(obj);

  if (!nv.render.active) nv.render();
};

nv.identity = function(d) { return d; };

nv.strip = function(s) { return s.replace(/(\s|&)/g,''); };

function daysInMonth(month,year) {
  return (new Date(year, month+1, 0)).getDate();
}

function d3_time_range(floor, step, number) {
  return function(t0, t1, dt) {
    var time = floor(t0), times = [];
    if (time < t0) step(time);
    if (dt > 1) {
      while (time < t1) {
        var date = new Date(+time);
        if ((number(date) % dt === 0)) times.push(date);
        step(time);
      }
    } else {
      while (time < t1) { times.push(new Date(+time)); step(time); }
    }
    return times;
  };
}

d3.time.monthEnd = function(date) {
  return new Date(date.getFullYear(), date.getMonth(), 0);
};

d3.time.monthEnds = d3_time_range(d3.time.monthEnd, function(date) {
    date.setUTCDate(date.getUTCDate() + 1);
    date.setDate(daysInMonth(date.getMonth() + 1, date.getFullYear()));
  }, function(date) {
    return date.getMonth();
  }
);

function d3_scale_quantile(domain, range) {
  var thresholds;
  function rescale() {
    var k = 0, q = range.length;
    thresholds = [];
    while (++k < q) thresholds[k - 1] = d3.quantile(domain, k / q);
    return scale;
  }
  function scale(x) {
    if (isNaN(x = +x)) return NaN;
    return range[d3.bisect(thresholds, x)];
  }
  scale.domain = function(x) {
    if (!arguments.length) return domain;
    domain = x.filter(function(d) {
      return !isNaN(d);
    }).sort(d3.ascending);
    return rescale();
  };
  scale.range = function(x) {
    if (!arguments.length) return range;
    range = x;
    return rescale();
  };
  //added to support ticks for quantile scale - hhr:8/30/2012
  scale.ticks = function(m) {
    return d3_scale_linearTicks(scale.domain(), m);
  };
  scale.quantiles = function() {
    return thresholds;
  };
  scale.copy = function() {
    return d3_scale_quantile(domain, range);
  };
  return rescale();
}
d3.scale.quantile = function() {
  return d3_scale_quantile([], []);
};

/*****
 * A no frills tooltip implementation.
 *****/


(function() {

  var nvtooltip = window.nv.tooltip = {};

  nvtooltip.show = function(pos, content, gravity, dist, parentContainer, classes) {

    var container = document.createElement('div'),
        inner = document.createElement('div'),
        arrow = document.createElement('div'),
        body = document.getElementsByTagName('body')[0];

    gravity = gravity || 's';
    dist = dist || 10;

    inner.className = 'tooltip-inner';
    arrow.className = 'tooltip-arrow';
    inner.innerHTML = content;
    container.style.left = 0;
    container.style.top = -1000;
    container.style.opacity = 0;
    container.className = 'tooltip xy-tooltip in';

    container.appendChild(inner);
    container.appendChild(arrow);
    body.appendChild(container);

    nvtooltip.position(container,pos,gravity,dist);
    container.style.opacity = 1;

    return container;
  };

  nvtooltip.cleanup = function() {

      // Find the tooltips, mark them for removal by this class (so others cleanups won't find it)
      var tooltips = document.getElementsByClassName('tooltip');
      var purging = [];
      while(tooltips.length) {
        purging.push(tooltips[0]);
        tooltips[0].style.transitionDelay = '0 !important';
        tooltips[0].style.opacity = 0;
        tooltips[0].className = 'nvtooltip-pending-removal out';
      }

      setTimeout(function() {

          while (purging.length) {
             var removeMe = purging.pop();
              removeMe.parentNode.removeChild(removeMe);
          }
      }, 500);
  };

  nvtooltip.position = function(container,pos,gravity,dist) {
    var body = document.getElementsByTagName('body')[0];
    gravity = gravity || 's';
    dist = dist || 10;

    var height = parseInt(container.offsetHeight,10),
        width = parseInt(container.offsetWidth,10),
        windowWidth = nv.utils.windowSize().width,
        windowHeight = nv.utils.windowSize().height,
        scrollTop = body.scrollTop,
        scrollLeft = body.scrollLeft,
        class_name = container.className.replace(/ top| right| bottom| left/g,''),
        left, top;

    function alignCenter() {
      var left = pos[0] - (width / 2);
      if (left < scrollLeft) left = scrollLeft + 5;
      if (left + width > windowWidth) left = windowWidth - width - 5;
      return left;
    }
    function alignMiddle() {
      var top = pos[1] - (height / 2);
      if (top < scrollTop) top = scrollTop + 5;
      if (top + height > scrollTop + windowHeight) top = scrollTop - height - 5;
      return top;
    }

    switch (gravity) {
      case 'e':
        top = alignMiddle();
        left = pos[0] - width - dist;
        if (left < scrollLeft) {
          left = pos[0] + dist;
          class_name += ' right';
        } else {
          class_name += ' left';
        }
        break;
      case 'w':
        top = alignMiddle();
        left = pos[0] + dist;
        if (left + width > windowWidth) {
          left = pos[0] - width - dist;
          class_name += ' left';
        } else {
          class_name += ' right';
        }
        break;
      case 'n':
        left = alignCenter();
        top = pos[1] + dist;
        if (top + height > scrollTop + windowHeight) {
          top = pos[1] - height - dist;
          class_name += ' top';
        } else {
          class_name += ' bottom';
        }
        break;
      case 's':
        left = alignCenter();
        top = pos[1] - height - dist;
        if (scrollTop > top) {
          top = pos[1] + 10;
          class_name += ' bottom';
        } else {
          class_name += ' top';
        }
        break;
    }

    container.style.left = left + 'px';
    container.style.top = top + 'px';

    container.className = class_name;
  };

})();

nv.utils.windowSize = function () {
    // Sane defaults
    var size = {width: 640, height: 480};

    // Earlier IE uses Doc.body
    if (document.body && document.body.offsetWidth) {
        size.width = document.body.offsetWidth;
        size.height = document.body.offsetHeight;
    }

    // IE can use depending on mode it is in
    if (document.compatMode === 'CSS1Compat' &&
        document.documentElement &&
        document.documentElement.offsetWidth ) {
        size.width = document.documentElement.offsetWidth;
        size.height = document.documentElement.offsetHeight;
    }

    // Most recent browsers use
    if (window.innerWidth && window.innerHeight) {
        size.width = window.innerWidth;
        size.height = window.innerHeight;
    }
    return (size);
};

// Easy way to bind multiple functions to window.onresize
// TODO: give a way to remove a function after its bound, other than removing alkl of them
// nv.utils.windowResize = function (fun)
// {
//   var oldresize = window.onresize;

//   window.onresize = function (e) {
//     if (typeof oldresize == 'function') oldresize(e);
//     fun(e);
//   }
// }

nv.utils.windowResize = function (fun) {
  if (window.attachEvent) {
      window.attachEvent('onresize', fun);
  }
  else if (window.addEventListener) {
      window.addEventListener('resize', fun, true);
  }
  else {
      //The browser does not support Javascript event binding
  }
};

nv.utils.windowUnResize = function (fun) {
  if (window.detachEvent) {
      window.detachEvent('onresize', fun);
  }
  else if (window.removeEventListener) {
      window.removeEventListener('resize', fun, true);
  }
  else {
      //The browser does not support Javascript event binding
  }
};

nv.utils.resizeOnPrint = function (fn) {
    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function (mql) {
            if (mql.matches) {
                fn();
            }
        });
    } else if (window.attachEvent) {
      window.attachEvent("onbeforeprint", fn);
    } else {
      window.onbeforeprint = fn;
    }
    //TODO: allow for a second call back to undo using
    //window.attachEvent("onafterprint", fn);
};

nv.utils.unResizeOnPrint = function (fn) {
    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.removeListener(function (mql) {
            if (mql.matches) {
                fn();
            }
        });
    } else if (window.detachEvent) {
      window.detachEvent("onbeforeprint", fn);
    } else {
      window.onbeforeprint = null;
    }
};

// Backwards compatible way to implement more d3-like coloring of graphs.
// If passed an array, wrap it in a function which implements the old default
// behavior
nv.utils.getColor = function (color) {
    if (!arguments.length) { return nv.utils.defaultColor(); } //if you pass in nothing, get default colors back

    if (Object.prototype.toString.call( color ) === '[object Array]') {
        return function (d, i) { return d.color || color[i % color.length]; };
    } else {
        return color;
        //can't really help it if someone passes rubbish as color
    }
};

// Default color chooser uses the index of an object as before.
nv.utils.defaultColor = function () {
    var colors = d3.scale.category20().range();
    return function (d, i) { return d.color || colors[i % colors.length]; };
};


// Returns a color function that takes the result of 'getKey' for each series and
// looks for a corresponding color from the dictionary,
nv.utils.customTheme = function (dictionary, getKey, defaultColors) {
  getKey = getKey || function (series) { return series.key; }; // use default series.key if getKey is undefined
  defaultColors = defaultColors || d3.scale.category20().range(); //default color function

  var defIndex = defaultColors.length; //current default color (going in reverse)

  return function (series, index) {
    var key = getKey(series);

    if (!defIndex) defIndex = defaultColors.length; //used all the default colors, start over

    if (typeof dictionary[key] !== "undefined") {
      return (typeof dictionary[key] === "function") ? dictionary[key]() : dictionary[key];
    } else {
      return defaultColors[--defIndex]; // no match in dictionary, use default color
    }
  };
};



// From the PJAX example on d3js.org, while this is not really directly needed
// it's a very cool method for doing pjax, I may expand upon it a little bit,
// open to suggestions on anything that may be useful
nv.utils.pjax = function (links, content) {
  d3.selectAll(links).on("click", function () {
    history.pushState(this.href, this.textContent, this.href);
    load(this.href);
    d3.event.preventDefault();
  });

  function load(href) {
    d3.html(href, function (fragment) {
      var target = d3.select(content).node();
      target.parentNode.replaceChild(d3.select(fragment).select(content).node(), target);
      nv.utils.pjax(links, content);
    });
  }

  d3.select(window).on("popstate", function () {
    if (d3.event.state) { load(d3.event.state); }
  });
};

//SUGAR ADDITIONS

//gradient color
nv.utils.colorLinearGradient = function (d, i, p, c, defs) {
  var id = 'lg_gradient_' + i
    , grad = defs.select('#' + id);
  if ( grad.empty() ) {
    if (p.position === 'middle')
    {
      nv.utils.createLinearGradient( id, p, defs, [
        { 'offset': '0%',  'stop-color': d3.rgb(c).darker().toString(),  'stop-opacity': 1 },
        { 'offset': '20%', 'stop-color': d3.rgb(c).toString(), 'stop-opacity': 1 },
        { 'offset': '50%', 'stop-color': d3.rgb(c).brighter().toString(), 'stop-opacity': 1 },
        { 'offset': '80%', 'stop-color': d3.rgb(c).toString(), 'stop-opacity': 1 },
        { 'offset': '100%','stop-color': d3.rgb(c).darker().toString(),  'stop-opacity': 1 }
      ]);
    }
    else
    {
      nv.utils.createLinearGradient( id, p, defs, [
        { 'offset': '0%',  'stop-color': d3.rgb(c).darker().toString(),  'stop-opacity': 1 },
        { 'offset': '50%', 'stop-color': d3.rgb(c).toString(), 'stop-opacity': 1 },
        { 'offset': '100%','stop-color': d3.rgb(c).brighter().toString(), 'stop-opacity': 1 }
      ]);
    }
  }
  return 'url(#'+ id +')';
};

// defs:definition container
// id:dynamic id for arc
// radius:outer edge of gradient
// stops: an array of attribute objects
nv.utils.createLinearGradient = function (id, params, defs, stops) {
  var x2 = params.orientation === 'horizontal' ? '0%' : '100%'
    , y2 = params.orientation === 'horizontal' ? '100%' : '0%'
    , grad = defs.append('linearGradient')
        .attr('id', id)
        .attr('x1', '0%')
        .attr('y1', '0%')
        .attr('x2', x2 )
        .attr('y2', y2 )
        //.attr('gradientUnits', 'userSpaceOnUse')objectBoundingBox
        .attr('spreadMethod', 'pad');
  for (var i=0; i<stops.length; i+=1)
  {
    var attrs = stops[i]
      , stop = grad.append('stop');
    for (var a in attrs)
    {
      if ( attrs.hasOwnProperty(a) ) {
        stop.attr(a, attrs[a]);
      }
    }
  }
};

nv.utils.colorRadialGradient = function (d, i, p, c, defs) {
  var id = 'rg_gradient_' + i
    , grad = defs.select('#' + id);
  if ( grad.empty() )
  {
    nv.utils.createRadialGradient( id, p, defs, [
      { 'offset': p.s, 'stop-color': d3.rgb(c).brighter().toString(), 'stop-opacity': 1 },
      { 'offset': '100%','stop-color': d3.rgb(c).darker().toString(), 'stop-opacity': 1 }
    ]);
  }
  return 'url(#' + id + ')';
};

nv.utils.createRadialGradient = function (id, params, defs, stops) {
  var grad = defs.append('radialGradient')
        .attr('id', id)
        .attr('r', params.r)
        .attr('cx', params.x)
        .attr('cy', params.y)
        .attr('gradientUnits', params.u)
        .attr('spreadMethod', 'pad');
  for (var i=0; i<stops.length; i+=1) {
    var attrs = stops[i]
      , stop = grad.append('stop');
    for (var a in attrs)
    {
      if ( attrs.hasOwnProperty(a) ) {
        stop.attr(a, attrs[a]);
      }
    }
  }
};

nv.utils.getAbsoluteXY = function (element) {
  var viewportElement = document.documentElement
    , box = element.getBoundingClientRect()
    , scrollLeft = viewportElement.scrollLeft + document.body.scrollLeft
    , scrollTop = viewportElement.scrollTop + document.body.scrollTop
    , x = box.left + scrollLeft
    , y = box.top + scrollTop;

  return {'left': x, 'top': y};
};

// Creates a rectangle with rounded corners
nv.utils.roundedRectangle = function (x, y, width, height, radius) {
  return "M" + x + "," + y +
       "h" + (width - radius * 2) +
       "a" + radius + "," + radius + " 0 0 1 " + radius + "," + radius +
       "v" + (height - 2 - radius * 2) +
       "a" + radius + "," + radius + " 0 0 1 " + -radius + "," + radius +
       "h" + (radius * 2 - width) +
       "a" + -radius + "," + radius + " 0 0 1 " + -radius + "," + -radius +
       "v" + ( -height + radius * 2 + 2 ) +
       "a" + radius + "," + radius + " 0 0 1 " + radius + "," + -radius +
       "z";
};

nv.utils.dropShadow = function (id, defs, options) {
  var opt = options || {}
    , h = opt.height || '130%'
    , o = opt.offset || 2
    , b = opt.blur || 1;

  var filter = defs.append('filter')
        .attr('id',id)
        .attr('height',h);
  var offset = filter.append('feOffset')
        .attr('in','SourceGraphic')
        .attr('result','offsetBlur')
        .attr('dx',o)
        .attr('dy',o); //how much to offset
  var color = filter.append('feColorMatrix')
        .attr('in','offsetBlur')
        .attr('result','matrixOut')
        .attr('type','matrix')
        .attr('values','1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 1 0');
  var blur = filter.append('feGaussianBlur')
        .attr('in','matrixOut')
        .attr('result','blurOut')
        .attr('stdDeviation',b); //stdDeviation is how much to blur
  var merge = filter.append('feMerge');
      merge.append('feMergeNode'); //this contains the offset blurred image
      merge.append('feMergeNode')
        .attr('in','SourceGraphic'); //this contains the element that the filter is applied to

  return 'url(#' + id + ')';
};
// <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
//   <defs>
//     <filter id="f1" x="0" y="0" width="200%" height="200%">
//       <feOffset result="offOut" in="SourceGraphic" dx="20" dy="20" />
//       <feColorMatrix result="matrixOut" in="offOut" type="matrix"
//       values="0.2 0 0 0 0 0 0.2 0 0 0 0 0 0.2 0 0 0 0 0 1 0" />
//       <feGaussianBlur result="blurOut" in="matrixOut" stdDeviation="10" />
//       <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
//     </filter>
//   </defs>
//   <rect width="90" height="90" stroke="green" stroke-width="3"
//   fill="yellow" filter="url(#f1)" />
// </svg>


nv.utils.maxStringSetLength = function (_data, _container, _format) {
  var maxLength = 0;
  _container.append('g').attr('class', 'tmp-text-strings');
  var calcContainers = _container.select('.tmp-text-strings').selectAll('text')
      .data(_data).enter()
        .append('text')
        .text(_format);
  calcContainers
    .each(function (d,i) {
      maxLength = Math.max(this.getBBox().width, maxLength);
    });
  _container.select('.tmp-text-strings').remove();
  return maxLength;
};
nv.models.axis = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var axis = d3.svg.axis()
    ;

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , thickness = 0 //only used for tickLabel currently
    , scale = d3.scale.linear()
    , axisLabelText = null
    , showMaxMin = true //TODO: showMaxMin should be disabled on all ordinal scaled axes
    , highlightZero = true
    , rotateLabels = 0
    , reduceXTicks = false // if false a tick will show for every data point
    , rotateYLabel = true
    , staggerLabels = false
    , isOrdinal = false
    , ticks = null
    , axisLabelDistance = 8 //The larger this number is, the closer the axis label is to the axis.
    ;

  axis
    .scale(scale)
    .orient('bottom')
    .tickFormat(function(d) { return d; })
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var scale0;

  //============================================================

  function chart(selection) {
    selection.each(function(data) {
      var container = d3.select(this);

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-axis').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-axis');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //------------------------------------------------------------

      var tickPaddingOriginal = axis.tickPadding()
        , fmt = axis.tickFormat()
        , w = (scale.range().length === 2) ? scale.range()[1] : (scale.range()[scale.range().length - 1] + (scale.range()[1] - scale.range()[0]))
        , label = {y: 0, dy: 0, x: w/2, a: 'middle', t: ''}
        , maxmin = {};

      if (ticks !== null) {
        axis.ticks(ticks);
      } else if (axis.orient() === 'top' || axis.orient() === 'bottom') {
        axis.ticks(Math.ceil(Math.abs(scale.range()[1] - scale.range()[0]) / 100));
      }

      if (rotateLabels % 360 && axis.orient() === 'bottom') {
        axis.tickPadding(0);
      }

      g.transition().call(axis);

      axis.tickPadding(tickPaddingOriginal);

      scale0 = scale0 || axis.scale();

      if (fmt === null) {
        fmt = scale0.tickFormat();
      }

      //------------------------------------------------------------
      //Calculate the longest tick width and height

      var maxTickWidth = 0
        , maxTickHeight = 0;
      var tickText = g.selectAll('g.tick').select('text');
      tickText.each(function(d,i){
        var bbox = this.getBBox()
          , size = {w: parseInt(bbox.width, 10), h: parseInt(bbox.height / 1.15, 10)};
        if (size.w > maxTickWidth) {
          maxTickWidth = size.w;
        }
        if (size.h > maxTickHeight) {
          maxTickHeight = size.h;
        }
      });

      thickness = tickPaddingOriginal + (!!axisLabelText ? axisLabelDistance : 0);

      //------------------------------------------------------------
      // Orientation parameters

      switch (axis.orient()) {
        case 'top':

          if (axisLabelText) {
            label.y = -thickness;
            label.dy = '-.71em';
          }

          if (showMaxMin) {
            maxmin = {
              data: scale.domain(),
              translate: function(d,i) { return 'translate(' + scale(d) + ',0)'; },
              dy: '0em',
              x: 0,
              y: -axis.tickPadding(),
              transform: '',
              anchor: rotateLabels ? (rotateLabels%360 > 0 ? 'start' : 'end') : 'middle'
            };
          }

          break;

        case 'bottom':

          if (rotateLabels % 360) {
            //Convert to radians before calculating sin. Add 30 to margin for healthy padding.
            var sin = Math.abs(Math.sin(rotateLabels * Math.PI / 180));
            thickness += (sin ? sin * maxTickWidth : maxTickWidth);
            thickness += (sin ? sin * maxTickHeight : 0);
            //Rotate all tickText
            tickText
              .attr('transform', function(d,i,j) { return 'translate(0,' + tickPaddingOriginal + ') rotate(' + rotateLabels + ')'; })
              .style('text-anchor', rotateLabels % 360 > 0 ? 'start' : 'end');
          } else {
            thickness += maxTickHeight;
          }

          if (axisLabelText) {
            label.y = thickness;
            label.dy = '.71em';
          }

          if (reduceXTicks) {
            g .selectAll('.tick')
                .each(function (d,i) {
                  d3.select(this).selectAll('text,line')
                    .style('opacity', i % Math.ceil(data[0].values.length / (w / 100)) !== 0 ? 0 : 1);
                });
          }

          if (showMaxMin) {
            maxmin = {
              data: [scale.domain()[0], scale.domain()[scale.domain().length - 1]],
              translate: function(d,i) { return 'translate(' + (scale(d) + (isOrdinal ? scale.rangeBand() / 2 : 0)) + ',0)'; },
              dy: '.71em',
              x: 0,
              y: axis.tickPadding(),
              rotate: function(d) { return 'rotate(' + rotateLabels + ' 0,0)'; },
              anchor: rotateLabels ? (rotateLabels%360 > 0 ? 'start' : 'end') : 'middle'
            };
          }

          if (staggerLabels) {
            tickText
                .attr('transform', function(d,i) { return 'translate(0,' + (i % 2 === 0 ? '0' : '12') + ')'; });
          }

          break;

        case 'right':

          thickness += maxTickWidth;

          if (axisLabelText) {
            label = {
              y: rotateYLabel ? -thickness : -10,
              dy: 0,
              x: rotateYLabel ? scale.range()[0] / 2 : axis.tickPadding(),
              a: rotateYLabel ? 'middle' : 'begin',
              t: rotateYLabel ? 'rotate(90)' : ''
            };
          }

          if (showMaxMin) {
            maxmin = {
              data: scale.domain(),
              translate: function(d,i) { return 'translate(0,' + scale(d) + ')'; },
              dy: '.32em',
              x: axis.tickPadding(),
              y: 0,
              rotate: '',
              anchor: 'start'
            };
          }
          break;

        case 'left':

          thickness += maxTickWidth;

          if (axisLabelText) {
            label = {
              y: rotateYLabel ? -thickness : -10, //TODO: consider calculating this based on largest tick width... OR at least expose this on chart
              dy: 0,
              x: rotateYLabel ? -scale.range()[0] / 2 : -axis.tickPadding(),
              a: rotateYLabel ? 'middle' : 'end',
              t: rotateYLabel ? 'rotate(-90)' : '',
            };
          }

          if (showMaxMin) {
            maxmin = {
              data: scale.domain(),
              translate: function(d,i) { return 'translate(0,' + scale(d) + ')'; },
              dy: '.32em',
              x: -axis.tickPadding(),
              y: 0,
              rotate: '',
              anchor: 'end'
            };
          }

          break;
      }

      //------------------------------------------------------------
      // Axis label

      var axisLabel = g.selectAll('text.nv-axislabel').data([axisLabelText]);
      axisLabel.exit().remove();
      axisLabel.enter().append('text').attr('class', 'nv-axislabel');

      if (axisLabelText) {
        axisLabel
          .text(function(d) { return d; })
          .attr('y', label.y)
          .attr('dy', label.dy)
          .attr('x', label.x)
          .style('text-anchor', label.a)
          .attr('transform', label.t);

        axisLabel.each(function(d,i){
          thickness += parseInt(this.getBBox().height / 1.15, 10);
        });
      }

      //------------------------------------------------------------
      // Min Max values

      if (showMaxMin) {
        var axisMaxMin = wrap.selectAll('g.nv-axisMaxMin').data(maxmin.data);
        axisMaxMin.enter().append('g').attr('class', 'nv-axisMaxMin').append('text')
          .style('opacity', 0);
        axisMaxMin.exit().remove();
        axisMaxMin
            .attr('transform', maxmin.translate)
          .select('text')
            .attr('dy', maxmin.dy)
            .attr('x', maxmin.x)
            .attr('y', maxmin.y)
            .attr('transform', maxmin.rotate)
            .style('text-anchor', maxmin.anchor)
            .text(function(d,i) {
              var v = fmt(d);
              return ('' + v).match('NaN') ? '' : v;
            });
        axisMaxMin.transition()
            .attr('transform', maxmin.translate)
          .select('text')
            .style('opacity', 1);
      }

      if (showMaxMin && (axis.orient() === 'left' || axis.orient() === 'right')) {
        //check if max and min overlap other values, if so, hide the values that overlap
        g.selectAll('g') // the g's wrapping each tick
            .each(function(d,i) {
              d3.select(this).select('text').attr('opacity', 1);
              if (scale(d) < scale.range()[1] + 10 || scale(d) > scale.range()[0] - 10) { // 10 is assuming text height is 16... if d is 0, leave it!
                if (d > 1e-10 || d < -1e-10) {// accounts for minor floating point errors... though could be problematic if the scale is EXTREMELY SMALL
                  d3.select(this).attr('opacity', 0);
                }
                d3.select(this).select('text').attr('opacity', 0); // Don't remove the ZERO line!!
              }
            });

        //if Max and Min = 0 only show min, Issue #281
        if (scale.domain()[0] === scale.domain()[1] && scale.domain()[0] === 0) {
          wrap.selectAll('g.nv-axisMaxMin')
            .style('opacity', function(d,i) { return !i ? 1 : 0; });
        }
      }

      if (showMaxMin && (axis.orient() === 'top' || axis.orient() === 'bottom')) {
        var maxMinRange = [];
        wrap.selectAll('g.nv-axisMaxMin')
            .each(function(d,i) {
              try {
                  if (i) // i== 1, max position
                      maxMinRange.push(scale(d) - this.getBBox().width - 4);  //assuming the max and min labels are as wide as the next tick (with an extra 4 pixels just in case)
                  else // i==0, min position
                      maxMinRange.push(scale(d) + this.getBBox().width + 4);
              }catch (err) {
                  if (i) // i== 1, max position
                      maxMinRange.push(scale(d) - 4);  //assuming the max and min labels are as wide as the next tick (with an extra 4 pixels just in case)
                  else // i==0, min position
                      maxMinRange.push(scale(d) + 4);
              }
            });
        // g.selectAll('g') // the g's wrapping each tick
        //     .each(function(d,i) {
        //       if (scale(d) < maxMinRange[0] || scale(d) > maxMinRange[1]) {
        //         if (d > 1e-10 || d < -1e-10) // accounts for minor floating point errors... though could be problematic if the scale is EXTREMELY SMALL
        //           d3.select(this).remove();
        //         else
        //           d3.select(this).select('text').remove(); // Don't remove the ZERO line!!
        //       }
        //     });
      }


      //highlight zero line ... Maybe should not be an option and should just be in CSS?
      if (highlightZero)
        g.selectAll('line.tick')
          .filter(function(d) { return !parseFloat(Math.round(d*100000)/1000000); }) //this is because sometimes the 0 tick is a very small fraction, TODO: think of cleaner technique
            .classed('zero', true);

      //store old scales for use in transitions on update
      scale0 = scale.copy();

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.axis = axis;

  d3.rebind(chart, axis, 'orient', 'tickValues', 'tickSubdivide', 'tickSize', 'tickPadding', 'tickFormat');
  d3.rebind(chart, scale, 'domain', 'range', 'rangeBand', 'rangeBands'); //these are also accessible by chart.scale(), but added common ones directly for ease of use

  chart.margin = function(_) {
    if(!arguments.length) return margin;
    margin.top    = typeof _.top    !== 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  !== 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom !== 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   !== 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return thickness;
    thickness = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return thickness;
    thickness = _;
    return chart;
  };

  chart.ticks = function(_) {
    if (!arguments.length) return ticks;
    ticks = _;
    return chart;
  };

  chart.axisLabel = function(_) {
    if (!arguments.length) return axisLabelText;
    axisLabelText = _;
    return chart;
  };

  chart.showMaxMin = function(_) {
    if (!arguments.length) return showMaxMin;
    showMaxMin = _;
    return chart;
  };

  chart.highlightZero = function(_) {
    if (!arguments.length) return highlightZero;
    highlightZero = _;
    return chart;
  };

  chart.scale = function(_) {
    if (!arguments.length) return scale;
    scale = _;
    axis.scale(scale);
    isOrdinal = typeof scale.rangeBands === 'function';
    d3.rebind(chart, scale, 'domain', 'range', 'rangeBand', 'rangeBands');
    return chart;
  };

  chart.rotateYLabel = function(_) {
    if(!arguments.length) return rotateYLabel;
    rotateYLabel = _;
    return chart;
  };

  chart.rotateLabels = function(_) {
    if(!arguments.length) return rotateLabels;
    rotateLabels = _;
    return chart;
  };

  chart.reduceXTicks = function (_) {
    if (!arguments.length) { return reduceXTicks; }
    reduceXTicks = _;
    return chart;
  };

  chart.staggerLabels = function(_) {
    if (!arguments.length) return staggerLabels;
    staggerLabels = _;
    return chart;
  };

  chart.axisLabelDistance = function(_) {
    if (!arguments.length) return axisLabelDistance;
    axisLabelDistance = _;
    return chart;
  };

  chart.maxTickWidth = function(_) {
    if (!arguments.length) return maxTickWidth;
    maxTickWidth = _;
    return chart;
  };

  //============================================================


  return chart;
};
nv.models.legend = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 10, bottom: 10, left: 10}
    , width = 400
    , height = 20
    , radius = 5
    , gutter = 10
    , lineHeight = 20
    , align = 'right'
    , equalColumns = true
    , strings = {close: 'close', type: 'legend'}
    , id = Math.floor(Math.random() * 10000) //Create semi-unique ID in case user doesn't select one
    , getKey = function(d) { return d.key.length > 0 ? d.key : 'undefined'; }
    , color = nv.utils.defaultColor()
    , classes = function (d,i) { return ''; }
    , dispatch = d3.dispatch('legendClick', 'legendMouseover', 'legendMouseout', 'linkClick')
    ;

  // Private Variables
  //------------------------------------------------------------

  var legendOpen = 0;

  //============================================================

  function legend(selection) {
    selection.each(function(data) {
      var availableWidth = width - margin.left - margin.right
        , availableHeight = height - margin.top - margin.bottom
        , container = d3.select(this)
        , legendWidth = 0
        , legendHeight = 0;

      //------------------------------------------------------------
      // Setup containers and skeleton of legend

      var wrap = container.selectAll('g.nv-chart-legend').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nv-chart-legend');

      var defs = wrapEnter.append('defs');
      defs
        .append('clipPath').attr('id', 'nv-edge-clip-' + id)
        .append('rect');
      var clip = wrap.select('#nv-edge-clip-' + id + ' rect');

      wrapEnter
        .append('rect').attr('class', 'nv-legend-background');
      var back = container.select('.nv-legend-background');

      wrapEnter
        .append('text').attr('class', 'nv-legend-link');
      var link = container.select('.nv-legend-link');

      wrapEnter
        .append('g').attr('class', 'nv-legend-mask')
        .append('g').attr('class', 'nv-legend');
      var mask = container.select('.nv-legend-mask');
      var g = container.select('g.nv-legend');

      var series = g.selectAll('.nv-series').data(function(d) { return d; });
      var seriesEnter = series.enter().append('g').attr('class', 'nv-series');

      var zoom = d3.behavior.zoom();

      function zoomLegend(d) {
        var trans = d3.transform(g.attr("transform")).translate
          , transX = trans[0]
          , transY = trans[1] + d3.event.sourceEvent.wheelDelta / 4
          , upMax = Math.max(transY, back.attr('height') - legendHeight); //should not go beyond diff
        if (upMax) {
          g .attr('transform', 'translate(' + transX + ',' + Math.min(upMax, 0) + ')');
        }
      }

      clip
        .attr('x', 0)
        .attr('y', 0)
        .attr('width', 0)
        .attr('height', 0);

      back
        .attr('x', 0)
        .attr('y', 0.5)
        .attr('rx', 2)
        .attr('ry', 2)
        .attr('width', 0)
        .attr('height', 0)
        .attr('filter', nv.utils.dropShadow('legend_back_' + id, defs, {blur: 2} ))
        .style('opacity', 0)
        .style('pointer-events', 'all');

      link
        .text(legendOpen === 1 ? legend.strings().close : legend.strings().type)
        .attr('text-anchor', align === 'right' ? 'end' : 'start')
        .attr('dy', '.32em')
        .attr('dx', 0)
        .attr('transform', 'translate(' + (align === 'right' ? width : 0) + ',' + (margin.top + radius) + ')')
        .style('opacity', 0)
        .on('click', function(d,i) {
          dispatch.linkClick(d,i);
        });

      mask
        .attr('clip-path', 'url(#nv-edge-clip-' + id + ')');

      seriesEnter
        .on('mouseover', function(d,i) {
          dispatch.legendMouseover(d,i);  //TODO: Make consistent with other event objects
        })
        .on('mouseout', function(d,i) {
          dispatch.legendMouseout(d,i);
        })
        .on('click', function(d,i) {
          dispatch.legendClick(d,i);
        });
      seriesEnter.append('circle')
        .style('stroke-width', 2)
        .attr('r', radius);
      seriesEnter.append('text')
        .style('stroke-width', 0)
        .style('stroke', 'inherit')
        .attr('text-anchor', 'start')
        .attr('dy', '.32em')
        .attr('dx', '8');
      series.classed('disabled', function(d) { return d.disabled; });
      series.exit().remove();
      series.select('circle')
        .attr('class', function(d,i) { return this.getAttribute('class') || classes(d,i); })
        .attr('fill', function(d,i) { return this.getAttribute('fill') || color(d,i); })
        .attr('stroke', function(d,i) { return this.getAttribute('fill') || color(d,i); });
      series.select('text').text(getKey);

      //------------------------------------------------------------

      //TODO: add ability to add key to legend
      //var label = g.append('text').text('Probability:').attr('class','nv-series-label').attr('transform','translate(0,0)');
      //TODO: implement fixed-width and max-width options (max-width is especially useful with the align option)

      if (equalColumns) {
        var keyWidths = []
          , keyCount = 0
          , keysPerRow = 0
          , columnWidths = []
          , computeWidth = function(prev, cur, index, array) {
              return prev + cur;
            };

        series.each(function(d,i) {
          keyWidths.push(d3.select(this).select('text').node().getComputedTextLength() + 2*radius + 3 + gutter); // 28 is ~ the width of the circle plus some padding
        });

        keyCount = keyWidths.length;
        keysPerRow = keyCount;
        legendWidth = keyWidths.reduce(computeWidth) - gutter;

        //keep decreasing the number of keys per row until
        //legend width is less than the available width
        while (keysPerRow > 1) {
          columnWidths = [];

          for (var k = 0, iCol = 0; k < keyCount; k += 1) {
            iCol = k % keysPerRow;
            if (keyWidths[k] > (columnWidths[iCol] || 0)) {
              columnWidths[iCol] = keyWidths[k];
            }
          }

          legendWidth = columnWidths.reduce(computeWidth) - gutter;

          if (legendWidth < availableWidth) {
            break;
          }

          keysPerRow -= 1;
        }

        if (Math.ceil(keyCount / keysPerRow) < 3) {

          var keyPositions = [];
          for (var i = 0, curX = radius; i < keysPerRow; i += 1) {
            keyPositions[i] = curX;
            curX += columnWidths[i];
          }

          height = margin.top + margin.bottom + radius * 2 + ((Math.ceil(keyCount / keysPerRow) - 1) * lineHeight);
          legendOpen = 0;

          zoom.on('zoom', null);

          clip
            .attr('x', 0 - margin.left)
            .attr('y', 0 - lineHeight + radius + 0.5)
            .attr('width', legendWidth + margin.right + margin.left)
            .attr('height', height);

          var offSet = 0.5 - margin.left;
          back
            .attr('x', align === 'right' ? width - legendWidth + offSet : align === 'center' ? offSet + (width - legendWidth) / 2 : offSet)
            .attr('width', legendWidth + margin.left + margin.right)
            .attr('height', height)
            .style('opacity', 0)
            .style('display', 'inline');

          //position legend as far right as possible within the total width
          mask
            .attr('transform', 'translate(' + (align === 'right' ? width - legendWidth : align === 'center' ? (width - legendWidth) / 2 : 0) + ',' + (margin.top + radius) + ')');

          g
            .style('opacity', 1)
            .style('display', 'inline');

          series
            .attr('transform', function(d, i) {
              return 'translate(' + keyPositions[i % keysPerRow] + ',' + (Math.floor(i / keysPerRow) * lineHeight) + ')';
            });

        } else {

          height = lineHeight + radius;
          legendWidth = d3.max(keyWidths) - gutter;
          legendHeight = margin.top + margin.bottom + radius * 2 + (keyCount - 1) * lineHeight;

          zoom.on('zoom', zoomLegend);

          clip
            .attr('x', 0 - margin.left)
            .attr('y', 0 - lineHeight + radius + 0.5)
            .attr('width', legendWidth + margin.right + margin.left)
            .attr('height', Math.min(availableHeight - height, legendHeight));

          back
            .attr('x', align === 'right' ? availableWidth - legendWidth + 0.5 : 0.5)
            .attr('y', lineHeight + radius + 0.5)
            .attr('width', legendWidth + margin.right + margin.left)
            .attr('height', Math.min(availableHeight - height, legendHeight))
            .style('opacity', legendOpen * 0.9)
            .style('display', legendOpen ? 'inline' : 'none')
            .call(zoom);

          link
            .style('opacity', 1);

          mask
            .attr('transform', 'translate(' + (align === 'right' ? width - margin.right - legendWidth : margin.left) + ',' + (margin.top + radius * 2 + lineHeight) + ')');

          g
            .style('opacity', legendOpen)
            .style('display', legendOpen ? 'inline' : 'none')
            .call(zoom);

          series
            .attr('transform', function(d, i) {
              return 'translate(' + radius + ',' + (i * lineHeight) + ')';
            });
        }

      } else {

        var xpos
          , ypos = radius
          , newxpos = radius;

        legendOpen = 0;

        series
          .attr('transform', function(d, i) {
            var length = d3.select(this).select('text').node().getComputedTextLength() + 2*radius + 3 + gutter;
            xpos = newxpos;

            if (availableWidth < xpos + length - gutter) {
              newxpos = xpos = radius;
              ypos += lineHeight;
            }

            newxpos += length;
            if (newxpos - gutter > legendWidth) {
              legendWidth = newxpos - gutter;
            }

            return 'translate(' + xpos + ',' + ypos + ')';
          });

        height = margin.top + margin.bottom + ypos + radius;

        //position legend as far right as possible within the total width
        g
          .attr('transform', 'translate(' + (width - margin.right - legendWidth) + ',' + margin.top + ')');

        back
          .attr('x', availableWidth - legendWidth + 0.5)
          .attr('width', legendWidth + margin.right + margin.left)
          .attr('height', margin.top + margin.bottom + radius + ypos);
      }

      dispatch.on('linkClick', function(d) {
        legendOpen = 1 - legendOpen;
        back
          .transition()
          .duration(200)
          .style('opacity', legendOpen * 0.9)
          .style('display', legendOpen ? 'inline' : 'none');
        g
          .transition()
          .duration(200)
          .style('opacity', legendOpen)
          .style('display', legendOpen ? 'inline' : 'none');
        link
          .text(legendOpen === 1 ? 'close' : 'legend');
      });

    });

    return legend;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  legend.dispatch = dispatch;

  legend.margin = function(_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    !== 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  !== 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom !== 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   !== 'undefined' ? _.left   : margin.left;
    return legend;
  };

  legend.width = function(_) {
    if (!arguments.length) { return width; }
    width = Math.round(_);
    return legend;
  };

  legend.height = function(_) {
    if (!arguments.length) { return height; }
    height = Math.round(_);
    return legend;
  };

  legend.id = function(_) {
    if (!arguments.length) { return id; }
    id = _;
    return legend;
  };

  legend.key = function(_) {
    if (!arguments.length) { return getKey; }
    getKey = _;
    return legend;
  };

  legend.color = function(_) {
    if (!arguments.length) { return color; }
    color = nv.utils.getColor(_);
    return legend;
  };

  legend.classes = function(_) {
    if (!arguments.length) { return classes; }
    classes = _;
    return legend;
  };

  legend.align = function(_) {
    if (!arguments.length) { return align; }
    align = _;
    return legend;
  };

  legend.equalColumns = function(_) {
    if (!arguments.length) { return equalColumns; }
    equalColumns = _;
    return legend;
  };

  legend.strings = function(_) {
    if (!arguments.length) { return strings; }
    strings = _;
    return legend;
  };
  //============================================================


  return legend;
};

nv.models.scatter = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin       = {top: 0, right: 0, bottom: 0, left: 0}
    , width        = 960
    , height       = 500
    , color        = nv.utils.defaultColor() // chooses color
    , fill         = color
    , classes      = function (d,i) { return 'nv-group nv-series-' + i; }
    , id           = Math.floor(Math.random() * 100000) //Create semi-unique ID incase user doesn't select one
    , x            = d3.scale.linear()
    , y            = d3.scale.linear()
    , z            = d3.scale.linear() //linear because d3.svg.shape.size is treated as area
    , getX         = function(d) { return d.x } // accessor to get the x value
    , getY         = function(d) { return d.y } // accessor to get the y value
    , getSize      = function(d) { return d.size || 1} // accessor to get the point size
    , getShape     = function(d) { return d.shape || 'circle' } // accessor to get point shape
    , onlyCircles  = true // Set to false to use shapes
    , forceX       = [] // List of numbers to Force into the X scale (ie. 0, or a max / min, etc.)
    , forceY       = [] // List of numbers to Force into the Y scale
    , forceSize    = [] // List of numbers to Force into the Size scale
    , interactive  = true // If true, plots a voronoi overlay for advanced point intersection
    , pointActive  = function(d) { return !d.notActive } // any points that return false will be filtered out
    , padData      = false // If true, adds half a data points width to front and back, for lining up a line chart with a bar chart
    , padDataOuter = .1 //outerPadding to imitate ordinal scale outer padding
    , clipEdge     = false // if true, masks points within x and y scale
    , clipVoronoi  = true // if true, masks each point with a circle... can turn off to slightly increase performance
    , clipRadius   = function() { return 10 } // function to get the radius for voronoi point clips
    , xDomain      = null // Override x domain (skips the calculation from data)
    , yDomain      = null // Override y domain
    , sizeDomain   = null // Override point size domain
    , sizeRange    = [16, 256]
    , singlePoint  = false
    , dispatch     = d3.dispatch('elementClick', 'elementMouseover', 'elementMouseout', 'elementMousemove')
    , useVoronoi   = true
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var x0, y0, z0 // used to store previous scales
    , timeoutID
    , needsUpdate = false // Flag for when the points are visually updating, but the interactive layer is behind, to disable tooltips
    ;

  //============================================================


  function chart(selection) {
    selection.each(function(data) {
      var availableWidth = width - margin.left - margin.right,
          availableHeight = height - margin.top - margin.bottom,
          container = d3.select(this);

      //add series index to each data point for reference
      data = data.map(function(series, i) {
        series.values = series.values.map(function(point) {
          point.series = i;
          return point;
        });
        return series;
      });

      //------------------------------------------------------------
      // Setup Scales

      // remap and flatten the data for use in calculating the scales' domains
      var seriesData = (xDomain && yDomain && sizeDomain) ? [] : // if we know xDomain and yDomain and sizeDomain, no need to calculate.... if Size is constant remember to set sizeDomain to speed up performance
            d3.merge(
              data.map(function(d) {
                return d.values.map(function(d,i) {
                  return { x: getX(d,i), y: getY(d,i), size: getSize(d,i) }
                })
              })
            );

      x   .domain(xDomain || d3.extent(seriesData.map(function(d) { return d.x }).concat(forceX)))

      if (padData && data[0])
        if (padDataOuter !== 0) {
          // adjust range to line up with value bars
          x.range([
            (availableWidth * padDataOuter + availableWidth) / (2 *data[0].values.length),
            availableWidth - availableWidth * (1 + padDataOuter) / (2 * data[0].values.length)
          ]);
        } else {
          // shift range so that largest bubble doesn't cover scales
          x.range([
            0 + Math.sqrt(sizeRange[1]/Math.PI),
            availableWidth - Math.sqrt(sizeRange[1]/Math.PI)
          ]);
        }
        //x.range([availableWidth * .5 / data[0].values.length, availableWidth * (data[0].values.length - .5)  / data[0].values.length ]);
      else
        x.range([0, availableWidth]);

      y   .domain(yDomain || d3.extent(seriesData.map(function(d) { return d.y }).concat(forceY)))
          .range([availableHeight, 0]);

      z   .domain(sizeDomain || d3.extent(seriesData.map(function(d) { return d.size }).concat(forceSize)))
          .range(sizeRange);

      // If scale's domain don't have a range, slightly adjust to make one... so a chart can show a single data point
      if (x.domain()[0] === x.domain()[1] || y.domain()[0] === y.domain()[1]) singlePoint = true;
      if (x.domain()[0] === x.domain()[1])
        x.domain()[0] ?
            x.domain([x.domain()[0] - x.domain()[0] * 0.01, x.domain()[1] + x.domain()[1] * 0.01])
          : x.domain([-1,1]);

      if (y.domain()[0] === y.domain()[1])
        y.domain()[0] ?
            y.domain([y.domain()[0] + y.domain()[0] * 0.01, y.domain()[1] - y.domain()[1] * 0.01])
          : y.domain([-1,1]);


      x0 = x0 || x;
      y0 = y0 || y;
      z0 = z0 || z;

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-scatter').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-scatter nv-chart-' + id + (singlePoint ? ' nv-single-point' : ''));
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function(d,i) {
        return nv.utils.colorRadialGradient( d, id+'-'+i, {x:0.5, y:0.5, r:0.5, s:0, u:'objectBoundingBox'}, color(d,i), wrap.select('defs') );
      };

      gEnter.append('g').attr('class', 'nv-groups');
      gEnter.append('g').attr('class', 'nv-point-paths');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------


      defsEnter.append('clipPath')
          .attr('id', 'nv-edge-clip-' + id)
        .append('rect');

      wrap.select('#nv-edge-clip-' + id + ' rect')
          .attr('width', availableWidth)
          .attr('height', availableHeight);

      g   .attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + id + ')' : '');


      function updateInteractiveLayer() {

        if (!interactive) return false;

        var eventElements;

        var vertices = d3.merge(data.map(function(group, groupIndex) {
            return group.values
              .map(function(point, pointIndex) {
                // *Adding noise to make duplicates very unlikely
                // **Injecting series and point index for reference
                return [x(getX(point,pointIndex)) * (Math.random() / 1e12 + 1)  , y(getY(point,pointIndex)) * (Math.random() / 1e12 + 1), groupIndex, pointIndex, point]; //temp hack to add noise untill I think of a better way so there are no duplicates
              })
              .filter(function(pointArray, pointIndex) {
                return pointActive(pointArray[4], pointIndex); // Issue #237.. move filter to after map, so pointIndex is correct!
              })
          })
        );

        //inject series and point index for reference into voronoi
        if (useVoronoi === true) {
          if (clipVoronoi) {
            var pointClipsEnter = wrap.select('defs').selectAll('.nv-point-clips')
                .data([id])
              .enter();

            pointClipsEnter.append('clipPath')
                  .attr('class', 'nv-point-clips')
                  .attr('id', 'nv-points-clip-' + id);

            var pointClips = wrap.select('#nv-points-clip-' + id).selectAll('circle')
                .data(vertices);
            pointClips.enter().append('circle')
                .attr('r', clipRadius);
            pointClips.exit().remove();
            pointClips
                .attr('cx', function(d) { return d[0] })
                .attr('cy', function(d) { return d[1] });

            wrap.select('.nv-point-paths')
                .attr('clip-path', 'url(#nv-points-clip-' + id + ')');
          }

          if(vertices.length < 3) {
            // Issue #283 - Adding 2 dummy points to the voronoi b/c voronoi requires min 3 points to work
            vertices.push([x.range()[0] - 20, y.range()[0] - 20, null, null]);
            vertices.push([x.range()[1] + 20, y.range()[1] + 20, null, null]);
            vertices.push([x.range()[0] - 20, y.range()[0] + 20, null, null]);
            vertices.push([x.range()[1] + 20, y.range()[1] - 20, null, null]);
          }

          var bounds = d3.geom.polygon([
              [-10,-10],
              [-10,height + 10],
              [width + 10,height + 10],
              [width + 10,-10]
          ]);

          var voronoi = d3.geom.voronoi(vertices).map(function(d, i) {
              return {
                'data': bounds.clip(d),
                'series': vertices[i][2],
                'point': vertices[i][3]
              }
            });

          var pointPaths = wrap.select('.nv-point-paths').selectAll('path')
              .data(voronoi);
          pointPaths.enter().append('path')
              .attr('class', function(d,i) { return 'nv-path-'+i; });
          pointPaths.exit().remove();
          pointPaths
              .attr('d', function(d) { return 'M' + d.data.join('L') + 'Z'; });

          pointPaths
              .on('click', function(d) {
                if (needsUpdate) return 0;
                var series = data[d.series],
                    point  = series.values[d.point];
                dispatch.elementClick({
                  point: point,
                  series: series,
                  pos: [x(getX(point, d.point)) + margin.left, y(getY(point, d.point)) + margin.top],
                  seriesIndex: d.series,
                  pointIndex: d.point
                });
              })
              .on('mouseover', function(d) {
                if (needsUpdate) return 0;
                var series = data[d.series],
                    point  = series.values[d.point];
                dispatch.elementMouseover({
                  point: point,
                  series: series,
                  pos: [d3.event.pageX, d3.event.pageY],
                  seriesIndex: d.series,
                  pointIndex: d.point
                });
              })
              .on('mouseout', function(d, i) {
                if (needsUpdate) return 0;
                var series = data[d.series],
                    point  = series.values[d.point];
                dispatch.elementMouseout({
                  point: point,
                  series: series,
                  seriesIndex: d.series,
                  pointIndex: d.point
                });
              })
              .on('mousemove', function(d,i){
                var series = data[d.series],
                    point  = series.values[d.point];
                dispatch.elementMousemove({
                  point: point,
                  pointIndex: d.point,
                  pos: [d3.event.pageX, d3.event.pageY],
                  id: id
                });
              });
        } else {
          /*
          // bring data in form needed for click handlers
          var dataWithPoints = vertices.map(function(d, i) {
              return {
                'data': d,
                'series': vertices[i][2],
                'point': vertices[i][3]
              }
            });
           */

          // add event handlers to points instead voronoi paths
          wrap.select('.nv-groups').selectAll('.nv-group')
            .selectAll('.nv-point')
              //.data(dataWithPoints)
              .style('pointer-events', 'auto') // recativate events, disabled by css
              .on('click', function(d,i) {
                //nv.log('test', d, i);
                if (needsUpdate || !data[d.series]) return 0; //check if this is a dummy point
                var series = data[d.series],
                    point  = series.values[i];
                dispatch.elementClick({
                  point: point,
                  series: series,
                  pos: [x(getX(point, i)) + margin.left, y(getY(point, i)) + margin.top],
                  seriesIndex: d.series,
                  pointIndex: i
                });
              })
              .on('mouseover', function(d,i) {
                if (needsUpdate || !data[d.series]) return 0; //check if this is a dummy point
                var series = data[d.series],
                    point  = series.values[i];
                dispatch.elementMouseover({
                  point: point,
                  series: series,
                  pos: [d3.event.pageX, d3.event.pageY],
                  seriesIndex: d.series,
                  pointIndex: i
                });
              })
              .on('mouseout', function(d,i) {
                if (needsUpdate || !data[d.series]) return 0; //check if this is a dummy point
                var series = data[d.series],
                    point  = series.values[i];
                dispatch.elementMouseout({
                  point: point,
                  series: series,
                  seriesIndex: d.series,
                  pointIndex: i
                });
              })
              .on('mousemove', function(d,i){
                var series = data[d.series],
                    point  = series.values[i];
                dispatch.elementMousemove({
                  point: point,
                  pointIndex: i,
                  pos: [d3.event.pageX, d3.event.pageY],
                  id: id
                });
              });
        }

        needsUpdate = false;
      }

      needsUpdate = true;

      var groups = wrap.select('.nv-groups').selectAll('.nv-group')
          .data(function(d) { return d }, function(d) { return d.key });
      groups.enter().append('g')
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6);
      d3.transition(groups.exit())
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6)
          .remove();
      groups
          .attr('class', function(d,i) { return this.getAttribute('class') || classes(d,d.series); })
          .attr('fill', function(d,i) { return this.getAttribute('fill') || fill(d,d.series); })
          .attr('stroke', function(d,i) { return this.getAttribute('stroke') || fill(d, d.series); })
          .classed('hover', function(d) { return d.hover; });
      d3.transition(groups)
          .style('stroke-opacity', 1)
          .style('fill-opacity', .5);


      if (onlyCircles) {

        var points = groups.selectAll('circle.nv-point')
            .data(function(d) { return d.values });
        points.enter().append('circle')
            .attr('cx', function(d,i) { return x0(getX(d,i)) })
            .attr('cy', function(d,i) { return y0(getY(d,i)) })
            .attr('r', function(d,i) { return Math.sqrt(z(getSize(d,i))/Math.PI) });
        points.exit().remove();
        d3.transition(groups.exit().selectAll('path.nv-point'))
            .attr('cx', function(d,i) { return x(getX(d,i)) })
            .attr('cy', function(d,i) { return y(getY(d,i)) })
            .remove();
        points.attr('class', function(d,i) { return 'nv-point nv-point-' + i });
        d3.transition(points)
            .attr('cx', function(d,i) { return x(getX(d,i)) })
            .attr('cy', function(d,i) { return y(getY(d,i)) })
            .attr('r', function(d,i) { return Math.sqrt(z(getSize(d,i))/Math.PI) });

      } else {

        var points = groups.selectAll('path.nv-point')
            .data(function(d) { return d.values });
        points.enter().append('path')
            .attr('transform', function(d,i) {
              return 'translate(' + x0(getX(d,i)) + ',' + y0(getY(d,i)) + ')'
            })
            .attr('d',
              d3.svg.symbol()
                .type(getShape)
                .size(function(d,i) { return z(getSize(d,i)) })
            );
        points.exit().remove();
        d3.transition(groups.exit().selectAll('path.nv-point'))
            .attr('transform', function(d,i) {
              return 'translate(' + x(getX(d,i)) + ',' + y(getY(d,i)) + ')'
            })
            .remove();
        points.attr('class', function(d,i) { return 'nv-point nv-point-' + i });
        d3.transition(points)
            .attr('transform', function(d,i) {
              //nv.log(d,i,getX(d,i), x(getX(d,i)));
              return 'translate(' + x(getX(d,i)) + ',' + y(getY(d,i)) + ')'
            })
            .attr('d',
              d3.svg.symbol()
                .type(getShape)
                .size(function(d,i) { return z(getSize(d,i)) })
            );
      }


      // Delay updating the invisible interactive layer for smoother animation
      clearTimeout(timeoutID); // stop repeat calls to updateInteractiveLayer
      timeoutID = setTimeout(updateInteractiveLayer, 300);
      //updateInteractiveLayer();

      //store old scales for use in transitions on update
      x0 = x.copy();
      y0 = y.copy();
      z0 = z.copy();

    });

    return chart;
  }


  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  dispatch.on('elementMouseover.point', function(d) {
    if (interactive)
      d3.select('.nv-chart-' + id + ' .nv-series-' + d.seriesIndex + ' .nv-point-' + d.pointIndex)
          .classed('hover', true);
  });

  dispatch.on('elementMouseout.point', function(d) {
    if (interactive)
      d3.select('.nv-chart-' + id + ' .nv-series-' + d.seriesIndex + ' .nv-point-' + d.pointIndex)
          .classed('hover', false);
  });

  //============================================================


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) return color;
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) return fill;
    fill = _;
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) return classes;
    classes = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) return gradient;
    gradient = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) return getX;
    getX = d3.functor(_);
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return getY;
    getY = d3.functor(_);
    return chart;
  };

  chart.size = function(_) {
    if (!arguments.length) return getSize;
    getSize = d3.functor(_);
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.xScale = function(_) {
    if (!arguments.length) return x;
    x = _;
    return chart;
  };

  chart.yScale = function(_) {
    if (!arguments.length) return y;
    y = _;
    return chart;
  };

  chart.zScale = function(_) {
    if (!arguments.length) return z;
    z = _;
    return chart;
  };

  chart.xDomain = function(_) {
    if (!arguments.length) return xDomain;
    xDomain = _;
    return chart;
  };

  chart.yDomain = function(_) {
    if (!arguments.length) return yDomain;
    yDomain = _;
    return chart;
  };

  chart.sizeDomain = function(_) {
    if (!arguments.length) return sizeDomain;
    sizeDomain = _;
    return chart;
  };

  chart.sizeRange = function(_) {
    if (!arguments.length) return sizeRange;
    sizeRange = _;
    return chart;
  };

  chart.forceX = function(_) {
    if (!arguments.length) return forceX;
    forceX = _;
    return chart;
  };

  chart.forceY = function(_) {
    if (!arguments.length) return forceY;
    forceY = _;
    return chart;
  };

  chart.forceSize = function(_) {
    if (!arguments.length) return forceSize;
    forceSize = _;
    return chart;
  };

  chart.interactive = function(_) {
    if (!arguments.length) return interactive;
    interactive = _;
    return chart;
  };

  chart.pointActive = function(_) {
    if (!arguments.length) return pointActive;
    pointActive = _;
    return chart;
  };

  chart.padData = function(_) {
    if (!arguments.length) return padData;
    padData = _;
    return chart;
  };

  chart.padDataOuter = function(_) {
    if (!arguments.length) return padDataOuter;
    padDataOuter = _;
    return chart;
  };

  chart.clipEdge = function(_) {
    if (!arguments.length) return clipEdge;
    clipEdge = _;
    return chart;
  };

  chart.clipVoronoi= function(_) {
    if (!arguments.length) return clipVoronoi;
    clipVoronoi = _;
    return chart;
  };

  chart.useVoronoi= function(_) {
    if (!arguments.length) return useVoronoi;
    useVoronoi = _;
    if (useVoronoi === false) {
        clipVoronoi = false;
    }
    return chart;
  };

  chart.clipRadius = function(_) {
    if (!arguments.length) return clipRadius;
    clipRadius = _;
    return chart;
  };

  chart.shape = function(_) {
    if (!arguments.length) return getShape;
    getShape = _;
    return chart;
  };

  chart.onlyCircles = function(_) {
    if (!arguments.length) return onlyCircles;
    onlyCircles = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) return id;
    id = _;
    return chart;
  };

  chart.singlePoint = function(_) {
    if (!arguments.length) return singlePoint;
    singlePoint = _;
    return chart;
  };

  //============================================================


  return chart;
}

nv.models.bubbleChart = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 10, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showControls = false
    , showLegend = true
    , getX = function (d) { return d.x; }
    , getY = function (d) { return d.y; }
    , forceY = [0] // 0 is forced by default.. this makes sense for the majority of bar graphs... user can always do chart.forceY([]) to remove
    , xDomain
    , yDomain
    , x
    , y
    , delay = 200
    , groupBy = function (d) { return d.y; }
    , filterBy = function (d) { return d.y; }
    , clipEdge = false // if true, masks lines within x and y scale
    , seriesLength = 0
    , reduceYTicks = false // if false a tick will show for every data point
    , tooltip = null
    , tooltips = true
    , tooltipContent = function (key, x, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + ' on ' + x + '</p>';
      }
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove')
    , bubbleClick = function (e) { return; }
    , format = d3.time.format("%Y-%m-%d")
    , controlWidth = function (w) { return showControls ? w * 0.3 : 0; }
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var scatter = nv.models.scatter()
    , xAxis = nv.models.axis()
        .orient('bottom')
        .tickPadding(5)
        .highlightZero(false)
        .showMaxMin(false)
    , yAxis = nv.models.axis()
        .orient('left')
        .highlightZero(false)
        .showMaxMin(false)
    , legend = nv.models.legend()
    ;

  //============================================================

  // TODO: test if new tooltip method is compatible with zoomed viewBox
  // var showTooltip = function (e, offsetElement) {
  //   // New addition to calculate position if SVG is scaled with viewBox, may move TODO: consider implementing everywhere else
  //   var offsets = {left:0,right:0};
  //   if (offsetElement) {
  //     var svg = d3.select(offsetElement).select('svg'),
  //         viewBox = svg.attr('viewBox');
  //     offsets = nv.utils.getAbsoluteXY(offsetElement);
  //     if (viewBox) {
  //       viewBox = viewBox.split(' ');
  //       var ratio = parseInt(svg.style('width'),10) / viewBox[2];
  //       e.pos[0] = e.pos[0] * ratio;
  //       e.pos[1] = e.pos[1] * ratio;
  //     }
  //   }

  //   var left = e.pos[0] + (offsets.left || 0) + margin.left,
  //       top = e.pos[1] + (offsets.top || 0) + margin.top,
  //       x = e.point.x,
  //       y = e.point.y,
  //       content = tooltip(e.series.key, x, y, e, chart);

  //   nv.tooltip.show([left, top], content, null, null, offsetElement);
  // };

  var showTooltip = function (e, offsetElement, properties) {
    var left = e.pos[0]
      , top = e.pos[1]
      , x = e.point.x
      , y = e.point.y
      , content = tooltipContent(e.series.key, x, y, e, chart);
    tooltip = nv.tooltip.show([left, top], content, e.value < 0 ? 'n' : 's', null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function (chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function (d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      // Now that group calculations are done,
      // group the data by filter so that legend filters
      var filteredData = d3.nest()
                          .key(filterBy)
                          .entries(data);

      //add series index to each data point for reference
      filteredData = filteredData
        .sort(function (a,b){
          //sort legend by key
          return parseInt(a.key, 10) < parseInt(b.key, 10) ? -1 : parseInt(a.key, 10) > parseInt(b.key, 10) ? 1 : 0;
        })
        .map(function (d, i) {
          d.series = i;
          d.classes = d.values[0].classes;
          d.color = d.values[0].color;
          return d;
        });

      var timeExtent = d3.extent(
              d3.merge(
                filteredData.map(function (d) {
                  return d.values.map(function (d,i) {
                    return d3.time.format("%Y-%m-%d").parse(d.x);
                  });
                })
              )
            );


    //properties.title = 'Total = $' + d3.format(',.02d')(total);
    chart.render = function () {

      container.selectAll('.nv-noData').remove();

      var width = width  || parseInt(container.style('width'), 10 || 960)
        , height = height || parseInt(container.style('height'), 10 || 400);

      var availableWidth = width - margin.left - margin.right
        , availableHeight = height - margin.top - margin.bottom;

      var innerWidth = availableWidth
        , innerHeight = availableHeight
        , innerMargin = {top: 0, right: 0, bottom: 0, left: 0};

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-bubbleChart').data([filteredData]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-bubbleChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-x nv-axis');
      gEnter.append('g').attr('class', 'nv-y nv-axis');
      gEnter.append('g').attr('class', 'nv-bubblesWrap');

      gEnter.append('g').attr('class', 'nv-controlsWrap');
      gEnter.append('g').attr('class', 'nv-legendWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');


      //------------------------------------------------------------
      // Setup Scales

      x = scatter.xScale();
      y = scatter.yScale();
      xAxis
        .scale(x);
      yAxis
        .scale(y);


      //------------------------------------------------------------
      // Title & Legend & Controls

      var titleHeight = 0
        , controlsHeight = 0
        , legendHeight = 0;

      if (showTitle && properties.title) {

        g .select('.nv-title').remove();

        g .select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height / 1.15, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        g .select('.nv-title')
            .attr('dy', '.71em');
      }

      if (showLegend) {

        legend
          .id('legend_' + chart.id())
          .width(availableWidth - controlWidth(availableWidth))
          .height(availableHeight - titleHeight)
          .key(function (d){ return d.key + '%'; });

        g .select('.nv-legendWrap')
          .datum(filteredData)
          .attr('transform', 'translate(' + controlWidth(availableWidth) + ',' + titleHeight + ')')
          .call(legend);

        legendHeight = legend.height();
      }

      //------------------------------------------------------------
      // Recalc inner margins

      innerMargin.top = titleHeight + Math.max(legendHeight,controlsHeight) + 20;
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Component(s)

      var yValues = getGroupTicks(data, innerHeight);
      innerMargin.left = nv.utils.maxStringSetLength(yValues, container, function (d,i) { return yValues[i].key; });
      innerWidth = availableWidth - innerMargin.left - innerMargin.right;

      var xD = [
        d3.time.month.floor(timeExtent[0]),
        d3.time.day.offset(d3.time.month.ceil(timeExtent[1]),-1)
      ];

      var yD = d3.extent(
            d3.merge(
              filteredData.map(function (d) {
                return d.values.map(function (d,i) {
                  return getY(d,i);
                });
              })
            ).concat(forceY)
          );

      scatter
        .size(function (d){ return d.y; }) // default size
        //.sizeDomain([16,256]) //set to speed up calculation, needs to be unset if there is a custom size accessor
        .sizeRange([256,1024])
        .singlePoint(true)
        .xScale(x)
        .xDomain(xD)
        .yScale(y)
        .yDomain(yD)
        .padData(true)
        .padDataOuter(0)
        .width(innerWidth)
        .height(innerHeight)
        //.margin(margin)
        .id(chart.id())
      ;

      var bubblesWrap = g.select('.nv-bubblesWrap')
            .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
            .datum(filteredData.filter(function (d) { return !d.disabled; }));

      bubblesWrap.call(scatter);

      xAxis
        .ticks(d3.time.months, 1)
        .tickSize(0)
        .tickValues(getTimeTicks(filteredData))
        .showMaxMin(false)
        .tickFormat(function (d) {
          return d3.time.format('%b')(new Date(d));
        });

      g .select('.nv-x.nv-axis')
          .call(xAxis);

      innerMargin[xAxis.orient()] += xAxis.height();
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      g .select('.nv-x.nv-axis')
        .attr('transform', 'translate(' + innerMargin.left + ',' + (innerHeight + innerMargin.top) + ')');

      scatter
        .width(innerWidth)
        .height(innerHeight);

      bubblesWrap
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(scatter);

      yAxis
        .ticks(yValues.length)
        .tickValues( yValues.map(function (d,i) { return yValues[i].y; }) )
        .tickSize(-innerWidth, 0)
        .tickFormat(function (d,i) { return yValues[i].key; });

      g .select('.nv-y.nv-axis')
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition()
          .call(yAxis);

    };

      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function (d, i) {
        d.disabled = !d.disabled;
        if (!data.filter(function (d) { return !d.disabled; }).length) {
          data.map(function (d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }
        container.transition().duration(chart.delay()).call(chart.render);
      });

      dispatch.on('tooltipShow', function (e) {
        if (tooltips) {
          showTooltip(e, that.parentNode);
        }
      });

      //============================================================

      chart.render();

      chart.update = function () { chart(selection); };
      chart.container = this;

    });

      // Calculate the x-axis ticks
      function getTimeTicks(data) {
        function daysInMonth(date) {
          return 32 - new Date(date.getFullYear(), date.getMonth(), 32).getDate();
        }
        var timeExtent =
              d3.extent(d3.merge(
                  data.map(function (d) {
                    return d.values.map(function (d,i) {
                      return d3.time.format("%Y-%m-%d").parse(getX(d));
                    });
                  })
                )
              );
        var timeRange =
              d3.time.month.range(
                d3.time.month.floor(timeExtent[0]),
                d3.time.month.ceil(timeExtent[1])
              );
        var timeTicks =
              timeRange.map(function (d) {
                return d3.time.day.offset(d3.time.month.floor(d), -1 + daysInMonth(d)/2);
              });
        return timeTicks;
      }

      // Group data by groupBy function to prep data for calculating y-axis groups
      // and y scale value for points
      function getGroupTicks(data,height) {

        var groupedData = d3.nest()
                            .key(groupBy)
                            .entries(data);

        // Calculate y scale parameters
        var gHeight = height/groupedData.length
          , gOffset = gHeight*0.25
          , gDomain = [0,1]
          , gRange = [0,1]
          , gScale = d3.scale.linear().domain(gDomain).range(gRange)
          , yValues = []
          , total = 0;

        // Calculate total for each data group and
        // point y value
        groupedData
          .map(function (s, i) {
            s.total = 0;

            s.values = s.values.sort(function (a, b) {
                return b.y < a.y ? -1 : b.y > a.y ? 1 : 0;
              })
              .map(function (p) {
                s.total += p.y;
                return p;
              });

            s.group = i;
            return s;
          })
          .sort(function (a, b) {
            return a.total < b.total ? -1 : a.total > b.total ? 1 : 0;
          })
          .map(function (s, i) {
            total += s.total;

            gDomain = d3.extent(s.values.map(function (p){ return p.y; }));
            gRange = [gHeight*i+gOffset, gHeight*(i+1)-gOffset];
            gScale.domain(gDomain).range(gRange);

            s.values = s.values
              .map(function (p) {
                p.group = s.group;
                p.opportunity = p.y;
                p.y = gScale(p.opportunity);
                return p;
              });

            yValues.push({y: d3.min(s.values.map(function (p){ return p.y; })), key: s.key});

            return s;
          });

        return yValues;
      }

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  scatter.dispatch.on('elementMouseover.tooltip', function (e) {
    dispatch.tooltipShow(e);
  });

  scatter.dispatch.on('elementMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function () {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  scatter.dispatch.on('elementMousemove', function (e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function (e) {
    if (tooltip) {
      nv.tooltip.position(tooltip, e.pos);
    }
  });

  scatter.dispatch.on('elementClick', function (e) {
    bubbleClick(e);
    nv.tooltip.cleanup();
  });

  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.scatter = scatter;
  chart.legend = legend;
  chart.xAxis = xAxis;
  chart.yAxis = yAxis;

  d3.rebind(chart, scatter, 'interactive', 'size', 'x', 'y', 'id', 'delay', 'forceX', 'forceY', 'xScale', 'yScale', 'zScale', 'xDomain', 'yDomain', 'sizeDomain', 'forceSize', 'clipEdge', 'clipVoronoi', 'clipRadius', 'color', 'fill', 'classes', 'gradient');
  d3.rebind(chart, xAxis, 'rotateLabels', 'reduceXTicks');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,d.series); },
        classes = function (d,i) { return 'nv-group nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(d.series/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (d.series*(params.step || 1)) % 20;
          return 'nv-group nv-series-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      return scatter.gradient(d,d.series);
    };

    scatter.color(colors);
    scatter.fill(fill);
    scatter.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function (_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showLegend = function (_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function (_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function (_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.delay = function(_) {
    if (!arguments.length) { return delay; }
    delay = _;
    return chart;
  };

  chart.tooltipContent = function (_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.bubbleClick = function (_) {
    if (!arguments.length) { return bubbleClick; }
    bubbleClick = _;
    return chart;
  };

  chart.noData = function (_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  chart.groupBy = function (_) {
    if (!arguments.length) { return groupBy; }
    groupBy = _;
    return chart;
  };

  chart.filterBy = function (_) {
    if (!arguments.length) { return filterBy; }
    filterBy = _;
    return chart;
  };

  chart.colorFill = function (_) {
    return chart;
  };

  //============================================================

  return chart;
};

nv.models.funnel = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , width = 960
    , height = 500
    , x = d3.scale.ordinal()
    , y = d3.scale.linear()
    , id = Math.floor(Math.random() * 10000) //Create semi-unique ID in case user doesn't select one
    , getX = function(d) { return d.x; }
    , getY = function(d) { return d.height; }
    , getV = function(d) { return d.value; }
    , forceY = [0] // 0 is forced by default.. this makes sense for the majority of bar graphs... user can always do chart.forceY([]) to remove
    , clipEdge = true
    , delay = 1200
    , xDomain
    , yDomain
    , fmtValueLabel = function (d) { return d.value; }
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-group nv-series-' + i; }
    , dispatch = d3.dispatch('chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout', 'elementMousemove')
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var x0, y0 //used to store previous scales
      ;

  //============================================================

  function chart(selection) {
    selection.each(function(data) {
      var availableWidth = width - margin.left - margin.right
        , availableHeight = height - margin.top - margin.bottom
        , container = d3.select(this)
        , labelBoxWidth = 20
        , funnelTotal = 0
        , funnelArea = 0
        , funnelBase = 0
        , funnelShift = 0
        , funnelMinHeight = 8;

      var w = Math.min(availableHeight/1.1, availableWidth-40) //width
        , r = 0.3 // ratio of width to height (or slope)
        , c = availableWidth / 2 //center
        ;

      function pointsTrapezoid(y0,y1,h) {
        var w0 = w / 2 - r * y0
          , w1 = w / 2 - r * y1;
        return (
          (c - w0) +','+  (y0 * h) +' '+
          (c - w1) +','+  (y1 * h) +' '+
          (c + w1) +','+  (y1 * h) +' '+
          (c + w0) +','+  (y0 * h)
        );
      }

      // v = 1/2 * h * (b + b + 2*r*h);
      // 2v = h * (b + b + 2*r*h);
      // 2v = h * (2*b + 2*r*h);
      // 2v = 2*b*h + 2*r*h*h;
      // v = b*h + r*h*h;
      // v - b*h - r*h*h = 0;
      // v/r - b*h/r - h*h = 0;
      // b/r*h + h*h + b/r/2*b/r/2 = v/r + b/r/2*b/r/2;
      // h*h + b/r*h + b/r/2*b/r/2 = v/r + b/r/2*b/r/2;
      // (h + b/r/2)(h + b/r/2) = v/r + b/r/2*b/r/2;
      // h + b/r/2 = Math.sqrt(v/r + b/r/2*b/r/2);
      // h  = Math.abs(Math.sqrt(v/r + b/r/2*b/r/2)) - b/r/2;

      function heightTrapezoid(a,b) {
        var x = b / r / 2;
        return Math.abs(Math.sqrt(a / r + x * x)) - x;
      }

      function areaTrapezoid (h,w) {
        return h * (w - h * r);
      }

      funnelArea = areaTrapezoid(availableHeight,w);
      funnelBase = w - 2*r*availableHeight;

      //add series index to each data point for reference
      data = data.map(function(series, i) {
        series.values = series.values.map(function(point) {
          point.series = i;
          point.value = point.value || point.y;
          funnelTotal += point.value;
          return point;
        });
        return series;
      });

      //add percent of total for each data point for reference
      data = data.map(function(series, i) {
        series.values = series.values.map(function(point) {
          point.height = heightTrapezoid(funnelArea * point.value / funnelTotal, funnelBase);
          if (point.height < funnelMinHeight) {
            funnelShift += point.height - funnelMinHeight;
            point.height = funnelMinHeight;
          } else if (funnelShift < 0 && point.height + funnelShift > funnelMinHeight) {
            point.height += funnelShift;
            funnelShift = 0;
          }
          funnelBase += 2*r*point.height;
          return point;
        });
        return series;
      });

      data = d3.layout.stack()
               .offset('zero')
               .values(function(d){ return d.values; })
               .y(getY)
               (data);

      //------------------------------------------------------------
      // Setup Scales

      // remap and flatten the data for use in calculating the scales' domains
      var seriesData = (xDomain && yDomain) ? [] : // if we know xDomain and yDomain, no need to calculate
            data.map(function(d) {
              return d.values.map(function(d,i) {
                return { x: getX(d,i), y: getY(d,i), y0: d.y0 };
              });
            });

      x   .domain(xDomain || d3.merge(seriesData).map(function(d) { return d.x; }))
          .rangeBands([0, availableWidth], 0.1);

      y   .domain(yDomain || d3.extent(d3.merge(seriesData).map(function(d) { return d.y + d.y0; }).concat(forceY)))
          .range([availableHeight, 0]);

      x0 = x0 || x;
      y0 = y0 || y;

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-funnel').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-funnel');
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function(d,i,p) {
        return nv.utils.colorLinearGradient( d, id+'-'+i, p, color(d,i), wrap.select('defs') );
      };

      gEnter.append('g').attr('class', 'nv-groups');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Clip Path

      defsEnter.append('clipPath')
          .attr('id', 'nv-edge-clip-' + id)
        .append('rect');
      wrap.select('#nv-edge-clip-' + id + ' rect')
          .attr('width', availableWidth)
          .attr('height', availableHeight);
      g.attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + id + ')' : '');

      //------------------------------------------------------------

      var groups = wrap.select('.nv-groups').selectAll('.nv-group')
          .data(function(d) { return d; }, function(d) { return d.key; });

      groups.enter().append('g')
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6);

      d3.transition(groups.exit()).duration(0)
        .selectAll('polygon.nv-bar')
        .delay(function(d,i) { return i * delay / data[0].values.length; })
          .attr('points', function(d) {
              return pointsTrapezoid(y(d.y0), y(d.y0+d.y), 0);
            })
          .remove();

      d3.transition(groups.exit()).duration(0)
        .selectAll('g.nv-label-value')
        .delay(function(d,i) { return i * delay / data[0].values.length; })
          .attr('y', 0)
          .style('fill-opacity', 1e-6)
          .attr('transform', 'translate('+ c +',0)')
          .remove();

      d3.transition(groups.exit()).duration(0)
        .selectAll('text.nv-label-group')
        .delay(function(d,i) { return i * delay / data[0].values.length; })
          .attr('y', 0)
          .style('fill-opacity', 1e-6)
          .attr('transform', 'translate('+ availableWidth +',0)')
          .remove();

      groups
          .attr('class', function(d,i) { return this.getAttribute('class') || classes(d,i); })
          .classed('hover', function(d) { return d.hover; })
          .attr('fill', function(d,i){ return this.getAttribute('fill') || fill(d,i); })
          .attr('stroke', function(d,i){ return this.getAttribute('fill') || fill(d,i); });

      d3.transition(groups).duration(0)
          .style('stroke-opacity', 1)
          .style('fill-opacity', 1);
      //------------------------------------------------------------
      // Polygons

      var funs = groups.selectAll('polygon.nv-bar')
          .data(function(d) { return d.values; });

      var funsEnter = funs.enter()
          .append('polygon')
            .attr('class', 'nv-bar positive')
            .attr('points', function(d) {
              return pointsTrapezoid(y(d.y0), y(d.y0+d.y), 0);
            });

      d3.transition(funs).duration(0)
          .delay(function(d,i) { return i * delay / data[0].values.length; })
          .attr('points', function(d) {
            return pointsTrapezoid(y(d.y0), y(d.y0+d.y), 1);
          });

      //------------------------------------------------------------
      // Value Labels

      var lblValue = groups.selectAll('.nv-label-value')
            .data( function(d) { return d.values; } );

      var lblValueEnter = lblValue.enter()
            .append('g')
              .attr('class', 'nv-label-value')
              .attr('transform', 'translate('+ c +',0)');

      // lblValueEnter.append('rect')
      //     .attr('x', -labelBoxWidth/2)
      //     .attr('y', -20)
      //     .attr('width', labelBoxWidth)
      //     .attr('height', 40)
      //     .attr('rx',3)
      //     .attr('ry',3)
      //     .style('fill', fill({},0))
      //     .attr('stroke', 'none')
      //     .style('fill-opacity', 0.4)
      //   ;

      lblValueEnter.append('text')
          .attr('x', 0)
          .attr('y', 5)
          .attr('text-anchor', 'middle')
          .text(function(d){ return (d.height > 2*funnelMinHeight) ? fmtValueLabel(d) : ''; })
          .style('pointer-events', 'none')
        ;

      // lblValue.selectAll('text').each(function(d,i){
      //       var width = this.getBBox().width + 20;
      //       if(width > labelBoxWidth) {
      //         labelBoxWidth = width;
      //       }
      //     });
      // lblValue.selectAll('rect').each(function(d,i){
      //       d3.select(this)
      //         .attr('width', labelBoxWidth)
      //         .attr('x', -labelBoxWidth/2);
      //     });

      d3.transition(lblValue).duration(0)
          .delay(function(d,i) { return i * delay / data[0].values.length; })
          .attr('transform', function(d){ return 'translate('+ c +','+ ( y(d.y0+d.y/2) ) +')'; });

      //------------------------------------------------------------
      // Group Labels

      var lblGroup = groups.selectAll('text.nv-label-group')
          .data( function(d) { return [ { y: d.values[0].y, y0: d.values[0].y0, key: d.count } ]; });

      var lblGroupEnter = lblGroup.enter()
          .append('text')
            .attr('class', 'nv-label-group')
            .attr('x', 0 )
            .attr('y', 0 )
            .attr('dx', -10)
            .attr('dy', 5)
            .attr('text-anchor', 'middle')
            .text(function(d) { return d.key; })
            .attr('stroke', 'none')
            .style('fill', 'black')
            .style('fill-opacity', 1e-6)
            .attr('transform', 'translate('+ availableWidth +',0)')
          ;

      d3.transition(lblGroup).duration(0)
          .delay(function(d,i) { return i * delay / data[0].values.length; })
          .style('fill-opacity', 1)
          .attr('transform', function(d){ return 'translate('+ availableWidth +','+ ( y(d.y0+d.y/2) ) +')'; })
        ;

      //------------------------------------------------------------

      funs
          .on('mouseover', function(d,i) { //TODO: figure out why j works above, but not here
            d3.select(this).classed('hover', true);
            dispatch.elementMouseover({
              value: getV(d,i),
              point: d,
              series: data[d.series],
              pos: [x(getX(d,i)) + ( x.rangeBand() * (data.length / 2) / data.length ), y(getY(d,i) + d.y0)],  // TODO: Figure out why the value appears to be shifted
              pointIndex: i,
              seriesIndex: d.series,
              e: d3.event
            });
          })
          .on('mouseout', function(d,i) {
            d3.select(this).classed('hover', false);
            dispatch.elementMouseout({
              value: getV(d,i),
              point: d,
              series: data[d.series],
              pointIndex: i,
              seriesIndex: d.series,
              e: d3.event
            });
          })
          .on('mousemove', function(d,i){
            dispatch.elementMousemove({
              point: d,
              pointIndex: i,
              pos: [d3.event.pageX, d3.event.pageY],
              id: id
            });
          })
          .on('click', function(d,i) {
            dispatch.elementClick({
              value: getV(d,i),
              point: d,
              series: data[d.series],
              pos: [x(getX(d,i)) + ( x.rangeBand() * (data.length / 2) / data.length ), y(getY(d,i) + d.y0)],  // TODO: Figure out why the value appears to be shifted
              pointIndex: i,
              seriesIndex: d.series,
              e: d3.event
            });
            d3.event.stopPropagation();
          })
          .on('dblclick', function(d,i) {
            dispatch.elementDblClick({
              value: getV(d,i),
              point: d,
              series: data[d.series],
              pos: [x(getX(d,i)) + ( x.rangeBand() * (data.length / 2) / data.length ), y(getY(d,i) + d.y0)],  // TODO: Figure out why the value appears to be shifted
              pointIndex: i,
              seriesIndex: d.series,
              e: d3.event
            });
            d3.event.stopPropagation();
          });


      //store old scales for use in transitions on update
      x0 = x.copy();
      y0 = y.copy();

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) return color;
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) return fill;
    fill = _;
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) return classes;
    classes = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) return gradient;
    gradient = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) return getX;
    getX = _;
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return getY;
    getY = _;
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin = _;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.xScale = function(_) {
    if (!arguments.length) return x;
    x = _;
    return chart;
  };

  chart.yScale = function(_) {
    if (!arguments.length) return y;
    y = _;
    return chart;
  };

  chart.xDomain = function(_) {
    if (!arguments.length) return xDomain;
    xDomain = _;
    return chart;
  };

  chart.yDomain = function(_) {
    if (!arguments.length) return yDomain;
    yDomain = _;
    return chart;
  };

  chart.forceY = function(_) {
    if (!arguments.length) return forceY;
    forceY = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) return id;
    id = _;
    return chart;
  };

  chart.delay = function(_) {
    if (!arguments.length) return delay;
    delay = _;
    return chart;
  };

  chart.clipEdge = function(_) {
    if (!arguments.length) return clipEdge;
    clipEdge = _;
    return chart;
  };

  chart.fmtValueLabel = function(_) {
    if (!arguments.length) return fmtValueLabel;
    fmtValueLabel = _;
    return chart;
  };

  //============================================================


  return chart;
}

nv.models.funnelChart = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 10, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showControls = false
    , showLegend = true
    , tooltip = null
    , tooltips = true
    , tooltipContent = function (key, x, y, e, graph) {
        return '<h3>' + key + " - " + x + '</h3>' +
               '<p>' +  y + '</p>';
      }
    , x
    , y
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove')
    , controlWidth = function (w) { return showControls ? w * 0.3 : 0; }
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var funnel = nv.models.funnel()
    , yAxis = nv.models.axis()
        .orient('left')
        .tickFormat(function (d) { return ''; })
    , legend = nv.models.legend()
    ;

  var showTooltip = function (e, offsetElement, properties) {
    var left = e.pos[0]
      , top = e.pos[1]
      , x = (e.point.value * 100 / properties.total).toFixed(1)
      , y = e.point.value
      , content = tooltipContent(e.series.key, x, y, e, chart);
    tooltip = nv.tooltip.show([left, top], content, e.value < 0 ? 'n' : 's', null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function (chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right
        , availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      var innerWidth = availableWidth
        , innerHeight = availableHeight
        , innerMargin = {top: 0, right: 0, bottom: 0, left: 0};

      chart.update = function () { container.transition().duration(chart.delay()).call(chart); };
      chart.container = this;

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length || !data.filter(function (d) { return d.values.length; }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function (d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------
      // Setup Scales

      x = funnel.xScale();
      //y = funnel.yScale(); //see below

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-funnelChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-funnelChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-y nv-axis');
      gEnter.append('g').attr('class', 'nv-funnelWrap');

      gEnter.append('g').attr('class', 'nv-legendWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Title & Legend & Controls

      var titleHeight = 0
        , controlsHeight = 0
        , legendHeight = 0;

      if (showTitle && properties.title) {
        g .select('.nv-title').remove();

        g .select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height / 1.15, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        g .select('.nv-title')
            .attr('dy', '.71em');
      }

      if (showLegend) {
        legend
          .id('legend_' + chart.id())
          .width(availableWidth - controlWidth(availableWidth))
          .height(availableHeight - titleHeight)
          .align('center');

        g .select('.nv-legendWrap')
          .datum(data)
          .attr('transform', 'translate(' + controlWidth(availableWidth) + ',' + titleHeight + ')')
          .call(legend);

        legendHeight = legend.height();
      }

      //------------------------------------------------------------
      // Recalc inner margins

      innerMargin.top = titleHeight + Math.max(legendHeight,controlsHeight) + 8;
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Component(s)

      var funnelWrap = g.select('.nv-funnelWrap')
            .datum(data.filter(function (d) { return !d.disabled; }));

      funnel
        .width(innerWidth)
        .height(innerHeight);

      funnelWrap
          .call(funnel);

      //------------------------------------------------------------
      // Setup Scales (again, not sure why it has to be here and not above?)

      var series1 = [{x:0,y:0}];
      var series2 = data.filter(function (d) {
              return !d.disabled;
            })
            .map(function (d) {
              return d.values.map(function (d,i) {
                return { x: d.x, y: d.y0+d.y };
              });
            });
      var tickData = d3.merge( series1.concat(series2) );

      // remap and flatten the data for use in calculating the scales' domains
      var minmax = d3.extent(tickData, function (d) { return d.y; });
      var aTicks = d3.merge(tickData).map(function (d) { return d.y; });

      y = d3.scale.linear().domain(minmax).range([innerHeight,0]);

      yScale = d3.scale.quantile()
                 .domain(aTicks)
                 .range(aTicks.map(function (d){ return y(d); }));

      //------------------------------------------------------------
      // Main Chart Components
      // Recall to set final size

      funnel
        .width(innerWidth)
        .height(innerHeight);

      funnelWrap
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(funnel);

      yAxis
        .tickSize(-innerWidth, 0)
        .scale(yScale)
        .tickValues(aTicks);

      g .select('.nv-y.nv-axis')
        .attr('transform', 'translate(' + (yAxis.orient() === 'left' ? innerMargin.left : innerWidth) + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(yAxis);

      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function (d, i) {
        d.disabled = !d.disabled;
        if (!data.filter(function (d) { return !d.disabled; }).length) {
          data.map(function (d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }
        container.transition().duration(chart.delay()).call(chart);
      });

      dispatch.on('tooltipShow', function (e) {
        if (tooltips) {
          showTooltip(e, that.parentNode, properties);
        }
      });

    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  funnel.dispatch.on('elementMouseover.tooltip', function (e) {
    dispatch.tooltipShow(e);
  });

  funnel.dispatch.on('elementMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function () {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  funnel.dispatch.on('elementMousemove', function (e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function (e) {
    if (tooltip) {
      nv.tooltip.position(tooltip, e.pos);
    }
  });


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.funnel = funnel;
  chart.legend = legend;
  chart.yAxis = yAxis;

  d3.rebind(chart, funnel, 'x', 'y', 'xDomain', 'yDomain', 'forceX', 'forceY', 'clipEdge', 'id', 'delay', 'fmtValueLabel', 'color', 'fill', 'classes', 'gradient');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,i); },
        classes = function (d,i) { return 'nv-group nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1)) % 20;
          return 'nv-group nv-series-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      var p = {orientation: params.orientation || 'vertical', position: params.position || 'middle'};
      return funnel.gradient(d,i,p);
    };

    funnel.color(colors);
    funnel.fill(fill);
    funnel.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.x = function (_) {
    if (!arguments.length) { return getX; }
    getX = _;
    lines.x(_);
    funnelWrap.x(_);
    return chart;
  };

  chart.y = function (_) {
    if (!arguments.length) { return getY; }
    getY = _;
    lines.y(_);
    funnel.y(_);
    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function (_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showLegend = function (_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function (_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function (_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function (_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.noData = function (_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  chart.colorFill = function (_) {
    return chart;
  };

  //============================================================

  return chart;
};

nv.models.gauge = function() {
  /* original inspiration for this chart type is at http://bl.ocks.org/3202712 */
  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , width = null
    , height = null
    , clipEdge = true
    , getValues = function(d) { return d.values; }
    , getX = function(d) { return d.key; }
    , getY = function(d) { return d.y; }
    , id = Math.floor(Math.random() * 10000) //Create semi-unique ID in case user doesn't select one
    , labelFormat = d3.format(',g')
    , valueFormat = d3.format(',.f')
    , showLabels = true
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-group nv-series-' + i; }
    , dispatch = d3.dispatch('chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout', 'elementMousemove')
  ;

  var ringWidth = 50
    , pointerWidth = 5
    , pointerTailLength = 5
    , pointerHeadLength = 90
    , minValue = 0
    , maxValue = 10
    , minAngle = -90
    , maxAngle = 90
    , transitionMs = 750
    , labelInset = 10
  ;

  //============================================================

  //colorScale = d3.scale.linear().domain([0, .5, 1].map(d3.interpolate(min, max))).range(["green", "yellow", "red"]);

  function chart(selection)
  {
    selection.each(

    function(chartData) {

      var properties = chartData.properties
        , data = chartData.data;

        var availableWidth = width - margin.left - margin.right
          , availableHeight = height - margin.top - margin.bottom
          , radius =  Math.min( (availableWidth/2), availableHeight ) / (  (100+labelInset)/100  )
          , container = d3.select(this)
          , range = maxAngle - minAngle
          , scale = d3.scale.linear().range([0,1]).domain([minValue, maxValue])
          , previousTick = 0
          , arcData = data.map( function(d,i){
              var rtn = {
                  key:d.key
                , y0:previousTick
                , y1:d.y
                , color:d.color
                , classes:d.classes
              };
              previousTick = d.y;
              return rtn;
            })
          , labelData = [0].concat( data.map( function(d){ return d.y; } ) )
          , prop = function(d){ return d*radius/100; }
          ;

        //------------------------------------------------------------
        // Setup containers and skeleton of chart

        var wrap = container.selectAll('g.nv-wrap.nv-gauge').data([data]);
        var wrapEnter = wrap.enter().append('g').attr('class','nvd3 nv-wrap nv-gauge');
        var defsEnter = wrapEnter.append('defs');
        var gEnter = wrapEnter.append('g');
        var g = wrap.select('g');

        //set up the gradient constructor function
        chart.gradient = function(d,i) {
          return nv.utils.colorRadialGradient( d, id+'-'+i, {x:0, y:0, r:radius, s:ringWidth/100, u:'userSpaceOnUse'}, color(d,i), wrap.select('defs') );
        };

        gEnter.append('g').attr('class', 'nv-arc-group');
        gEnter.append('g').attr('class', 'nv-labels');
        gEnter.append('g').attr('class', 'nv-pointer');
        gEnter.append('g').attr('class', 'nv-odometer');

        wrap.attr('transform', 'translate('+ (margin.left/2 + margin.right/2 + prop(labelInset)) +','+ (margin.top + prop(labelInset)) +')');
        //g.select('.nv-arc-gauge').attr('transform', 'translate('+ availableWidth/2 +','+ availableHeight/2 +')');

        //------------------------------------------------------------

        // defsEnter.append('clipPath')
        //     .attr('id', 'nv-edge-clip-' + id)
        //   .append('rect');
        // wrap.select('#nv-edge-clip-' + id + ' rect')
        //     .attr('width', availableWidth)
        //     .attr('height', availableHeight);
        // g.attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + id + ')' : '');

        //------------------------------------------------------------
        // Gauge arcs
        var arc = d3.svg.arc()
          .innerRadius( prop(ringWidth) )
          .outerRadius( radius )
          .startAngle(function(d, i) {
            return deg2rad( newAngle(d.y0) );
          })
          .endAngle(function(d, i) {
            return deg2rad( newAngle(d.y1) );
          });

        var ag = g.select('.nv-arc-group')
            .attr('transform', centerTx);

        ag.selectAll('.nv-arc-path').transition().duration(10)
            .attr('fill', function(d,i){ return fill(d,i); } )
            .attr('d', arc);

        ag.selectAll('.nv-arc-path')
            .data(arcData)
          .enter().append('path')
            //.attr('class', 'nv-arc-path')
            .attr('class', function(d,i) { return this.getAttribute('class') || classes(d,i); })
            .attr('fill', function(d,i){ return fill(d,i); } )
            .attr('stroke', '#ffffff')
            .attr('stroke-width', 3)
            .attr('d', arc)
            .on('mouseover', function(d,i){
              d3.select(this).classed('hover', true);
              dispatch.elementMouseover({
                  point: d,
                  pointIndex: i,
                  pos: [d3.event.pageX, d3.event.pageY],
                  id: id
              });
            })
            .on('mouseout', function(d,i){
              d3.select(this).classed('hover', false);
              dispatch.elementMouseout({
                  point: d,
                  index: i,
                  id: id
              });
            })
            .on('mousemove', function(d,i){
              dispatch.elementMousemove({
                point: d,
                pointIndex: i,
                pos: [d3.event.pageX, d3.event.pageY],
                id: id
              });
            })
            .on('click', function(d,i) {
              dispatch.elementClick({
                  point: d,
                  index: i,
                  pos: d3.event,
                  id: id
              });
              d3.event.stopPropagation();
            })
            .on('dblclick', function(d,i) {
              dispatch.elementDblClick({
                  point: d,
                  index: i,
                  pos: d3.event,
                  id: id
              });
              d3.event.stopPropagation();
            });

        //------------------------------------------------------------
        // Gauge labels
        var lg = g.select('.nv-labels')
            .attr('transform', centerTx);

        lg.selectAll('text').transition().duration(0)
            .attr('transform', function(d) {
              return 'rotate('+ newAngle(d) +') translate(0,'+ (-radius-prop(1.5)) +')';
            })
            .style('font-size', prop(0.6)+'em');

        lg.selectAll('text')
            .data(labelData)
          .enter().append('text')
            .attr('transform', function(d) {
              return 'rotate('+ newAngle(d) +') translate(0,'+ (-radius-prop(1.5)) +')';
            })
            .text(labelFormat)
            .style('text-anchor', 'middle')
            .style('font-size', prop(0.6)+'em');

        //------------------------------------------------------------
        // Gauge pointer
        var pointerData = [
              [ Math.round(prop(pointerWidth)/2),    0 ],
              [ 0, -Math.round(prop(pointerHeadLength))],
              [ -(Math.round(prop(pointerWidth)/2)), 0 ],
              [ 0, Math.round(prop(pointerWidth)) ],
              [ Math.round(prop(pointerWidth)/2),    0 ]
            ];

        var pg = g.select('.nv-pointer')
            .attr('transform', centerTx);

        pg.selectAll('path').transition().duration(120)
          .attr('d', d3.svg.line().interpolate('monotone'));

        var pointer = pg.selectAll('path')
            .data([pointerData])
          .enter().append('path')
            .attr('d', d3.svg.line().interpolate('monotone')/*function(d) { return pointerLine(d) +'Z';}*/ )
            .attr('transform', 'rotate('+ minAngle +')');

        setGaugePointer(properties.value);

        //------------------------------------------------------------
        // Odometer readout
        g.selectAll('.nv-odom').remove();

        g.select('.nv-odomText').transition().duration(0)
            .style('font-size', prop(0.7)+'em');

        g.select('.nv-odometer')
          .append('text')
            .attr('class', 'nv-odom nv-odomText')
            .attr('x', 0)
            .attr('y', 0 )
            .attr('text-anchor', 'middle')
            .text( valueFormat( properties.value) )
            .style('stroke', 'none')
            .style('fill', 'black')
            .style('font-size', prop(0.7)+'em')
          ;

        var bbox = g.select('.nv-odomText').node().getBBox();

        g.select('.nv-odometer')
          .insert('path','.nv-odomText')
          .attr('class', 'nv-odom nv-odomBox')
          .attr("d",
            nv.utils.roundedRectangle(
              -bbox.width/2, -bbox.height+prop(1.5), bbox.width+prop(4), bbox.height+prop(2), prop(2)
            )
          )
          .attr('fill', '#eeffff')
          .attr('stroke','black')
          .attr('stroke-width','2px')
          .attr('opacity',0.8)
        ;

        g.select('.nv-odometer')
            .attr('transform', 'translate('+ radius +','+ ( margin.top + prop(70) + bbox.width ) +')');


        //------------------------------------------------------------
        // private functions
        function setGaugePointer(d) {
          pointer.transition()
            .duration(transitionMs)
            .ease('elastic')
            .attr('transform', 'rotate('+ newAngle(d) +')');
        }

        function deg2rad(deg) {
          return deg * Math.PI/180;
        }

        function newAngle(d) {
          return minAngle + ( scale(d) * range );
        }

        // Center translation
        function centerTx() {
          return 'translate('+ radius +','+ radius +')';
        }

        chart.setGaugePointer = setGaugePointer;

      }

    );

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) return color;
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) return fill;
    fill = _;
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) return classes;
    classes = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) return gradient;
    gradient = _;
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin = _;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.values = function(_) {
    if (!arguments.length) return getValues;
    getValues = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) return getX;
    getX = _;
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return getY;
    getY = d3.functor(_);
    return chart;
  };

  chart.showLabels = function(_) {
    if (!arguments.length) return showLabels;
    showLabels = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) return id;
    id = _;
    return chart;
  };

  chart.valueFormat = function(_) {
    if (!arguments.length) return valueFormat;
    valueFormat = _;
    return chart;
  };

  chart.labelThreshold = function(_) {
    if (!arguments.length) return labelThreshold;
    labelThreshold = _;
    return chart;
  };

  // GAUGE
  chart.ringWidth = function(_) {
    if (!arguments.length) return ringWidth;
    ringWidth = _;
    return chart;
  };
  chart.pointerWidth = function(_) {
    if (!arguments.length) return pointerWidth;
    pointerWidth = _;
    return chart;
  };
  chart.pointerTailLength = function(_) {
    if (!arguments.length) return pointerTailLength;
    pointerTailLength = _;
    return chart;
  };
  chart.pointerHeadLength = function(_) {
    if (!arguments.length) return pointerHeadLength;
    pointerHeadLength = _;
    return chart;
  };
  chart.minValue = function(_) {
    if (!arguments.length) return minValue;
    minValue = _;
    return chart;
  };
  chart.maxValue = function(_) {
    if (!arguments.length) return maxValue;
    maxValue = _;
    return chart;
  };
  chart.minAngle = function(_) {
    if (!arguments.length) return minAngle;
    minAngle = _;
    return chart;
  };
  chart.maxAngle = function(_) {
    if (!arguments.length) return maxAngle;
    maxAngle = _;
    return chart;
  };
  chart.transitionMs = function(_) {
    if (!arguments.length) return transitionMs;
    transitionMs = _;
    return chart;
  };
  chart.labelFormat = function(_) {
    if (!arguments.length) return labelFormat;
    labelFormat = _;
    return chart;
  };
  chart.labelInset = function(_) {
    if (!arguments.length) return labelInset;
    labelInset = _;
    return chart;
  };
  chart.setPointer = function(_) {
    if (!arguments.length) return chart.setGaugePointer;
    chart.setGaugePointer(_);
    return chart;
  };
  chart.isRendered = function(_) {
    return (svg !== undefined);
  };


  //============================================================

  return chart;
}

nv.models.gaugeChart = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 30, right: 20, bottom: 20, left: 20}
    , width = null
    , height = null
    , showTitle = false
    , showLegend = true
    , tooltip = null
    , tooltips = true
    , tooltipContent = function(key, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + '</p>';
      }
    , x //can be accessed via chart.xScale()
    , y //can be accessed via chart.yScale()
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove')
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var gauge = nv.models.gauge()
    , legend = nv.models.legend()
    ;

  var showTooltip = function(e, offsetElement) {
    var left = e.pos[0],
        top = e.pos[1],
        y = gauge.valueFormat()((e.point.y1-e.point.y0)),
        content = tooltipContent(e.point.key, y, e, chart);

    tooltip = nv.tooltip.show([left, top], content, e.value < 0 ? 'n' : 's', null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function(chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right,
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      chart.update = function() { container.transition().duration(chart.delay()).call(chart); };
      chart.container = this;


      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function(d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-gaugeChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-gaugeChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-gaugeWrap');

      gEnter.append('g').attr('class', 'nv-legendWrap');

      //------------------------------------------------------------
      // Title & Legend

      var titleHeight = 0
        , legendHeight = 0;

      if (showLegend) {

        legend
          .id('legend_' + chart.id())
          .width(availableWidth)
          .height(availableHeight)
          .align('center')
          .key(gauge.x());

        g.select('.nv-legendWrap')
            .datum(data)
            .call(legend);

        legendHeight = legend.height();

        if ( margin.top !== legendHeight + titleHeight ) {
          margin.top = legendHeight + titleHeight;
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;
        }

        g.select('.nv-legendWrap')
            .attr('transform', 'translate(0,' + (-margin.top) +')');
      }

      if (showTitle && properties.title ) {

        g.select('.nv-title').remove();

        g.select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        if ( margin.top !== titleHeight + legendHeight )
        {
          margin.top = titleHeight + legendHeight;
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;
        }

        g.select('.nv-titleWrap')
            .attr('transform', 'translate(0,' + ( -margin.top+parseInt(g.select('.nv-title').node().getBBox().height, 10) ) + ')');
      }

      //------------------------------------------------------------


      wrap.attr('transform', 'translate('+ margin.left +','+ margin.top +')');


      //------------------------------------------------------------
      // Main Chart Component(s)

      gauge
        .width(availableWidth)
        .height(availableHeight);

      var gaugeWrap = g.select('.nv-gaugeWrap')
          .datum(chartData);

      gaugeWrap.transition().call(gauge);

      //gauge.setPointer(properties.value);

      //------------------------------------------------------------


      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      dispatch.on('tooltipShow', function(e) {
        if (tooltips) {
          showTooltip(e);
        }
      });

    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  gauge.dispatch.on('elementMouseover.tooltip', function(e) {
    e.pos = [e.pos[0] + margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  gauge.dispatch.on('elementMouseout.tooltip', function(e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function() {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  gauge.dispatch.on('elementMousemove', function(e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function(e) {
    if (tooltip) {
      nv.tooltip.position(tooltip,e.pos);
    }
  });


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.legend = legend;
  chart.gauge = gauge;

  d3.rebind(chart, gauge, 'valueFormat', 'values', 'x', 'y', 'id', 'showLabels', 'setPointer', 'ringWidth', 'labelThreshold', 'maxValue', 'minValue', 'transitionMs', 'color', 'fill', 'classes', 'gradient');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,i); },
        classes = function (d,i) { return 'nv-group nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1))%20;
          return 'nv-group nv-series-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      return gauge.gradient(d,i);
    };

    gauge.color(colors);
    gauge.fill(fill);
    gauge.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) { return margin; }
    margin = _;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function(_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showLegend = function(_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function(_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function(_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function(_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.noData = function(_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  //============================================================


  return chart;
};

nv.models.line = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var scatter = nv.models.scatter()
    ;

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , width = 960
    , height = 500
    , getX = function(d) { return d.x; } // accessor to get the x value from a data point
    , getY = function(d) { return d.y; } // accessor to get the y value from a data point
    , defined = function(d,i) { return !isNaN(getY(d,i)) && getY(d,i) !== null; } // allows a line to be not continuous when it is not defined
    , isArea = function(d) { return (d && d.area) || false; } // decides if a line is an area or just a line
    , clipEdge = false // if true, masks lines within x and y scale
    , x //can be accessed via chart.xScale()
    , y //can be accessed via chart.yScale()
    , delay = 200
    , interpolate = "linear" // controls the line interpolation
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-group nv-series-'+ i; }
    ;

  scatter
    .size(16) // default size
    .sizeDomain([16,256]) //set to speed up calculation, needs to be unset if there is a custom size accessor
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var x0, y0 //used to store previous scales
      ;

  //============================================================


  function chart(selection) {
    selection.each(function(data) {
      var availableWidth = width - margin.left - margin.right,
          availableHeight = height - margin.top - margin.bottom,
          container = d3.select(this);

      //------------------------------------------------------------
      // Setup Scales

      x = scatter.xScale();
      y = scatter.yScale();

      x0 = x0 || x;
      y0 = y0 || y;

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-line').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-line');
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function(d,i,p) {
        return nv.utils.colorLinearGradient( d, chart.id() + '-' + i, p, color(d,i), wrap.select('defs') );
      };

      gEnter.append('g').attr('class', 'nv-groups');
      gEnter.append('g').attr('class', 'nv-scatterWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------

      scatter
        .width(availableWidth)
        .height(availableHeight);

      var scatterWrap = wrap.select('.nv-scatterWrap');
          //.datum(data); // Data automatically trickles down from the wrap

      d3.transition(scatterWrap).call(scatter);


      defsEnter.append('clipPath')
          .attr('id', 'nv-edge-clip-' + scatter.id())
        .append('rect');

      wrap.select('#nv-edge-clip-' + scatter.id() + ' rect')
          .attr('width', availableWidth)
          .attr('height', availableHeight);

      g   .attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + scatter.id() + ')' : '');
      scatterWrap
          .attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + scatter.id() + ')' : '');


      var groups = wrap.select('.nv-groups').selectAll('.nv-group')
          .data(function(d) { return d; }, function(d) { return d.key; });
      groups.enter().append('g')
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6);
      d3.transition(groups.exit())
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6)
          .remove();
      groups
          .classed('hover', function(d) { return d.hover; })
          .attr('class', function(d,i) { return this.getAttribute('class') || classes(d,i); })
          .attr('fill', function(d,i){ return this.getAttribute('fill') || fill(d,i); })
          .attr('stroke', function(d,i){ return this.getAttribute('stroke') || fill(d,i); });
      d3.transition(groups)
          .style('stroke-opacity', 1)
          .style('fill-opacity', 0.5);


      var areaPaths = groups.selectAll('path.nv-area')
          .data(function(d) { return isArea(d) ? [d] : []; }); // this is done differently than lines because I need to check if series is an area
      areaPaths.enter().append('path')
          .attr('class', 'nv-area')
          .attr('d', function(d) {
            return d3.svg.area()
                .interpolate(interpolate)
                .defined(defined)
                .x(function(d,i) { return x0(getX(d,i)); })
                .y0(function(d,i) { return y0(getY(d,i)); })
                .y1(function(d,i) { return y0( y.domain()[0] <= 0 ? y.domain()[1] >= 0 ? 0 : y.domain()[1] : y.domain()[0] ); })
                //.y1(function(d,i) { return y0(0) }) //assuming 0 is within y domain.. may need to tweak this
                .apply(this, [d.values]);
          });
      areaPaths.exit().remove();

      d3.transition(groups.exit().selectAll('path.nv-area'))
          .attr('d', function(d) {
            return d3.svg.area()
                .interpolate(interpolate)
                .defined(defined)
                .x(function(d,i) { return x0(getX(d,i)); })
                .y0(function(d,i) { return y0(getY(d,i)); })
                .y1(function(d,i) { return y0( y.domain()[0] <= 0 ? y.domain()[1] >= 0 ? 0 : y.domain()[1] : y.domain()[0] ); })
                //.y1(function(d,i) { return y0(0) }) //assuming 0 is within y domain.. may need to tweak this
                .apply(this, [d.values]);
          });
      d3.transition(areaPaths)
          .attr('d', function(d) {
            return d3.svg.area()
                .interpolate(interpolate)
                .defined(defined)
                .x(function(d,i) { return x(getX(d,i)); })
                .y0(function(d,i) { return y(getY(d,i)); })
                .y1(function(d,i) { return y0( y.domain()[0] <= 0 ? y.domain()[1] >= 0 ? 0 : y.domain()[1] : y.domain()[0] ); })
                //.y1(function(d,i) { return y0(0) }) //assuming 0 is within y domain.. may need to tweak this
                .apply(this, [d.values]);
          });


      var linePaths = groups.selectAll('path.nv-line')
          .data(function(d) { return [d.values]; });
      linePaths.enter().append('path')
          .attr('class', 'nv-line')
          .attr('d',
            d3.svg.line()
              .interpolate(interpolate)
              .defined(defined)
              .x(function(d,i) { return x0(getX(d,i)); })
              .y(function(d,i) { return y0(getY(d,i)); })
          );
      d3.transition(groups.exit().selectAll('path.nv-line'))
          .attr('d',
            d3.svg.line()
              .interpolate(interpolate)
              .defined(defined)
              .x(function(d,i) { return x0(getX(d,i)); })
              .y(function(d,i) { return y0(getY(d,i)); })
          );
      d3.transition(linePaths)
          .attr('d',
            d3.svg.line()
              .interpolate(interpolate)
              .defined(defined)
              .x(function(d,i) { return x(getX(d,i)); })
              .y(function(d,i) { return y(getY(d,i)); })
          );


      //store old scales for use in transitions on update
      x0 = x.copy();
      y0 = y.copy();

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = scatter.dispatch;
  chart.scatter = scatter;

  d3.rebind(chart, scatter, 'id', 'interactive', 'size', 'xScale', 'yScale', 'zScale', 'xDomain', 'yDomain', 'sizeDomain', 'forceX', 'forceY', 'forceSize', 'clipVoronoi', 'clipRadius', 'padData');

  chart.color = function(_) {
    if (!arguments.length) { return color; }
    color = _;
    scatter.color(color);
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) { return fill; }
    fill = _;
    scatter.fill(fill);
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) { return classes; }
    classes = _;
    scatter.classes(classes);
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) { return gradient; }
    gradient = _;
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) { return getX; }
    getX = _;
    scatter.x(_);
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) { return getY; }
    getY = _;
    scatter.y(_);
    return chart;
  };

  chart.delay = function(_) {
    if (!arguments.length) { return delay; }
    delay = _;
    return chart;
  };

  chart.clipEdge = function(_) {
    if (!arguments.length) { return clipEdge; }
    clipEdge = _;
    return chart;
  };

  chart.interpolate = function(_) {
    if (!arguments.length) { return interpolate; }
    interpolate = _;
    return chart;
  };

  chart.defined = function(_) {
    if (!arguments.length) { return defined; }
    defined = _;
    return chart;
  };

  chart.isArea = function(_) {
    if (!arguments.length) { return isArea; }
    isArea = d3.functor(_);
    return chart;
  };

  //============================================================


  return chart;
};

nv.models.lineChart = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 20, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showControls = false
    , showLegend = true
    , tooltip = null
    , tooltips = true
    , tooltipContent = function (key, x, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + ' on ' + x + '</p>';
      }
    , x
    , y
    , state = {}
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'stateChange', 'changeState')
    , controlWidth = function (w) { return showControls ? w * 0.3 : 0; }
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var lines = nv.models.line()
    , xAxis = nv.models.axis()
        .orient('bottom')
        .tickPadding(7)
        .highlightZero(false)
        .showMaxMin(false)
        .tickFormat(function (d) { return d; })
    , yAxis = nv.models.axis()
        .orient('left')
        .tickPadding(4)
        .tickFormat(d3.format(',.1f'))
    , legend = nv.models.legend()
    , controls = nv.models.legend()
    ;

  var showTooltip = function (e, offsetElement) {
    var left = e.pos[0]
      , top = e.pos[1]
      , x = xAxis.tickFormat()(lines.x()(e.point, e.pointIndex))
      , y = yAxis.tickFormat()(lines.y()(e.point, e.pointIndex))
      , content = tooltipContent(e.series.key, x, y, e, chart);
    tooltip = nv.tooltip.show([left, top], content, null, null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function (chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right
        , availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      var innerWidth = availableWidth
        , innerHeight = availableHeight
        , innerMargin = {top: 0, right: 0, bottom: 0, left: 0};

      chart.update = function () { container.transition().duration(chart.delay()).call(chart); };
      chart.container = this;

      //set state.disabled
      state.disabled = data.map(function (d) { return !!d.disabled; });

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length || !data.filter(function (d) { return d.values.length; }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function (d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------
      // Setup Scales

      x = lines.xScale();
      y = lines.yScale();
      xAxis
        .scale(x);
      yAxis
        .scale(y);

      //------------------------------------------------------------

      //add series index to each data point for reference
      data = data.map(function (d, i) {
        d.series = i;
        return d;
      });

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-lineChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-lineChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-x nv-axis');
      gEnter.append('g').attr('class', 'nv-y nv-axis');
      gEnter.append('g').attr('class', 'nv-linesWrap');

      gEnter.append('g').attr('class', 'nv-controlsWrap');
      gEnter.append('g').attr('class', 'nv-legendWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Title & Legend & Controls

      var titleHeight = 0
        , controlsHeight = 0
        , legendHeight = 0;

      if (showTitle && properties.title) {
        g .select('.nv-title').remove();

        g .select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height / 1.15, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        g .select('.nv-title')
            .attr('dy', '.71em');
      }

      var controlsData = [
        { key: 'Linear', disabled: lines.interpolate() !== 'linear' },
        { key: 'Basis', disabled: lines.interpolate() !== 'basis' },
        { key: 'Monotone', disabled: lines.interpolate() !== 'monotone' },
        { key: 'Cardinal', disabled: lines.interpolate() !== 'cardinal' },
        { key: 'Line', disabled: lines.isArea()() === true },
        { key: 'Area', disabled: lines.isArea()() === false }
      ];

      if (showControls) {
        controls
          .id('controls_' + chart.id())
          .width(controlWidth(availableWidth))
          .height(availableHeight - titleHeight)
          .align('left')
          .strings({close: 'close', type: 'controls'})
          .color(['#444']);

        g .select('.nv-controlsWrap')
          .datum(controlsData)
          .attr('transform', 'translate(0,' + titleHeight + ')')
          .call(controls);

        controlsHeight = controls.height();
      }

      if (showLegend) {
        legend
          .id('legend_' + chart.id())
          .width(availableWidth - controlWidth(availableWidth))
          .height(availableHeight - titleHeight);

        g .select('.nv-legendWrap')
          .datum(data)
          .attr('transform', 'translate(' + controlWidth(availableWidth) + ',' + titleHeight + ')')
          .call(legend);

        legendHeight = legend.height();
      }

      //------------------------------------------------------------
      // Recalc inner margins

      innerMargin.top = titleHeight + Math.max(legendHeight,controlsHeight) + 4;
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Component(s)

      var linesWrap = g.select('.nv-linesWrap')
            .datum(data.filter(function (d) { return !d.disabled; }));

      lines
        .width(innerWidth)
        .height(innerHeight);

      linesWrap
          .call(lines);

      //------------------------------------------------------------
      // Setup Axes

      //------------------------------------------------------------
      // X-Axis

      g .select('.nv-x.nv-axis')
          .call(xAxis);

      //innerMargin.right = xAxis.maxTextWidth() / 2;
      innerMargin[xAxis.orient()] += xAxis.height();
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Y-Axis

      g .select('.nv-y.nv-axis')
          .call(yAxis);

      innerMargin[yAxis.orient()] += yAxis.width();
      innerWidth = availableWidth - innerMargin.left - innerMargin.right;

      //------------------------------------------------------------
      // Main Chart Components
      // Recall to set final size

      lines
        .width(innerWidth)
        .height(innerHeight);

      linesWrap
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(lines);

      xAxis
        .ticks(innerWidth / 100)
        .tickSize(-innerHeight, 0);

      g .select('.nv-x.nv-axis')
        .attr('transform', 'translate(' + innerMargin.left + ',' + (xAxis.orient() === 'bottom' ? innerHeight + innerMargin.top : innerMargin.top) + ')')
        .transition()
          .call(xAxis);

      yAxis
        .ticks(innerHeight / 36)
        .tickSize(-innerWidth, 0);

      g .select('.nv-y.nv-axis')
        .attr('transform', 'translate(' + (yAxis.orient() === 'left' ? innerMargin.left : innerMargin.left + innerWidth) + ',' + innerMargin.top + ')')
        .transition()
          .call(yAxis);

      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function (d, i) {
        d.disabled = !d.disabled;
        if (!data.filter(function (d) { return !d.disabled; }).length) {
          data.map(function (d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }
        state.disabled = data.map(function (d) { return !!d.disabled; });
        dispatch.stateChange(state);
        container.transition().duration(chart.delay()).call(chart);
      });

      controls.dispatch.on('legendClick', function (d, i) {
        if (!d.disabled) { return; }
        controlsData = controlsData.map(function (s) {
          s.disabled = true;
          return s;
        });
        d.disabled = false;

        switch (d.key) {
          case 'Basis':
            lines.interpolate('basis');
            break;
          case 'Linear':
            lines.interpolate('linear');
            break;
          case 'Monotone':
            lines.interpolate('monotone');
            break;
          case 'Cardinal':
            lines.interpolate('cardinal');
            break;
          case 'Line':
            lines.isArea(false);
            break;
          case 'Area':
            lines.isArea(true);
            break;
        }

        container.transition().duration(chart.delay()).call(chart);
      });

      dispatch.on('tooltipShow', function (e) {
        if (tooltips) {
          showTooltip(e, that.parentNode);
        }
      });

      // Update chart from a state object passed to event handler
      dispatch.on('changeState', function (e) {
        if (typeof e.disabled !== 'undefined') {
          data.forEach(function (series,i) {
            series.disabled = e.disabled[i];
          });
          state.disabled = e.disabled;
        }
        container.transition().duration(chart.delay()).call(chart);
      });

    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  lines.dispatch.on('elementMouseover.tooltip', function (e) {
    e.pos = [e.pos[0] + margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  lines.dispatch.on('elementMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function () {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  lines.dispatch.on('elementMousemove.tooltip', function (e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function (e) {
    if (tooltip) {
      nv.tooltip.position(tooltip, e.pos, 's');
    }
  });


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.lines = lines;
  chart.legend = legend;
  chart.controls = controls;
  chart.xAxis = xAxis;
  chart.yAxis = yAxis;

  d3.rebind(chart, lines, 'defined', 'isArea', 'x', 'y', 'size', 'xScale', 'yScale', 'xDomain', 'yDomain', 'forceX', 'forceY', 'interactive', 'clipEdge', 'id', 'delay', 'clipVoronoi', 'id', 'interpolate', 'color', 'fill', 'classes', 'gradient');
  d3.rebind(chart, xAxis, 'rotateLabels', 'reduceXTicks');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d, d.series); },
        classes = function (d,i) { return 'nv-group nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(d.series/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (d.series * (params.step || 1)) % 20;
          return 'nv-group nv-series-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass + ' nv-stroke' + d.series);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      var p = {orientation: params.orientation || 'horizontal', position: params.position || 'base'};
      return lines.gradient(d,d.series,p);
    };

    lines.color(colors);
    lines.fill(fill);
    lines.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function (_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showControls = function (_) {
    if (!arguments.length) { return showControls; }
    showControls = _;
    return chart;
  };

  chart.showLegend = function (_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function (_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function (_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function (_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.state = function (_) {
    if (!arguments.length) { return state; }
    state = _;
    return chart;
  };

  chart.noData = function (_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  //============================================================

  return chart;
};

nv.models.lineWithFocusChart = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var lines = nv.models.line()
    , lines2 = nv.models.line()
    , xAxis = nv.models.axis()
    , yAxis = nv.models.axis()
    , x2Axis = nv.models.axis()
    , y2Axis = nv.models.axis()
    , legend = nv.models.legend()
    , brush = d3.svg.brush()
    ;

  var margin = {top: 30, right: 30, bottom: 30, left: 60}
    , margin2 = {top: 0, right: 30, bottom: 20, left: 60}
    , color = nv.utils.defaultColor()
    , width = null
    , height = null
    , height2 = 100
    , x
    , y
    , x2
    , y2
    , showLegend = true
    , brushExtent = null
    , tooltips = true
    , tooltip = function(key, x, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + ' at ' + x + '</p>'
      }
    , noData = "No Data Available."
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'brush')
    ;

  lines
    .clipEdge(true)
    ;
  lines2
    .interactive(false)
    ;
  xAxis
    .orient('bottom')
    .tickPadding(5)
    ;
  yAxis
    .orient('left')
    ;
  x2Axis
    .orient('bottom')
    .tickPadding(5)
    ;
  y2Axis
    .orient('left')
    ;
  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var showTooltip = function(e, offsetElement) {
    var left = e.pos[0] + ( offsetElement.offsetLeft || 0 ),
        top = e.pos[1] + ( offsetElement.offsetTop || 0),
        x = xAxis.tickFormat()(lines.x()(e.point, e.pointIndex)),
        y = yAxis.tickFormat()(lines.y()(e.point, e.pointIndex)),
        content = tooltip(e.series.key, x, y, e, chart);

    nv.tooltip.show([left, top], content, null, null, offsetElement);
  };

  //============================================================


  function chart(selection) {
    selection.each(function(data) {
      var container = d3.select(this),
          that = this;

      var availableWidth = (width  || parseInt(container.style('width')) || 960)
                             - margin.left - margin.right,
          availableHeight1 = (height || parseInt(container.style('height')) || 400)
                             - margin.top - margin.bottom - height2,
          availableHeight2 = height2 - margin2.top - margin2.bottom;

      chart.update = function() { chart(selection) };
      chart.container = this;


      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length || !data.filter(function(d) { return d.values.length }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight1 / 2)
          .text(function(d) { return d });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup Scales

      x = lines.xScale();
      y = lines.yScale();
      x2 = lines2.xScale();
      y2 = lines2.yScale();

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-lineWithFocusChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-lineWithFocusChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-legendWrap');

      var focusEnter = gEnter.append('g').attr('class', 'nv-focus');
      focusEnter.append('g').attr('class', 'nv-x nv-axis');
      focusEnter.append('g').attr('class', 'nv-y nv-axis');
      focusEnter.append('g').attr('class', 'nv-linesWrap');

      var contextEnter = gEnter.append('g').attr('class', 'nv-context');
      contextEnter.append('g').attr('class', 'nv-x nv-axis');
      contextEnter.append('g').attr('class', 'nv-y nv-axis');
      contextEnter.append('g').attr('class', 'nv-linesWrap');
      contextEnter.append('g').attr('class', 'nv-brushBackground');
      contextEnter.append('g').attr('class', 'nv-x nv-brush');

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Legend

      if (showLegend) {
        legend.width(availableWidth);

        g.select('.nv-legendWrap')
            .datum(data)
            .call(legend);

        if ( margin.top != legend.height()) {
          margin.top = legend.height();
          availableHeight1 = (height || parseInt(container.style('height')) || 400)
                             - margin.top - margin.bottom - height2;
        }

        g.select('.nv-legendWrap')
            .attr('transform', 'translate(0,' + (-margin.top) +')')
      }

      //------------------------------------------------------------


      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');


      //------------------------------------------------------------
      // Main Chart Component(s)

      lines
        .width(availableWidth)
        .height(availableHeight1)
        .color(
          data
            .map(function(d,i) {
              return d.color || color(d, i);
            })
            .filter(function(d,i) {
              return !data[i].disabled;
          })
        );

      lines2
        .defined(lines.defined())
        .width(availableWidth)
        .height(availableHeight2)
        .color(
          data
            .map(function(d,i) {
              return d.color || color(d, i);
            })
            .filter(function(d,i) {
              return !data[i].disabled;
          })
        );

      g.select('.nv-context')
          .attr('transform', 'translate(0,' + ( availableHeight1 + margin.bottom + margin2.top) + ')')

      var contextLinesWrap = g.select('.nv-context .nv-linesWrap')
          .datum(data.filter(function(d) { return !d.disabled }))

      d3.transition(contextLinesWrap).call(lines2);

      //------------------------------------------------------------


      /*
      var focusLinesWrap = g.select('.nv-focus .nv-linesWrap')
          .datum(data.filter(function(d) { return !d.disabled }))

      d3.transition(focusLinesWrap).call(lines);
     */


      //------------------------------------------------------------
      // Setup Main (Focus) Axes

      xAxis
        .scale(x)
        .ticks( availableWidth / 100 )
        .tickSize(-availableHeight1, 0);

      yAxis
        .scale(y)
        .ticks( availableHeight1 / 36 )
        .tickSize( -availableWidth, 0);

      g.select('.nv-focus .nv-x.nv-axis')
          .attr('transform', 'translate(0,' + availableHeight1 + ')');

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup Brush

      brush
        .x(x2)
        .on('brush', onBrush);

      if (brushExtent) brush.extent(brushExtent);

      var brushBG = g.select('.nv-brushBackground').selectAll('g')
          .data([brushExtent || brush.extent()])

      var brushBGenter = brushBG.enter()
          .append('g');

      brushBGenter.append('rect')
          .attr('class', 'left')
          .attr('x', 0)
          .attr('y', 0)
          .attr('height', availableHeight2);

      brushBGenter.append('rect')
          .attr('class', 'right')
          .attr('x', 0)
          .attr('y', 0)
          .attr('height', availableHeight2);

      gBrush = g.select('.nv-x.nv-brush')
          .call(brush);
      gBrush.selectAll('rect')
          //.attr('y', -5)
          .attr('height', availableHeight2);
      gBrush.selectAll('.resize').append('path').attr('d', resizePath);

      onBrush();

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup Secondary (Context) Axes

      x2Axis
        .scale(x2)
        .ticks( availableWidth / 100 )
        .tickSize(-availableHeight2, 0);

      g.select('.nv-context .nv-x.nv-axis')
          .attr('transform', 'translate(0,' + y2.range()[0] + ')');
      d3.transition(g.select('.nv-context .nv-x.nv-axis'))
          .call(x2Axis);


      y2Axis
        .scale(y2)
        .ticks( availableHeight2 / 36 )
        .tickSize( -availableWidth, 0);

      d3.transition(g.select('.nv-context .nv-y.nv-axis'))
          .call(y2Axis);

      g.select('.nv-context .nv-x.nv-axis')
          .attr('transform', 'translate(0,' + y2.range()[0] + ')');

      //------------------------------------------------------------


      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function(d,i) {
        d.disabled = !d.disabled;

        if (!data.filter(function(d) { return !d.disabled }).length) {
          data.map(function(d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }

        selection.transition().call(chart);
      });

      dispatch.on('tooltipShow', function(e) {
        if (tooltips) showTooltip(e, that.parentNode);
      });

      //============================================================


      //============================================================
      // Functions
      //------------------------------------------------------------

      // Taken from crossfilter (http://square.github.com/crossfilter/)
      function resizePath(d) {
        var e = +(d == 'e'),
            x = e ? 1 : -1,
            y = availableHeight2 / 3;
        return 'M' + (.5 * x) + ',' + y
            + 'A6,6 0 0 ' + e + ' ' + (6.5 * x) + ',' + (y + 6)
            + 'V' + (2 * y - 6)
            + 'A6,6 0 0 ' + e + ' ' + (.5 * x) + ',' + (2 * y)
            + 'Z'
            + 'M' + (2.5 * x) + ',' + (y + 8)
            + 'V' + (2 * y - 8)
            + 'M' + (4.5 * x) + ',' + (y + 8)
            + 'V' + (2 * y - 8);
      }


      function updateBrushBG() {
        if (!brush.empty()) brush.extent(brushExtent);
        brushBG
            .data([brush.empty() ? x2.domain() : brushExtent])
            .each(function(d,i) {
              var leftWidth = x2(d[0]) - x.range()[0],
                  rightWidth = x.range()[1] - x2(d[1]);
              d3.select(this).select('.left')
                .attr('width',  leftWidth < 0 ? 0 : leftWidth);

              d3.select(this).select('.right')
                .attr('x', x2(d[1]))
                .attr('width', rightWidth < 0 ? 0 : rightWidth);
            });
      }


      function onBrush() {
        brushExtent = brush.empty() ? null : brush.extent();
        extent = brush.empty() ? x2.domain() : brush.extent();


        dispatch.brush({extent: extent, brush: brush});


        updateBrushBG();

        // Update Main (Focus)
        var focusLinesWrap = g.select('.nv-focus .nv-linesWrap')
            .datum(
              data
                .filter(function(d) { return !d.disabled })
                .map(function(d,i) {
                  return {
                    key: d.key,
                    values: d.values.filter(function(d,i) {
                      return lines.x()(d,i) >= extent[0] && lines.x()(d,i) <= extent[1];
                    })
                  }
                })
            );
        d3.transition(focusLinesWrap).call(lines);


        // Update Main (Focus) Axes
        d3.transition(g.select('.nv-focus .nv-x.nv-axis'))
            .call(xAxis);
        d3.transition(g.select('.nv-focus .nv-y.nv-axis'))
            .call(yAxis);
      }

      //============================================================


    });

    return chart;
  }


  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  lines.dispatch.on('elementMouseover.tooltip', function(e) {
    e.pos = [e.pos[0] +  margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  lines.dispatch.on('elementMouseout.tooltip', function(e) {
    dispatch.tooltipHide(e);
  });

  dispatch.on('tooltipHide', function() {
    if (tooltips) nv.tooltip.cleanup();
  });

  //============================================================


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.legend = legend;
  chart.lines = lines;
  chart.lines2 = lines2;
  chart.xAxis = xAxis;
  chart.yAxis = yAxis;
  chart.x2Axis = x2Axis;
  chart.y2Axis = y2Axis;

  d3.rebind(chart, lines, 'defined', 'isArea', 'size', 'xDomain', 'yDomain', 'forceX', 'forceY', 'interactive', 'clipEdge', 'clipVoronoi', 'id');

  chart.x = function(_) {
    if (!arguments.length) return lines.x;
    lines.x(_);
    lines2.x(_);
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return lines.y;
    lines.y(_);
    lines2.y(_);
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.margin2 = function(_) {
    if (!arguments.length) return margin2;
    margin2 = _;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.height2 = function(_) {
    if (!arguments.length) return height2;
    height2 = _;
    return chart;
  };

  chart.color = function(_) {
    if (!arguments.length) return color;
    color =nv.utils.getColor(_);
    legend.color(color);
    return chart;
  };

  chart.showLegend = function(_) {
    if (!arguments.length) return showLegend;
    showLegend = _;
    return chart;
  };

  chart.tooltips = function(_) {
    if (!arguments.length) return tooltips;
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function(_) {
    if (!arguments.length) return tooltip;
    tooltip = _;
    return chart;
  };

  chart.interpolate = function(_) {
    if (!arguments.length) return lines.interpolate();
    lines.interpolate(_);
    lines2.interpolate(_);
    return chart;
  };

  chart.noData = function(_) {
    if (!arguments.length) return noData;
    noData = _;
    return chart;
  };

  // Chart has multiple similar Axes, to prevent code duplication, probably need to link all axis functions manually like below
  chart.xTickFormat = function(_) {
    if (!arguments.length) return xAxis.tickFormat();
    xAxis.tickFormat(_);
    x2Axis.tickFormat(_);
    return chart;
  };

  chart.yTickFormat = function(_) {
    if (!arguments.length) return yAxis.tickFormat();
    yAxis.tickFormat(_);
    y2Axis.tickFormat(_);
    return chart;
  };

  //============================================================


  return chart;
}
nv.models.multiBar = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , width = 960
    , height = 500
    , x = d3.scale.ordinal()
    , y = d3.scale.linear()
    , id = Math.floor(Math.random() * 10000) //Create semi-unique ID in case user doesn't select one
    , getX = function (d) { return d.x; }
    , getY = function (d) { return d.y; }
    , forceY = [0] // 0 is forced by default.. this makes sense for the majority of bar graphs... user can always do chart.forceY([]) to remove
    , stacked = false
    , barColor = null // adding the ability to set the color for each rather than the whole group
    , disabled // used in conjunction with barColor to communicate from multiBarHorizontalChart what series are disabled
    , clipEdge = true
    , showValues = false
    , valueFormat = d3.format(',.2f')
    , withLine = false
    , vertical = true
    , delay = 200
    , xDomain
    , yDomain
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-group nv-series-'+ i; }
    , dispatch = d3.dispatch('chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout', 'elementMousemove')
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var x0, y0 //used to store previous scales
      ;

  //============================================================

  function chart(selection) {
    selection.each(function (data) {
      var availableWidth = width - margin.left - margin.right
        , availableHeight = height - margin.top - margin.bottom
        , container = d3.select(this)
        , orientation = vertical ? 'vertical' : 'horizontal'
        , limX = vertical ? 'height' : 'width'
        , limY = vertical ? 'width' : 'height'
        , limDimX = vertical ? availableWidth : availableHeight
        , limDimY = vertical ? availableHeight : availableWidth
        , xVal = vertical ? 'x' : 'y'
        , yVal = vertical ? 'y' : 'x'
        , valuePadding = 0
        ;

      if (stacked) {
        data = d3.layout.stack()
                 .offset('zero')
                 .values(function (d) { return d.values; })
                 .y(getY)
                 (data);
      }

      //add series index to each data point for reference
      data = data.map(function (series, i) {
        series.values = series.values.map(function (point) {
          point.series = i;
          return point;
        });
        return series;
      });

      //------------------------------------------------------------
      // HACK for negative value stacking
      if (stacked) {
        data[0].values.map(function (d,i) {
          var posBase = 0, negBase = 0;
          data.map(function (d) {
            var f = d.values[i];
            f.size = Math.abs(f.y);
            if (f.y < 0) {
              f.y1 = negBase - (vertical ? 0 : f.size);
              negBase = negBase - f.size;
            } else {
              f.y1 = posBase + (vertical ? f.size : 0);
              posBase = posBase + f.size;
            }
          });
        });
      }

      //------------------------------------------------------------
      // Setup Scales

      // remap and flatten the data for use in calculating the scales' domains
      var seriesData = (xDomain && yDomain) ? [] : // if we know xDomain and yDomain, no need to calculate
            data.map(function (d) {
              return d.values.map(function (d,i) {
                return { x: getX(d,i), y: getY(d,i), y0: d.y0, y1: d.y1 };
              });
            });

      var bw = 48 * (stacked?1:data.length) + 16;
      var op = Math.max(0.25,(limDimX - data[0].values.length*bw + 16) / (2*bw));

      if (!withLine) {
        /*TODO: used in reports to keep bars from being too wide
          breaks pareto chart, so need to update line to adjust x position */
        x .domain(xDomain || d3.merge(seriesData).map(function (d) { return d.x; }))
          .rangeRoundBands([0, limDimX], 0.25, op);
      } else {
        x .domain(xDomain || d3.merge(seriesData).map(function (d) { return d.x; }))
          .rangeBands([0, limDimX], 0.3);
      }

      y .domain(yDomain || d3.extent(d3.merge(seriesData).map(function (d) {
          if (vertical) {
            return stacked ? (d.y > 0 ? d.y1 : d.y1 + d.y ) : d.y;
          } else {
            return stacked ? (d.y > 0 ? d.y1 + d.y : d.y1 ) : d.y;
          }
        }).concat(forceY)))
        .range(vertical ? [availableHeight, 0] : [0, availableWidth]);

      x0 = x0 || x;
      y0 = y0 || y;

      //------------------------------------------------------------
      // recalculate y.range if show values
      if (showValues && !stacked) {
        valuePadding = nv.utils.maxStringSetLength(
            d3.merge(seriesData).map(function (d) { return d.y; }),
            container,
            valueFormat
          );
        valuePadding += 4;
        if (vertical) {
          y.range([limDimY - (y.domain()[0] < 0 ? valuePadding : 0), (y.domain()[1] > 0 ? valuePadding : 0)]);
        } else {
          y.range([(y.domain()[0] < 0 ? valuePadding : 0), limDimY - (y.domain()[1] > 0 ? valuePadding : 0) ]);
        }
      }

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-multibar' + (vertical ? '' : 'Horizontal')).data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-multibar' + (vertical ? '' : 'Horizontal'));
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function (d,i,p) {
        return nv.utils.colorLinearGradient( d, id +'-'+ i, p, color(d,i), wrap.select('defs') );
      };

      gEnter.append('g').attr('class', 'nv-groups');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------

      defsEnter.append('clipPath')
          .attr('id', 'nv-edge-clip-' + id)
        .append('rect');
      wrap.select('#nv-edge-clip-' + id + ' rect')
          .attr('width', availableWidth)
          .attr('height', availableHeight);

      g .attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + id + ')' : '');

      //------------------------------------------------------------

      var groups = wrap.select('.nv-groups').selectAll('.nv-group')
          .data(function (d) { return d; }, function (d) { return d.key; });
      groups.enter().append('g')
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6);
      d3.transition(groups.exit())
          .style('stroke-opacity', 1e-6)
          .style('fill-opacity', 1e-6)
        .selectAll('g.nv-bar')
        .delay(function (d,i) { return i * delay/ data[0].values.length; })
          .attr('y', function (d) { return stacked ? y0(d.y0) : y0(0); })
          .attr(limY, 0)
          .remove();
      groups
        .attr('class', function (d,i) { return this.getAttribute('class') || classes(d,i); })
        .classed('hover', function (d) { return d.hover; })
        .attr('fill', function (d,i){ return this.getAttribute('fill') || fill(d,i); })
        .attr('stroke', function (d,i){ return this.getAttribute('fill') || fill(d,i); });
      d3.transition(groups)
          .style('stroke-opacity', 1)
          .style('fill-opacity', 1);


      var bars = groups.selectAll('g.nv-bar')
            .data(function (d) { return d.values; });

      bars.exit().remove();

      var barsEnter = bars.enter().append('g')
            .attr('class', function (d,i) { return getY(d,i) < 0 ? 'nv-bar negative' : 'nv-bar positive'; })
            .attr('transform', function (d,i,j) {
              var trans = {
                x: stacked ? 0 : (j * x.rangeBand() / data.length ) + x(getX(d,i)),
                y: y0(stacked ? d.y0 : 0)
              };
              return 'translate(' + trans[xVal] + ',' + trans[yVal] + ')';
            });

      barsEnter.append('rect')
        .attr(limY, 0)
        .attr(limX, x.rangeBand() / (stacked ? 1 : data.length) );

      bars
        .on('mouseover', function (d,i) { //TODO: figure out why j works above, but not here
          d3.select(this).classed('hover', true);
          dispatch.elementMouseover({
            value: getY(d,i),
            point: d,
            series: data[d.series],
            pos: [d3.event.pageX, d3.event.pageY],
            pointIndex: i,
            seriesIndex: d.series,
            e: d3.event
          });
        })
        .on('mouseout', function (d,i) {
          d3.select(this).classed('hover', false);
          dispatch.elementMouseout({
            value: getY(d,i),
            point: d,
            series: data[d.series],
            pointIndex: i,
            seriesIndex: d.series,
            e: d3.event
          });
        })
        .on('mousemove', function (d,i){
          dispatch.elementMousemove({
            point: d,
            pointIndex: i,
            pos: [d3.event.pageX, d3.event.pageY],
            id: id
          });
        })
        .on('click', function (d,i) {
          dispatch.elementClick({
            value: getY(d,i),
            point: d,
            series: data[d.series],
            pos: [x(getX(d,i)) + (x.rangeBand() * (stacked ? data.length / 2 : d.series + 0.5) / data.length), y(getY(d,i) + (stacked ? d.y0 : 0))],  // TODO: Figure out why the value appears to be shifted
            pointIndex: i,
            seriesIndex: d.series,
            e: d3.event
          });
          d3.event.stopPropagation();
        })
        .on('dblclick', function (d,i) {
          dispatch.elementDblClick({
            value: getY(d,i),
            point: d,
            series: data[d.series],
            pos: [x(getX(d,i)) + (x.rangeBand() * (stacked ? data.length / 2 : d.series + 0.5) / data.length), y(getY(d,i) + (stacked ? d.y0 : 0))],  // TODO: Figure out why the value appears to be shifted
            pointIndex: i,
            seriesIndex: d.series,
            e: d3.event
          });
          d3.event.stopPropagation();
        });


      barsEnter.append('text');

      if (showValues && !stacked) {
        bars.select('text')
            .attr('text-anchor', function (d,i) { return getY(d,i) < 0 ? 'end' : 'start'; })
            .attr('x', function (d,i) {
              if (!vertical) {
                return getY(d,i) < 0 ? -4 : y(getY(d,i)) - y(0) + 4;
              } else {
                return getY(d,i) < 0 ? y(0) - y(getY(d,i)) - 4 : 4;
              }
            })
            .attr('y', x.rangeBand() / data.length / 2)
            .attr('dy', '.45em')
            .attr('transform', 'rotate(' + (vertical ? -90 : 0) + ' 0,0)')
            .text(function (d,i) { return valueFormat(getY(d,i)); });
      } else {
        bars.selectAll('text').text('');
      }

      bars
          .attr('class', function (d,i) { return getY(d,i) < 0 ? 'nv-bar negative' : 'nv-bar positive'; });

      if (barColor) {
        if (!disabled) {
          disabled = data.map(function () { return true; });
        }
        bars
          //.style('fill', barColor)
          //.style('stroke', barColor)
          //.style('fill', function (d,i,j) { return d3.rgb(barColor(d,i)).darker(j).toString(); })
          //.style('stroke', function (d,i,j) { return d3.rgb(barColor(d,i)).darker(j).toString(); })
          .style('fill', function (d,i,j) {
            return d3.rgb(barColor(d,i)).darker(disabled.map(function (d,i) { return i; }).filter(function (d,i){ return !disabled[i]; })[j]).toString();
          })
          .style('stroke', function (d,i,j) {
            return d3.rgb(barColor(d,i)).darker(disabled.map(function (d,i) { return i; }).filter(function (d,i){ return !disabled[i]; })[j]).toString();
          });
      }


      if (stacked) {
        d3.transition(bars)
            .delay(function (d,i) { return i * delay / data[0].values.length; })
            .attr('transform', function (d,i) {
              var trans = {
                x: x(getX(d,i)),
                y: y(d.y1)
              };
              return 'translate(' + trans[xVal] + ',' + trans[yVal] + ')';
            })
            .each('end', function () {
              d3.select(this).select('rect')
                .attr('x', function (d,i) {
                  return getY(d,i) < 0 ? 0 : 1;
                })
                .attr(limX, function (d,i) {
                  return Math.max(Math.abs(y(getY(d,i) + d.y0) - y(d.y0)) - 1, 0);
                })
                .attr(limY, x.rangeBand());
            });
      } else {
        d3.transition(bars)
          .delay(function (d,i) { return i * delay / data[0].values.length; })
          .attr('transform', function (d,i) {
            var trans = {
              x: d.series * x.rangeBand() / data.length + x(getX(d,i)),
              y: getY(d,i) < 0 ? (vertical ? y(0) : y(getY(d,i))) : (vertical ? y(getY(d,i)) : y(0))
            };
            return 'translate(' + trans[xVal] + ',' + trans[yVal] + ')';
          })
          .each('end', function () {
            d3.select(this).select('rect')
              .attr('x', function (d,i) {
                return getY(d,i) < 0 ? 0 : 2;
              })
              .attr(limX, function (d,i) {
                return Math.max(Math.abs(y(getY(d,i)) - y(0)) - 2, 0) || 0;
              })
              .attr(limY, x.rangeBand() / data.length );
          });
      }

      //store old scales for use in transitions on update
      x0 = x.copy();
      y0 = y.copy();

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function (_) {
    if (!arguments.length) return color;
    color = _;
    return chart;
  };
  chart.fill = function (_) {
    if (!arguments.length) return fill;
    fill = _;
    return chart;
  };
  chart.classes = function (_) {
    if (!arguments.length) return classes;
    classes = _;
    return chart;
  };
  chart.gradient = function (_) {
    if (!arguments.length) return gradient;
    gradient = _;
    return chart;
  };

  chart.x = function (_) {
    if (!arguments.length) return getX;
    getX = _;
    return chart;
  };

  chart.y = function (_) {
    if (!arguments.length) return getY;
    getY = _;
    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.xScale = function (_) {
    if (!arguments.length) return x;
    x = _;
    return chart;
  };

  chart.yScale = function (_) {
    if (!arguments.length) return y;
    y = _;
    return chart;
  };

  chart.xDomain = function (_) {
    if (!arguments.length) return xDomain;
    xDomain = _;
    return chart;
  };

  chart.yDomain = function (_) {
    if (!arguments.length) return yDomain;
    yDomain = _;
    return chart;
  };

  chart.forceY = function (_) {
    if (!arguments.length) return forceY;
    forceY = _;
    return chart;
  };

  chart.stacked = function (_) {
    if (!arguments.length) return stacked;
    stacked = _;
    return chart;
  };

  chart.clipEdge = function (_) {
    if (!arguments.length) return clipEdge;
    clipEdge = _;
    return chart;
  };

  chart.barColor = function (_) {
    if (!arguments.length) return barColor;
    barColor = nv.utils.getColor(_);
    return chart;
  };

  chart.disabled = function (_) {
    if (!arguments.length) return disabled;
    disabled = _;
    return chart;
  };

  chart.id = function (_) {
    if (!arguments.length) return id;
    id = _;
    return chart;
  };

  chart.delay = function (_) {
    if (!arguments.length) return delay;
    delay = _;
    return chart;
  };

  chart.showValues = function (_) {
    if (!arguments.length) return showValues;
    showValues = _;
    return chart;
  };

  chart.valueFormat= function (_) {
    if (!arguments.length) return valueFormat;
    valueFormat = _;
    return chart;
  };

  chart.withLine = function (_) {
    if (!arguments.length) return withLine;
    withLine = _;
    return chart;
  };

  chart.vertical = function (_) {
    if (!arguments.length) return vertical;
    vertical = _;
    return chart;
  };

  //============================================================


  return chart;
};

nv.models.multiBarChart = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 20, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showControls = false
    , showLegend = true
    , tooltip = null
    , tooltips = true
    , tooltipContent = function (key, x, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + ' on ' + x + '</p>';
      }
    , x
    , y
    , state = {stacked: false}
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'stateChange', 'changeState')
    , controlWidth = function (w) { return showControls ? w * 0.3 : 0; }
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var multibar = nv.models.multiBar()
        .vertical(true)
        .stacked(false)
    , xAxis = nv.models.axis()
        .orient('bottom')
        .tickSize(0)
        .tickPadding(7)
        .highlightZero(false)
        .showMaxMin(false)
        .tickFormat(function (d) { return d; })
    , yAxis = nv.models.axis()
        .orient('left')
        .tickPadding(4)
        .tickFormat(d3.format(',.1f'))
    , legend = nv.models.legend()
    , controls = nv.models.legend()
    ;

  var showTooltip = function (e, offsetElement, groupTotals) {
    var left = e.pos[0]
      , top = e.pos[1]
      , x = (groupTotals) ?
              (e.point.y * 100 / groupTotals[e.pointIndex].t).toFixed(1) :
              xAxis.tickFormat()(multibar.x()(e.point, e.pointIndex))
      , y = yAxis.tickFormat()(multibar.y()(e.point, e.pointIndex))
      , content = tooltipContent(e.series.key, x, y, e, chart);
    tooltip = nv.tooltip.show([left, top], content, e.value < 0 ? 'n' : 's', null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function (chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right
        , availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      var innerWidth = availableWidth
        , innerHeight = availableHeight
        , innerMargin = {top: 0, right: 0, bottom: 0, left: 0};

      chart.update = function () { container.transition().duration(chart.delay()).call(chart); };
      chart.container = this;

      //set state.disabled
      state.disabled = data.map(function (d) { return !!d.disabled; });

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length || !data.filter(function (d) { return d.values.length; }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function (d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------
      // Setup Scales

      x = multibar.xScale();
      y = multibar.yScale();
      xAxis
        .scale(x);
      yAxis
        .scale(y);

      //------------------------------------------------------------

      var dataBars = data.filter(function (d) { return !d.disabled && (!d.type || d.type === 'bar'); });

      var groupLabels = properties.labels;

      var groupTotals = properties.values;

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-multiBarChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-multiBarChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-x nv-axis');
      gEnter.append('g').attr('class', 'nv-y nv-axis');
      gEnter.append('g').attr('class', 'nv-barsWrap');

      gEnter.append('g').attr('class', 'nv-controlsWrap');
      gEnter.append('g').attr('class', 'nv-legendWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Title & Legend & Controls

      var titleHeight = 0
        , controlsHeight = 0
        , legendHeight = 0;

      if (showTitle && properties.title) {
        g .select('.nv-title').remove();

        g .select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height / 1.15, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        g .select('.nv-title')
            .attr('dy', '.71em');
      }

      var controlsData = [
        { key: 'Grouped', disabled: multibar.stacked() },
        { key: 'Stacked', disabled: !multibar.stacked() }
      ];

      if (showControls) {
        controls
          .id('controls_' + chart.id())
          .width(controlWidth(availableWidth))
          .height(availableHeight - titleHeight)
          .align('left')
          .strings({close: 'close', type: 'controls'})
          .color(['#444']);

        g .select('.nv-controlsWrap')
          .datum(controlsData)
          .attr('transform', 'translate(0,' + titleHeight + ')')
          .call(controls);

        controlsHeight = controls.height();
      }

      if (showLegend) {
        legend
          .id('legend_' + chart.id())
          .width(availableWidth - controlWidth(availableWidth))
          .height(availableHeight - titleHeight);

        if (multibar.barColor()) {
          data.forEach(function (series,i) {
            series.color = d3.rgb('#ccc').darker(i * 1.5).toString();
          });
        }

        g .select('.nv-legendWrap')
          .datum(data)
          .attr('transform', 'translate(' + controlWidth(availableWidth) + ',' + titleHeight + ')')
          .call(legend);

        legendHeight = legend.height();
      }

      //------------------------------------------------------------
      // Recalc inner margins

      innerMargin.top = titleHeight + Math.max(legendHeight,controlsHeight) + 4;
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Component(s)

      var barsWrap = g.select('.nv-barsWrap')
            .datum(dataBars.length ? dataBars : [{values:[]}]);

      multibar
        .disabled(data.map(function (series) { return series.disabled; }))
        .width(innerWidth)
        .height(innerHeight);

      barsWrap
          .call(multibar);

      //------------------------------------------------------------
      // Setup Axes

      //------------------------------------------------------------
      // X-Axis

      if (groupLabels) {
        xAxis
          .tickFormat(function (d,i) {
            return groupLabels[i] ? groupLabels[i].l : 'undefined';
          });
      }

      g .select('.nv-x.nv-axis')
          .call(xAxis);

      innerMargin[xAxis.orient()] += xAxis.height();
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Y-Axis

      g .select('.nv-y.nv-axis')
          .call(yAxis);

      innerMargin[yAxis.orient()] += yAxis.width();
      innerWidth = availableWidth - innerMargin.left - innerMargin.right;

      //------------------------------------------------------------
      // Main Chart Components
      // Recall to set final size

      multibar
        .width(innerWidth)
        .height(innerHeight);

      barsWrap
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(multibar);

      g .select('.nv-x.nv-axis')
        .attr('transform', 'translate(' + innerMargin.left + ',' + (xAxis.orient() === 'bottom' ? innerHeight + innerMargin.top : innerMargin.top) + ')')
        .transition()
          .call(xAxis);

      yAxis
        .ticks(innerHeight / 36)
        .tickSize(-innerWidth, 0);

      g .select('.nv-y.nv-axis')
        .attr('transform', 'translate(' + (yAxis.orient() === 'left' ? innerMargin.left : innerMargin.left + innerWidth) + ',' + innerMargin.top + ')')
        .transition()
          .call(yAxis);

      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function (d, i) {
        d.disabled = !d.disabled;
        if (!data.filter(function (d) { return !d.disabled; }).length) {
          data.map(function (d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }
        state.disabled = data.map(function (d) { return !!d.disabled; });
        dispatch.stateChange(state);
        container.transition().duration(chart.delay()).call(chart);
      });

      controls.dispatch.on('legendClick', function (d, i) {
        if (!d.disabled) { return; }
        controlsData = controlsData.map(function (s) {
          s.disabled = true;
          return s;
        });
        d.disabled = false;

        switch (d.key) {
          case 'Grouped':
            multibar.stacked(false);
            break;
          case 'Stacked':
            multibar.stacked(true);
            break;
        }

        state.stacked = multibar.stacked();
        dispatch.stateChange(state);

        container.transition().duration(chart.delay()).call(chart);
      });

      dispatch.on('tooltipShow', function (e) {
        if (tooltips) {
          showTooltip(e, that.parentNode, groupTotals);
        }
      });

      // Update chart from a state object passed to event handler
      dispatch.on('changeState', function (e) {
        if (typeof e.disabled !== 'undefined') {
          data.forEach(function (series,i) {
            series.disabled = e.disabled[i];
          });
          state.disabled = e.disabled;
        }

        if (typeof e.stacked !== 'undefined') {
          multibar.stacked(e.stacked);
          state.stacked = e.stacked;
        }

        container.transition().duration(chart.delay()).call(chart);
      });

    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  multibar.dispatch.on('elementMouseover.tooltip', function (e) {
    e.pos = [e.pos[0] + margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  multibar.dispatch.on('elementMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function () {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  multibar.dispatch.on('elementMousemove.tooltip', function (e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function (e) {
    if (tooltip) {
      nv.tooltip.position(tooltip, e.pos, 's');
    }
  });


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.multibar = multibar;
  chart.legend = legend;
  chart.controls = controls;
  chart.xAxis = xAxis;
  chart.yAxis = yAxis;

  d3.rebind(chart, multibar, 'x', 'y', 'xDomain', 'yDomain', 'forceX', 'forceY', 'clipEdge', 'id', 'stacked', 'delay', 'showValues', 'valueFormat', 'color', 'fill', 'classes', 'gradient');
  d3.rebind(chart, xAxis, 'rotateLabels', 'reduceXTicks');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,i); },
        classes = function (d,i) { return 'nv-group nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1)) % 20;
          return 'nv-group nv-series-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      var p = {orientation: params.orientation || 'vertical', position: params.position || 'middle'};
      return multibar.gradient(d,i,p);
    };

    multibar.color(colors);
    multibar.fill(fill);
    multibar.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function (_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showControls = function (_) {
    if (!arguments.length) { return showControls; }
    showControls = _;
    return chart;
  };

  chart.showLegend = function (_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function (_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function (_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function (_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.state = function (_) {
    if (!arguments.length) { return state; }
    state = _;
    return chart;
  };

  chart.noData = function (_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  //============================================================

  return chart;
};

nv.models.multiBarHorizontalChart = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 20, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showControls = false
    , showLegend = true
    , tooltip = null
    , tooltips = true
    , tooltipContent = function (key, x, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + ' on ' + x + '</p>';
      }
    , x
    , y
    , state = {stacked: false}
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'stateChange', 'changeState')
    , controlWidth = function (w) { return showControls ? w * 0.3 : 0; }
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var multibar = nv.models.multiBar()
        .vertical(false)
        .stacked(false)
    , xAxis = nv.models.axis()
        .orient('left')
        .tickSize(0)
        .tickPadding(7)
        .highlightZero(false)
        .showMaxMin(false)
        .tickFormat(function (d) { return d; })
    , yAxis = nv.models.axis()
        .orient('bottom')
        .tickPadding(4)
        .tickFormat(d3.format(',.1f'))
    , legend = nv.models.legend()
    , controls = nv.models.legend()
    ;

  var showTooltip = function (e, offsetElement, groupTotals) {
    var left = e.pos[0]
      , top = e.pos[1]
      , x = (groupTotals) ?
              (e.point.y * 100 / groupTotals[e.pointIndex].t).toFixed(1) :
              xAxis.tickFormat()(multibar.x()(e.point, e.pointIndex))
      , y = yAxis.tickFormat()(multibar.y()(e.point, e.pointIndex))
      , content = tooltipContent(e.series.key, x, y, e, chart);
    tooltip = nv.tooltip.show([left, top], content, e.value < 0 ? 'e' : 'w', null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function (chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right
        , availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      var innerWidth = availableWidth
        , innerHeight = availableHeight
        , innerMargin = {top: 0, right: 0, bottom: 0, left: 0};

      chart.update = function () { container.transition().duration(chart.delay()).call(chart); };
      chart.container = this;

      //set state.disabled
      state.disabled = data.map(function (d) { return !!d.disabled; });

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length || !data.filter(function (d) { return d.values.length; }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function (d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------
      // Setup Scales

      x = multibar.xScale();
      y = multibar.yScale();
      xAxis
        .scale(x);
      yAxis
        .scale(y);

      //------------------------------------------------------------

      var dataBars = data.filter(function (d) { return !d.disabled && (!d.type || d.type === 'bar'); });

      var groupLabels = properties.labels;

      var groupTotals = properties.values;

      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-multiBarHorizontalChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-multiBarHorizontalChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-x nv-axis');
      gEnter.append('g').attr('class', 'nv-y nv-axis');
      gEnter.append('g').attr('class', 'nv-barsWrap');

      gEnter.append('g').attr('class', 'nv-controlsWrap');
      gEnter.append('g').attr('class', 'nv-legendWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Title & Legend & Controls

      var titleHeight = 0
        , controlsHeight = 0
        , legendHeight = 0;

      if (showTitle && properties.title) {
        g .select('.nv-title').remove();

        g .select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height / 1.15, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        g .select('.nv-title')
            .attr('dy', '.71em');
      }

      var controlsData = [
        { key: 'Grouped', disabled: multibar.stacked() },
        { key: 'Stacked', disabled: !multibar.stacked() }
      ];

      if (showControls) {
        controls
          .id('controls_' + chart.id())
          .width(controlWidth(availableWidth))
          .height(availableHeight - titleHeight)
          .align('left')
          .strings({close: 'close', type: 'controls'})
          .color(['#444']);

        g .select('.nv-controlsWrap')
          .datum(controlsData)
          .attr('transform', 'translate(0,' + titleHeight + ')')
          .call(controls);

        controlsHeight = controls.height();
      }

      if (showLegend) {
        legend
          .id('legend_' + chart.id())
          .width(availableWidth - controlWidth(availableWidth))
          .height(availableHeight - titleHeight);

        g .select('.nv-legendWrap')
          .datum(data)
          .attr('transform', 'translate(' + controlWidth(availableWidth) + ',' + titleHeight + ')')
          .call(legend);

        legendHeight = legend.height();
      }

      //------------------------------------------------------------
      // Recalc inner margins

      innerMargin.top = titleHeight + Math.max(legendHeight,controlsHeight) + 4;
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Component(s)

      var barsWrap = g.select('.nv-barsWrap')
            .datum(dataBars.length ? dataBars : [{values:[]}]);

      multibar
        .disabled(data.map(function (series) { return series.disabled; }))
        .width(innerWidth)
        .height(innerHeight);

      barsWrap
          .call(multibar);

      //------------------------------------------------------------
      // Setup Axes

      //------------------------------------------------------------
      // X-Axis

      if (groupLabels) {
        xAxis
          .tickFormat(function (d,i) {
            return groupLabels[i] ? groupLabels[i].l : 'undefined';
          });
      }

      g .select('.nv-x.nv-axis')
          .call(xAxis);

      innerMargin[xAxis.orient()] += xAxis.width();
      innerWidth = availableWidth - innerMargin.left - innerMargin.right;

      //------------------------------------------------------------
      // Y-Axis

      g .select('.nv-y.nv-axis')
          .call(yAxis);

      innerMargin[yAxis.orient()] += yAxis.height();
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Components
      // Recall to set final size

      multibar
        .width(innerWidth)
        .height(innerHeight);

      barsWrap
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(multibar);

      g .select('.nv-x.nv-axis')
        .attr('transform', 'translate(' + (xAxis.orient() === 'left' ? innerMargin.left : innerMargin.left + innerWidth) + ',' + innerMargin.top + ')')
        .transition()
          .call(xAxis);

      yAxis
        .ticks(innerWidth / 50)
        .tickSize(-innerHeight, 0);

      g .select('.nv-y.nv-axis')
        .attr('transform', 'translate(' + innerMargin.left + ',' + (yAxis.orient() === 'bottom' ? innerHeight + innerMargin.top : innerMargin.top) + ')')
        .transition()
          .call(yAxis);

      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function (d, i) {
        d.disabled = !d.disabled;
        if (!data.filter(function (d) { return !d.disabled; }).length) {
          data.map(function (d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }
        state.disabled = data.map(function (d) { return !!d.disabled; });
        dispatch.stateChange(state);
        container.transition().duration(chart.delay()).call(chart);
      });

      controls.dispatch.on('legendClick', function (d, i) {
        if (!d.disabled) { return; }
        controlsData = controlsData.map(function (s) {
          s.disabled = true;
          return s;
        });
        d.disabled = false;

        switch (d.key) {
          case 'Grouped':
            multibar.stacked(false);
            break;
          case 'Stacked':
            multibar.stacked(true);
            break;
        }

        state.stacked = multibar.stacked();
        dispatch.stateChange(state);

        container.transition().duration(chart.delay()).call(chart);
      });

      dispatch.on('tooltipShow', function (e) {
        if (tooltips) {
          showTooltip(e, that.parentNode, groupTotals);
        }
      });

      // Update chart from a state object passed to event handler
      dispatch.on('changeState', function (e) {
        if (typeof e.disabled !== 'undefined') {
          data.forEach(function (series,i) {
            series.disabled = e.disabled[i];
          });
          state.disabled = e.disabled;
        }

        if (typeof e.stacked !== 'undefined') {
          multibar.stacked(e.stacked);
          state.stacked = e.stacked;
        }

        container.transition().duration(chart.delay()).call(chart);
      });

    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  multibar.dispatch.on('elementMouseover.tooltip', function (e) {
    e.pos = [e.pos[0] + margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  multibar.dispatch.on('elementMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function () {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  multibar.dispatch.on('elementMousemove.tooltip', function (e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function (e) {
    if (tooltip) {
      nv.tooltip.position(tooltip, e.pos, 'w');
    }
  });


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.multibar = multibar;
  chart.legend = legend;
  chart.controls = controls;
  chart.xAxis = xAxis;
  chart.yAxis = yAxis;

  d3.rebind(chart, multibar, 'x', 'y', 'xDomain', 'yDomain', 'forceX', 'forceY', 'clipEdge', 'id', 'stacked', 'delay', 'showValues', 'valueFormat', 'color', 'fill', 'classes', 'gradient');
  d3.rebind(chart, xAxis, 'rotateLabels', 'reduceXTicks');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,i); },
        classes = function (d,i) { return 'nv-group nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1)) % 20;
          return 'nv-group nv-series-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      var p = {orientation: params.orientation || 'horizontal', position: params.position || 'middle'};
      return multibar.gradient(d,i,p);
    };

    multibar.color(colors);
    multibar.fill(fill);
    multibar.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function (_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showControls = function (_) {
    if (!arguments.length) { return showControls; }
    showControls = _;
    return chart;
  };

  chart.showLegend = function (_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function (_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function (_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function (_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.state = function (_) {
    if (!arguments.length) { return state; }
    state = _;
    return chart;
  };

  chart.noData = function (_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  //============================================================

  return chart;
};
nv.models.paretoLegend = function() {
    //'use strict';
    //============================================================
    // Public Variables with Default Settings
    //------------------------------------------------------------

    var margin = {top: 0, right: 0, bottom: 0, left: 0},
        width = 400,
        height = 20,
        getKey = function(d) {
            return d.key;
        },
        dispatch = d3.dispatch('legendClick', 'legendDblclick', 'legendMouseover', 'legendMouseout'),
        color = nv.utils.defaultColor(),
        classes = function(d, i) {
            return '';
        };

    function chart(selection) {
        selection.each(function(data) {
            var container = d3.select(this);

            if(!data || !data.length || !data.filter(function(d) {
                return d.values.length;
            }).length) {
                return chart;
            } else {
                container.selectAll('g.nv-legend').remove();
            }
            //------------------------------------------------------------
            // Setup containers and skeleton of chart
            var wrap = container.selectAll('g.nv-legend').data([data]),
                gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-legend').append('g'),
                g = wrap.select('g');

            var series = g.selectAll('.nv-series')
                .data(function(d) {
                    return d;
                });

            var seriesEnter = series.enter().append('g').attr('class', 'nv-series')
                .on('mouseover', function(d, i) {
                    dispatch.legendMouseover(d, i);  //TODO: Make consistent with other event objects
                })
                .on('mouseout', function(d, i) {
                    dispatch.legendMouseout(d, i);
                })
                .on('click', function(d, i) {
                    dispatch.legendClick(d, i);
                })
                .on('dblclick', function(d, i) {
                    dispatch.legendDblclick(d, i);
                });

            if(data[0].type === 'bar') {
                seriesEnter.append('circle')
                    .attr('class', function(d, i) {
                        return this.getAttribute('class') || classes(d, i);
                    })
                    .attr('fill', function(d, i) {
                        return this.getAttribute('fill') || color(d, i);
                    })
                    .attr('stroke', function(d, i) {
                        return this.getAttribute('fill') || color(d, i);
                    })
                    .attr('stroke-width', 2)
                    .attr('r', 5)
                    .attr('transform', 'translate(0,-4)');

                seriesEnter.append('text')
                    .text(getKey)
                    .attr('dy', '1.3em')
                    .attr('dx', 0)
                    .attr('text-anchor', 'middle');
            } else {
                seriesEnter.append('circle')
                    .attr('class', function(d, i) {
                        return this.getAttribute('class') || classes(d, i);
                    })
                    .attr('fill', function(d, i) {
                        return this.getAttribute('fill') || color(d, i);
                    })
                    .attr('stroke', function(d, i) {
                        return this.getAttribute('fill') || color(d, i);
                    })
                    .attr('stroke-width', 0)
                    .attr('r', function(d, i) {
                        return d.type === 'dash' ? 0 : 5;
                    })
                    .attr('transform', 'translate(-15,-4)');

                seriesEnter.append('line')
                    .attr('class', function(d, i) {
                        return this.getAttribute('class') || classes(d, i);
                    })
                    .attr('stroke', function(d, i) {
                        return this.getAttribute('stroke') || color(d, i);
                    })
                    .attr('stroke-width', 3)
                    .attr('x0', 0)
                    .attr('x1', function(d, i) {
                        return d.type === 'dash' ? 40 : 30;
                    })
                    .attr('y0', 0)
                    .attr('y1', 0)
                    .style('stroke-dasharray', function(d, i) {
                        return d.type === 'dash' ? '8, 8' : '0,0';
                    })
                    .style('stroke-width', '4px')
                    .attr('transform', function(d, i) {
                        return d.type === 'dash' ? 'translate(-20,-4)' : 'translate(-15,-4)';
                    });

                seriesEnter.append('circle')
                    .attr('class', function(d, i) {
                        return this.getAttribute('class') || classes(d, i);
                    })
                    .attr('fill', function(d, i) {
                        return this.getAttribute('fill') || color(d, i);
                    })
                    .attr('stroke', function(d, i) {
                        return this.getAttribute('fill') || color(d, i);
                    })
                    .attr('stroke-width', 0)
                    .attr('r', function(d, i) {
                        return d.type === 'dash' ? 0 : 5;
                    })
                    .attr('transform', 'translate(15,-4)');

                seriesEnter.append('text')
                    .text(getKey)
                    .attr('dy', '1.3em')
                    .attr('dx', 0)
                    .attr('text-anchor', 'middle');
            }

            series.classed('disabled', function(d) {
                return d.disabled;
            });

            series.exit().remove();
        });

        return chart;
    }

    //============================================================
    // Expose Public Variables
    //------------------------------------------------------------

    chart.dispatch = dispatch;

    chart.margin = function(_) {
        if(!arguments.length) {
            return margin;
        }
        margin = _;
        return chart;
    };

    chart.width = function(_) {
        if(!arguments.length) {
            return width;
        }
        width = _;
        return chart;
    };

    chart.height = function(_) {
        if(!arguments.length) {
            return height;
        }
        height = _;
        return chart;
    };

    chart.key = function(_) {
        if(!arguments.length) {
            return getKey;
        }
        getKey = _;
        return chart;
    };

    chart.color = function(_) {
        if(!arguments.length) {
            return color;
        }
        if(Object.prototype.toString.call(_) === '[object Array]') {
            color = d3.nv.utils.getColor(_);
        } else {
            color = _;
        }
        return chart;
    };

    chart.classes = function(_) {
        if(!arguments.length) {
            return classes;
        }
        classes = _;
        return chart;
    };

    chart.align = function(_) {
        if(!arguments.length) {
            return align;
        }
        align = _;
        return chart;
    };

    //============================================================


    return chart;
};
nv.models.paretoChart = function() {
    //'use strict';
    //============================================================
    // Public Variables with Default Settings
    //------------------------------------------------------------

    var margin = {top: 10, right: 10, bottom: 40, left: 60},
        width = null,
        height = null,
        getX = function(d) { return d.x; },
        getY = function(d) { return d.y; },
        showControls = true,
        showLegend = true,
        showTitle = false,
        reduceXTicks = false, // if false a tick will show for every data point
        reduceYTicks = false, // if false a tick will show for every data point
        rotateLabels = 0,
        //, rotateLabels = -15
        tooltip = null,
        tooltips = true,
        tooltipBar = function(key, x, y, e, graph) {
            return '<p><b>' + key + '</b></p>' +
                '<p><b>' + y + '</b></p>' +
                '<p><b>' + x + '%</b></p>';
        },
        tooltipLine = function(key, x, y, e, graph) {
            return '<p><p>' + key + ': <b>' + y + '</b></p>';
        },
        tooltipQuota = function(key, x, y, e, graph) {
            return '<p>' + key + ': <b>$' + y + '</b></p>';
        },
        //, x //can be accessed via chart.xScale()
        //, y //can be accessed via chart.yScale()
        noData = 'No Data Available.';

    var multibar = nv.models.multiBar().stacked(true).clipEdge(false).withLine(true),
        //, x = d3.scale.linear(), // needs to be both line and historicalBar x Axis
        x = multibar.xScale(),
        lines = nv.models.line(),
        y = multibar.yScale(),
        xAxis = nv.models.axis().scale(x).orient('bottom').tickPadding(10),
        yAxis = nv.models.axis().scale(y).orient('left').tickPadding(10).showMaxMin(false),
        barLegend = nv.models.paretoLegend(),
        lineLegend = nv.models.paretoLegend(),
        controls = nv.models.legend(),
        dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove');

    xAxis
        .highlightZero(false)
        .showMaxMin(false);

    //============================================================
    // Private Variables
    //------------------------------------------------------------

    var showTooltip = function(e, offsetElement, dataGroup) {
        var left = e.pos[0],
            top = e.pos[1],
            per = (e.point.y * 100 / dataGroup[e.pointIndex].t).toFixed(1),
            amt = yAxis.tickFormat()(lines.y()(e.point, e.pointIndex)),
            content = (e.series.type === 'bar' ? tooltipBar(e.series.key, per, amt, e, chart) : tooltipLine(e.series.key, per, amt, e, chart));

        tooltip = nv.tooltip.show([left, top], content, 's', null, offsetElement);
    };

    var showQuotaTooltip = function(e, offsetElement) {
        var left = e.pos[0],
            top = e.pos[1],
            amt = d3.format(',.2s')(e.val),
            content = tooltipQuota(e.key, 0, amt, e, chart);

        tooltip = nv.tooltip.show([left, top], content, 's', null, offsetElement);
    };

    var barClick = function(data, e, container) {
        var d = e.series,
            selectedSeries = e.seriesIndex;

        d.disabled = !d.disabled;

        if (!chart.stacked()) {
            data.filter(function(d) {
                return d.series === selectedSeries && d.type === 'line';
            }).map(function(d) {
                d.disabled = !d.disabled;
                return d;
            });
        }

        // if there are no enabled data series, enable them all
        if (!data.filter(function(d) {
            return !d.disabled && d.type === 'bar';
        }).length) {
            data.map(function(d) {
                d.disabled = false;
                container.selectAll('.nv-series').classed('disabled', false);
                return d;
            });
        }

        container.transition().duration(300).call(chart);
    };

    var getAbsoluteXY = function(element) {
        var viewportElement = document.documentElement,
            box = element.getBoundingClientRect(),
            scrollLeft = viewportElement.scrollLeft + document.body.scrollLeft,
            scrollTop = viewportElement.scrollTop + document.body.scrollTop,
            x = box.left + scrollLeft,
            y = box.top + scrollTop;

        return {"x": x, "y": y};
    };
    //============================================================


    function chart(selection) {

        selection.each(function(chartData) {

            var properties = chartData.properties,
                data = chartData.data,
                container = d3.select(this),
                that = this,
                availableWidth = (width || parseInt(container.style('width'), 10) || 960),
                availableHeight = (height || parseInt(container.style('height'), 10) || 400),
                availableLegend = (width || parseInt(container.style('width'), 10) || 960) - 20;

            chart.update = function() {
                container.transition().duration(300).call(chart);
            };
            chart.container = this;


            //------------------------------------------------------------
            // Display noData message if there's nothing to show.

            if (!data || !data.length || !data.filter(function(d) {
                return d.values.length;
            }).length) {
                var noDataText = container.selectAll('.nv-noData').data([noData]);

                noDataText.enter().append('text')
                    .attr('class', 'nvd3 nv-noData')
                    .attr('dy', '-.7em')
                    .style('text-anchor', 'middle');

                noDataText
                    .attr('x', margin.left + availableWidth / 2)
                    .attr('y', margin.top + availableHeight / 2)
                    .text(function(d) {
                        return d;
                    });

                return chart;
            } else {
                container.selectAll('.nv-noData').remove();
            }

            var dataBars = data.filter(function(d) {
                    return !d.disabled && d.type === 'bar';
                }),
                dataLines = data.filter(function(d) {
                    return !d.disabled && d.type === 'line';
                }),
                dataGroup = properties.groupData,
                quotaValue = properties.quota || 0,
                quotaLabel = properties.quotaLabel || '',
                targetQuotaValue = properties.targetQuota || 0
                targetQuotaLabel = properties.targetQuotaLabel || '';

            //TODO: try to remove x scale computation from this layer
            // var series1 = data.filter(
            //       function(d) {
            //         return !d.disabled && d.type==='bar'
            //       }
            //     ).map(
            //       function(d) {
            //         return d.values.map(
            //           function(d,i) {
            //             return { x: getX(d,i), y: getY(d,i) }
            //           }
            //         )
            //       }
            //   );
            var seriesX = data.filter(function(d) {
                    return !d.disabled;
                }).map(function(d) {
                    return d.valuesOrig.map(function(d, i) {
                        return getX(d, i);
                    });
                });

            var seriesY = data.filter(function(d) {
                    return !d.disabled;
                }).map(function(d) {
                    return d.valuesOrig.map(function(d, i) {
                        return getY(d, i);
                    });
                });

            var lx = x.domain(d3.merge(seriesX)).rangeBands([0, availableWidth - margin.left - margin.right], 0.3),
                ly = Math.max(d3.max(d3.merge(seriesY)), quotaValue, targetQuotaValue || 0),
                forceY = Math.round((ly + ly * 0.1) * 0.1) * 10,
                lOffset = lx(1) + lx.rangeBand() / (multibar.stacked() || dataLines.length === 1 ? 2 : 4);

            //------------------------------------------------------------
            // Setup containers and skeleton of chart

            var g = container.selectAll('g.nv-wrap.nv-multiBarWithLegend').data([data]),
                gEnter = g.enter().append('g').attr('class', 'nvd3 nv-wrap nv-multiBarWithLegend'),
                cEnter = gEnter.append('g').attr('class', 'nv-chartWrap');

            cEnter.append('g').attr('class', 'nv-x nv-axis');
            cEnter.append('g').attr('class', 'nv-y nv-axis');
            cEnter.append('g').attr('class', 'nv-barsWrap');
            cEnter.append('g').attr('class', 'nv-quotaWrap');
            cEnter.append('g').attr('class', 'nv-linesWrap1');
            cEnter.append('g').attr('class', 'nv-linesWrap2');

            //------------------------------------------------------------
            // Title & Legend

            var titleHeight = 0,
                legendHeight = 0;

            if (showLegend) {

                var quotaLegend = {'key': quotaLabel, 'type': 'dash', 'color': '#444', 'values': {'series': 0, 'x': 0, 'y': 0}},
                    targetQuotaLegend;

                // bar series legend
                gEnter.append('g').attr('class', 'nv-legendWrap nv-barLegend');
                barLegend.width(availableLegend);
                g.select('.nv-legendWrap.nv-barLegend')
                    .datum(
                        //data
                        data.filter(function(d) {
                            //return !d.disabled
                            return d.type === 'bar';
                        })
                    )
                    .call(barLegend);

                var legends = [quotaLegend];
                if (targetQuotaValue > 0) {
                    targetQuotaLegend = {'key': targetQuotaLabel, 'type': 'dash', 'color': '#777', 'values': {'series': 0, 'x': 0, 'y': 0}};
                    legends.push(targetQuotaLegend);
                }

                // line series legend
                gEnter.append('g').attr('class', 'nv-legendWrap nv-lineLegend');
                lineLegend.width(availableLegend);
                g.select('.nv-legendWrap.nv-lineLegend')
                    .datum(
                        data.filter(function(d) {
                            return d.type === 'line';
                        }).concat(legends)
                    )
                    .call(lineLegend);


                // Calculate legend key positions
                var barKeys = g.select('.nv-legendWrap.nv-barLegend').selectAll('.nv-series'),
                    lineKeys = g.select('.nv-legendWrap.nv-lineLegend').selectAll('.nv-series'),
                    barWidths = [],
                    lineWidths = [];

                barKeys.select('text').each(function(d, i) {
                    barWidths.push(Math.max(d3.select(this).node().getComputedTextLength() + 10, 40)); // 28 is ~ the width of the circle plus some padding
                });
                lineKeys.select('text').each(function(d, i) {
                    lineWidths.push(Math.max(d3.select(this).node().getComputedTextLength() + 10, 50)); // 28 is ~ the width of the circle plus some padding
                });

                var barTotal = d3.sum(barWidths),
                    lineTotal = d3.sum(lineWidths),
                    barAvailable = barTotal * availableLegend / (barTotal + lineTotal),
                    lineAvailable = lineTotal * availableLegend / (barTotal + lineTotal),
                    barCols = [],
                    lineCols = [],
                    iBars = barWidths.length,
                    iLines = lineWidths.length,
                    iCols = 0,
                    columnWidths = [0];

                while (iCols <= iBars && d3.sum(columnWidths) < barAvailable) {
                    barCols = columnWidths;
                    iCols += 1;
                    columnWidths = [0];
                    for(var i = 0; i < iBars; i += 1) {
                        if (!columnWidths[i % iCols] || barWidths[i] > columnWidths[i % iCols]) {
                            columnWidths[i % iCols] = barWidths[i];
                        }
                    }
                }

                iCols = 0;
                columnWidths = [0];
                while (iCols <= iLines && d3.sum(columnWidths) < lineAvailable) {
                    lineCols = columnWidths;
                    iCols += 1;
                    columnWidths = [0];
                    for (var i = 0; i < iLines; i += 1) {
                        if (!columnWidths[i % iCols] || lineWidths[i] > columnWidths[i % iCols]) {
                            columnWidths[i % iCols] = lineWidths[i];
                        }
                    }
                }

                iBars = barCols.length;
                iLines = lineCols.length;

                var runningTotal = 0,
                    barPositions = barCols.map(function(d, i) {
                        runningTotal += barCols[i] / 2 + (i > 0 ? barCols[i - 1] / 2 : 0);
                        return runningTotal;
                    });

                runningTotal = 0;
                var linePositions = lineCols.map(function(d, i) {
                    runningTotal += lineCols[i] / 2 + (i > 0 ? lineCols[i - 1] / 2 : 0);
                    return runningTotal;
                });

                barKeys.attr('transform', function(d, i) {
                    return 'translate(' + barPositions[i % iBars] + ',' + (Math.floor(i / iBars) * 35) + ')';
                });
                lineKeys.attr('transform', function(d, i) {
                    return 'translate(' + linePositions[i % iLines] + ',' + (Math.floor(i / iLines) * 35) + ')';
                });

                barLegend.height(Math.ceil(barWidths.length / iBars) * 35);
                lineLegend.height(Math.ceil(lineWidths.length / iCols) * 35);
                legendHeight = Math.max(barLegend.height(), lineLegend.height()) + 10;

                //calculate position
                g.select('.nv-legendWrap.nv-barLegend')
                    .attr('transform', 'translate(' + 10 + ',' + (10 + margin.top) + ')');

                g.select('.nv-legendWrap.nv-lineLegend')
                    .attr('transform', 'translate(' + (10 + availableLegend - d3.sum(lineCols)) + ',' + (10 + margin.top) + ')');
            }

            if (showTitle && properties.title) {
                gEnter.append('g').attr('class', 'nv-titleWrap');

                g.select('.nv-title').remove();

                g.select('.nv-titleWrap')
                    .append('text')
                    .attr('class', 'nv-title')
                    .attr('x', 0)
                    .attr('y', 0)
                    .attr('text-anchor', 'start')
                    .text(properties.title)
                    .attr('stroke', 'none')
                    .attr('fill', 'black')
                ;

                titleHeight = parseInt(g.select('.nv-title').node().getBBox().height, 10) +
                    parseInt(g.select('.nv-title').style('margin-top'), 10) +
                    parseInt(g.select('.nv-title').style('margin-bottom'), 10);

                g.select('.nv-titleWrap')
                    .attr('transform', 'translate(0,' + (-margin.top + parseInt(g.select('.nv-title').node().getBBox().height, 10)) + ')');
            }

            //------------------------------------------------------------
            // Controls

            if (showControls) {
                gEnter.append('g').attr('class', 'nv-controlsWrap');

                var controlsData = [
                    { key: 'Grouped', disabled: multibar.stacked() },
                    { key: 'Stacked', disabled: !multibar.stacked() }
                ];

                controls.width(availableWidth * 0.3).color(['#444']);

                g.select('.nv-controlsWrap')
                    .datum(controlsData)
                    .attr('transform', 'translate(0,' + (-margin.top + titleHeight) + ')')
                    .call(controls);
            }

            g.select('.nv-chartWrap')
                .attr('transform', 'translate(' + margin.left + ',' + (margin.top + titleHeight + legendHeight) + ')');

            availableWidth -= (margin.left + margin.right);
            availableHeight -= (margin.top + margin.bottom + titleHeight + legendHeight);

            //------------------------------------------------------------
            // Main Bar Chart

            multibar
                .width(availableWidth)
                .height(availableHeight)
                .forceY([0, forceY])
                .id(chart.id());

            var barsWrap = g.select('.nv-barsWrap')
                .datum(dataBars.length ? dataBars : [
                    {values: []}
                ]);

            barsWrap.call(multibar);


            //------------------------------------------------------------
            // Quota Line

            g.selectAll('.nv-quotaWrap line').remove();

            if (quotaValue) {
                g.select('.nv-quotaWrap').append('line')
                    .attr('class', 'nv-quotaLine')
                    .attr('x1', 0)
                    .attr('y1', 0)
                    .attr('x2', availableWidth)
                    .attr('y2', 0)
                    .attr('transform', 'translate(0,' + y(quotaValue) + ')')
                    .style('stroke-dasharray', '8, 8')
                    .style('stroke-width', '4px');

                g.select('.nv-quotaWrap').append('line')
                    .attr('class', 'nv-quotaLine nv-quotaLineBackground')
                    .attr('x1', 0)
                    .attr('y1', 0)
                    .attr('x2', availableWidth)
                    .attr('y2', 0)
                    .attr('transform', 'translate(0,' + y(quotaValue) + ')')
                    .style('stroke-width', '12px')
                    .style('opacity', '0')
                    .datum({key:quotaLabel, val:quotaValue});
            }

            // Target Quota Line
            if (targetQuotaValue > 0) {
                g.select('.nv-quotaWrap').append('line')
                    .attr('class', 'nv-quotaLineTarget')
                    .attr('x1', 0)
                    .attr('y1', 0)
                    .attr('x2', availableWidth)
                    .attr('y2', 0)
                    .attr('transform', 'translate(0,' + y(targetQuotaValue) + ')')
                    .style('stroke-dasharray', '8, 8')
                    .style('stroke', '#777')
                    .style('stroke-width', '4px');

                g.select('.nv-quotaWrap').append('line')
                    .attr('class', 'nv-quotaLineTarget nv-quotaLineBackground')
                    .attr('x1', 0)
                    .attr('y1', 0)
                    .attr('x2', availableWidth)
                    .attr('y2', 0)
                    .attr('transform', 'translate(0,' + y(targetQuotaValue) + ')')
                    .style('stroke-width', '12px')
                    .style('opacity', '0')
                    .datum({key:targetQuotaLabel, val:targetQuotaValue});
            }

            g.selectAll('.nv-quotaWrap line.nv-quotaLineBackground')
                .on('mouseover', function(d) {
                    var e = {
                        pos: [d3.event.pageX, d3.event.pageY],
                        val: d.val,
                        key: d.key
                    };
                    showQuotaTooltip(e, that.parentNode);
                })
                .on('mouseout', function() {
                    dispatch.tooltipHide();
                })
                .on('mousemove', function() {
                    dispatch.tooltipMove({
                        pos: [d3.event.pageX, d3.event.pageY]
                    });
                });


            //------------------------------------------------------------
            // Main Line Chart

            lines
                .margin({top: 0, right: lOffset, bottom: 0, left: lOffset})
                .width(availableWidth)
                .height(availableHeight)
                .forceY([0, forceY])
                .id(chart.id());

            var linesWrap1 = g.select('.nv-linesWrap1')
                .datum(
                    dataLines.length ? dataLines.map(function(d) {
                        if (!multibar.stacked()) {
                            d.values = d.valuesOrig.map(function(v, i) {
                                return {'series': v.series, 'x': (v.x + v.series * 0.25 - i * 0.25), 'y': v.y};
                            });
                        } else {
                            d.values.map(function(v) {
                                v.y = 0;
                            });
                            dataBars
                                .map(function(v, i) {
                                    v.values.map(function(v, i) {
                                        d.values[i].y += v.y;
                                    });
                                });
                            d.values.map(function(v, i) {
                                if (i > 0) {
                                    v.y += d.values[i - 1].y;
                                }
                            });
                        }
                        return d;
                    }) : [
                        {values: []}
                    ]
                );

            var linesWrap2 = g.select('.nv-linesWrap2').datum(dataLines);
            linesWrap1.call(lines);
            linesWrap2.call(lines);
            linesWrap1.selectAll('path').style('stroke-width', 6).style('stroke', '#FFFFFF');
            linesWrap2.transition().selectAll('circle').attr('r', 6).style('stroke', '#FFFFFF');
            linesWrap2.transition().selectAll('path').style('stroke-width', 4);

            //------------------------------------------------------------
            // Setup Axes

            xAxis
                .ticks(availableWidth / 100)
                .tickSize(0)
                .tickFormat(function(d, i) {
                    return dataGroup[i] ? dataGroup[i].l : 'asfd';
                });

            g.select('.nv-x.nv-axis')
                .attr('transform', 'translate(0,' + y.range()[0] + ')');
            g.select('.nv-x.nv-axis').transition()
                .call(xAxis);

            var xTicks = g.select('.nv-x.nv-axis > g').selectAll('g');

            xTicks
                .selectAll('line, text')
                .style('opacity', 1);

            xTicks.select('text').each(function(d) {

                var textContent = this.textContent,
                    textNode = d3.select(this),
                    textArray = textContent.split(' '),
                    l = textArray.length,
                    i = 0,
                    dy = 0.71,
                    maxWidth = x.rangeBand();

                if (this.getBBox().width > maxWidth) {
                    this.textContent = '';

                    do {
                        var textString,
                            textSpan = textNode.append('tspan')
                                .text(textArray[i] + ' ')
                                .attr('dy', dy + 'em')
                                .attr('x', 0 + 'px');

                        if (i === 0) {
                            dy = 0.9;
                        }

                        i += 1;

                        while (i < l) {
                            textString = textSpan.text();
                            textSpan.text(textString + ' ' + textArray[i]);
                            if (this.getBBox().width <= maxWidth) {
                                i += 1;
                            }
                            else {
                                textSpan.text(textString);
                                break;
                            }
                        }
                    } while(i < l);
                }
            });

            if (reduceXTicks) {
                xTicks
                    .filter(function(d, i) {
                        return i % Math.ceil(data[0].values.length / (availableWidth / 100)) !== 0;
                    })
                    .selectAll('text, line')
                    .style('opacity', 0);
            }
            if (rotateLabels) {
                xTicks
                    .selectAll('text')
                    .attr('transform', function(d, i, j) {
                        return 'rotate(' + rotateLabels + ' 0,0) translate(0,10)';
                    })
                    .attr('text-transform', rotateLabels > 0 ? 'start' : 'end');
            }

            yAxis
                .ticks(availableHeight / 100)
                .tickSize(-availableWidth, 0)
                .tickFormat(function(d) {
                    return '$' + d3.format(',.2s')(d);
                });

            g.select('.nv-y.nv-axis').transition()
                .style('opacity', dataBars.length ? 1 : 0)
                .call(yAxis);

            // Quota line label
            g.selectAll('text.nv-quotaValue').remove();
            g.select('.nv-y.nv-axis').append('text')
                .attr('class', 'nv-quotaValue')
                .text('$' + d3.format(',.2s')(quotaValue))
                .attr('dy', '.36em')
                .attr('dx', '0')
                .attr('text-anchor', 'end')
                .attr('transform', 'translate(-10,' + y(quotaValue) + ')');

            if (targetQuotaValue > 0) {
                // Target Quota line label
                g.selectAll('text.nv-targetQuotaValue').remove();
                g.select('.nv-y.nv-axis').append('text')
                    .attr('class', 'nv-targetQuotaValue')
                    .text('$' + d3.format(',.2s')(targetQuotaValue))
                    .attr('dy', '.36em')
                    .attr('dx', '0')
                    .attr('text-anchor', 'end')
                    .attr('transform', 'translate(-10,' + y(targetQuotaValue) + ')');
            }

            //============================================================
            // Event Handling/Dispatching (in chart's scope)
            //------------------------------------------------------------

            barLegend.dispatch.on('legendClick', function(d, i) {
                var selectedSeries = d.series;
                //swap bar disabled
                d.disabled = !d.disabled;
                //swap line disabled for same series
                if (!chart.stacked()) {
                    data.filter(function(d) {
                        return d.series === selectedSeries && d.type === 'line';
                    }).map(function(d) {
                        d.disabled = !d.disabled;
                        return d;
                    });
                }
                // if there are no enabled data series, enable them all
                if (!data.filter(function(d) {
                    return !d.disabled && d.type === 'bar';
                }).length) {
                    data.map(function(d) {
                        d.disabled = false;
                        g.selectAll('.nv-series').classed('disabled', false);
                        return d;
                    });
                }
                container.transition().duration(300).call(chart);
            });

            controls.dispatch.on('legendClick', function(d, i) {
                if (!d.disabled) {
                    return;
                }
                controlsData = controlsData.map(function(s) {
                    s.disabled = true;
                    return s;
                });
                d.disabled = false;

                switch(d.key) {
                    case 'Grouped':
                        multibar.stacked(false);
                        break;
                    case 'Stacked':
                        multibar.stacked(true);
                        break;
                }

                container.transition().duration(300).call(chart);
            });

            lines.dispatch.on('elementMouseover.tooltip', function(e) {
                dispatch.tooltipShow(e);
            });

            lines.dispatch.on('elementMouseout.tooltip', function(e) {
                dispatch.tooltipHide(e);
            });

            lines.dispatch.on('elementMousemove', function(e) {
                dispatch.tooltipMove(e);
            });

            multibar.dispatch.on('elementMouseover.tooltip', function(e) {
                dispatch.tooltipShow(e);
            });

            multibar.dispatch.on('elementMouseout.tooltip', function(e) {
                dispatch.tooltipHide(e);
            });

            multibar.dispatch.on('elementMousemove', function(e) {
                dispatch.tooltipMove(e);
            });

            multibar.dispatch.on('elementClick', function(e) {
                barClick(data, e, container);
            });

            if (tooltips) {
                dispatch.on('tooltipShow', function(e) {
                    showTooltip(e, that.parentNode, dataGroup);
                });

                dispatch.on('tooltipHide', nv.tooltip.cleanup);

                dispatch.on('tooltipMove', function(e) {
                    if (tooltip) {
                        nv.tooltip.position(tooltip, e.pos);
                    }
                });
            }
        });

        return chart;
    }

    //============================================================
    // Event Handling/Dispatching (out of chart's scope)
    //------------------------------------------------------------

    /*multibar.dispatch.on('elementMouseover.tooltip2', function(e) {
     e.pos = [e.pos[0] +  margin.left, e.pos[1] + margin.top];
     dispatch.tooltipShow(e);
     });

     multibar.dispatch.on('elementMouseout.tooltip', function(e) {
     dispatch.tooltipHide(e);
     });
     dispatch.on('tooltipHide', function() {
     if (tooltips) nv.tooltip.cleanup();
     });*/

    //============================================================
    // Expose Public Variables
    //------------------------------------------------------------

    // expose chart's sub-components
    chart.dispatch = dispatch;
    chart.lines = lines;
    chart.multibar = multibar;
    chart.barLegend = barLegend;
    chart.lineLegend = lineLegend;
    chart.xAxis = xAxis;
    chart.yAxis = yAxis;

    d3.rebind(chart, multibar, 'x', 'y', 'xDomain', 'yDomain', 'forceX', 'forceY', 'clipEdge', 'id', 'stacked', 'delay', 'color', 'fill', 'gradient', 'classes');

    chart.colorData = function(_) {
        var colors = function(d, i) {
                return nv.utils.defaultColor()(d, i);
            },
            classes = function(d, i) {
                return 'nv-group nv-series-' + i;
            },
            type = arguments[0],
            params = arguments[1] || {};

        switch(type) {
            case 'graduated':
                var c1 = params.c1
                    , c2 = params.c2
                    , l = params.l;
                colors = function(d, i) {
                    return d3.interpolateHsl(d3.rgb(c1), d3.rgb(c2))(i / l);
                };
                break;
            case 'class':
                colors = function() {
                    return 'inherit';
                };
                classes = function(d, i) {
                    var iClass = (i * (params.step || 1)) % 20;
                    return 'nv-group nv-series-' + i + ' ' + ( d.classes || 'nv-fill' + (iClass > 9 ? '' : '0') + iClass );
                };
                break;
        }

        var fill = (!params.gradient) ? colors : function(d, i) {
            var p = {orientation: params.orientation || 'vertical', position: params.position || 'middle'};
            return multibar.gradient(d, i, p);
        };

        multibar.color(colors);
        multibar.fill(fill);
        multibar.classes(classes);

        lines.color(function(d, i) {
            return d3.interpolateHsl(d3.rgb('#1a8221'), d3.rgb('#62b464'))(i / 1);
        });
        lines.fill(function(d, i) {
            return d3.interpolateHsl(d3.rgb('#1a8221'), d3.rgb('#62b464'))(i / 1);
        });
        lines.classes(classes);

        barLegend.color(colors);
        barLegend.classes(classes);

        lineLegend.color(function(d, i) {
            return d.color || d3.interpolateHsl(d3.rgb('#1a8221'), d3.rgb('#62b464'))(i / 1);
        });
        lineLegend.classes(classes);

        return chart;
    };

    chart.x = function(_) {
        if (!arguments.length) {
            return getX;
        }
        getX = _;
        lines.x(_);
        multibar.x(_);
        return chart;
    };

    chart.y = function(_) {
        if (!arguments.length) {
            return getY;
        }
        getY = _;
        lines.y(_);
        multibar.y(_);
        return chart;
    };

    chart.margin = function(_) {
        if (!arguments.length) {
            return margin;
        }
        margin = _;
        return chart;
    };

    chart.width = function(_) {
        if (!arguments.length) {
            return width;
        }
        width = _;
        return chart;
    };

    chart.height = function(_) {
        if (!arguments.length) {
            return height;
        }
        height = _;
        return chart;
    };

    chart.showControls = function(_) {
        if (!arguments.length) {
            return showControls;
        }
        showControls = _;
        return chart;
    };

    chart.showLegend = function(_) {
        if (!arguments.length) {
            return showLegend;
        }
        showLegend = _;
        return chart;
    };

    chart.showTitle = function(_) {
        if (!arguments.length) {
            return showTitle;
        }
        showTitle = _;
        return chart;
    };

    chart.reduceXTicks = function(_) {
        if (!arguments.length) {
            return reduceXTicks;
        }
        reduceXTicks = _;
        return chart;
    };

    chart.rotateLabels = function(_) {
        if (!arguments.length) {
            return rotateLabels;
        }
        rotateLabels = _;
        return chart;
    };

    chart.tooltipBar = function(_) {
        if (!arguments.length) {
            return tooltipBar;
        }
        tooltipBar = _;
        return chart;
    };

    chart.tooltipLine = function(_) {
        if (!arguments.length) {
            return tooltipLine;
        }
        tooltipLine = _;
        return chart;
    };

    chart.tooltipQuota = function(_) {
        if (!arguments.length) {
            return tooltipQuota;
        }
        tooltipQuota = _;
        return chart;
    };

    chart.tooltips = function(_) {
        if (!arguments.length) {
            return tooltips;
        }
        tooltips = _;
        return chart;
    };

    chart.tooltipContent = function(_) {
        if (!arguments.length) {
            return tooltipContent;
        }
        tooltipContent = _;
        return chart;
    };

    chart.noData = function(_) {
        if (!arguments.length) {
            return noData;
        }
        noData = _;
        return chart;
    };

    chart.barClick = function(_) {
        if (!arguments.length) {
            return barClick;
        }
        barClick = _;
        return chart;
    };

    chart.tooltip = function(_) {
        if (!arguments.length) return tooltip;
        tooltip = _;
        return chart;
    };

    chart.colorFill = function(_) {
        return chart;
    };

    return chart;
};
nv.models.pie = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , width = 500
    , height = 500
    , getValues = function(d) { return d; }
    , getX = function(d) { return d.key; }
    , getY = function(d) { return d.value; }
    , getDescription = function(d) { return d.description }
    , id = Math.floor(Math.random() * 10000) //Create semi-unique ID in case user doesn't select one
    , valueFormat = d3.format(',.2f')
    , showLabels = true
    , pieLabelsOutside = true
    , donutLabelsOutside = false
    , labelThreshold = .02 //if slice percentage is under this, don't show label
    , donut = false
    , labelSunbeamLayout = false
    , startAngle = false
    , endAngle = false
    , donutRatio = 0.5
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-slice nv-series-' + i; }
    , dispatch = d3.dispatch('chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout', 'elementMousemove')
    ;

  //============================================================


  function chart(selection) {
    selection.each(function(data) {
      var availableWidth = width - margin.left - margin.right
        , availableHeight = height - margin.top - margin.bottom
        , radius = Math.min(availableWidth, availableHeight) / 2
        , arcRadius = radius-(radius / 5)
        , container = d3.select(this)
      ;


      //------------------------------------------------------------
      // Setup containers and skeleton of chart
      var wrap = container.selectAll('.nv-wrap.nv-pie').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class','nvd3 nv-wrap nv-pie nv-chart-' + id);
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function(d,i) {
        return nv.utils.colorRadialGradient( d, id+'-'+i, {x:0, y:0, r:radius, s:(donut?'50%':'0%'), u:'userSpaceOnUse'}, color(d,i), wrap.select('defs') );
      };

      gEnter.append('g').attr('class', 'nv-pie');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');
      g.select('.nv-pie').attr('transform', 'translate(' + availableWidth / 2 + ',' + availableHeight / 2 + ')');

      //------------------------------------------------------------


      container
          .on('click', function(d,i) {
              dispatch.chartClick({
                  data: d,
                  index: i,
                  pos: d3.event,
                  id: id
              });
          });


      var arc = d3.svg.arc()
                  .outerRadius(arcRadius);

      if (startAngle) arc.startAngle(startAngle)
      if (endAngle) arc.endAngle(endAngle);
      if (donut) arc.innerRadius(radius * donutRatio);

      // Setup the Pie chart and choose the data element
      var pie = d3.layout.pie()
          .sort(null)
          .value(function(d) { return d.disabled ? 0 : getY(d); });

      var slices = wrap.select('.nv-pie').selectAll('.nv-slice')
          .data(pie);

      slices.exit().remove();

      var ae = slices.enter().append('g')
              .attr('class', function(d,i) { return this.getAttribute('class') || classes(d.data, i); })
              .on('mouseover', function(d,i){
                d3.select(this).classed('hover', true);
                dispatch.elementMouseover({
                    label: getX(d.data),
                    value: getY(d.data),
                    point: d.data,
                    pointIndex: i,
                    pos: [d3.event.pageX, d3.event.pageY],
                    id: id
                });
              })
              .on('mouseout', function(d,i){
                d3.select(this).classed('hover', false);
                dispatch.elementMouseout({
                    label: getX(d.data),
                    value: getY(d.data),
                    point: d.data,
                    index: i,
                    id: id
                });
              })
              .on('mousemove', function(d,i){
                dispatch.elementMousemove({
                  point: d,
                  pointIndex: i,
                  pos: [d3.event.pageX, d3.event.pageY],
                  id: id
                });
              })
              .on('click', function(d,i) {
                dispatch.elementClick({
                    label: getX(d.data),
                    value: getY(d.data),
                    point: d.data,
                    index: i,
                    pos: d3.event,
                    id: id
                });
                d3.event.stopPropagation();
              })
              .on('dblclick', function(d,i) {
                dispatch.elementDblClick({
                    label: getX(d.data),
                    value: getY(d.data),
                    point: d.data,
                    index: i,
                    pos: d3.event,
                    id: id
                });
                d3.event.stopPropagation();
              });

        slices
            .attr('fill', function(d,i) { return fill(d.data, i); })
            .attr('stroke', function(d,i) { return fill(d.data, i); });

        var paths = ae.append('path')
            .each(function(d) { this._current = d; });
            //.attr('d', arc);

        d3.transition(slices.select('path'))
            .attr('d', arc)
            .attrTween('d', arcTween);

        if (showLabels) {
          // This does the normal label
          var labelsArc = d3.svg.arc().innerRadius(0);

          if (pieLabelsOutside){ labelsArc = arc; }

          if (donutLabelsOutside) { labelsArc = d3.svg.arc().outerRadius(arc.outerRadius()); }

          ae.append("g").classed("nv-label", true)
            .each(function(d, i) {
              var group = d3.select(this);

              group
                .attr('transform', function(d) {
                     if (labelSunbeamLayout) {
                       d.outerRadius = arcRadius + 10; // Set Outer Coordinate
                       d.innerRadius = arcRadius + 15; // Set Inner Coordinate
                       var rotateAngle = (d.startAngle + d.endAngle) / 2 * (180 / Math.PI);
                       if ((d.startAngle+d.endAngle)/2 < Math.PI) {
                       	 rotateAngle -= 90;
                       } else {
                       	 rotateAngle += 90;
                       }
                       return 'translate(' + labelsArc.centroid(d) + ') rotate(' + rotateAngle + ')';
                     } else {
                       d.outerRadius = radius + 10; // Set Outer Coordinate
                       d.innerRadius = radius + 15; // Set Inner Coordinate
                       return 'translate(' + labelsArc.centroid(d) + ')'
                     }
                });

              group.append('rect')
                  .style('stroke', '#fff')
                  .style('fill', '#fff')
                  .attr("rx", 3)
                  .attr("ry", 3);

              group.append('text')
                  .style('text-anchor', labelSunbeamLayout ? ((d.startAngle + d.endAngle) / 2 < Math.PI ? 'start' : 'end') : 'middle') //center the text on it's origin or begin/end if orthogonal aligned
                  .style('fill', '#000');
            });

          slices.select(".nv-label").transition()
            .attr('transform', function(d) {
              if (labelSunbeamLayout) {
                  d.outerRadius = arcRadius + 10; // Set Outer Coordinate
                  d.innerRadius = arcRadius + 15; // Set Inner Coordinate
                  var rotateAngle = (d.startAngle + d.endAngle) / 2 * (180 / Math.PI);
                  if ((d.startAngle+d.endAngle)/2 < Math.PI) {
                    rotateAngle -= 90;
                  } else {
                    rotateAngle += 90;
                  }
                  return 'translate(' + labelsArc.centroid(d) + ') rotate(' + rotateAngle + ')';
                } else {
                  d.outerRadius = radius + 10; // Set Outer Coordinate
                  d.innerRadius = radius + 15; // Set Inner Coordinate
                  return 'translate(' + labelsArc.centroid(d) + ')'
                }
            });

          slices.each(function(d, i) {
            var slice = d3.select(this);

            slice
              .select(".nv-label text")
                .style('text-anchor', labelSunbeamLayout ? ((d.startAngle + d.endAngle) / 2 < Math.PI ? 'start' : 'end') : 'middle') //center the text on it's origin or begin/end if orthogonal aligned
                .text(function(d, i) {
                  var percent = (d.endAngle - d.startAngle) / (2 * Math.PI);
                  return (d.value && percent > labelThreshold) ? getX(d.data) : '';
                });

            var textBox = slice.select('text').node().getBBox();
            slice.select(".nv-label rect")
              .attr("width", textBox.width + 10)
              .attr("height", textBox.height + 10)
              .attr("transform", function() {
                return "translate(" + [textBox.x - 5, textBox.y - 5] + ")";
              });
          });
        }


        // Computes the angle of an arc, converting from radians to degrees.
        function angle(d) {
          var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
          return a > 90 ? a - 180 : a;
        }

        function arcTween(a) {
          if (!donut) a.innerRadius = 0;
          var i = d3.interpolate(this._current, a);
          this._current = i(0);
          return function(t) {
            return arc(i(t));
          };
        }

        function tweenPie(b) {
          b.innerRadius = 0;
          var i = d3.interpolate({startAngle: 0, endAngle: 0}, b);
          return function(t) {
              return arc(i(t));
          };
        }

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) return color;
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) return fill;
    fill = _;
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) return classes;
    classes = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) return gradient;
    gradient = _;
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.values = function(_) {
    if (!arguments.length) return getValues;
    getValues = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) return getX;
    getX = _;
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return getY;
    getY = d3.functor(_);
    return chart;
  };

  chart.description = function(_) {
    if (!arguments.length) return getDescription;
    getDescription = _;
    return chart;
  };

  chart.showLabels = function(_) {
    if (!arguments.length) return showLabels;
    showLabels = _;
    return chart;
  };

  chart.labelSunbeamLayout = function(_) {
    if (!arguments.length) return labelSunbeamLayout;
    labelSunbeamLayout = _;
    return chart;
  };

  chart.donutLabelsOutside = function(_) {
    if (!arguments.length) return donutLabelsOutside;
    donutLabelsOutside = _;
    return chart;
  };

  chart.pieLabelsOutside = function(_) {
    if (!arguments.length) return pieLabelsOutside;
    pieLabelsOutside = _;
    return chart;
  };

  chart.donut = function(_) {
    if (!arguments.length) return donut;
    donut = _;
    return chart;
  };

  chart.donutRatio = function(_) {
    if (!arguments.length) return donutRatio;
    donutRatio = _;
    return chart;
  };

  chart.startAngle = function(_) {
    if (!arguments.length) return startAngle;
    startAngle = _;
    return chart;
  };

  chart.endAngle = function(_) {
    if (!arguments.length) return endAngle;
    endAngle = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) return id;
    id = _;
    return chart;
  };

  chart.valueFormat = function(_) {
    if (!arguments.length) return valueFormat;
    valueFormat = _;
    return chart;
  };

  chart.labelThreshold = function(_) {
    if (!arguments.length) return labelThreshold;
    labelThreshold = _;
    return chart;
  };
  //============================================================


  return chart;
}
nv.models.pieChart = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var pie = nv.models.pie()
    , legend = nv.models.legend()
    ;

  var margin = {top: 30, right: 20, bottom: 20, left: 20}
    , width = null
    , height = null
    , showLegend = true
    , showTitle = false
    , hole = false
    , tooltip = null
    , tooltips = true
    , tooltipContent = function(key, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + '</p>';
      }
    , state = {}
    , noData = "No Data Available."
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'stateChange', 'changeState')
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var showTooltip = function(e, offsetElement, total) {

    var left = e.pos[0]
      , top = e.pos[1]
      , x = (pie.y()(e.point) * 100 / total).toFixed(1)
      , y = pie.valueFormat()( pie.y()(e.point) )
      , content = tooltipContent( e.point.key, x, y, e, chart )
      //content = tooltip(pie.x()(e.point), y, e, chart);
    ;

    //nv.tooltip.show([left, top], content, e.value < 0 ? 'n' : 's', null, offsetElement);
    tooltip = nv.tooltip.show([left, top], content, null, null, offsetElement);
  };

  //============================================================


  function chart(selection) {
    selection.each(function(chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this),
          that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right
        , availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom
        , total = d3.sum( data.map( function(d) { return d.value; }) )
      ;

      chart.update = function() { container.transition().duration(300).call(chart); };
      chart.container = this;

      //set state.disabled
      state.disabled = data.map(function(d) { return !!d.disabled });

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function(d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-pieChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-pieChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-pieWrap');

      //------------------------------------------------------------
      // Title & Legend

      var titleHeight = 0
        , legendHeight = 0;

      if (showLegend) {
        gEnter.append('g').attr('class', 'nv-legendWrap');

        legend
          .id('legend_' + chart.id())
          .width(availableWidth)
          .height(availableHeight)
          .align('center')
          .key(pie.x());

        wrap.select('.nv-legendWrap')
            .datum(data)
            .call(legend);

        legendHeight = legend.height() + 10;

        if (margin.top !== legendHeight + titleHeight) {
          margin.top = legendHeight + titleHeight;
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;
        }

        wrap.select('.nv-legendWrap')
            .attr('transform', 'translate(0,' + (-margin.top) +')');
      }

      if (showTitle && properties.title) {
        gEnter.append('g').attr('class', 'nv-titleWrap');

        g.select('.nv-title').remove();

        g.select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0 )
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        if (margin.top !== titleHeight + legendHeight) {
          margin.top = titleHeight + legendHeight;
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;
        }

        g.select('.nv-titleWrap')
            .attr('transform', 'translate(0,'+ ( -margin.top+parseInt(g.select('.nv-title').node().getBBox().height, 10) ) +')');
      }

      //------------------------------------------------------------


      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');


      //------------------------------------------------------------
      // Main Chart Component(s)

      pie
        .width(availableWidth)
        .height(availableHeight);


      var pieWrap = g.select('.nv-pieWrap')
          .datum(data);

      pieWrap.transition().call(pie);

      wrap.selectAll('.nv-pie-hole').remove();

      if (hole) {
          var pieHole = wrap.append('g').append('text')
            .text(hole)
              .attr('text-anchor', 'middle')
              .attr('class','nv-pie-hole')
              .attr('transform', 'translate('+ availableWidth/2 +','+ (12+availableHeight/2) +')')
              .attr('fill', '#333');
      }
      //------------------------------------------------------------


      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function(d,i, that) {
        d.disabled = !d.disabled;

        if (!pie.values()(data).filter(function(d) { return !d.disabled; }).length) {
          pie.values()(data).map(function(d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }

        state.disabled = data.map(function(d) { return !!d.disabled });
        dispatch.stateChange(state);

        container.transition().duration(300).call(chart);
      });

      dispatch.on('tooltipShow', function(e) {
        if (tooltips) {
          showTooltip(e, that.parentNode, total);
        }
      });

      // pie.dispatch.on('elementMouseout.tooltip', function(e) {
      //   dispatch.tooltipHide(e);
      // });

      // Update chart from a state object passed to event handler
      dispatch.on('changeState', function(e) {

        if (typeof e.disabled !== 'undefined') {
          data.forEach(function(series,i) {
            series.disabled = e.disabled[i];
          });

          state.disabled = e.disabled;
        }

        container.transition().duration(300).call(chart);
      });

      //============================================================


    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  pie.dispatch.on('elementMouseover.tooltip', function(e) {
    e.pos = [e.pos[0] +  margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  pie.dispatch.on('elementMouseout.tooltip', function(e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function() {
    if (tooltips) nv.tooltip.cleanup();
  });

  pie.dispatch.on('elementMousemove', function(e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function(e) {
    if (tooltip) {
      nv.tooltip.position(tooltip,e.pos);
    }
  });

  //============================================================


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.legend = legend;
  chart.dispatch = dispatch;
  chart.pie = pie;

  d3.rebind(chart, pie, 'valueFormat', 'values', 'x', 'y', 'description', 'id', 'showLabels', 'donutLabelsOutside', 'pieLabelsOutside', 'donut', 'donutRatio', 'labelThreshold', 'color', 'fill', 'classes', 'gradient');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,i); },
        classes = function (d,i) { return 'nv-slice nv-series-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1))%20;
          return 'nv-slice nv-series-'+ i +' '+ ( d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass );
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      return pie.gradient(d,i);
    };

    pie.color(colors);
    pie.fill(fill);
    pie.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.showTitle = function(_) {
    if (!arguments.length) return showTitle;
    showTitle = _;
    return chart;
  };

  chart.showLegend = function(_) {
    if (!arguments.length) return showLegend;
    showLegend = _;
    return chart;
  };

  chart.tooltips = function(_) {
    if (!arguments.length) return tooltips;
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function(_) {
    if (!arguments.length) return tooltipContent;
    tooltipContent = _;
    return chart;
  };

  chart.state = function(_) {
    if (!arguments.length) return state;
    state = _;
    return chart;
  };

  chart.noData = function(_) {
    if (!arguments.length) return noData;
    noData = _;
    return chart;
  };

  chart.hole = function(_) {
    if (!arguments.length) return hole;
    hole = _;
    return chart;
  };

  chart.tooltip = function(_) {
    if (!arguments.length) return tooltip;
    tooltip = _;
    return chart;
  };

  chart.colorFill = function(_) {
    return chart;
  };

  //============================================================


  return chart;
};

nv.models.sparkline = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 2, right: 0, bottom: 2, left: 0}
    , width = 400
    , height = 32
    , animate = true
    , x = d3.scale.linear()
    , y = d3.scale.linear()
    , getX = function(d) { return d.x }
    , getY = function(d) { return d.y }
    , color = nv.utils.getColor(['#000'])
    , xDomain
    , yDomain
    ;

  //============================================================


  function chart(selection) {
    selection.each(function(data) {
      var availableWidth = width - margin.left - margin.right,
          availableHeight = height - margin.top - margin.bottom,
          container = d3.select(this);


      //------------------------------------------------------------
      // Setup Scales

      x   .domain(xDomain || d3.extent(data, getX ))
          .range([0, availableWidth]);

      y   .domain(yDomain || d3.extent(data, getY ))
          .range([availableHeight, 0]);

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-sparkline').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-sparkline');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')

      //------------------------------------------------------------


      var paths = wrap.selectAll('path')
          .data(function(d) { return [d] });
      paths.enter().append('path');
      paths.exit().remove();
      paths
          .style('stroke', function(d,i) { return d.color || color(d, i) })
          .attr('d', d3.svg.line()
            .x(function(d,i) { return x(getX(d,i)) })
            .y(function(d,i) { return y(getY(d,i)) })
          );


      // TODO: Add CURRENT data point (Need Min, Mac, Current / Most recent)
      var points = wrap.selectAll('circle.nv-point')
          .data(function(data) {
              var yValues = data.map(function(d, i) { return getY(d,i); });
              function pointIndex(index) {
                  if (index != -1) {
	              var result = data[index];
                      result.pointIndex = index;
                      return result;
                  } else {
                      return null;
                  }
              }
              var maxPoint = pointIndex(yValues.lastIndexOf(y.domain()[1])),
                  minPoint = pointIndex(yValues.indexOf(y.domain()[0])),
                  currentPoint = pointIndex(yValues.length - 1);
              return [minPoint, maxPoint, currentPoint].filter(function (d) {return d != null;});
          });
      points.enter().append('circle');
      points.exit().remove();
      points
          .attr('cx', function(d,i) { return x(getX(d,d.pointIndex)) })
          .attr('cy', function(d,i) { return y(getY(d,d.pointIndex)) })
          .attr('r', 2)
          .attr('class', function(d,i) {
            return getX(d, d.pointIndex) == x.domain()[1] ? 'nv-point nv-currentValue' :
                   getY(d, d.pointIndex) == y.domain()[0] ? 'nv-point nv-minValue' : 'nv-point nv-maxValue'
          });
    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) return getX;
    getX = d3.functor(_);
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return getY;
    getY = d3.functor(_);
    return chart;
  };

  chart.xScale = function(_) {
    if (!arguments.length) return x;
    x = _;
    return chart;
  };

  chart.yScale = function(_) {
    if (!arguments.length) return y;
    y = _;
    return chart;
  };

  chart.xDomain = function(_) {
    if (!arguments.length) return xDomain;
    xDomain = _;
    return chart;
  };

  chart.yDomain = function(_) {
    if (!arguments.length) return yDomain;
    yDomain = _;
    return chart;
  };

  chart.animate = function(_) {
    if (!arguments.length) return animate;
    animate = _;
    return chart;
  };

  chart.color = function(_) {
    if (!arguments.length) return color;
    color = nv.utils.getColor(_);
    return chart;
  };

  //============================================================


  return chart;
}

nv.models.sparklinePlus = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var sparkline = nv.models.sparkline();

  var margin = {top: 15, right: 100, bottom: 10, left: 50}
    , width = null
    , height = null
    , x
    , y
    , index = []
    , paused = false
    , xTickFormat = d3.format(',r')
    , yTickFormat = d3.format(',.2f')
    , showValue = true
    , alignValue = true
    , rightAlignValue = false
    , noData = "No Data Available."
    ;

  //============================================================


  function chart(selection) {
    selection.each(function(data) {
      var container = d3.select(this);

      var availableWidth = (width  || parseInt(container.style('width')) || 960)
                             - margin.left - margin.right,
          availableHeight = (height || parseInt(container.style('height')) || 400)
                             - margin.top - margin.bottom;

      var currentValue = sparkline.y()(data[data.length-1], data.length-1);

      chart.update = function() { chart(selection) };
      chart.container = this;


      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function(d) { return d });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------



      //------------------------------------------------------------
      // Setup Scales

      x = sparkline.xScale();
      y = sparkline.yScale();

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-sparklineplus').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-sparklineplus');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-sparklineWrap');
      gEnter.append('g').attr('class', 'nv-valueWrap');
      gEnter.append('g').attr('class', 'nv-hoverArea');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Main Chart Component(s)

      var sparklineWrap = g.select('.nv-sparklineWrap');

      sparkline
        .width(availableWidth)
        .height(availableHeight);

      sparklineWrap
          .call(sparkline);

      //------------------------------------------------------------


      var valueWrap = g.select('.nv-valueWrap');

      var value = valueWrap.selectAll('.nv-currentValue')
          .data([currentValue]);

      value.enter().append('text').attr('class', 'nv-currentValue')
          .attr('dx', rightAlignValue ? -8 : 8)
          .attr('dy', '.9em')
          .style('text-anchor', rightAlignValue ? 'end' : 'start');

      value
          .attr('x', availableWidth + (rightAlignValue ? margin.right : 0))
          .attr('y', alignValue ? function(d) { return y(d) } : 0)
          .style('fill', sparkline.color()(data[data.length-1], data.length-1))
          .text(yTickFormat(currentValue));



      gEnter.select('.nv-hoverArea').append('rect')
          .on('mousemove', sparklineHover)
          .on('click', function() { paused = !paused })
          .on('mouseout', function() { index = []; updateValueLine(); });
          //.on('mouseout', function() { index = null; updateValueLine(); });

      g.select('.nv-hoverArea rect')
          .attr('transform', function(d) { return 'translate(' + -margin.left + ',' + -margin.top + ')' })
          .attr('width', availableWidth + margin.left + margin.right)
          .attr('height', availableHeight + margin.top);



      function updateValueLine() { //index is currently global (within the chart), may or may not keep it that way
        if (paused) return;

        var hoverValue = g.selectAll('.nv-hoverValue').data(index)

        var hoverEnter = hoverValue.enter()
          .append('g').attr('class', 'nv-hoverValue')
            .style('stroke-opacity', 0)
            .style('fill-opacity', 0);

        hoverValue.exit()
          .transition().duration(250)
            .style('stroke-opacity', 0)
            .style('fill-opacity', 0)
            .remove();

        hoverValue
            .attr('transform', function(d) { return 'translate(' + x(sparkline.x()(data[d],d)) + ',0)' })
          .transition().duration(250)
            .style('stroke-opacity', 1)
            .style('fill-opacity', 1);

        if (!index.length) return;

        hoverEnter.append('line')
            .attr('x1', 0)
            .attr('y1', -margin.top)
            .attr('x2', 0)
            .attr('y2', availableHeight);


        hoverEnter.append('text').attr('class', 'nv-xValue')
            .attr('x', -6)
            .attr('y', -margin.top)
            .attr('text-anchor', 'end')
            .attr('dy', '.9em')


        g.select('.nv-hoverValue .nv-xValue')
            .text(xTickFormat(sparkline.x()(data[index[0]], index[0])));

        hoverEnter.append('text').attr('class', 'nv-yValue')
            .attr('x', 6)
            .attr('y', -margin.top)
            .attr('text-anchor', 'start')
            .attr('dy', '.9em')

        g.select('.nv-hoverValue .nv-yValue')
            .text(yTickFormat(sparkline.y()(data[index[0]], index[0])));

      }


      function sparklineHover() {
        if (paused) return;

        var pos = d3.mouse(this)[0] - margin.left;

        function getClosestIndex(data, x) {
          var distance = Math.abs(sparkline.x()(data[0], 0) - x);
          var closestIndex = 0;
          for (var i = 0; i < data.length; i++){
            if (Math.abs(sparkline.x()(data[i], i) - x) < distance) {
              distance = Math.abs(sparkline.x()(data[i], i) - x);
              closestIndex = i;
            }
          }
          return closestIndex;
        }

        index = [getClosestIndex(data, Math.round(x.invert(pos)))];

        updateValueLine();
      }

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.sparkline = sparkline;

  d3.rebind(chart, sparkline, 'x', 'y', 'xScale', 'yScale', 'color');

  chart.margin = function(_) {
    if (!arguments.length) return margin;
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.xTickFormat = function(_) {
    if (!arguments.length) return xTickFormat;
    xTickFormat = _;
    return chart;
  };

  chart.yTickFormat = function(_) {
    if (!arguments.length) return yTickFormat;
    yTickFormat = _;
    return chart;
  };

  chart.showValue = function(_) {
    if (!arguments.length) return showValue;
    showValue = _;
    return chart;
  };

  chart.alignValue = function(_) {
    if (!arguments.length) return alignValue;
    alignValue = _;
    return chart;
  };

  chart.rightAlignValue = function(_) {
    if (!arguments.length) return rightAlignValue;
    rightAlignValue = _;
    return chart;
  };

  chart.noData = function(_) {
    if (!arguments.length) return noData;
    noData = _;
    return chart;
  };

  //============================================================


  return chart;
}

nv.models.stackedArea = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 0, right: 0, bottom: 0, left: 0}
    , width = 960
    , height = 500
    , getX = function (d) { return d.x; } // accessor to get the x value from a data point
    , getY = function (d) { return d.y; } // accessor to get the y value from a data point
    , style = 'stack'
    , offset = 'zero'
    , order = 'default'
    , interpolate = 'linear'  // controls the line interpolation
    , clipEdge = false // if true, masks lines within x and y scale
    , x //can be accessed via chart.xScale()
    , y //can be accessed via chart.yScale()
    , delay = 200
    , scatter = nv.models.scatter()
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-area nv-area-'+ i; }
    , dispatch =  d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'areaClick', 'areaMouseover', 'areaMouseout', 'areaMousemove')
    ;

  scatter
    .size(2.2) // default size
    .sizeDomain([2.2]) // all the same size by default
    ;

  /************************************
   * offset:
   *   'wiggle' (stream)
   *   'zero' (stacked)
   *   'expand' (normalize to 100%)
   *   'silhouette' (simple centered)
   *
   * order:
   *   'inside-out' (stream)
   *   'default' (input order)
   ************************************/

  //============================================================


  function chart(selection) {
    selection.each(function (data) {
      var availableWidth = width - margin.left - margin.right,
          availableHeight = height - margin.top - margin.bottom,
          container = d3.select(this);

      //------------------------------------------------------------
      // Setup Scales

      x = scatter.xScale();
      y = scatter.yScale();

      //------------------------------------------------------------


      // Injecting point index into each point because d3.layout.stack().out does not give index
      // ***Also storing getY(d,i) as stackedY so that it can be set to 0 if series is disabled
      data = data.map(function (aseries, i) {
        aseries.values = aseries.values.map(function (d, j) {
          d.index = j;
          d.stackedY = aseries.disabled ? 0 : getY(d,j);
          return d;
        });
        return aseries;
      });


      data = d3.layout.stack()
        .order(order)
        .offset(offset)
        .values(function (d) { return d.values; })  //TODO: make values customizeable in EVERY model in this fashion
        .x(getX)
        .y(function (d) { return d.stackedY; })
        .out(function (d, y0, y) {
          d.display = {
            y: y,
            y0: y0
          };
        })
        (data);


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-stackedarea').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-stackedarea');
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function (d,i,p) {
        return nv.utils.colorLinearGradient( d, chart.id() +'-'+ i, p, color(d,i), wrap.select('defs') );
      };

      gEnter.append('g').attr('class', 'nv-areaWrap');
      gEnter.append('g').attr('class', 'nv-scatterWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------

      scatter
        .width(availableWidth)
        .height(availableHeight)
        .x(getX)
        .y(function (d) { return d.display.y + d.display.y0; })
        .forceY([0]);


      var scatterWrap = g.select('.nv-scatterWrap')
          .datum(data.filter(function (d) { return !d.disabled; }));

      //d3.transition(scatterWrap).call(scatter);
      scatterWrap.call(scatter);


      defsEnter.append('clipPath')
          .attr('id', 'nv-edge-clip-' + chart.id())
        .append('rect');

      wrap.select('#nv-edge-clip-' + chart.id() + ' rect')
          .attr('width', availableWidth)
          .attr('height', availableHeight);

      g   .attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + chart.id() + ')' : '');


      var area = d3.svg.area()
          .x(function (d,i)  { return x(getX(d,i)); })
          .y0(function (d) { return y(d.display.y0); })
          .y1(function (d) { return y(d.display.y + d.display.y0); })
          .interpolate(interpolate);

      var zeroArea = d3.svg.area()
          .x(function (d,i)  { return x(getX(d,i)); })
          .y0(function (d) { return y(d.display.y0); })
          .y1(function (d) { return y(d.display.y0); });


      var path = g.select('.nv-areaWrap').selectAll('path.nv-area')
          .data(function (d) { return d; });
          //.data(function (d) { return d }, function (d) { return d.key });
      path.enter().append('path')
			//.attr('class', function (d,i) { return 'nv-area nv-area-' + i })
          .on('mouseover', function (d,i) {
            d3.select(this).classed('hover', true);
            dispatch.areaMouseover({
              point: d,
              series: d.key,
              pos: [d3.event.pageX, d3.event.pageY],
              seriesIndex: i
            });
          })
          .on('mouseout', function (d,i) {
            d3.select(this).classed('hover', false);
            dispatch.areaMouseout({
              point: d,
              series: d.key,
              pos: [d3.event.pageX, d3.event.pageY],
              seriesIndex: i
            });
          })
          .on('click', function (d,i) {
            d3.select(this).classed('hover', false);
            dispatch.areaClick({
              point: d,
              series: d.key,
              pos: [d3.event.pageX, d3.event.pageY],
              seriesIndex: i
            });
          });
      //d3.transition(path.exit())
      path.exit()
          .attr('d', function (d,i) { return zeroArea(d.values,i); })
          .remove();
      path
          .attr('class', function (d,i) { return this.getAttribute('class') || classes(d,i); })
          .attr('fill', function (d,i){ return d.color || fill(d,i); })
          .attr('stroke', function (d,i){ return d.color || fill(d,i); });
      //d3.transition(path)
      path
          .attr('d', function (d,i) { return area(d.values,i); });


      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      scatter.dispatch.on('elementMouseover.area', function (e) {
        g.select('.nv-chart-' + chart.id() + ' .nv-area-' + e.seriesIndex).classed('hover', true);
      });
      scatter.dispatch.on('elementMouseout.area', function (e) {
        g.select('.nv-chart-' + chart.id() + ' .nv-area-' + e.seriesIndex).classed('hover', false);
      });

      //============================================================

    });

    return chart;
  }


  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  scatter.dispatch.on('elementClick.area', function (e) {
    dispatch.areaClick(e);
  });
  scatter.dispatch.on('elementMouseover.tooltip', function (e) {
    e.pos = [e.pos[0] + margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });
  scatter.dispatch.on('elementMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });

  //============================================================


  //============================================================
  // Global getters and setters
  //------------------------------------------------------------

  chart.dispatch = dispatch;
  chart.scatter = scatter;

  d3.rebind(chart, scatter, 'interactive', 'size', 'id', 'xScale', 'yScale', 'zScale', 'xDomain', 'yDomain', 'sizeDomain', 'forceX', 'forceY', 'forceSize', 'clipVoronoi', 'clipRadius');

  chart.color = function (_) {
    if (!arguments.length) { return color; }
    color = _;
    scatter.color(color);
    return chart;
  };
  chart.fill = function (_) {
    if (!arguments.length) { return fill; }
    fill = _;
    scatter.fill(fill);
    return chart;
  };
  chart.classes = function (_) {
    if (!arguments.length) { return classes; }
    classes = _;
    scatter.classes(classes);
    return chart;
  };
  chart.gradient = function (_) {
    if (!arguments.length) { return gradient; }
    gradient = _;
    return chart;
  };

  chart.x = function (_) {
    if (!arguments.length) { return getX; }
    getX = d3.functor(_);
    return chart;
  };

  chart.y = function (_) {
    if (!arguments.length) { return getY; }
    getY = d3.functor(_);
    return chart;
  };

  chart.delay = function (_) {
    if (!arguments.length) { return delay; }
    delay = _;
    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.clipEdge = function (_) {
    if (!arguments.length) { return clipEdge; }
    clipEdge = _;
    return chart;
  };

  chart.offset = function (_) {
    if (!arguments.length) { return offset; }
    offset = _;
    return chart;
  };

  chart.order = function (_) {
    if (!arguments.length) { return order; }
    order = _;
    return chart;
  };

  //shortcut for offset + order
  chart.style = function (_) {
    if (!arguments.length) { return style; }
    style = _;

    switch (style) {
      case 'stack':
        chart.offset('zero');
        chart.order('default');
        break;
      case 'stream':
        chart.offset('wiggle');
        chart.order('inside-out');
        break;
      case 'stream-center':
          chart.offset('silhouette');
          chart.order('inside-out');
          break;
      case 'expand':
        chart.offset('expand');
        chart.order('default');
        break;
    }

    return chart;
  };

  chart.interpolate = function (_) {
    if (!arguments.length) { return interpolate; }
    interpolate = _;
    return interpolate;
  };

  //============================================================

  return chart;
};

nv.models.stackedAreaChart = function () {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 10, right: 20, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showControls = false
    , showLegend = true
    , tooltip = null
    , tooltips = true
    , tooltipContent = function (key, x, y, e, graph) {
        return '<h3>' + key + '</h3>' +
               '<p>' +  y + ' on ' + x + '</p>';
      }
    , x
    , y
    , yAxisTickFormat = d3.format(',.2f')
    , state = {style: 'stack'}
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'stateChange', 'changeState')
    , controlWidth = function (w) { return showControls ? w * 0.3 : 0; }
    ;

  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var stacked = nv.models.stackedArea()
    , xAxis = nv.models.axis()
        .orient('bottom')
        .tickPadding(7)
        .highlightZero(false)
        .showMaxMin(false)
        .tickFormat(function (d) { return d; })
    , yAxis = nv.models.axis()
        .orient('left')
        .tickPadding(4)
        .tickFormat(stacked.offset() === 'expand' ? d3.format('%') : yAxisTickFormat)
    , legend = nv.models.legend()
    , controls = nv.models.legend()
    ;

  stacked.scatter
    .pointActive(function (d) {
      //console.log(stacked.y()(d), !!Math.round(stacked.y()(d) * 100));
      return !!Math.round(stacked.y()(d) * 100);
    });

  var showTooltip = function (e, offsetElement) {
    var left = e.pos[0] + ( offsetElement.offsetLeft || 0 )
      , top = e.pos[1] + ( offsetElement.offsetTop || 0)
      , x = xAxis.tickFormat()(stacked.x()(e.point, e.pointIndex))
      , y = yAxis.tickFormat()(stacked.y()(e.point, e.pointIndex))
      , content = tooltipContent(e.series.key, x, y, e, chart);
    tooltip = nv.tooltip.show([left, top], content, null, null, offsetElement);
  };

  //============================================================

  function chart(selection) {

    selection.each(function (chartData) {

      var properties = chartData.properties
        , data = chartData.data;

      var container = d3.select(this)
        , that = this;

      var availableWidth = (width || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right
        , availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      var innerWidth = availableWidth
        , innerHeight = availableHeight
        , innerMargin = {top: 0, right: 0, bottom: 0, left: 0};

      chart.update = function () { container.transition().duration(chart.delay()).call(chart); };
      chart.container = this;

      //set state.disabled
      state.disabled = data.map(function (d) { return !!d.disabled; });

      //------------------------------------------------------------
      // Display No Data message if there's nothing to show.

      if (!data || !data.length || !data.filter(function (d) { return d.values.length; }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function (d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------
      // Setup Scales

      x = stacked.xScale();
      y = stacked.yScale();
      xAxis
        .scale(x);
      yAxis
        .scale(y);

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-stackedAreaChart').data([data]);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-stackedAreaChart').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-titleWrap');

      gEnter.append('g').attr('class', 'nv-x nv-axis');
      gEnter.append('g').attr('class', 'nv-y nv-axis');
      gEnter.append('g').attr('class', 'nv-stackedWrap');

      gEnter.append('g').attr('class', 'nv-controlsWrap');
      gEnter.append('g').attr('class', 'nv-legendWrap');

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Title & Legend & Controls

      var titleHeight = 0
        , controlsHeight = 0
        , legendHeight = 0;

      if (showTitle && properties.title) {
        g .select('.nv-title').remove();

        g .select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').node().getBBox().height / 1.15, 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        g .select('.nv-title')
            .attr('dy', '.71em');
      }

      var controlsData = [
        { key: 'Stacked', disabled: stacked.offset() !== 'zero' },
        { key: 'Stream', disabled: stacked.offset() !== 'wiggle' },
        { key: 'Expanded', disabled: stacked.offset() !== 'expand' }
      ];

      if (showControls) {
        controls
          .id('controls_' + chart.id())
          .width(controlWidth(availableWidth))
          .height(availableHeight - titleHeight)
          .align('left')
          .strings({close: 'close', type: 'controls'})
          .color(['#444']);

        g .select('.nv-controlsWrap')
          .datum(controlsData)
          .attr('transform', 'translate(0,' + titleHeight + ')')
          .call(controls);

        controlsHeight = controls.height();
      }

      if (showLegend) {
        legend
          .id('legend_' + chart.id())
          .width(availableWidth - controlWidth(availableWidth))
          .height(availableHeight - titleHeight);

        g .select('.nv-legendWrap')
          .datum(data)
          .attr('transform', 'translate(' + controlWidth(availableWidth) + ',' + titleHeight + ')')
          .call(legend);

        legendHeight = legend.height();
      }

      //------------------------------------------------------------
      // Recalc inner margins

      innerMargin.top = titleHeight + Math.max(legendHeight,controlsHeight) + 4;
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Main Chart Component(s)

      var stackedWrap = g.select('.nv-stackedWrap')
            .datum(data);

      stacked
        .width(innerWidth)
        .height(innerHeight);

      stackedWrap
          .call(stacked);

      //------------------------------------------------------------
      // Setup Axes

      //------------------------------------------------------------
      // X-Axis

      g .select('.nv-x.nv-axis')
          .call(xAxis);

      //innerMargin.right = xAxis.maxTextWidth() / 2;
      innerMargin[xAxis.orient()] += xAxis.height();
      innerHeight = availableHeight - innerMargin.top - innerMargin.bottom;

      //------------------------------------------------------------
      // Y-Axis

      g .select('.nv-y.nv-axis')
          .call(yAxis);

      innerMargin[yAxis.orient()] += yAxis.width();
      innerWidth = availableWidth - innerMargin.left - innerMargin.right;

      //------------------------------------------------------------
      // Main Chart Components
      // Recall to set final size

      stacked
        .width(innerWidth)
        .height(innerHeight);

      stackedWrap
        .attr('transform', 'translate(' + innerMargin.left + ',' + innerMargin.top + ')')
        .transition().duration(chart.delay())
          .call(stacked);

      xAxis
        .ticks(innerWidth / 100)
        .tickSize(-innerHeight, 0);

      g .select('.nv-x.nv-axis')
        .attr('transform', 'translate(' + innerMargin.left + ',' + (xAxis.orient() === 'bottom' ? innerHeight + innerMargin.top : innerMargin.top) + ')')
        .transition()
          .call(xAxis);

      yAxis
        .ticks(stacked.offset() === 'wiggle' ? 0 : innerHeight / 36)
        .tickSize(-innerWidth, 0);

      g .select('.nv-y.nv-axis')
        .attr('transform', 'translate(' + (yAxis.orient() === 'left' ? innerMargin.left : innerMargin.left + innerWidth) + ',' + innerMargin.top + ')')
        .transition()
          .call(yAxis);

      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      stacked.dispatch.on('areaClick.toggle', function (e) {
        if (data.filter(function (d) { return !d.disabled; }).length === 1) {
          data = data.map(function (d) {
            d.disabled = false;
            return d;
          });
        } else {
          data = data.map(function (d,i) {
            d.disabled = (i !== e.seriesIndex);
            return d;
          });
        }

        state.disabled = data.map(function (d) { return !!d.disabled; });
        dispatch.stateChange(state);

        container.transition().duration(chart.delay()).call(chart);
      });

      legend.dispatch.on('legendClick', function (d, i) {
        d.disabled = !d.disabled;
        if (!data.filter(function (d) { return !d.disabled; }).length) {
          data.map(function (d) {
            d.disabled = false;
            return d;
          });
        }
        state.disabled = data.map(function (d) { return !!d.disabled; });
        dispatch.stateChange(state);
        container.transition().duration(chart.delay()).call(chart);
      });

      controls.dispatch.on('legendClick', function (d, i) {
        if (!d.disabled) { return; }
        controlsData = controlsData.map(function (s) {
          s.disabled = true;
          return s;
        });
        d.disabled = false;

        switch (d.key) {
          case 'Stacked':
            stacked.style('stack');
            break;
          case 'Stream':
            stacked.style('stream');
            break;
          case 'Expanded':
            stacked.style('expand');
            break;
        }

        state.style = stacked.style();
        dispatch.stateChange(state);

        container.transition().duration(chart.delay()).call(chart);
      });

      dispatch.on('tooltipShow', function (e) {
        if (tooltips) {
          showTooltip(e, that.parentNode);
        }
      });

      // Update chart from a state object passed to event handler
      dispatch.on('changeState', function (e) {
        if (typeof e.disabled !== 'undefined') {
          data.forEach(function (series,i) {
            series.disabled = e.disabled[i];
          });
          state.disabled = e.disabled;
        }

        if (typeof e.style !== 'undefined') {
          stacked.style(e.style);
        }

        container.transition().duration(chart.delay()).call(chart);
      });

    });

    return chart;
  }

  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  stacked.dispatch.on('areaMouseover.tooltip', function (e) {
    dispatch.tooltipShow(e);
  });

  stacked.dispatch.on('tooltipShow', function (e) {
    //disable tooltips when value ~= 0
    //// TODO: consider removing points from voronoi that have 0 value instead of this hack
    // if (!Math.round(stacked.y()(e.point) * 100)) {  // 100 will not be good for very small numbers... will have to think about making this valu dynamic, based on data range
    //   setTimeout(function () { d3.selectAll('.point.hover').classed('hover', false); }, 0);
    //   return false;
    // }
    dispatch.tooltipShow(e);
  });

  stacked.dispatch.on('tooltipHide', function (e) {
    dispatch.tooltipHide(e);
  });

  stacked.dispatch.on('areaMouseout.tooltip', function (e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function () {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  stacked.dispatch.on('areaMousemove.tooltip', function (e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function (e) {
    if (tooltip) {
      nv.tooltip.position(tooltip, e.pos);
    }
  });


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.stacked = stacked;
  chart.legend = legend;
  chart.controls = controls;
  chart.xAxis = xAxis;
  chart.yAxis = yAxis;

  d3.rebind(chart, stacked, 'x', 'y', 'id', 'size', 'xScale', 'yScale', 'xDomain', 'yDomain', 'sizeDomain', 'interactive', 'offset', 'order', 'style', 'clipEdge', 'delay', 'forceX', 'forceY', 'forceSize', 'color', 'fill', 'classes', 'gradient');
  d3.rebind(chart, xAxis, 'rotateLabels', 'reduceXTicks');

  chart.colorData = function (_) {
    var colors = function (d,i) { return nv.utils.defaultColor()(d,i); },
        classes = function (d,i) { return 'nv-area nv-area-' + i; },
        type = arguments[0],
        params = arguments[1] || {};

    switch (type) {
      case 'graduated':
        var c1 = params.c1
          , c2 = params.c2
          , l = params.l;
        colors = function (d,i) { return d3.interpolateHsl( d3.rgb(c1), d3.rgb(c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1)) % 20;
          return 'nv-area nv-area-' + i + ' ' + (d.classes || 'nv-fill' + (iClass>9?'':'0') + iClass + ' nv-stroke' + i);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      var p = {orientation: params.orientation || 'horizontal', position: params.position || 'base'};
      return stacked.gradient(d,i,p);
    };

    stacked.color(colors);
    stacked.fill(fill);
    stacked.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    return chart;
  };

  chart.margin = function (_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    != 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  != 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom != 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   != 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function (_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function (_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function (_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showControls = function (_) {
    if (!arguments.length) { return showControls; }
    showControls = _;
    return chart;
  };

  chart.showLegend = function (_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function (_) {
    if (!arguments.length) { return tooltip; }
    tooltip = _;
    return chart;
  };

  chart.tooltips = function (_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function (_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.state = function (_) {
    if (!arguments.length) { return state; }
    state = _;
    return chart;
  };

  chart.noData = function (_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  yAxis.tickFormat = function (_) {
    if (!arguments.length) return yAxisTickFormat;
    yAxisTickFormat = _;
    return yAxis;
  };

  //============================================================

  return chart;
};

nv.models.treemap = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var margin = {top: 20, right: 0, bottom: 0, left: 0}
    , width = 0
    , height = 0
    , x //can be accessed via chart.xScale()
    , y //can be accessed via chart.yScale()
    , id = Math.floor(Math.random() * 10000) //Create semi-unique ID incase user doesn't select one
    , getSize = function(d) { return d.size; } // accessor to get the size value from a data point
    , groupBy = function(d) { return d.name; } // accessor to get the name value from a data point
    , clipEdge = true // if true, masks lines within x and y scale
    , groups = []
    , leafClick = function () { return false; }
    , color = nv.utils.defaultColor()
    , fill = color
    , classes = function (d,i) { return 'nv-child'; }
    , dispatch = d3.dispatch('chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout', 'elementMousemove')
    ;

  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var x0, y0 //used to store previous scales
      ;

  //============================================================


  function chart(selection) {
    selection.each(function(chartData) {

      var data = chartData[0];

      //this is for data sets that don't include a colorIndex
      //excludes leaves
      function reduceGroups(d) {
        var i, l;
        if ( d.children && groupBy(d) && groups.indexOf(groupBy(d)) === -1 )
        {
          groups.push(groupBy(d));
          l = d.children.length;
          for (i = 0; i < l; i += 1) {
            reduceGroups(d.children[i]);
          }
        }
      }
      reduceGroups(data);

      var availableWidth = width - margin.left - margin.right
        , availableHeight = height - margin.top - margin.bottom
        , container = d3.select(this)
        , transitioning
        ;

      x = d3.scale.linear()
            .domain([0, data.dx])
            .range([0, availableWidth]);

      y = d3.scale.linear()
            .domain([0, data.dy])
            .range([0, availableHeight]);

      x0 = x0 || x;
      y0 = y0 || y;

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-treemap').data([data]);
      var wrapEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-treemap');
      var defsEnter = wrapEnter.append('defs');
      var gEnter = wrapEnter.append('g');
      var g = wrap.select('g');

      //set up the gradient constructor function
      chart.gradient = function(d,i,p) {
        var iColor = (d.parent.colorIndex||groups.indexOf(groupBy(d.parent))||i);
        return nv.utils.colorLinearGradient( d, id +'-'+ i, p, color(d, iColor, groups.length), wrap.select('defs') );
      };

      //wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      //------------------------------------------------------------
      // Clip Path

      defsEnter.append('clipPath')
          .attr('id', 'nv-edge-clip-' + id)
        .append('rect');
      wrap.select('#nv-edge-clip-' + id + ' rect')
          .attr('width', width)
          .attr('height', height);
      g.attr('clip-path', clipEdge ? 'url(#nv-edge-clip-' + id + ')' : '');


      //------------------------------------------------------------
      // Main Chart

      var grandparent = gEnter.append('g').attr('class', 'nv-grandparent');

      grandparent.append('rect')
        //.attr('y', -margin.top)
        .attr('width', width)
        .attr('height', margin.top);

      grandparent.append('text')
        .attr('x', 6)
        .attr('y', 6)
        .attr('dy', '.75em');

      display(data);

      function display(d) {

        var treemap = d3.layout.treemap()
              .value(getSize)
              .sort(function(a, b) { return getSize(a) - getSize(b); })
              .round(false);

        layout(d);

        grandparent.datum(d.parent).on('click', transition).select('text').text(name(d));

        var g1 = gEnter.insert('g', '.nv-grandparent')
          .attr('class', 'nv-depth')
          .attr('height', availableHeight)
          .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

        var g = g1.selectAll('g').data(d.children).enter().append('g');

        // Transition for nodes with children.
        g.filter(function(d) { return d.children; })
          .classed('nv-children', true)
          .on('click', transition);

        // Navigate for nodes without children (leaves).
        g.filter(function(d) { return !(d.children); })
          .on('click', leafClick);

        g.on('mouseover', function(d,i){
            d3.select(this).classed('hover', true);
            dispatch.elementMouseover({
              point: d,
              pointIndex: i,
              pos: [d3.event.pageX, d3.event.pageY],
              id: id
            });
          })
          .on('mouseout', function(d,i){
            d3.select(this).classed('hover', false);
            dispatch.elementMouseout();
          })
          .on('mousemove', function(d,i){
            dispatch.elementMousemove({
              point: d,
              pointIndex: i,
              pos: [d3.event.pageX, d3.event.pageY],
              id: id
            });
          });

        var child_rects = g.selectAll('.nv-child').data(function(d) {
            return d.children || [d];
          }).enter().append('rect')
              .attr('class', classes)
              .attr('fill', function(d,i){
                var iColor = (d.parent.colorIndex||groups.indexOf(groupBy(d.parent))||i);
                return this.getAttribute('fill') || fill(d, iColor, groups.length); })
              .call(rect);

        child_rects
          .on('mouseover', function(d,i){
            d3.select(this).classed('hover', true);
            dispatch.elementMouseover({
                label: groupBy(d),
                value: getSize(d),
                point: d,
                pointIndex: i,
                pos: [d3.event.pageX, d3.event.pageY],
                id: id
            });
          })
          .on('mouseout', function(d,i){
            d3.select(this).classed('hover', false);
            dispatch.elementMouseout();
          });

        g.append('rect')
          .attr('class', 'nv-parent')
          .call(rect);

        g.append('text')
          .attr('dy', '.75em')
          .text(function(d) { return groupBy(d); })
          .call(text);

        function transition(d) {
          dispatch.elementMouseout();
          if (transitioning || !d) { return; }
          transitioning = true;

          var g2 = display(d),
              t1 = g1.transition().duration(750),
              t2 = g2.transition().duration(750);

          // Update the domain only after entering new elements.
          x.domain([d.x, d.x + d.dx]);
          y.domain([d.y, d.y + d.dy]);

          // Enable anti-aliasing during the transition.
          container.style('shape-rendering', null);

          // Draw child nodes on top of parent nodes.
          container.selectAll('.nv-depth').sort(function(a, b) { return a.depth - b.depth; });

          // Fade-in entering text.
          g2.selectAll('text').style('fill-opacity', 0);

          // Transition to the new view.
          t1.selectAll('text').call(text).style('fill-opacity', 0);
          t2.selectAll('text').call(text).style('fill-opacity', 1);
          t1.selectAll('rect').call(rect);
          t2.selectAll('rect').call(rect);

          // Remove the old node when the transition is finished.
          t1.remove().each('end', function() {
            container.style('shape-rendering', 'crispEdges');
            transitioning = false;
          });
        }

        function layout(d) {
          if(d.children) {
            treemap.nodes({children: d.children});
            d.children.forEach(function(c) {
              c.x = d.x + c.x * d.dx;
              c.y = d.y + c.y * d.dy;
              c.dx *= d.dx;
              c.dy *= d.dy;
              c.parent = d;
              layout(c);
            });
          }
        }

        function text(t) {
          t.attr('x', function(d) { return x(d.x) + 6; })
            .attr('y', function(d) { return y(d.y) + 6; });
        }

        function rect(r) {
          r.attr('x', function(d) { return x(d.x); })
            .attr('y', function(d) { return y(d.y); })
            .attr('width', function(d) { return x(d.x + d.dx) - x(d.x); })
            .attr('height', function(d) { return y(d.y + d.dy) - y(d.y); });
        }

        function name(d) {
          if(d.parent) {
            return name(d.parent) + ' / ' + groupBy(d);
          }
          return groupBy(d);
        }

        return g;
      }

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) { return color; }
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) { return fill; }
    fill = _;
    return chart;
  };
  chart.classes = function(_) {
    if (!arguments.length) { return classes; }
    classes = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) { return gradient; }
    gradient = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) { return getX; }
    getX = _;
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) { return getY; }
    getY = _;
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    !== 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  !== 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom !== 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   !== 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.xScale = function(_) {
    if (!arguments.length) { return x; }
    x = _;
    return chart;
  };

  chart.yScale = function(_) {
    if (!arguments.length) { return y; }
    y = _;
    return chart;
  };

  chart.xDomain = function(_) {
    if (!arguments.length) { return xDomain; }
    xDomain = _;
    return chart;
  };

  chart.yDomain = function(_) {
    if (!arguments.length) { return yDomain; }
    yDomain = _;
    return chart;
  };

  chart.leafClick = function(_) {
    if (!arguments.length) { return leafClick; }
    leafClick = _;
    return chart;
  };

  chart.getSize = function(_) {
    if (!arguments.length) { return getSize; }
    getSize = _;
    return chart;
  };

  chart.groupBy = function(_) {
    if (!arguments.length) { return groupBy; }
    groupBy = _;
    return chart;
  };

  chart.groups = function(_) {
    if (!arguments.length) { return groups; }
    groups = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) { return id; }
    id = _;
    return chart;
  };

  //============================================================


  return chart;
};

nv.models.treemapChart = function() {

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  var treemap = nv.models.treemap()
    , legend = nv.models.legend()
    ;

  var margin = {top: 0, right: 10, bottom: 10, left: 10}
    , width = null
    , height = null
    , showTitle = false
    , showLegend = false
    , tooltip = null
    , tooltips = true
    , tooltipContent = function(point) {
        var tt = '<p>Value: <b>' + d3.format(',.2s')(point.value) + '</b></p>' +
          '<p>Name: <b>' + point.name + '</b></p>';
        return tt;
      }
      //create a clone of the d3 array
    , colorArray = d3.scale.category20().range().map( function(d){ return d; })
    , x //can be accessed via chart.xScale()
    , y //can be accessed via chart.yScale()
    , noData = 'No Data Available.'
    , dispatch = d3.dispatch('tooltipShow', 'tooltipHide', 'tooltipMove', 'elementMousemove')
    ;


  //============================================================


  //============================================================
  // Private Variables
  //------------------------------------------------------------

  var showTooltip = function(e, offsetElement) {
    //console.log(e.pos)
    var left = e.pos[0],// + ( (offsetElement && offsetElement.offsetLeft) || 0 ),
        top = e.pos[1],// + ( (offsetElement && offsetElement.offsetTop) || 0 ),
        content = tooltipContent(e.point);
    tooltip = nv.tooltip.show( [left, top], content, null, null, offsetElement );
  };

  //============================================================


  function chart(selection) {
    selection.each(function(chartData) {

      var data = [chartData];

      var container = d3.select(this),
          that = this;

      var availableWidth = (width  || parseInt(container.style('width'), 10) || 960) - margin.left - margin.right,
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;

      chart.update = function() { container.transition().duration(300).call(chart); };
      chart.container = this;

      //------------------------------------------------------------
      // Display noData message if there's nothing to show.

      if (!data || !data.length || !data.filter(function(d) { return d.children.length; }).length) {
        var noDataText = container.selectAll('.nv-noData').data([noData]);

        noDataText.enter().append('text')
          .attr('class', 'nvd3 nv-noData')
          .attr('dy', '-.7em')
          .style('text-anchor', 'middle');

        noDataText
          .attr('x', margin.left + availableWidth / 2)
          .attr('y', margin.top + availableHeight / 2)
          .text(function(d) { return d; });

        return chart;
      } else {
        container.selectAll('.nv-noData').remove();
      }

      //------------------------------------------------------------

      //remove existing colors from default color array, if any
      if (colorData === 'data') {
        removeColors(data[0]);
      }


      //------------------------------------------------------------
      // Setup containers and skeleton of chart

      var wrap = container.selectAll('g.nv-wrap.nv-treemapWithLegend').data(data);
      var gEnter = wrap.enter().append('g').attr('class', 'nvd3 nv-wrap nv-treemapWithLegend').append('g');
      var g = wrap.select('g');

      gEnter.append('g').attr('class', 'nv-treemapWrap');

      //------------------------------------------------------------


      //------------------------------------------------------------
      // Title & Legend

      var titleHeight = 0
        , legendHeight = 0;

      if (showLegend) {
        gEnter.append('g').attr('class', 'nv-legendWrap');

        legend
          .id('legend_' + chart.id())
          .width(availableWidth + margin.left)
          .height(availableHeight);

        g.select('.nv-legendWrap')
          .datum(data)
          .call(legend);

        legendHeight = legend.height() + 10;

        if (margin.top !== legendHeight + titleHeight) {
          margin.top = legendHeight + titleHeight;
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;
        }

        g.select('.nv-legendWrap')
          .attr('transform', 'translate(' + (-margin.left) + ',' + (-margin.top) + ')');
      }

      if (showTitle && properties.title) {
        gEnter.append('g').attr('class', 'nv-titleWrap');

        g.select('.nv-title').remove();

        g.select('.nv-titleWrap')
          .append('text')
            .attr('class', 'nv-title')
            .attr('x', 0)
            .attr('y', 0)
            .attr('text-anchor', 'start')
            .text(properties.title)
            .attr('stroke', 'none')
            .attr('fill', 'black')
          ;

        titleHeight = parseInt(g.select('.nv-title').style('height'), 10) +
          parseInt(g.select('.nv-title').style('margin-top'), 10) +
          parseInt(g.select('.nv-title').style('margin-bottom'), 10);

        if (margin.top !== titleHeight + legendHeight) {
          margin.top = titleHeight + legendHeight;
          availableHeight = (height || parseInt(container.style('height'), 10) || 400) - margin.top - margin.bottom;
        }

        g.select('.nv-titleWrap')
          .attr('transform', 'translate(0,' + (-margin.top + parseInt(g.select('.nv-title').style('height'), 10)) + ')');
      }

      //------------------------------------------------------------


      //------------------------------------------------------------

      wrap.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');


      //------------------------------------------------------------
      // Main Chart Component(s)

      treemap
        .width(availableWidth)
        .height(availableHeight);


      var treemapWrap = g.select('.nv-treemapWrap')
          .datum(data.filter(function(d) { return !d.disabled; }));

      treemapWrap.transition().call(treemap);

      //------------------------------------------------------------



      //============================================================
      // Event Handling/Dispatching (in chart's scope)
      //------------------------------------------------------------

      legend.dispatch.on('legendClick', function(d,i) {
        d.disabled = !d.disabled;

        if (!data.filter(function(d) { return !d.disabled; }).length) {
          data.map(function(d) {
            d.disabled = false;
            wrap.selectAll('.nv-series').classed('disabled', false);
            return d;
          });
        }

        container.transition().duration(300).call(chart);
      });

      dispatch.on('tooltipShow', function(e) {
        if (tooltips) {
          showTooltip(e, that.parentNode);
        }
      });

      //============================================================

      function removeColors(d) {
        var i, l;
        if (d.color && colorArray.indexOf(d.color) !== -1) {
          colorArray.splice(colorArray.indexOf(d.color),1);
        }
        if ( d.children )
        {
          l = d.children.length;
          for (i = 0; i < l; i += 1) {
            removeColors(d.children[i]);
          }
        }
      }

    });

    return chart;
  }


  //============================================================
  // Event Handling/Dispatching (out of chart's scope)
  //------------------------------------------------------------

  treemap.dispatch.on('elementMouseover', function(e) {
    e.pos = [e.pos[0] + margin.left, e.pos[1] + margin.top];
    dispatch.tooltipShow(e);
  });

  treemap.dispatch.on('elementMouseout', function(e) {
    dispatch.tooltipHide(e);
  });
  dispatch.on('tooltipHide', function() {
    if (tooltips) {
      nv.tooltip.cleanup();
    }
  });

  treemap.dispatch.on('elementMousemove', function(e) {
    dispatch.tooltipMove(e);
  });
  dispatch.on('tooltipMove', function(e) {
    if (tooltip) {
      nv.tooltip.position(tooltip,e.pos);
    }
  });
  //============================================================


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  // expose chart's sub-components
  chart.dispatch = dispatch;
  chart.legend = legend;
  chart.treemap = treemap;

  d3.rebind(chart, treemap, 'x', 'y', 'xDomain', 'yDomain', 'forceX', 'forceY', 'clipEdge', 'id', 'delay', 'leafClick', 'getSize', 'getName', 'groups', 'color', 'fill', 'classes', 'gradient');

  chart.colorData = function (_) {
    if (!arguments.length) { return colorData; }

    var type = arguments[0],
        params = arguments[1] || {},
        colors = function (d,i) {
          var c = (type === 'data' && d.color) ? {color:d.color} : {};
          return nv.utils.getColor(colorArray)(c,i);
        },
        classes = function (d,i) { return 'nv-child'; }
        ;

    switch (type) {
      case 'graduated':
        colors = function (d,i,l) { return d3.interpolateHsl( d3.rgb(params.c1), d3.rgb(params.c2) )(i/l); };
        break;
      case 'class':
        colors = function () { return 'inherit'; };
        classes = function (d,i) {
          var iClass = (i*(params.step || 1))%20;
          return 'nv-child ' + (d.className || 'nv-fill' + (iClass>9?'':'0') + iClass);
        };
        break;
    }

    var fill = (!params.gradient) ? colors : function (d,i) {
      var p = {orientation: params.orientation || 'horizontal', position: params.position || 'base'};
      return treemap.gradient(d,i,p);
    };

    treemap.color(colors);
    treemap.fill(fill);
    treemap.classes(classes);

    legend.color(colors);
    legend.classes(classes);

    colorData = arguments[0];

    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) { return getX; }
    getX = _;
    treemap.x(_);
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) { return getY; }
    getY = _;
    treemap.y(_);
    return chart;
  };

  chart.margin = function(_) {
    if (!arguments.length) { return margin; }
    margin.top    = typeof _.top    !== 'undefined' ? _.top    : margin.top;
    margin.right  = typeof _.right  !== 'undefined' ? _.right  : margin.right;
    margin.bottom = typeof _.bottom !== 'undefined' ? _.bottom : margin.bottom;
    margin.left   = typeof _.left   !== 'undefined' ? _.left   : margin.left;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) { return width; }
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) { return height; }
    height = _;
    return chart;
  };

  chart.showTitle = function(_) {
    if (!arguments.length) { return showTitle; }
    showTitle = _;
    return chart;
  };

  chart.showLegend = function(_) {
    if (!arguments.length) { return showLegend; }
    showLegend = _;
    return chart;
  };

  chart.tooltip = function(_) {
    if (!arguments.length) return tooltip;
    tooltip = _;
    return chart;
  };

  chart.tooltips = function(_) {
    if (!arguments.length) { return tooltips; }
    tooltips = _;
    return chart;
  };

  chart.tooltipContent = function(_) {
    if (!arguments.length) { return tooltipContent; }
    tooltipContent = _;
    return chart;
  };

  chart.noData = function(_) {
    if (!arguments.length) { return noData; }
    noData = _;
    return chart;
  };

  //============================================================

  return chart;
};

nv.models.tree = function() {

  // issues: 1. zoom slider doesn't zoom on chart center
  // orientation
  // bottom circles

  // all hail, stepheneb
  // https://gist.github.com/1182434
  // http://mbostock.github.com/d3/talk/20111018/tree.html
  // https://groups.google.com/forum/#!topic/d3-js/-qUd_jcyGTw/discussion
  // http://ajaxian.com/archives/foreignobject-hey-youve-got-html-in-my-svg

  //============================================================
  // Public Variables with Default Settings
  //------------------------------------------------------------

  // specific to org chart
  var r = 5.5
    , padding = { 'top': 10, 'right': 10, 'bottom': 10, 'left': 10 } // this is the distance from the edges of the svg to the chart
    , duration = 300
    , zoomExtents = { 'min': 0.25, 'max': 2 }
    , nodeSize = { 'width': 100, 'height': 50 }
    , nodeImgPath = '../img/'
    , nodeRenderer = function(d) { return '<div class="nv-tree-node"></div>'; }
    , horizontal = false
  ;

  var id = Math.floor( Math.random() * 10000 ) //Create semi-unique ID in case user doesn't select one
    , color = nv.utils.defaultColor()
    , fill = function(d,i) { return color(d,i); }
    , gradient = function(d,i) { return color(d,i); }

    , setX = function(d,v) { d.x = v; }
    , setY = function(d,v) { d.y = v; }
    , setX0 = function(d,v) { if (horizontal) { d.y0 = v; } else { d.x0 = v; } }
    , setY0 = function(d,v) { if (horizontal) { d.x0 = v; } else { d.y0 = v; } }

    , getX = function(d) { return (horizontal?d.y:d.x); }
    , getY = function(d) { return (horizontal?d.x:d.y); }
    , getX0 = function(d) { return (horizontal?d.y0:d.x0); }
    , getY0 = function(d) { return (horizontal?d.x0:d.y0); }

    , getId = function(d) { return d.id; }

    , fillGradient = function(d,i) {
        return nv.utils.colorRadialGradient( d, i, 0, 0, '35%', '35%', color(d,i), wrap.select('defs') );
      }
    , useClass = false
    , valueFormat = d3.format(',.2f')
    , showLabels = true
    , dispatch = d3.dispatch( 'chartClick', 'elementClick', 'elementDblClick', 'elementMouseover', 'elementMouseout' )
  ;

  //============================================================

  function chart(selection)
  {
    selection.each(function(data) {

      var diagonal = d3.svg.diagonal()
            .projection(function(d) {
              return [getX(d), getY(d)];
            });
      var zoom = d3.behavior.zoom().scaleExtent([zoomExtents.min, zoomExtents.max])
            .on('zoom', function() {
              gEnter.attr('transform', 'translate('+ d3.event.translate +')scale('+ d3.event.scale +')' );
            });
      //------------------------------------------------------------
      // Setup svgs and skeleton of chart

      var svg = d3.select(this);
      var wrap = svg.selectAll('.nv-wrap').data([1]);
      var wrapEnter = wrap.enter().append('g')
            .attr('class', 'nvd3 nv-wrap')
            .attr('id', 'nv-chart-' + id)
            .call( zoom );

      var defsEnter = wrapEnter.append('defs');

      var backg = wrapEnter.append('svg:rect')
            .attr('id', 'backg')
            .attr('transform', 'translate('+ padding.left +','+ padding.top +')')
            .style('fill', 'transparent');

      var gEnter = wrapEnter.append('g');

      var treeChart = gEnter.append('g')
            .attr('class', 'nv-tree')
            .attr('id', 'vis');

      // Compute the new tree layout.
      var tree = d3.layout.tree()
            .size(null)
            .elementsize([(horizontal ? nodeSize.height : nodeSize.width),1])
            .separation( function separation(a,b) { return a.parent == b.parent ? 1 : 1; });

      var svgSize = { // the size of the svg container
          'width': parseInt(svg.style('width'), 10)
        , 'height': parseInt(svg.style('height'), 10)
      };

      data.x0 = data.x0 || 0;
      data.y0 = data.y0 || 0;

      var _data = data;

      chart.showall = function() {
        function expandAll(d) {
          if ( (d.children && d.children.length) || (d._children && d._children.length) ) {
            if (d._children && d._children.length) {
              d.children = d._children;
              d._children = null;
            }
            d.children.forEach(expandAll);
          }
        }
        expandAll(_data);
        zoom.translate([0, 0]).scale(1);
        gEnter.attr('transform', 'translate('+ [0,0] +')scale('+ 1 +')');
        chart.update(_data);
      };

      chart.resize = function() {
        svgSize = {
            'width': parseInt(svg.style('width'), 10 )
          , 'height': parseInt(svg.style('height'), 10 )
        };
        chart.reset();
        chart.update(_data);
      };

      chart.orientation = function(orientation) {
        horizontal = (orientation === 'horizontal' || !horizontal ? true : false);
        chart.reset();
        chart.update(_data);
      };

      chart.reset = function() {
        zoom.translate([0, 0]).scale(1);
        gEnter.attr('transform', 'translate('+ [0,0] +')scale('+ 1 +')');
      };

      chart.zoom = function(step) {
        var limit = (step>0 ? zoomExtents.max : zoomExtents.min)
          , scale = Math.min( Math.max( zoom.scale() + step, zoomExtents.min), zoomExtents.max);
        if (scale !== limit) {
          zoom.translate([0, 0]).scale(scale);
          gEnter.attr('transform', 'translate('+ [0,0] +')scale('+ scale +')');
        }
        return scale;
      };

      chart.zoomLevel = function(level) {
        var scale = Math.min( Math.max( level, zoomExtents.min), zoomExtents.max);
        zoom.translate([0, 0]).scale(scale);
        gEnter.attr('transform', 'translate('+ [0,0] +')scale('+ scale +')');
        return scale;
      };

      chart.filter = function(node) {
        var __data = {}
          , found = false;

        function findNode(d) {
          if (getId(d) === node) {
            __data = d;
            found = true;
          } else if (!found && d.children) {
            d.children.forEach(findNode);
          }
        }

        // Initialize the display to show a few nodes.
        findNode(data);

        __data.x0 = 0;
        __data.y0 = 0;

        _data = __data;

        chart.update(_data);
      };

      chart.update = function(source) {

        // Click tree node.
        function leafClick(d) {
          toggle(d);
          chart.update(d);
        }

        // Toggle children.
        function toggle(d) {
          if (d.children) {
            d._children = d.children;
            d.children = null;
          } else {
            d.children = d._children;
            d._children = null;
          }
        }

        var nodes = tree.nodes(_data);
        var root = nodes[0];

        var availableSize = { // the size of the svg container minus padding
            'width': svgSize.width - padding.left - padding.right
          , 'height': svgSize.height - padding.top  - padding.bottom
        };

        var chartSize = { // the size of the chart itself
            'width': d3.min(nodes, getX) + d3.max(nodes, getX )
          , 'height': d3.min(nodes, getY) + d3.max(nodes, getY)
        };

        if (horizontal) {
          chartSize.width = (chartSize.width * nodeSize.width*2) + nodeSize.width;
        } else {
          chartSize.height = (chartSize.height * nodeSize.height*2) + nodeSize.height;
        }

        // initial chart scale to fit chart in container
        var scale = d3.min([ availableSize.width/chartSize.width, availableSize.height/chartSize.height ]);

        // initial chart translation to position chart in the center of container
        var center = (availableSize.width/chartSize.width < availableSize.height/chartSize.height) ?
                [ 0, ((availableSize.height/scale)-chartSize.height)/2 ]
              :
                [ ((availableSize.width/scale)-chartSize.width)/2, 0 ]
              ;

        // this is needed because the origin of a node is at the bottom
        var offset = {
            'top': (horizontal ? padding.top/2 : nodeSize.height)
          , 'left': (horizontal ? nodeSize.width : padding.left/2)
        };

        backg
          .attr('width', availableSize.width)
          .attr('height', availableSize.height);

        treeChart.attr('transform', 'translate('+ [
            (offset.left + center[0]) * scale,
            (offset.top + center[1]) * scale
          ] +')scale('+ scale +')');

        nodes.forEach(function(d) {
          setY(d, d.depth * (horizontal ? 2 * nodeSize.width : 2 * nodeSize.height) );
        });

        // Update the nodes…
        var node = treeChart.selectAll('g.nv-card')
              .data(nodes, getId);

        // Enter any new nodes at the parent's previous position.
        var nodeEnter = node.enter().append('svg:g')
              .attr('class', 'nv-card')
              .attr('id', function(d) { return 'nv-card-'+ getId(d); })
              .attr("transform", function(d) {
                if (getY(source) === 0) {
                  return "translate(" + getX(root) + "," + getY(root) + ")";
                } else {
                  return "translate(" + getX0(source) + "," + getY0(source) + ")";
                }
              })
              .on('click', leafClick);

        // node content
        nodeEnter.append("foreignObject").attr('class', 'nv-foreign-object')
            .attr("width", 1)
            .attr("height", 1)
            .attr("x", -1)
            .attr("y", -1)
          .append("xhtml:body")
            .style("font", "14px 'Helvetica Neue'")
            .html(nodeRenderer);

        // node circle
        var xcCircle = nodeEnter.append('svg:g').attr('class', 'nv-expcoll')
              .style('opacity', 1e-6);
            xcCircle.append('svg:circle').attr('class', 'nv-circ-back')
              .attr('r', r);
            xcCircle.append('svg:line').attr('class', 'nv-line-vert')
              .attr('x1', 0).attr('y1', 0.5-r).attr('x2', 0).attr('y2', r-0.5)
              .style('stroke', '#bbb');
            xcCircle.append('svg:line').attr('class', 'nv-line-hrzn')
              .attr('x1', 0.5-r).attr('y1', 0).attr('x2', r-0.5).attr('y2', 0)
              .style('stroke', '#fff');

        //Transition nodes to their new position.
        var nodeUpdate = node.transition()
              .duration(duration)
              .attr('transform', function(d) { return 'translate('+ getX(d) +','+ getY(d) +')'; });

            nodeUpdate.select('.nv-expcoll')
              .style('opacity', function(d) { return ( (d.children && d.children.length) || (d._children && d._children.length) ) ? 1 : 0; });
            nodeUpdate.select('.nv-circ-back')
              .style('fill', function(d) { return (d._children && d._children.length) ? '#777' : (d.children?'#bbb':'none'); });
            nodeUpdate.select('.nv-line-vert')
              .style('stroke', function(d) { return (d._children && d._children.length) ? '#fff' : '#bbb'; });

            nodeUpdate.selectAll('.nv-foreign-object')
              .attr("width", nodeSize.width)
              .attr("height", nodeSize.height)
              .attr("x", (horizontal ? -nodeSize.width+r : -nodeSize.width/2) )
              .attr("y", (horizontal ? -nodeSize.height/2+r : -nodeSize.height+r*2) );

        // Transition exiting nodes to the parent's new position.
        var nodeExit = node.exit().transition()
              .duration(duration)
              .attr('transform', function(d) { return "translate(" + getX(source) + "," + getY(source) + ")"; })
              .remove();
            nodeExit.selectAll('.nv-expcoll')
              .style('stroke-opacity', 1e-6);

            nodeExit.selectAll('.nv-foreign-object')
              .attr("width", 1)
              .attr("height", 1)
              .attr("x", -1)
              .attr("y", -1);

        // Update the links
        var link = treeChart.selectAll('path.link')
              .data(tree.links(nodes), function(d) { return getId(d.source) + '-' + getId(d.target); });

            // Enter any new links at the parent's previous position.
            link.enter().insert('svg:path', 'g')
              .attr('class', 'link')
              .attr('d', function(d) {
                var o = ( getY(source) === 0 ) ? { x: source.x, y: source.y } : { x: source.x0, y: source.y0 };
                return diagonal({ source: o, target: o });
              });

            // Transition links to their new position.
            link.transition()
              .duration(duration)
              .attr('d', diagonal);

            // Transition exiting nodes to the parent's new position.
            link.exit().transition()
              .duration(duration)
              .attr('d', function(d) {
                var o = { x: source.x, y: source.y };
                return diagonal({ source: o, target: o });
              })
              .remove();

        // Stash the old positions for transition.
        nodes
          .forEach(function(d) {
            setX0(d, getX(d));
            setY0(d, getY(d));
          });

      };

      chart.gradient( fillGradient );
      chart.update(_data);

    });

    return chart;
  }


  //============================================================
  // Expose Public Variables
  //------------------------------------------------------------

  chart.dispatch = dispatch;

  chart.color = function(_) {
    if (!arguments.length) return color;
    color = _;
    return chart;
  };
  chart.fill = function(_) {
    if (!arguments.length) return fill;
    fill = _;
    return chart;
  };
  chart.gradient = function(_) {
    if (!arguments.length) return gradient;
    gradient = _;
    return chart;
  };
  chart.useClass = function(_) {
    if (!arguments.length) return useClass;
    useClass = _;
    return chart;
  };

  chart.width = function(_) {
    if (!arguments.length) return width;
    width = _;
    return chart;
  };

  chart.height = function(_) {
    if (!arguments.length) return height;
    height = _;
    return chart;
  };

  chart.values = function(_) {
    if (!arguments.length) return getValues;
    getValues = _;
    return chart;
  };

  chart.x = function(_) {
    if (!arguments.length) return getX;
    getX = _;
    return chart;
  };

  chart.y = function(_) {
    if (!arguments.length) return getY;
    getY = d3.functor(_);
    return chart;
  };

  chart.showLabels = function(_) {
    if (!arguments.length) return showLabels;
    showLabels = _;
    return chart;
  };

  chart.id = function(_) {
    if (!arguments.length) return id;
    id = _;
    return chart;
  };

  chart.valueFormat = function(_) {
    if (!arguments.length) return valueFormat;
    valueFormat = _;
    return chart;
  };

  chart.labelThreshold = function(_) {
    if (!arguments.length) return labelThreshold;
    labelThreshold = _;
    return chart;
  };

  // ORG

  chart.radius = function(_) {
    if (!arguments.length) return r;
    r = _;
    return chart;
  };

  chart.duration = function(_) {
    if (!arguments.length) return duration;
    duration = _;
    return chart;
  };

  chart.zoomExtents = function(_) {
    if (!arguments.length) return zoomExtents;
    zoomExtents = _;
    return chart;
  };

  chart.padding = function(_) {
    if (!arguments.length) return padding;
    padding = _;
    return chart;
  };

  chart.nodeSize = function(_) {
    if (!arguments.length) return nodeSize;
    nodeSize = _;
    return chart;
  };

  chart.nodeImgPath = function(_) {
    if (!arguments.length) return nodeImgPath;
    nodeImgPath = _;
    return chart;
  };

  chart.nodeRenderer = function(_) {
    if (!arguments.length) return nodeRenderer;
    nodeRenderer = _;
    return chart;
  };

  chart.horizontal = function(_) {
    if (!arguments.length) return horizontal;
    horizontal = _;
    return chart;
  };

  chart.getId = function(_) {
    if (!arguments.length) return getId;
    getId = _;
    return chart;
  };
  //============================================================

  return chart;
};
})();