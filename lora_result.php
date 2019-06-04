<?php   
	if (isset($_POST['lora_ip']))
		$number = $_POST['lora_ip'];
	else 
		$number = $_GET['number']; 

	require_once "conn.php";

  	$pagesize = 29; //單頁顯示筆數
	$maxsize = 100; //上限頁數

	$db -> exec("SET NAMES utf8"); 
	$sql = "select lora.* , lora_unit.unit from lora NATURAL JOIN lora_unit where lora_ip = ".$number." ORDER BY lora.id DESC Limit " . $pagesize * $maxsize;
  
	$res = $db->query($sql);
	$rownum = $res->rowCount();
	$color = 3; 

	$pagenum = (int)ceil($rownum / $pagesize);

	if (isset($_GET['page'])) 
		$page = $_GET['page'];
	else 
		$page = 1; 

	if (isset($_GET['number'])) 
		$numberget = $_GET['number'];
	else 
		$numberget = $number; 
	
	$prepage = $page - 1;
	$nextpage = $page + 1;

	$pageurl='';

	if ($page == 1)
		$pageurl.='<center><br>首頁 | 上一頁 | ';
	else
		$pageurl.="<center><br><a href=\"?page=1&number=$numberget\">首頁</a> | <a href=\"?page=$prepage&number=$numberget\">上一頁</a> | ";

	if ($page==$pagenum || $pagenum==0)
		$pageurl.='下一頁 | 最後一頁';
	else
		$pageurl.="<a href=\"?page=$nextpage&number=$numberget\">下一頁</a> | <a href=\"?page=$pagenum&number=$numberget\">最後一頁</a>";

	$sqlpage = "SELECT lora.* , lora_unit.unit FROM lora NATURAL JOIN lora_unit where lora_ip = ".$numberget." ORDER BY lora.id DESC Limit " . ($page-1)* $pagesize . ",$pagesize";
	$respage = $db->query($sqlpage);

	echo "</table></center>";
	echo $pageurl;
	echo "<center>當前第 $page 頁 | 共 $pagenum 頁</center>";
?>
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