<center><form name="unit" method="get">
<select name="lora_ip" onchange="javascript:submit()">

<?php 
	require_once "conn.php"; 

	$db -> exec("SET NAMES utf8"); 
	$sql = "SELECT lora_unit.* , lora_status.lora_ip FROM lora_unit NATURAL JOIN lora_status"; 
	$result = $db->query($sql);
	$rowcount = 0;

	echo "<option value=''>加工機</option>" ;
	echo "<option value=''>全部</option>" ;

    foreach($result as $row)
		echo "<option value='$row[1]'>$row[0]</option>";

?>

</select></form></center>

<style type="text/css"> 
table tr:nth-child(odd) td{
           background:#ccc;
}
table tr:nth-child(even) td{
            background:#fff;
}
</style> 

<?php   

	$db -> exec("SET NAMES utf8"); 

  	$pagesize = 29;
	$maxsize = 100;

	if (@$_GET['lora_ip'] != '')
	{
		$lora_ip = $_GET['lora_ip'];
		$sql = "select lora.* , lora_unit.unit from lora NATURAL JOIN lora_unit where lora_ip = ".$lora_ip." ORDER BY lora.id DESC Limit " . $pagesize * $maxsize;
	}
	else 
	{
		$lora_ip = NULL; 
		$sql = "SELECT lora.* , lora_unit.unit FROM lora NATURAL JOIN lora_unit Limit " . $pagesize * $maxsize;
	}
  
	$res = $db->query($sql);
	$rownum = $res->rowCount();
	$color = 3; 

	$pagenum = (int)ceil($rownum / $pagesize);

	if (isset($_GET['page'])) 
		$page = $_GET['page'];
	else 
		$page = 1; 

	if (isset($_GET['lora_ip'])) 
		$numberget = $_GET['lora_ip'];
	else 
		$numberget = $lora_ip; 
	
	$prepage = $page - 1;
	$nextpage = $page + 1;

	$pageurl='';

	if ($page == 1)
		$pageurl.='<center><br>首頁 | 上一頁 | ';
	else
		$pageurl.="<center><br><a href=\"?page=1&lora_ip=$numberget\">首頁</a> | <a href=\"?page=$prepage&lora_ip=$numberget\">上一頁</a> | ";

	if ($page == $pagenum || $pagenum==0)
		$pageurl.='下一頁 | 最後一頁';
	else
		$pageurl.="<a href=\"?page=$nextpage&lora_ip=$numberget\">下一頁</a> | <a href=\"?page=$pagenum&lora_ip=$numberget\">最後一頁</a>";

	if ($lora_ip == '')
		$sqlpage = "SELECT lora.* , lora_unit.unit FROM lora NATURAL JOIN lora_unit ORDER BY lora.id DESC Limit " . ($page-1)* $pagesize . ",$pagesize";
	else
		$sqlpage = "SELECT lora.* , lora_unit.unit FROM lora NATURAL JOIN lora_unit where lora_ip = ".$numberget." ORDER BY lora.id DESC Limit " . ($page-1)* $pagesize . ",$pagesize";

	$respage = $db->query($sqlpage);

  	echo "<center><table border='1' width='500'>";
	echo "<td align='center'>序號";
	echo "<td align='center'>時間";
	echo "<td align='center'>識別";
	echo "<td align='center'>數值";
	echo "<td align='center'>加工機";
	echo "<td align='center'>狀態";

	foreach($respage as $row)
	{
		if (($row[$color] == 4) or ($row[$color] == 5) or ($row[$color] == 6) or ($row[$color] == 7))
			$status = 'ERROR';
		else if (($row[$color] == 2) or ($row[$color] == 3))
			$status = 'WARNING';
		else if ($row[$color] == 1)
			$status = 'OK';
		else
			$status = $row[$color];

		echo "<tr>";

		for($i=0 ; $i < (count($row)/2) ; $i++)
			echo "<td align='center'>".$row[$i];

		echo "<td align='center'>".$status;
		echo "</td></tr>";
	}
	echo "</table></center>";
	echo $pageurl;
	echo "<center>當前第 $page 頁 | 共 $pagenum 頁</center>";
?>