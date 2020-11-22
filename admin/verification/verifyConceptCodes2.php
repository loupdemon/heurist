<?php
    /*
    * Copyright (C) 2005-2020 University of Sydney
    *
    * Licensed under the GNU License, Version 3.0 (the "License"); you may not use this file except
    * in compliance with the License. You may obtain a copy of the License at
    *
    * http://www.gnu.org/licenses/gpl-3.0.txt
    *
    * Unless required by applicable law or agreed to in writing, software distributed under the License
    * is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
    * or implied. See the License for the specific language governing permissions and limitations under
    * the License.
    */

    /**
    * Verifies missed IDinOriginatingDB
    *
    * @author      Artem Osmakov   <artem.osmakov@sydney.edu.au>
    * @copyright   (C) 2005-2020 University of Sydney
    * @link        http://HeuristNetwork.org
    * @version     3.1
    * @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU License 3.0
    * @package     Heurist academic knowledge management system
    * @subpackage  !!!subpackagename for file such as Administration, Search, Edit, Application, Library
    */
define('OWNER_REQUIRED',1);   
define('PDIR','../../');  //need for proper path to js and css    

require_once(dirname(__FILE__).'/../../hclient/framecontent/initPageMin.php');

/*
if( $system->verifyActionPassword($_REQUEST['pwd'], $passwordForServerFunctions) ){
    print $response = $system->getError()['message'];
    exit();
}
*/

?>            
<div style="font-family:Arial,Helvetica;font-size:12px">
            <p>This list shows record and base field types with missed IDinOriginatingDB fields</p>
<?php            


$mysqli = $system->get_mysqli();
    
    //1. find all database
    $query = 'show databases';

    $res = $mysqli->query($query);
    if (!$res) {  print $query.'  '.$mysqli->error;  return; }
    $databases = array();
    while (($row = $res->fetch_row())) {
        if( strpos($row[0], 'hdb_')===0 ){
            //if($row[0]>'hdb_Masterclass_Cookbook')
                $databases[] = $row[0];
        }
    }
    
    foreach ($databases as $idx=>$db_name){

        $query = 'SELECT sys_dbSubVersion from sysIdentification';
        $ver = mysql__select_value($mysqli, $query);
        
        if($ver<3) continue;

        
        $rec_types = array();
        $det_types = array();
        $terms = array();
        $is_found = false;

        //RECORD TYPES
        
        $query = 'SELECT rty_ID, rty_Name, rty_NameInOriginatingDB, rty_OriginatingDBID, rty_IDInOriginatingDB FROM '
            .$db_name.'.defRecTypes WHERE  rty_OriginatingDBID>0 AND (NOT (rty_IDInOriginatingDB>0))';
        
        $res = $mysqli->query($query);
        if (!$res) {  print $query.'  '.$mysqli->error;  return; }
        
        while (($row = $res->fetch_row())) {
               $is_found = true;
               array_push($rec_types, $row);
        }

        //FIELD TYPES
        $query = 'SELECT dty_ID, dty_Name, dty_NameInOriginatingDB, dty_OriginatingDBID, dty_IDInOriginatingDB FROM '
            .$db_name.'.defDetailTypes WHERE  dty_OriginatingDBID>0 AND (NOT (dty_IDInOriginatingDB>0)) ';
        
        $res = $mysqli->query($query);
        if (!$res) {  print $query.'  '.$mysqli->error;  return; }
        
        while (($row = $res->fetch_row())) {
               $is_found = true;
               array_push($det_types, $row);
        }
        
        //TERMS
        $query = 'SELECT trm_ID, trm_Label, trm_NameInOriginatingDB, trm_OriginatingDBID, trm_IDInOriginatingDB FROM '
            .$db_name.'.defTerms WHERE  trm_OriginatingDBID>0 AND (NOT (trm_IDInOriginatingDB>0)) ';
            
        $res = $mysqli->query($query);
        if (!$res) {  print $query.'  '.$mysqli->error;  return; }
        
        while (($row = $res->fetch_row())) {
               $is_found = true;
               array_push($terms, $row);
        }
        
        if($is_found){
            print '<h4 style="margin:0;padding-top:20px">'.substr($db_name,4).'</h4><table style="font-size:12px">';    
            if(count($rec_types)>0){
                print '<tr><td colspan=5><i>Record types</i></td></tr>';
                foreach($rec_types as $row){
                    print '<tr><td>'.implode('</td><td>',$row).'</td></tr>';
                }
            }
            if(count($det_types)>0){
                print '<tr><td colspan=5>&nbsp;</td></tr>';
                print '<tr><td colspan=5><i>Detail types</i></td></tr>';
                foreach($det_types as $row){
                    print '<tr><td>'.implode('</td><td>',$row).'</td></tr>';
                }
            }
            if(count($terms)>0){
                print '<tr><td colspan=5><i>Terms</i></td></tr>';
                foreach($terms as $row){
                    print '<tr><td>'.implode('</td><td>',$row).'</td></tr>';
                }
            }
            print '</table>';
        } 
        
    }//while  databases
    print '[end report]</div>';
?>