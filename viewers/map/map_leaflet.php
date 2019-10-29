<?php

/**
* Mapping page. It can be loaded in app_timemap widget or launched as standalone page
*
* @package     Heurist academic knowledge management system
* @link        http://HeuristNetwork.org
* @copyright   (C) 2005-2019 University of Sydney
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

//&callback=initMap" async defer  for gmap
//<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=drawing"></script>
//
define('PDIR','../../');  //need for proper path to js and css    
require_once(dirname(__FILE__).'/../../hclient/framecontent/initPage.php');

$system->defineConstants();
?>
<script type="text/javascript" src="<?php echo PDIR;?>external/jquery.layout/jquery.layout-latest.js"></script>

<link rel="stylesheet" href="<?php echo PDIR;?>external/leaflet/geocoder/Control.Geocoder.css" />
<?php
if(true || $_SERVER["SERVER_NAME"]=='localhost'||$_SERVER["SERVER_NAME"]=='127.0.0.1'){
?>
    <link rel="stylesheet" href="<?php echo PDIR;?>external/leaflet/leaflet.css"/>
    <script type="text/javascript" src="<?php echo PDIR;?>external/leaflet/leaflet.js"></script>
    <script type="text/javascript" src="<?php echo PDIR;?>external/jquery.fancytree/jquery.fancytree-all.min.js"></script>
    <script src="<?php echo PDIR;?>external/leaflet/geocoder/Control.Geocoder.js"></script>
<?php
}else{
?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
       integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
       crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
       integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
       crossorigin=""></script>   
    <!-- link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" /-->
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.16.1/jquery.fancytree-all.min.js"></script>
<?php
}
?>
<!-- leaflet plugins -->
<script src="<?php echo PDIR;?>external/leaflet/leaflet-providers.js"></script>
<script src="<?php echo PDIR;?>external/leaflet/bookmarks/Leaflet.Bookmarks.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo PDIR;?>external/leaflet/bookmarks/leaflet.bookmarks.css">
<script src="<?php echo PDIR;?>external/leaflet/leaflet.browser.print.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo PDIR;?>external/leaflet/markercluster/MarkerCluster.css">
<link type="text/css" rel="stylesheet" href="<?php echo PDIR;?>external/leaflet/markercluster/MarkerCluster.Default.css">
<script src="<?php echo PDIR;?>external/leaflet/markercluster/leaflet.markercluster.js"></script>
<script src="<?php echo PDIR;?>external/leaflet/wise-leaflet-pip.js"></script>



<link rel="stylesheet" type="text/css" href="<?php echo PDIR;?>external/jquery.fancytree/skin-themeroller/ui.fancytree.css" />

<script type="text/javascript" src="<?php echo PDIR;?>hclient/core/temporalObjectLibrary.js"></script>

<script type="text/javascript" src="<?php echo PDIR;?>viewers/map/mapping.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>viewers/map/timeline.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>viewers/map/mapManager.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>viewers/map/mapPublish.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>viewers/map/mapDocument.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>viewers/map/mapLayer2.js"></script>

<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/editing/editing2.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/editing/editing_input.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/editing/editSymbology.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>external/js/evol.colorpicker.js" charset="utf-8"></script>
<link href="<?php echo PDIR;?>external/js/evol.colorpicker.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/entity/manageEntity.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/entity/searchEntity.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/entity/manageRecords.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/entity/searchRecords.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/viewers/resultList.js"></script>
<script type="text/javascript" src="<?php echo PDIR;?>hclient/widgets/viewers/resultListMenu.js"></script>


<!--
// WARNING: CHANGES MADE TO vis.js
// These changes are not in our repository
// line:285 remove margin for item's label
// line:345,15753, 15860, 16108 correct orientation for bottom order
// line: 13607 getDataRangeHeurist function
// line: 13594 catch exception of datetime convertation
//
-->
<?php if (!(@$_REQUEST['noinit']!=1 && @$_REQUEST['notimeline'])) { ?>
<script type="text/javascript" src="<?php echo PDIR;?>external/vis/dist/vis.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo PDIR;?>external/vis/dist/vis.css" />
<?php } ?>
<style>
    
    .leaflet-div-icon {
        background: none;
        border: none;
    }

    /*   
    .vis-item-overflow{
    position:absolute;
    }
    .vis-item-overflow .vis-item{
    left:0px;
    top:0px;
    height:100%;
    border:none;
    }
    .vis-item-overflow .vis-item-content{
    position:relative;
    left: 4px;
    }
    .vis-item-overflow .vis-selected{
    z-index:2;
    background-color:#bee4f8 !important;
    border:none;
    }
    .vis-item.vis-selected{
    background-color:#bee4f8 !important;
    }
    */

    .vis-panel.vis-left, .vis-time-axis.vis-foreground{
        background-color:#DDDDDD;
    }
    .vis-item-content{
        width: 10em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .vis-item-bbox{
        background-color:rgb(142, 169, 185);
    }
    
    #term_tree{
         background:none;
         border:none;
    }

    div.svs-contextmenu3{
        right:7px;
        display:none;
        background:lightgray;
        color:black;/*#95A7B7;*/
    }

    leaflet-control-browser-print{
        width: 22px !important;
        height: 22px !important;
    }
    
    .leaflet-browser-print{
        width: 22px !important;
        height: 22px !important;
        line-height: 22px !important;
        background-position: 3px !important;
    }
    
    .map_popup .detail, .map_popup .detail > a{
        max-width: 200px;
    }
    
    .v1 .browser-print-mode{
        padding: 3px 10px;
    }
</style>

<!-- Initializing -->

<script type="text/javascript">

    var mapping, menu_datasets, btn_datasets;
    
    // Callback function on page initialization - see initPage.php
    function onPageInit(success){
        
        if(!success) return;
        
        var lt = window.hWin.HAPI4.sysinfo['layout'];
        if(lt=='Beyond1914' || lt=='UAdelaide'){
                $("head").append($('<link rel="stylesheet" type="text/css" href="'
                    +window.hWin.HAPI4.baseURL+'hclient/widgets/expertnation/expertnation.css?t='+(new Date().getTime())+'">'));
        }        
        
        
        function __gp(name){
            return window.hWin.HEURIST4.util.getUrlParameter(name, location.search)
        }

        var layout_params = null;
        
        //take layout parameters from url 
        // params: 
        //   nomap, notimeline
        //   controls: [all,none,zoom,bookmark,geocoder,print,publish,legend]
        //   legend: [basemaps,search,mapdocs|onedoc]
        //   basemap: name of initial basemap
        //   extent: fixed extent    
        layout_params = {};
        layout_params['nomap'] = __gp('nomap');
        layout_params['notimeline'] = __gp('notimeline');
        layout_params['nocluster'] = __gp('nocluster');
        layout_params['editstyle'] = __gp('editstyle');
        layout_params['basemap'] = __gp('basemap');  //name of basemap
        layout_params['extent'] = __gp('extent'); //@todo
        
        layout_params['controls'] = __gp('controls'); //comma separated list of visible controls
        layout_params['legend'] = __gp('legend'); //comma separated list of visible panels: basemap,search,mapdocs
        layout_params['template'] = __gp('template'); //smarty template for popup info
        
        layout_params['published'] = __gp('published');
        layout_params['show_tooltip'] = __gp('show_tooltip');
        
        mapping = $('#mapping').mapping({
            element_layout: '#mapping',
            element_map: '#map',
            layout_params:layout_params,
            oninit: onMapInit
        });
    }

    //
    // init map data based on url parameters
    //
    function onMapInit( mapwdiget ){
        //take url parameters and open mapdocument or/and perform query
            //take from frame
            var mapdocument = window.hWin.HEURIST4.util.getUrlParameter('mapdocument', location.search);
            //take from top most
            if( window.hWin.HEURIST4.util.isempty(mapdocument) ){
                mapdocument = window.hWin.HEURIST4.util.getUrlParameter('mapdocument', window.hWin.location.search);
                if(!(mapdocument>0)){
                    mapdocument = null;
                }
            }
            
            var with_mapdoc = !window.hWin.HEURIST4.util.isempty(mapdocument);

            if( with_mapdoc ){
                mapwdiget.mapping('openMapDocument', mapdocument);
            }
        
            var request = window.hWin.HEURIST4.util.parseHeuristQuery(location.search );
            if( !window.hWin.HEURIST4.util.isempty(request['q']) ){
                //do not zoom to current search if mapdoc is defined
                mapwdiget.mapping('addSearchResult', request, 'Current query', with_mapdoc);
            }
        
    }
</script>

</head>

<!-- HTML -->

<body>
    <div id="mapping" style="min-height:1px;height:100%; width:100%;cursor:progress">
        <!-- Map -->
        <div class="ui-layout-center">
                <div id="map" style="width:100%; height:100%" class="ui-layout-content"><span id="map-loading">Mapping...</span></div>
                <div id="map_empty_message" style="width:100%; height:100%;margin:7px;display: none;">There are no spatial objects to plot on map</div>
            
        </div>

        <!-- Toolbar -->
        <div class="ui-layout-north" id="mapToolbarDiv" style="display: block !important; height: 30px; z-index:999;">

            <span id="map-settingup-message" style="padding-left:1em;line-height:2em;color:red">
                Setting up map ...
            </span>

            <div id="map_limit_warning" style="border-radius:6px;background-color: rgb(172, 231, 255);font-weight:bold; color:red; padding:8px; display:none;">
            </div>
        </div>

        <!-- Timeline -->
        <div class="ui-layout-south">
            <div id="timeline" style="width:100%;height:100%;overflow-y:auto;"></div>
            <div id="timeline_toolbar" style="position:absolute;top:1;left:1;height:20px;"></div>
        </div>
    </div>
    
    <div id="timeline-edit-dialog"  style="display:none" class="ui-heurist-bg-light">
        
            <div style="padding:5px">
                <label><input type="radio" name="time-label" checked value="0">Full length labels</label><br>
                <label><input type="radio" name="time-label" value="1">Truncate label to bar</label><br>
                <label><input type="radio" name="time-label" value="2">Fixed label width</label><br>
                <label><input type="radio" name="time-label" value="3">Hide labels</label>
            </div>
            
            <div style="padding:5px">
                <label><input type="radio" name="time-label-pos" checked value="0">Label within the bar</label><br>
                <!-- <label><input type="radio" name="time-label-pos" value="1">Label to the right of the bar</label><br> -->
                <label><input type="radio" name="time-label-pos" value="2">Label above the bar</label>
            </div>
        
            <div style="padding:5px">
                <label><input type="radio" name="time-label-stack" checked value="0">Bars stacked on the above the other</label><br>
                <label><input type="radio" name="time-label-stack" value="1">Bars wrapped to minimise height of timeline</label>
            </div>

            <div style="padding:5px">
                <label><input type="checkbox" name="time-filter-map" value="1">Filter map with current timeline range</label>
            </div>
        
    </div>
    
</body>
</html>
