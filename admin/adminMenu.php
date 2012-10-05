<?php
/**
 * adminMenu.php
 *
 * Gmenu for administration functions
 *
 * @copyright (C) 2005-2010 University of Sydney Digital Innovation Unit.
 * @link: http://HeuristScholar.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @author Ian Johnson
 * @version 2010.1011
 * @package Heurist academic knowledge management system
 * @todo
 **/

require_once(dirname(__FILE__).'/../common/connect/applyCredentials.php');

if (! is_logged_in()) {
	header('Location: ' . HEURIST_URL_BASE . 'common/connect/login.php?db='.HEURIST_DBNAME."&last_uri=".urlencode(HEURIST_CURRENT_URL));
//HEURIST_URL_BASE.'admin/adminMenu.php?db='.HEURIST_DBNAME);
	return;
}

$url = "../common/html/msgWelcomeAdmin.html";

if(array_key_exists('mode',$_REQUEST)){

	$mode = $_REQUEST['mode'];
	if($mode=="users"){
//HEURIST_BASE_URL
		$url = "ugrps/manageUsers.php?db=".HEURIST_DBNAME;
		if(array_key_exists('recID',$_REQUEST)){
			$recID = $_REQUEST['recID'];
			$url = $url."&recID=".$recID;
		}
		//clear
		//window.history.pushState("object or string", "Title", location.pathname+'?db=
	}else if($mode=="rectype"){
		$url = "structure/manageRectypes.php?db=".HEURIST_DBNAME;
		if(array_key_exists('rtID',$_REQUEST)){
			$rtID = $_REQUEST['rtID'];
			$url = $url."&rtID=".$rtID;
		}
	}

}
?>
<html>

	<head>
		<title>Heurist - <?=HEURIST_DBNAME?> Admin</title>
		<link rel="icon" href="../favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="../common/css/global.css">
		<link rel="stylesheet" type="text/css" href="../common/css/admin.css">
	</head>
	<body>
		<script src="../external/jquery/jquery-1.5.1.min.js"></script>
		<script src="../external/jquery/jquery-ui-1.8.13.custom.min.js"></script>
		<script type="text/javascript" src="../external/yui/2.8.2r1/build/yahoo/yahoo-min.js"></script>
		<script type="text/javascript" src="../external/yui/2.8.2r1/build/json/json-min.js"></script>
		<script src="../common/js/utilsLoad.js"></script><!-- core HEURIST functions -->
		<script src="../common/js/utilsUI.js"></script><!-- core HEURIST functions -->
		<script src="../common/php/displayPreferences.php"></script> <!-- sets body css classes based on prefs in session -->
		<script>
			$(function() {

				var icons = {
					header: "header",
					headerSelected: "headerselected"
				};
				$( "#sidebar-inner" ).accordion({
					collapsible: true,
					active:false,
					icons: icons
				});
			});

		</script>
		<script type="text/javascript">
			function loadContent(url){
				var recordFrame = document.getElementById("adminFrame");
				recordFrame.src = top.HEURIST.basePath+"common/html/msgLoading.html";
				setTimeout(function(){
						recordFrame.src = top.HEURIST.basePath+"admin/"+url;
						},500);
				return false;
			};
		</script>
		<script>
			var bugReportURL = '../export/email/formEmailRecordPopup.html?rectype=bugreport&db=<?=HEURIST_DBNAME?>';
			window.history.pushState("object or string", "Title", location.pathname+'?db=<?=HEURIST_DBNAME?>');
		</script>
		<a id=home-link href="../index.php?db=<?=HEURIST_DBNAME?>">
			<div id=logo title="Click the logo at top left of any Heurist page to return to your Favourites"></div>
		</a>
		<div id=version></div>
		<!-- database name -->
		<a id="dbSearch-link" href="../index.php?db=<?=HEURIST_DBNAME?>">
			<div id="dbname" ><?=HEURIST_DBNAME?> <span>Database Administration</span></div>
		</a>
		<div id="quicklinks" style="top:10px;right:15px">
			<ul id=quicklink-cell>
				<li id="reportBug" class="button white"><a href="#" onClick="top.HEURIST.util.popupURL(top, bugReportURL,{'close-on-blur': false,'no-resize': false, height: 400,width: 740,callback: function (title, bd, bibID) {if (bibID) {window.close(bibID, title);}} });return false;" title="Click to send a bug report or feature request" ><img src="../common/images/16x16.gif"></a></li>
				<li class="button white"><a href="javascript:void(0)" onClick="{top.HEURIST.util.reloadStrcuture();}" title="Click to clear the internal working memory of Heurist" >refresh memory</a></li>
			</ul>
		</div>

		<!-- sidebar -->
		<div id="sidebar">
             <div style="padding:20px;">
         <a  href="../index.php?db=<?=HEURIST_DBNAME?>" 
         title="Click the logo at top left of any Heurist page to return to the main search page"><b>USER VIEW &nbsp;></b></a>
            </div>
  
			<div id="sidebar-inner">
				<!-- <div id="accordion">-->
 
				<h3><a href="#">DATABASE: </a>
				<span class="description">Overall management of the database and its relation with other Heurist databases registered in the Heurist index at HeuristScholar.org</span></h3>
				<div class="adminSection">
					<ul>

						<li class="seperator"><a href="#" onClick="loadContent('structure/getListOfDatabases.php')"
							type="List the databases on the current server">List databases</a></li>
						<li><a href="#" onClick="loadContent('setup/createNewDB.php')"
							type="Create a new database with essential structure elements">New database</a></li>
						<li><a href="#" onClick="loadContent('setup/straightCopyDatabase.php?db=<?=HEURIST_DBNAME?>')"
							title="Clones a complete database with all data, users, attached files, templates etc.">Clone database</a></li>
<?php
if (is_admin()) {
?>
						<li><a href="setup/deleteCurrentDB.php?db=<?=HEURIST_DBNAME?>"
							title="Delete a database completely">Delete entire database</a></li>
						<li><a href="setup/clearCurrentDB.php?db=<?=HEURIST_DBNAME?>"
							title="Clear all data from the current database, database definitions are unaffected">Delete all records</a></li>
<?php
}
?>
						<li class="seperator"><a href="#" onClick="loadContent('setup/registerDB.php?db=<?=HEURIST_DBNAME?>')"
							title="Register this database with the Heurist Master Index">Registration</a></li>
						<li><a href="#" onClick="loadContent('setup/editSysIdentification.php?db=<?=HEURIST_DBNAME?>')"
							title="Edit the internal metadata describing the database and set some global behaviours">Properties</a></li>
						<li><a href="#" onClick="loadContent('setup/editSysIdentificationAdvanced.php?db=<?=HEURIST_DBNAME?>')"
							title="Edit advanced behaviours">Advanced properties</a></li>
						<li><a href="#" onClick="loadContent('rollback/rollbackRecords.php?db=<?=HEURIST_DBNAME?>')"
							title="Selectively roll back the data in the database to a specific date and time)">Rollback</a></li>
						<li class="seperator"></li>
		</ul>
				</div>

				<h3><a href="#">STRUCTURE: </a>
				<span class="description">Management of the database model which defines the types of data which can be recorded and the layout of data entry</span></h3>
				<div class="adminSection">
					<ul>
						<li class="seperator"><a href="#" onClick="loadContent('structure/manageRectypes.php?db=<?=HEURIST_DBNAME?>')"
							title="Add new / modify existing record types and their use of globally defined fields">Record types / fields</a></li>
						<li><a href="#" onClick="loadContent('structure/editRectypeConstraints.php?db=<?=HEURIST_DBNAME?>')"
							title="Define constraints on the record types which can be related, and allowable relationship types">Record constraints</a></li>
						<li  class="seperator"><a href="#" onClick="loadContent('structure/selectDBForImport.php?db=<?=HEURIST_DBNAME?>')"
							title="Selectively import structural elements from other Heurist databases">Import structure</a></li>
                        <li><a href="#" onClick="loadContent('structure/manageDetailTypes.php?db=<?=HEURIST_DBNAME?>')"
                            title="Direct access to the global field definitions">Manage field types</a></li>
                        <li><a href="#" onClick="loadContent('structure/editTerms.php?db=<?=HEURIST_DBNAME?>')"
                            title="Define terms used for relationship types and for other enumerated fields">Manage terms</a></li>
						<li><a href="#" onClick="loadContent('describe/listRectypeDescriptions.php?db=<?=HEURIST_DBNAME?>')"
							title="Display/print a formatted view of the database structure">Structure (human readable)</a></li>
						<li><a href="#" onClick="loadContent('structure/getDBStructure.php?db=<?=HEURIST_DBNAME?>&amp;pretty=1')"
							title="Lists the record type and field definitions in a computer-readable form">Structure (exchange format)</a></li>
						<li><a href="#" onClick="loadContent('describe/getDBStructureAsXForms.php?db=<?=HEURIST_DBNAME?>')"
							title="Save the record types as XForms">Structure (XForms)</a></li>
						<li><a href="#" onClick="loadContent('setup/editMimetypes.php?db=<?=HEURIST_DBNAME?>')"
							title="Define the relationship between file extensions and mime type">Mime types</a></li>
						<li class="seperator"></li>
					</ul>
				</div>


				<h3><a href="#">ACCESS: </a><span class="description">Management of users and groups, the assignment of users to group and the assignment of group tags</span></h3>
				<div class="adminSection">
					<ul>
						<!-- 17/10/11 - Artem has moved addition of users to the workgroups page -->
						<!-- TO DO: Take this menu entry out so that navigation is via user groups -->
                        <li><a href="#"
                            onClick="{loadContent('ugrps/manageGroups.php?db=<?=HEURIST_DBNAME?>');return false;}"
                            title="Assign users to usergroups and set their roles">Manage workgroups</a></li>
                        <li><a href="#"
                            onClick="loadContent('ugrps/editGroupTags.php?db=<?=HEURIST_DBNAME?>')" title="Add and remove workgroup tags">Workgroup tags</a></li>
                        <li><a href="#"
                            onClick="loadContent('ugrps/quitGroupForSession.php?db=<?=HEURIST_DBNAME?>')" title="Quit a workgroup for this session to allow testing of non-group-members view">Quit workgroup temporarily</a></li>
						<li class="seperator"><a href="#"
							onClick="loadContent('setup/getUserFromDB.php?db=<?=HEURIST_DBNAME?>')"
							title="Import users one at a time from another database on the system">Import a user</a></li>
                        <li><a href="#" onClick="loadContent('ugrps/manageUsers.php?db=<?=HEURIST_DBNAME?>')"
                            title="Add and edit database users and usergroups, including authorization of new users">Manage users</a></li>
</ul>
				</div>


				<h3><a href="#">UTILITIES: </a><span class="description">Verification of the integrity of the database and various data-cleaning functions and utilities</span></h3>
				<div class="adminSection">
					<ul>
                        <li class="seperator"><a href="#"
						    onClick="loadContent('verification/recalcTitlesAllRecords.php?db=<?=HEURIST_DBNAME?>')"
							title="Rebuilds the constructed record titles listed in search results, for all records">Rebuild titles</a></li>
						<!-- : Also have capabuility for specific records and rectypes</p> -->
						<li><a href="#"
							onClick="loadContent('verification/checkRectypeTitleMask.php?check=1&amp;db=<?=HEURIST_DBNAME?>')"
							title="Check correctness of each Record Type's title mask with respect to field definitions.">Check Title Masks</a></li>
						<li><a href="#"
							onClick="loadContent('verification/checkRectypeTitleMask.php?check=2&amp;db=<?=HEURIST_DBNAME?>')"
							title="Check correctness and synch canonical mask of each Record Type's title mask with respect to field definitions.">Synch Canonical Title Masks</a></li>
						<li><a href="#"
							onClick="loadContent('verification/listRecordPointerErrors.php?db=<?=HEURIST_DBNAME?>')"
							title="Find record pointer which point to an incorrect record type or to nothing at all">Invalid pointer check</a></li>
						<li><a href="#"
							onClick="loadContent('verification/listFieldTypeDefinitionErrors.php?db=<?=HEURIST_DBNAME?>')"
							title="Find field types with invalid terms or rectypes">Invalid field type check</a></li>
						<li><a href="#"
							onClick="loadContent('verification/listDuplicateRecords.php?fuzziness=10&amp;db=<?=HEURIST_DBNAME?>')"
							title="Fuzzy search to identify records which might contain duplicate data">Find duplicate records</a></li>
						<li><a href="#"
							onClick="loadContent('verification/checkXHTML.php?db=<?=HEURIST_DBNAME?>')"
							title="Check the wysiwyg text fields in records and blog entries for structural errors">Wysiwyg text check</a></li>
						<li><a href="#"
							onClick="loadContent('verification/checkInvalidChars.php?db=<?=HEURIST_DBNAME?>')"
							title="Check the wysiwyg text fields for invalid characters">Invalid characters check</a></li>
						<li><a href="#"
							onClick="loadContent('verification/cleanInvalidChars.php?db=<?=HEURIST_DBNAME?>')"
							title="Attempt to clean up invalid characters in the wysiwyg text fields">Clean invalid characters</a></li>
						<li><a href="../search/search.html?q=_BROKEN_&amp;w=all&amp;db=<?=HEURIST_DBNAME?>" target="_blank"
							title="Show records with URLs which point to a non-existant or otherwise faulty address">Broken URL search</a></li>
                        <!-- Other non-verification functions -->
                        <li class="seperator"><a href="#"
                            onClick="loadContent('verification/removeDatabaseLocks.php?db=<?=HEURIST_DBNAME?>')"
                            title="Remove database locks - use ONLY if you are sure no-one else is accessing adminstrative functions">Clear database locks</a></li>
						<li class="seperator"><a href="../import/direct/getRecordsFromDB.php?db=<?=HEURIST_DBNAME?>" target="_blank"
							title="Import records directly from one database to another, mapping record types, fields types and terms">Database-to-database transfer</a></li>
						<!-- Section for specific maintenance functionality which will be removed later. Yes, could be run directly, but this makes them easily available-->
						<li><a href="../import/direct/upgradeToNamedFiles.php?db=<?=HEURIST_DBNAME?>" target="_blank"
							title="Update the old format bare-number associated file storage to new format path + name">Upgrade associated files naming</a></li>
						<li class="seperator"><a href="#" onClick="loadContent('../export/publish/manageReports.html?db=<?=HEURIST_DBNAME?>')"
							title="Add new / modify existing scheduled reports">Scheduled Reports</a></li>
					</ul>
				</div>
				<!--</div>-->
				<!-- end accordion -->
			</div>
		</div>
		<!-- end sidebar -->


		<div id="page">
			<div id="page-inner">
				<div class="contentframe">
					<iframe width="100%" height="100%" id="adminFrame" name="adminFrame" frameborder="0" src="<?=$url?>"></iframe>
				</div>
			</div>
		</div>

	</body>
</html>