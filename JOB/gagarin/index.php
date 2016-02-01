<!DOCTYPE html>
<html>
<head>
	<title>Ведение клиентов</title>
	<meta name="author" content="Maxim">
	<meta charset="UTF-8">
	<link rel="stylesheet" type"text/css" href="style/style.css">


	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
	<script>
		$(function() {
			$(".meter > span").each(function() {
				$(this)
					.data("origWidth", $(this).width())
					.width(0)
					.animate({
						width: $(this).data("origWidth")
					}, 2000);
			});
		});

		$(function() {
			$(".meter_min > span").each(function() {
				$(this)
					.data("origWidth", $(this).width())
					.width(0)
					.animate({
						width: $(this).data("origWidth")
					}, 2000);
			});
		});
	</script>


</head>


<body>
<div id="header">
<div id="titleblock"><h1><center>Клиенты GagarinMedia</center></h1></div>
	<div id="wripper">
		<div id="content">
		

<?php
//Соединение с базой данных
mysql_connect("127.0.0.1", "admin", "1") or die('Ошибка соединения с MySQL!');
mysql_select_db("gagarin_clients") or die ('Ошибка соединения с базой данных MySQL!');
//настройка кодировки
// Выполняем запрос
mysql_query('SET NAMES utf8');




#Обновление статуса ведения
if((!empty($_POST['CLI_ID']))&&(isset($_POST['state']))){
	$cid=$_POST['CLI_ID'];
	$status=$_POST['state'];
	mysql_query("UPDATE `client_status` SET `STATUS`= $status WHERE `id`=$cid;");
}



$status_text = "Статус";
$cid = 1;
$status = 1;
$rand = 0;
$result = mysql_query("SELECT * FROM `client_status` ORDER BY ID DESC;");
	 		while ($row = mysql_fetch_assoc($result)) 
		 	{	


		 		$stat_desc = mysql_query("SELECT COMMENT FROM `id_com` WHERE id_stat=".$row['STATUS'].";");
		 			while ($stat_desc = mysql_fetch_assoc($stat_desc)) 
		 				{
		 					$status_text = $stat_desc['COMMENT'];
		 				}

		 	echo '
		 	<div class="cli">
			<div class="cli_name">'.$row['NAME'].'</div>
			<div class="cli_wait_for">'.$status_text.'. <br> '.$row['COMMENT'].'</div>
			<div class="cli_status">Изменить стадию 

			<form action="" method="POST"> 
				<select name="state">
					<option value="0">Контракт подписан</option>
					<option value="1">Контракт оплачен</option>
					<option value="2">Съёмка назначена на конкретное число</option>
					<option value="3">Снято</option>
					<option value="4">Смонтирован черновик</option>
					<option value="5">Правки от клиента приняты</option>
					<option value="6">Смонтирован чистовик</option>
					<option value="7">Клиент принял модуль</option>
					<option value="8">Назначена дата подключения</option>
					<option value="9">Подключено</option>
					<option value="10">Акт подписан</option>
				</select>
				<input type="hidden" name="CLI_ID" value="'.$row['id'].'">
				<input type=submit value="OK">
			</form>

			';

			$int_bar = $row['STATUS'] * 10;

			echo '

			</div>
			<div class="meter animate">
			<span style="width: '.$int_bar.'%"><span>
			</div>

			<div class="dop">
				<div class="container_left">
					<div class="dop_name">Cайт</div>
					<div class="meter_min animate green">
					<span style="width: 10%"><span>
					</div>
				</div>

				<div class="container_left">
					<div class="dop_name">Instagram</div>
					<div class="meter_min animate green">
					<span style="width: 20%"><span>
					</div>
				</div>
				
				<div class="container_left">
					<div class="dop_name">Cross</div>
					<div class="meter_min animate green">
					<span style="width: 30%"><span>
					</div>
				</div>

			</div>
		</div>
		<div class="stripped_line"></div>
		';
		}



#	mysql_query("UPDATE `client_status` SET `STATUS`= $status WHERE `id`=$POST['CLI_ID'];");

#UPDATE `client_status` SET `id`=[value-1],`NAME`=[value-2],`STATUS`=[value-3],`COMMENT`=[value-4] WHERE 1
#INSERT INTO `client_status`(`id`, `NAME`, `STATUS`, `COMMENT`) VALUES (1,'Тест', 1, 'Тест')

?>



		</div><!--CONTENT-->
	</div> <!--WRIPPER-->
</div> <!--HEADER-->


</body>
</html>