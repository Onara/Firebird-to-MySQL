<?php
//CLI script usage
//php script_name "source_dbname" "source_tablename" "target_dbname" "target_tablename"

/*
 * format of first parameter of the getSQl function, list all the mapping inside this array
 * $test=array(
               array('target_field_name','source_field_name'),
               array('RX_NUMBER','TX-Trans Number'), //example
               );
 * 
 */
 
  
 if(PHP_SAPI != 'cli'){
   echo "This script is designed to run from command line and transfer data from one table to another in two different databases or in the same</br>".PHP_EOL;
	 echo "USAGE:  php script_name 'source_dbname' 'source_tablename' 'target_dbname' 'target_tablename'";
   die;
 }
 else {
     if($argc < 5){
	  echo "Insufficient number of arguments, needs 4".PHP_EOL;
	  die("Stopped:.... Usage: php ".$_SERVER['SCRIPT_NAME']. " source_dbname source_tablename target_dbname target_tablename");
	 }
	 
	 $db_source=trim(mysql_real_escape_string($argv[1]));
	 $table_source=trim(mysql_real_escape_string($argv[2]));
	 $db_target=trim(mysql_real_escape_string($argv[3]));
	 $table_target=trim(mysql_real_escape_string($argv[4]));
 }
 

function checkTableCompatibility($database_source, $table_source,$database_target,$table_target)
{
	
	mysql_connect("localhost", "root", "");
	$result_t = mysql_query("SELECT * FROM $database_target.$table_target LIMIT 1");// select from target table
	$result_s = mysql_query("SELECT * FROM $database_source.$table_source LIMIT 1");// select from source table
	$fields_t = mysql_num_fields($result_t);
	$fields_s = mysql_num_fields($result_s);
	
	if($fields_t != $fields_s)	
	{
		//send an email to admin and return true if filed names and trypes are matching for target table
		// adding a field at the end is not going to stop this script for now
	  
	}
	
	for ($i=0; $i < $fields_t; $i++) {
	    $name_t  = mysql_field_name($result_t, $i);
		$name_s  = mysql_field_name($result_s, $i);
		$type_t=mysql_field_type($result_t, $i);
		$type_s=mysql_field_type($result_s, $i);
		
				
		if(strcmp($name_t,$name_s)!=0){
			
			//send an email to admin stating field name change and return false
			return false;
		}	   
		
		if(strcmp($type_t,$type_s)!=0){
			
			//send an email to admin stating field name change and return false
			return false;
		}	   
	}
	
	mysql_free_result($result_t);
	mysql_free_result($result_s);
	mysql_close();
	
	return true;
}

function getFieldList($database_source, $table_source,$database_target,$table_target)
{
			
	// an array to keep Fieldname mappings to be fed into getSQl function
	$returnArray=array();
	
	mysql_connect("localhost", "root", "");
	$result = mysql_query("SELECT * FROM $database_target.$table_target LIMIT 1");
	$fields = mysql_num_fields($result);
	$rows   = mysql_num_rows($result);
	
	for ($i=0; $i < $fields; $i++) {
	    $name  = mysql_field_name($result, $i);
	   $returnArray[$i][0]=mysql_real_escape_string($name);
	   $returnArray[$i][1]=mysql_real_escape_string($name);
	}
	mysql_free_result($result);
	mysql_close();
	
	return $returnArray;
}

//function to build and return corresponding sql statement to insert or update on duplicate key
//Accepts an array of field mappings
//returns sql statement
function getSQL($fieldMap, $source_d, $source_t, $target_d, $target_t, $sPrefix_t='s',$tPrefix_t='t')
{
	//cleaning up user inputs
	$source_db=trim(mysql_real_escape_string($source_d));
	$target_db=trim(mysql_real_escape_string($target_d));
	$source=trim(mysql_real_escape_string($source_t));
	$target=trim(mysql_real_escape_string($target_t));
	$sPrefix=trim(mysql_real_escape_string($sPrefix_t));
	$tPrefix=trim(mysql_real_escape_string($tPrefix_t));
		
	$fieldListFrom="";
	$fieldListTo="";
	$fieldUpdateList="";
	
	foreach($fieldMap as $map){
		
		$fieldListFrom=$fieldListFrom."`".$sPrefix."`.`".$map[1]."`,";
		$fieldListTo=$fieldListTo."`".$map[0]."`,";
		$fieldUpdateList=$fieldUpdateList."`".$map[0]."`=`".$sPrefix."`.`".$map[1]."`,";
	}
	
	// remove last ,
	$fieldListFrom=substr($fieldListFrom,0,-1);
	$fieldListTo=substr($fieldListTo,0,-1);
	$fieldUpdateList=substr($fieldUpdateList,0,-1);
	
	
	$sql= " INSERT INTO
	        `$target_db`.`$target` 
	        ( $fieldListTo)
	        SELECT 
	        $fieldListFrom
	        FROM
	        `$source_db`.`$source` as $sPrefix 
	        ON DUPLICATE KEY UPDATE 
	        $fieldUpdateList";
	
	return $sql;
}
  
  
 
  //$fieldMap=getFieldList('test','test1    ','test','test2   ');
 // $sql=getSQL($fieldMap,'test','test1','test','test2');
  
  //echo $sql;

  if(checkTableCompatibility($db_source,$table_source,$db_target,$table_target))
  {echo 'Yes';
     //var_dump(getFieldList('test','test1    ','test','test2   '));
   }
  else {
      echo 'no';
  }
