/**
*  Various utility functions
*
* @todo - split to generic utilities and UI utilities
* @todo - split utilities for hapi and load them dynamically from hapi
*
* @see editing_input.js
*
* @package     Heurist academic knowledge management system
* @link        http://HeuristNetwork.org
* @copyright   (C) 2005-2016 University of Sydney
* @author      Artem Osmakov   <artem.osmakov@sydney.edu.au>
* @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU License 3.0
* @version     4.0
*/

/*
* Licensed under the GNU License, Version 3.0 (the "License"); you may not use this file except in compliance
* with the License. You may obtain a copy of the License at http://www.gnu.org/licenses/gpl-3.0.txt
* Unless required by applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied
* See the License for the specific language governing permissions and limitations under the License.
*/

if (!top.HEURIST4){
    top.HEURIST4 = {};
}
//init only once
if (!top.HEURIST4.util) 
{

top.HEURIST4.util = {


    isnull: function(obj){
        return ( (typeof obj==="undefined") || (obj===null));
    },

    isempty: function(obj){
        if (top.HEURIST4.util.isnull(obj)){
            return true;
        }else if(top.HEURIST4.util.isArray(obj)){
            return obj.length<1;
        }else{
            return (obj==="") || (obj==="null");
        }

    },

    isNumber: function (n) {
        //return typeof n === 'number' && isFinite(n);
        return !isNaN(parseFloat(n)) && isFinite(n);
    },

    cloneJSON:function (data){
        try{
            return JSON.parse(JSON.stringify(data));
        }catch (ex2){
            console.log('cannot clone json array '+data);
            return [];
        }
    },

    // get current font size in em
    em: function(input) {
        var emSize = parseFloat($("body").css("font-size"));
        return (emSize * input);
    },

    // get current font size in pixels
    px: function(input) {
        var emSize = parseFloat($("body").css("font-size"));
        return (input / emSize);
    },

    //
    // enable or disable element
    //
    setDisabled: function(element, mode){
      if(element){
          if(!$.isArray(element)){
                element = [element];
          }
          $.each(element, function(idx, ele){
              ele = $(ele);
              if (mode) {
                    ele.prop('disabled', 'disabled');
                    ele.addClass('ui-state-disabled');
              }else{
                    ele.removeProp('disabled');
                    ele.removeClass('ui-state-disabled');
              }
          });
      }
    },
    
    isIE: function () {
        var myNav = navigator.userAgent.toLowerCase();
        return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
    },
    
    //
    //
    //
    checkProtocolSupport: function(url){
        
        if (top.HEURIST4.util.isIE()) { //This bastard always needs special treatment
        
            if (typeof (navigator.msLaunchUri) == typeof (Function)) {
                
                navigator.msLaunchUri(url,
                    function () { /* Success */ },
                    function () { /* Failure */ top.HEURIST4.msg.showMsgErr('Not supported') });
                return;
            }
                
            try {
                var flash = new ActiveXObject("Plugin.mailto");
            } catch (e) {
                //not installed
            }
        } else { //firefox,chrome,opera
            //navigator.plugins.refresh(true);
            var mimeTypes = navigator.mimeTypes;
            var mime = navigator.mimeTypes['application/x-mailto'];
            if(mime) {
                //installed
            } else {
                //not installed
                 top.HEURIST4.msg.showMsgErr('Not supported');
            }
        }      
      
        
    },

    //
    // from object to query string
    //
    composeHeuristQueryFromRequest: function(query_request, encode){
            var query_string = 'db=' + top.HAPI4.database;
        
            if(!top.HEURIST4.util.isnull(query_request)){

                query_string = query_string + '&w='+query_request.w;
                
                if(!top.HEURIST4.util.isempty(query_request.q)){
                    
                    if($.isArray(query_request.q)){
                        sq = JSON.stringify(query_request.q);
                    }else{
                        sq = query_request.q;
                    }
                    
                    if(encode){
                        sq = encodeURIComponent(sq);
                    }
                    
                    query_string = query_string + '&q=' + sq;
                }
                if(!top.HEURIST4.util.isempty(query_request.rules)){
                    //@todo simplify rules array - rempove redundant info
                    query_string = query_string + '&rules=' + 
                        (encode?encodeURIComponent(query_request.rules):query_request.rules);
                }
            }else{
                query_string = query_string + '&w=all';
            }        
            return query_string;        
    },

    composeHeuristQuery2: function(params){
        if(params)
            return top.HEURIST4.util.composeHeuristQuery(params.q, params.w, params.rules, params.notes);
        else
            return '?';
    },

    composeHeuristQuery: function(query, domain, rules, notes){
            var query_to_save = [];
            if(!(top.HEURIST4.util.isempty(domain) || domain=="all")){
                query_to_save.push('w='+domain);
            }
            if(!top.HEURIST4.util.isempty(query)){
               query_to_save.push('q='+query);
            }
            if(!top.HEURIST4.util.isempty(rules)){
               query_to_save.push('rules='+rules);
            }
            if(!top.HEURIST4.util.isempty(notes)){
               query_to_save.push('notes='+notes);
            }
            return '?'+query_to_save.join('&');
    },

    //
    // both paramater should be JSON array or Object
    //
    mergeHeuristQuery: function(query1, query2){
        //JSON.parse
        if(jQuery.type(query1) === "string"){
            try{
                query1 = JSON.parse(query1);
            }catch (ex2){
                query1 = {};
            }
        }
        if(jQuery.type(query2) === "string"){
            try{
                query2 = JSON.parse(query2);
            }catch (ex2){
                query2 = {};
            }
        }
        if(top.HEURIST4.util.isnull(query1) || $.isEmptyObject(query1)){
            return query2;
        }
        if(top.HEURIST4.util.isnull(query2) || $.isEmptyObject(query2)){
            return query1;
        }
        if(!$.isArray(query1)){
            query1 = [query1];
        }
        if(!$.isArray(query2)){
            query2 = [query2];
        }
        return query1.concat(query2)
    },
    
    //
    // converts query string to object
    //
    parseHeuristQuery: function(qsearch)
    {
        var domain = null, rules = '', notes = '';
        if(qsearch && qsearch.indexOf('?')==0){
            domain  = top.HEURIST4.util.getUrlParameter('w', qsearch);
            rules   = top.HEURIST4.util.getUrlParameter('rules', qsearch);
            notes   = top.HEURIST4.util.getUrlParameter('notes', qsearch);
            qsearch = top.HEURIST4.util.getUrlParameter('q', qsearch);
        }
        domain = (domain=='b' || domain=='bookmark')?'bookmark':'all';

        return {q:qsearch, w:domain, rules:rules, notes:notes};
    },

    getUrlParameter: function getUrlParameter(name, query){

        if(!query){
            query = window.location.search;
        }

        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( query );

        if( results == null ) {
            return null;
        } else {
            return results[1];
        }
    },

    isArrayNotEmpty: function (a){
        return (top.HEURIST4.util.isArray(a) && a.length>0);
    },

    isArray: function (a)
    {
        return Object.prototype.toString.apply(a) === '[object Array]';
    },

    htmlEscape: function(s) {
        return s?s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&#39;").replace(/"/g, "&#34;"):'';
    },

    isObject: function (a)
    {
        return Object.prototype.toString.apply(a) === '[object Object]';
    },

    stopEvent: function(e){
        if (!e) e = window.event;

        if (e) {
            e.cancelBubble = true;
            if (e.stopPropagation) e.stopPropagation();
        }
        return e;
    },

    //
    // we have to reduce the usage to minimum. Need to implement method in hapi
    //
    sendRequest: function(url, request, caller, callback){

        if(!request.db){
            request.db = top.HAPI4.database;
        }

        //note jQuery ajax does not properly in the loop - success callback does not work often
        $.ajax({
            url: url,
            type: "POST",
            data: request,
            dataType: "json",
            cache: false,
            error: function(jqXHR, textStatus, errorThrown ) {
                if(callback){
                    callback(caller, {status:top.HAPI4.ResponseStatus.UNKNOWN_ERROR,
                        message: jqXHR.responseText });
                }
                //message:'Error connecting server '+textStatus});
            },
            success: function( response, textStatus, jqXHR ){
                if(callback){
                    callback(caller, response);
                }
            }
        });
    },

    getScrollBarWidth: function() {
        var $outer = $('<div>').css({visibility: 'hidden', width: 100, overflow: 'scroll'}).appendTo('body'),
            widthWithScroll = $('<div>').css({width: '100%'}).appendTo($outer).outerWidth();
        $outer.remove();
        return 100 - widthWithScroll;
    },

    /**
    * convert wkt to
    * format - 0 timemap, 1 google
    *
    * @todo 2 - kml
    * @todo 3 - OpenLayers
    */
    parseCoordinates: function(type, wkt, format, google) {

        if(type==1 && typeof google.maps.LatLng != "function") {
            return null;
        }

        var matches = null;

        switch (type) {
            case "p":
            case "point":
                matches = wkt.match(/POINT\s?\((\S+)\s+(\S+)\)/i);
                break;

            case "c":  //circle
            case "circle":
                matches = wkt.match(/LINESTRING\s?\((\S+)\s+(\S+),\s*(\S+)\s+\S+,\s*\S+\s+\S+,\s*\S+\s+\S+\)/i);
                break;

            case "l":  //polyline
            case "polyline":
            case "path":
                matches = wkt.match(/LINESTRING\s?\((.+)\)/i);
                if (matches){
                    matches = matches[1].match(/\S+\s+\S+(?:,|$)/g);
                }
                break;

            case "r":  //rectangle
            case "rect":
                //matches = wkt.match(/POLYGON\(\((\S+)\s+(\S+),\s*(\S+)\s+(\S+),\s*(\S+)\s+(\S+),\s*(\S+)\s+(\S+),\s*\S+\s+\S+\)\)/i);
                //break;
            case "pl": //polygon
            case "polygon":
                matches = wkt.match(/POLYGON\s?\(\((.+)\)\)/i);
                if (matches) {
                    matches = matches[1].match(/\S+\s+\S+(?:,|$)/g);
                }
                break;
        }


        var bounds = null, southWest, northEast,
        shape  = null,
        points = []; //google points

        if(matches && matches.length>0){

            switch (type) {
                case "p":
                case "point":
                
                    var x0 = parseFloat(matches[1]);
                    var y0 = parseFloat(matches[2]);

                    if(format==0){
                        shape = { point:{lat: y0, lon:x0 } };
                    }else{
                        point = new google.maps.LatLng(y0, x0);
                        points.push(point);
                        bounds = new google.maps.LatLngBounds(
                            new google.maps.LatLng(y0 - 0.5, x0 - 0.5),
                            new google.maps.LatLng(y0 + 0.5, x0 + 0.5));
                    }
                    
                    

                    break;

                /*
                case "r":  //rectangle
                case "rect":

                    if(matches.length<6){
                        matches.push(matches[3]);
                        matches.push(matches[4]);
                    }

                    var x0 = parseFloat(matches[0]);
                    var y0 = parseFloat(matches[2]);
                    var x1 = parseFloat(matches[5]);
                    var y1 = parseFloat(matches[6]);

                    if(format==0){
                        shape  = [
                            {lat: y0, lon: x0},
                            {lat: y0, lon: x1},
                            {lat: y1, lon: x1},
                            {lat: y1, lon: x0},
                        ];

                        shape = {polygon:shape};
                    }else{

                        southWest = new google.maps.LatLng(y0, x0);
                        northEast = new google.maps.LatLng(y1, x1);
                        bounds = new google.maps.LatLngBounds(southWest, northEast);

                        points.push(southWest, new google.maps.LatLng(y0, x1), northEast, new google.maps.LatLng(y1, x0));
                    }

                    break;
                */
                case "c":  //circle
                case "circle":  //circle

                    if(format==0){

                        var x0 = parseFloat(matches[1]);
                        var y0 = parseFloat(matches[2]);
                        var radius = parseFloat(matches[3]) - parseFloat(matches[1]);

                        shape = [];
                        for (var i=0; i <= 40; ++i) {
                            var x = x0 + radius * Math.cos(i * 2*Math.PI / 40);
                            var y = y0 + radius * Math.sin(i * 2*Math.PI / 40);
                            shape.push({lat: y, lon: x});
                        }
                        shape = {polygon:shape};
                        /*
                        bounds = new google.maps.LatLngBounds(
                            new google.maps.LatLng(y0 - radius, x0 - radius),
                            new google.maps.LatLng(y0 + radius, x0 + radius));
                         */
                        
                    }else{
                        /* ARTEM TODO
                        var centre = new google.maps.LatLng(parseFloat(matches[2]), parseFloat(matches[1]));
                        var oncircle = new google.maps.LatLng(parseFloat(matches[2]), parseFloat(matches[3]));
                        setstartMarker(centre);
                        createcircle(oncircle);

                        //bounds = circle.getBounds();
                        */
                    }

                    break;

                case "l":  ///polyline
                case "path":
                case "polyline":

                case "r":  //rectangle
                case "rect":
                case "pl": //polygon
                case "polygon":

                    shape = [];

                    var j;
                    var minLat = 9999, maxLat = -9999, minLng = 9999, maxLng = -9999;
                    for (j=0; j < matches.length; ++j) {
                        var match_matches = matches[j].match(/(\S+)\s+(\S+)(?:,|$)/);

                        var point = {lat:parseFloat(match_matches[2]), lon:parseFloat(match_matches[1])};

                        if(format==0){
                            shape.push(point);
                        }else{
                            points.push(new google.maps.LatLng(points.lat, points.lon));
                        }
                        
                        if (point.lat < minLat) minLat = point.lat;
                        if (point.lat > maxLat) maxLat = point.lat;
                        if (point.lon < minLng) minLng = point.lon;
                        if (point.lon > maxLng) maxLng = point.lon;
                        
                    }

                    if(format==0){
                        shape = (type=="l" || type=="polyline")?{polyline:shape}:{polygon:shape};
                    }else{
                        southWest = new google.maps.LatLng(minLat, minLng);
                        northEast = new google.maps.LatLng(maxLat, maxLng);
                        bounds = new google.maps.LatLngBounds(southWest, northEast);
                    }
                    
            }

        }
        
        if(format==0){
            return shape; //{bounds:bounds, shape:shape};
        }else{
            return {bounds:bounds, points:points};
        }

    },//end parseCoordinates

    // @todo change temporal to moment.js for conversion
    parseDates: function(start, end){
         if(window['Temporal'] && start){   
                //Temporal.isValidFormat(start)){
                
                            // for VISJS timeline
                            function __forVis(dt){
                                if(dt){
                                    var res = dt.toString('yyyy-MM-ddTHH:mm:ssz');
                                    if(res.indexOf('-')==0){ //BCE
                                        res = res.substring(1);
                                        res = '-'+('000000'+res).substring(res.length);
                                    }
                                    return res;
                                }else{
                                    return '';
                                }
                                
                            }    
                
                
                            try{
                                var temporal;
                                if($.type( start ) === "string"  && start.search(/VER=/)){
                                    temporal = new Temporal(start);
                                    if(temporal){
                                        var dt = temporal.getTDate('PDB');  //probable begin
                                        if(!dt) dt = temporal.getTDate('TPQ');
                                        
                                        if(dt){ //this is range - find end date
                                            var dt2 = temporal.getTDate('PDE'); //probable end
                                            if(!dt2) dt2 = temporal.getTDate('TAQ');
                                            end = __forVis(dt2);
                                        }else{
                                            dt = temporal.getTDate('DAT');  //simple date
                                        }
                                        
                                        if(dt){
                                            start = __forVis(dt);
                                        }else{
                                            return null;
                                        }
                                    }
                                }
                                if(start!="" && $.type( end ) === "string"  && end.search(/VER=/)){
                                    temporal = new Temporal(end);
                                    if(temporal){
                                        var dt = temporal.getTDate('PDE'); //probable end
                                        if(!dt) dt = temporal.getTDate('TAQ');
                                        if(!dt) dt = temporal.getTDate('DAT');
                                        end = __forVis(dt);
                                    }
                                }
                            }catch(e){
                                return null;
                            }
                            return [start, end];
         }
         return null;
   },    
    
    //
    // Get CSS property value for a not yet applied class
    //
    getCSS: function (prop, fromClass) {
        var $inspector = $("<div>").css('display', 'none').addClass(fromClass);
        $("body").append($inspector); // add to DOM, in order to read the CSS property
        try {
            return $inspector.css(prop);
        } finally {
            $inspector.remove(); // and remove from DOM
        }
    },
    
    /*: function(e){
        for(var r=0,i=0;i<e.length;i++){
            r=(r<<5)-r+e.charCodeAt(i),r&=r;   
        }
        return r
    },*/

    hashString: function(str) {
    
        var hash = 0, i, c;
        var strlen = str?str.length:0;
        if (strlen == 0) return hash;
        
        for (i = 0; i < strlen; i++) {
            c = str.charCodeAt(i);
            hash = ((hash<<5)-hash)+c;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash;    
    },
    
    //
    // download given url as a file (repalcement of usage A)
    //
    downloadURL: function(url) {
        $idown = $('#idown');
        if ($idown.length>0) {
            $idown.attr('src',url);
        } else {
            $idown = $('<iframe>', { id:'idown', src:url }).hide().appendTo('body');
        }
    },
    
    //
    // download content of given element (for example text area) as a text file
    //
    downloadInnerHtml: function (filename, ele, mimeType) {
        
        var elHtml = $(ele).html();
        
        mimeType = mimeType || 'text/plain';
        var  content = 'data:' + mimeType  +  ';charset=utf-8,' + encodeURIComponent(elHtml);

        var link = document.createElement('a');
        mimeType = mimeType || 'text/plain';
        link.setAttribute('download', filename);
        link.setAttribute('href', content);
        link.click(); 

        link = null;
    },    
    
    isRecordSet: function(recordset){
        return !top.HEURIST4.util.isnull(recordset) && $.isFunction(recordset.isA) && recordset.isA("hRecordSet");   
    },
    
    random: function(){
        //Math.round(new Date().getTime() + (Math.random() * 100));
        return Math.floor((Math.random() * 10000) + 1);
    }

}//end util

String.prototype.htmlEscape = function() {
    return this.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&#39;");
}
String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

if (!Array.prototype.indexOf)
{
    Array.prototype.indexOf = function(elt /*, from*/)
    {
        var len = this.length;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
        ? Math.ceil(from)
        : Math.floor(from);
        if (from < 0)
            from += len;

        for (; from < len; from++)
        {
            if (from in this &&
                this[from] === elt)
                return from;
        }
        return -1;
    };
}

}



$.getMultiScripts = function(arr, path) {
    var _arr = $.map(arr, function(scr) {
        return $.getScript( (path||"") + scr );
    });

    _arr.push($.Deferred(function( deferred ){
        $( deferred.resolve );
    }));

    return $.when.apply($, _arr);
}
