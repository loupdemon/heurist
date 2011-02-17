<?php

/**
 * filename, brief description, date of creation, by whom
 * @copyright (C) 2005-2010 University of Sydney Digital Innovation Unit.
 * @link: http://HeuristScholar.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Heurist academic knowledge management system
 * @todo
 **/

?>

<?php
/**
 * publisherOutput.php - specifies the script and redirection to produce desired output style
 *
 * @package publisherOutput.php
 * @version 2007-03-28
 * @author Kim Jackson, Maria Shvedova
 * last modified 2008-09-04 ms. its readable. at last.
 */

//-------------------------------------------------------
//functions
//-------------------------------------------------------

/**
 * This function checks if the stylesheet exists
 * @param $style requested style for the output (from referring url)
 * @return boolean
 */
function stylesheet_exists($style){
	if (!eregi ('.xsl', $style)){
		$style = $style.".xsl";
	}

	if (file_exists("xsl/".$style)){
		return true;
	} else {
		return false;
	}
}

function get_style_from_pubargs($ss_publish_args){
	$style_arg = strstr($ss_publish_args, "&style=");
	$arr_args = explode("=", $style_arg);
	return $arr_args[1];
}

require_once(dirname(__FILE__).'/../../common/connect/applyCredentials.php');
require_once(dirname(__FILE__).'/../../common/php/dbMySqlWrappers.php');


if (array_key_exists('js', $_REQUEST)) $js = '-javascript'; else $js = '';

if (array_key_exists('out', $_REQUEST)){
	$output = $_REQUEST['out'];
}else {
	$output  = '';
}

if (array_key_exists('style', $_REQUEST)){
	$style = $_REQUEST['style'];
}else {
	$style  = '';
}


if ($_REQUEST['pub_id']) {

	mysql_connection_db_select(DATABASE);
	$res = mysql_query('select * from usrSavedSearches where svs_ID='.$_REQUEST['pub_id']);

	if (mysql_num_rows($res) != 0) {

		if (!$style) { //that means we are dealing with "forced" searches
			$pub = mysql_fetch_assoc($res);
			$style = get_style_from_pubargs($pub['svs_PublishArgs']);
			if ($style === "endnotexml") {
				$style = "endnotexml.xsl"; //cheater! should have modified cocoon pipeline instead!
				$output = "xml";
			}
			//if no style in args, could be one of the ex-published_searches table searches. or someone deleted style parameter from url. whatever.
			//in any case lets asign a default html style output to those.
			if ($style == "") {
				$style  = "pub_details-full.xsl";
			}
		}

		switch ($style) {

			case 'endnoterefer': //last remaining style that is not Cocoon driven YET.
			header('Location: '.HEURIST_URL_BASE.'export/other/endnote.php?pub_id=' . $_REQUEST['pub_id']);
			break;

		case 'genericxml': //no javascript parameter for genericxml
			header('Location: http://'.HEURIST_HOST_NAME.'/cocoon/'.INSTALL_DIR.'/publish/main/' . $_REQUEST['pub_id'] . (HEURIST_DBNAME ? '?db='.HEURIST_DBNAME: ''));
			break;
					// FIXME:  need to change these so that they reflect the setup file values for installs
		default:

			if (stylesheet_exists($style)){
				header('Location: http://'.HEURIST_HOST_NAME.'/cocoon/'.INSTALL_DIR.'/publish/main/'. $_REQUEST['pub_id'] .(!$output ? '': '/'. $output). '/' . $style . $js . (HEURIST_DBNAME ? '?db='.HEURIST_DBNAME: ''));
			} else if (stylesheet_exists("pub_".$style)){	//FIXME: temporary while we decide the final naming
				header('Location: http://'.HEURIST_HOST_NAME.'/cocoon/'.INSTALL_DIR.'/publish/main/'. $_REQUEST['pub_id'] .(!$output ? '': '/'. $output). '/' . 'pub_'.$style . $js . (HEURIST_DBNAME ? '?db='.HEURIST_DBNAME: ''));
			}else{
				die ("Stylesheet ( ".$style." ) doesn't exist");
			}
			break;
 		}

	} else {
		die('No published search found with id ' . $_REQUEST['pub_id']);
	}

} else {
	die('No published search id supplied');
}

?>
