<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script language="JavaScript">
$(document).ready( function() {
	lora();
});
 
function lora() {
	setTimeout( function() { 
	updates(); 
	lora();
	}, 1000);
}

function paddingLeft(str,lenght){
	if(str.length >= lenght)
		return str;
	else
		return paddingLeft("0" + str,lenght);
}

function updates() {
	$.getJSON("lora_load_gui_encode.php", function(data) {
		var datanum = data["result"].length;
		for (var unit = 0 ; unit < datanum ; unit ++)
		{
			var lora_ip = data["result"][unit]["1"];
			var time = data["result"][unit]["time"];
			var led_data = data["result"][unit]["led_data"];

			var led_color = 0,led_str_color = 0;

			if((led_data == 4) || (led_data == 5) || (led_data == 6) || (led_data == 7))
			{
				led_color = 'red';
				led_str_color = 'white';
			}
			else if ((led_data == 2) || (led_data == 3))
			{
				led_color = 'yellow';
				led_str_color = 'black';
			}
			else if (led_data == 1)
			{
				led_color = '#00FF00';
				led_str_color = 'black';
			}
			else
			{
				led_color = 'NULL';
				led_str_color = 'black';
			}

			document.getElementById("But" + unit).value = '(設備名稱) ' + lora_ip + ' (時間) ' + time;
　			document.getElementById("But" + unit).style = 'background-color:' + led_color + ';color:' + led_str_color;

			var greendata = 0,yellowdata = 0,reddata = 0;
			var led_bir = paddingLeft(parseInt(led_data).toString(2),3);

			if (led_bir[2] == 1)
				greendata = '#00FF00';
			else
				greendata = 'NULL';

			if (led_bir[1] == 1)
				yellowdata = 'yellow';
			else
				yellowdata = 'NULL';

			if (led_bir[0] == 1)
				reddata = 'red';
			else
				reddata = 'NULL';
			
　			document.getElementById("But_circular1" + unit).style = 'background-color:' + greendata;
　			document.getElementById("But_circular2" + unit).style = 'background-color:' + yellowdata;
　			document.getElementById("But_circular3" + unit).style = 'background-color:' + reddata;
		}
	});  
}

</script>

<style>
.button {
	border: none;
	padding: 12px 30px;
	text-align: center;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
	width: 400px;
}

.button_circular {
	border-radius: 50%;
	border-color:black;
	border-width:1px;
	border-style:solid;
	padding:12px;
</style>

<iframe name='nowtime' src='nowtime.php' frameBorder="0" width='100%' height='13%'></iframe>
<form action="lora_result_v2.php" method="post">

<?php
require_once 'lora_load_gui_data.php';

$res = $db->prepare('SELECT COUNT(*) FROM lora_unit');
$res->execute();
$num_rows = $res->fetchColumn();

echo "<center>";

for($amount = 0;$amount < $num_rows;$amount ++)
{
	echo "<input id='But".$amount."'";
	echo "type='submit' class='button' style='' name='lora_ip'";
	echo "onclick=\"value=".$unit[$amount]."\">";

	echo " <button id='But_circular1".$amount."'disabled='disabled'";
	echo "class='button_circular'></button> ";
	echo "<button id='But_circular2".$amount."'disabled='disabled'";
	echo "class='button_circular'></button> ";
	echo "<button id='But_circular3".$amount."'disabled='disabled'";
	echo "class='button_circular'></button>";
	echo "<br>";

}

echo "</center>";

?>