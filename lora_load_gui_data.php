<?php 
	require_once "conn.php"; 
	//date_default_timezone_set('Asia/Taipei');
	//$nowtime = date('Y-m-d H:i:s');

	$db -> exec("SET NAMES utf8"); 
	$sql = "SELECT lora_status.* , lora_unit.unit FROM lora_status NATURAL JOIN lora_unit ORDER BY lora_status.lora_ip ASC";
	$result = $db->query($sql); 

	$i = 0;
	foreach($result as $row)
	{
		$time[$i] = $row[0];
		$unit[$i] = substr($row[1], -2);;
		$unitcolor[$i] = $row[2];
		$row[1] = $row[3];

		//$buff[$i] = strtotime($nowtime) - strtotime($time[$i]);
		//$row[$i] = ToString(substr_replace($row[2],$row[4],0, strlen($row[2])))

		$data[$i] = $row;

		$i++;

	}	

?>
