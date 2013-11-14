<?php

$test=array(
array('PATIENT_CODE','TX-Patient Code'),
array('RX_NUMBER','TX-Trans Number'),
);

function getFieldList($databaseName_source, $tableName_source,$databseName_target,$tableName_target)
{
$returnArray=array();

$link = mysql_connect("localhost", "root", "", "test");

/* check connection */
if (mysql_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$result_source = mysql_query($link,"SELECT * FROM $tableName_source");
while($fields_source = mysql_fetch_field($result_source)){

echo $fields_source->name;
}

mysql_free_result($result_source);
}

function getSQL($fieldMap, $source, $target, $sPrefix='s',$tPrefix='t')
{
	
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
	        $target 
	        ( $fieldListTo)
	        SELECT 
	        $fieldListFrom
	        FROM
	        $source as $sPrefix 
	        ON DUPLICATE KEY UPDATE 
	        $fieldUpdateList";
	
	return $sql;
}
  
  
  //echo getSQL($test,'source','target');    
  getFieldList('pk','test','jmuthukudage','test2');
