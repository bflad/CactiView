/*
#########################################################################
#
#       CactiView v2.0 - Brian Flad
#       http://fladpad.com - bflad417@gmail.com
#       
#       Forked and updated from the excellent:
#       CactiView v0.2 - Laurie Denness
#       http://laurie.denness.net - laurie@denness.net
#
#       Displays a rotation selection of graphs from Cacti/Ganglia
#
#       Configuration is available in config.json
#
#########################################################################
*/

// stolen from: http://stackoverflow.com/a/901144
function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.href);
  if(results == null)
  return "";
  else
  return decodeURIComponent(results[1].replace(/\+/g, " "));
}

// Set initial variables

var current_index = -1;
var next_index, previous_index;
var graphs, url;
var rotationTimer = false;
var rotating = true;
var timeout = 15000;

if (getParameterByName("height") != "") {
  var window_height = getParameterByName("height");
}
else {
  var window_height = $(window).height();
}

if (getParameterByName("width") != "") {
  var window_width = getParameterByName("width");
}
else {
  var window_width = $(window).width();
}

var left_graph_div_height = Math.floor(window_height) - 5;
var left_graph_div_width = Math.floor((window_width) * 0.75) - 8;
var right_graph_div_height = Math.floor((window_height) * 0.30) - 5;
var right_graph_div_width = Math.floor((window_width) * 0.25) - 8;
var timestamp_div_height = Math.floor((window_height) * 0.1) - 5;
var timestamp_div_width = right_graph_div_width;
var timestamp_font_size = Math.floor(timestamp_div_height * 0.40);
var current_title_font_size = Math.floor(window_height * 0.065);
var next_title_font_size = Math.floor(current_title_font_size * 0.50);

var left_graph_height = left_graph_div_height - 70;
var left_graph_width = left_graph_div_width - 105;
var right_graph_height = right_graph_div_height - 70;
var right_graph_width = right_graph_div_width - 105;

// define rest of functions

function graphUrl(graph_element,current_index) {
  if (graph_element == "#left-graph") {
    var graph_height = left_graph_height;
    var graph_width = left_graph_width;
    switch (graphs[current_index]['source']) {
      case "cacti":
        var graph_period = "1";
      break;
      case "ganglia":
        var graph_period = "day";
      break;
    }
  }
  else if (graph_element.match(/^#right-graph/)) {
    var graph_height = right_graph_height;
    var graph_width = right_graph_width;
    if (graph_element == "#right-graph-top") {
      switch (graphs[current_index]['source']) {
        case "cacti":
          var graph_period = "2";
        break;
        case "ganglia":
          var graph_period = "week";
        break;
      }
    }
    else if (graph_element == "#right-graph-middle") {
      switch (graphs[current_index]['source']) {
        case "cacti":
          var graph_period = "3";
        break;
        case "ganglia":
          var graph_period = "month";
        break;
      }
    }
    else if (graph_element == "#right-graph-bottom") {
      switch (graphs[current_index]['source']) {
        case "cacti":
          var graph_period = "4";
        break;
        case "ganglia":
          var graph_period = "year";
        break;
      }
    }
  }
  else {
    alert("Unknown graph element!");
    return "";
  }

  switch (graphs[current_index]['source']) {
    case "cacti":
    return url['cacti']+
      'action=view'+
      '&local_graph_id='+graphs[current_index]['graph_id']+
      '&rra_id=0'+
      '&graph_height='+graph_height+
      '&graph_width='+graph_width+
      '&graph_nolegend=1'+
      '&rra_id='+graph_period;
    case "ganglia":
    return url['ganglia']+
      's=descending'+
      '&m=load_one'+
      '&r=day'+
      '&c='+graphs[current_index]['cluster']+
      '&g='+graphs[current_index]['graph_type']+
      '&height='+graph_height+
      '&width='+graph_width+
      '&hc=4'+
      '&mc=2'+
      '&nolegend=1'+
      '&r='+graph_period;
    default:
    alert('Graph source not defined!');
    return '';
  }
}

function keyboardHandler(objEvent) {
  // Internet Explorer
  if ( objEvent == null ) {
    keycode = event.keyCode;
    escapeKey = 27;
    // Mozilla and others
  } else {
    keycode = objEvent.keyCode;
    escapeKey = objEvent.DOM_VK_ESCAPE;
  }
  // Get the key in lower case form
  key = String.fromCharCode(keycode).toLowerCase();

  // Verify the key to show the previous page
  if ( keycode == 37 ) {
    previousPage();
  }
  // Verify the key to show the next page
  else if ( keycode == 39 ) {
    nextPage();
  }
  // Verify the key to pause page or restart rotation
  else if ( keycode == 32 ) {
    if (rotating) {
      rotationTimer = stopRotation(rotationTimer);
      rotating = false;
    }
    else {
      nextPage();
      rotationTimer = startRotation(rotationTimer);
      rotating = true;
    }
  }
}

function loadGraph(graph_element,current_index) {
  var img = new Image();
  $(img).load(function () {
    $(this).hide();
    $(graph_element).removeClass('loading').html(this);
    $(this).fadeIn();
  }).error(function () {
  // notify the user that the image could not be loaded
  alert("Error loading image!");
  }).attr('src', graphUrl(graph_element,current_index));
}

function nextPage() {
  current_index++;
  if (rotating) {
    // resets rotation timer
    rotationTimer = startRotation(rotationTimer);
  }
  reloadPage();
}

function previousPage() {
  current_index--;
  if (rotating) {
    // resets rotation timer
    rotationTimer = startRotation(rotationTimer);
  }
  reloadPage();
}

function reloadPage() {
  if (current_index < 0) {
    current_index = graphs.length - 1;
    next_index = 0;
    previous_index = current_index - 1;
  }
  else if (current_index >= graphs.length) {
    current_index = 0;
    next_index = current_index + 1;
    previous_index = graphs.length - 1;
  }
  else {
    next_index = current_index + 1;
    if (next_index >= graphs.length) {
      next_index = 0;
    }
    previous_index = current_index - 1;
    if (previous_index < 0) {
      previous_index = 0;
    }
  }

  $('#current_title').text(graphs[current_index]['title']);
  $('#next_title').text("Next: "+graphs[next_index]['title']);
  $('#timestamp').html(timestamp());
  $('.left-graph').addClass('loading').html("");
  $('.right-graph').addClass('loading').html("");

  loadGraph("#left-graph",current_index);
  loadGraph("#right-graph-top",current_index);
  loadGraph("#right-graph-middle",current_index);
  loadGraph("#right-graph-bottom",current_index);
}

function startRotation(rotationTimer) {
  if (rotationTimer) {
    window.clearInterval(rotationTimer);
  }
  rotationTimer = window.setInterval("nextPage();",timeout);
  return rotationTimer;
}

function stopRotation(rotationTimer) {
  window.clearInterval(rotationTimer);
  rotationTimer = window.setInterval("reloadPage();",timeout);
  return rotationTimer;
}

function timestamp() {
  var now = new Date();
  var monthnames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
  var year = now.getFullYear();
  var monthname = monthnames[now.getMonth()];
  var monthday = now.getDate();
  var hour = now.getHours();
  var minute = now.getMinutes();
  var second = now.getSeconds();
  var ampm = "AM";
  if (hour   > 11) { ampm = "PM";             }
  if (hour   > 12) { hour = hour - 12;      }
  if (hour   == 0) { hour = 12;             }
  if (hour   < 10) { hour   = "0" + hour;   }
  if (minute < 10) { minute = "0" + minute; }
  if (second < 10) { second = "0" + second; }
  return monthname + ' ' + monthday + ', ' + year + "<br />" +
  hour + ':' + minute + ':' + second + " " + ampm;
}