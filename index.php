<!DOCTYPE html>
<html>
<head>
	<title>Уязвимая библиотека</title>
	<meta name="author" content="Maxim">
	<meta charset="UTF-8">
	<link rel="stylesheet" type"text/css" href="style/style.css">
	<script type="text/javascript">
		setTimeout("hide()",3000);
		function hide()
		{
			document.getElementById("banner").style.opacity="0";
		}
	</script> 
</head>

<body>
<!--HEADER BEGINS-->
<div id="header">

<?php

##############################################AUTHORIZATION####################################



include('config.php');

session_start(); //Запускаем сессии

class AuthClass {
private $_login = "2"; //Устанавливаем логин
private $_password = "1"; //Устанавливаем пароль

/**
 * Проверяет, авторизован пользователь или нет
 * Возвращает true если авторизован, иначе false
 * @return boolean 
 */
public function isAuth() {
	if (isset($_SESSION["is_auth"])) { //Если сессия существует
		return $_SESSION["is_auth"]; //Возвращаем значение переменной сессии is_auth (хранит true если авторизован, false если не авторизован)
	}
		else return false; //Пользователь не авторизован, т.к. переменная is_auth не создана
}

	/**
	* Авторизация пользователя
	* @param string $login
	* @param string $passwors 
	*/
public function auth($login, $passwors) {
	if ($login == $this->_login && $passwors == $this->_password) { //Если логин и пароль введены правильно
		$_SESSION["is_auth"] = true; //Делаем пользователя авторизованным
		$_SESSION["login"] = $login; //Записываем в сессию логин пользователя
		return true;
	}
	else { //Логин и пароль не подошел
		$_SESSION["is_auth"] = false;
		return false; 
	}
}

/**
* Метод возвращает логин авторизованного пользователя 
*/
public function getLogin() {
	if ($this->isAuth()) { //Если пользователь авторизован
		return $_SESSION["login"]; //Возвращаем логин, который записан в сессию
	}
}

public function out() {
	$_SESSION = array(); //Очищаем сессию
	session_destroy(); //Уничтожаем
	}
}

$auth = new AuthClass();

if (isset($_POST["login"]) && isset($_POST["password"])) { //Если логин и пароль были отправлены
	if (!$auth->auth($_POST["login"], $_POST["password"])) { //Если логин и пароль введен не правильно
		echo "<h2 style=\"color:red;\">Логин и пароль введен не правильно!</h2>";
	}
}

if (isset($_GET["is_exit"])) { //Если нажата кнопка выхода
	if ($_GET["is_exit"] == 1) {
		$auth->out(); //Выходим
		header("Location: ?is_exit=0"); //Редирект после выхода
	}
}




if ($auth->isAuth()) { // Если пользователь авторизован, приветствуем: 
	echo '
		<nav>
			<ul>
				<li><a href="index.php">Информация о сервисе</a></li>
				<li><a href="index.php?givebook">Подарить книгу</a></li>
				<li><a href="index.php?getbook">Взять книгу</a></li>
				<li><a href="index.php?readers">Наши читатели</a></li>
				<li><a href="index.php?authors">Авторы наших книг</a></li>
				<li><a href="index.php?finedesk">Доска позора</a></li>
				<li><a href="index.php?shelves">Полки</a></li>
				<li><a href="index.php?is_exit=1">Выйти</a></li> 
			</ul>
		</nav>';
} 


else { //Если не авторизован, показываем форму ввода логина и пароля
echo '
<div id="bottom">
	<form class="loginform" action="index.php" method="POST">
		Логин: <input type="text" name="login" value="" /><br/>
		Пароль: <input type="password" name="password" value="" /><br/>
		<input type="submit" value="Войти" />
	</form>
</div>
';
}
####################################################################################

$path = "style/img/book.png";

if (isset($_REQUEST['getbook']))
{
	$result = mysql_query("SELECT * FROM `books` ORDER BY ID DESC;");
 		while ($row = mysql_fetch_assoc($result)) 
	 	{	
	 		echo '<div class="items">';
	 		echo '<img src="'.$row['IMAGE'].'" class="item" >';
			echo '<div class="description_aut"><span>Автор:</span><br>'.$row['AUTHOR'].'</div>';
			echo '<div class="description_nam"><span>Название:</span><br>'.$row['NAME'].'</div>';
			echo '<div class="otziv"><span>Последняя оценка:</span><br>'.$row['OTZIV'].'</div>';
			echo '<div class="description_des"><span>Описание:</span><br>'.$row['DESCRIPTION'].'</div>';
			echo '<form action="index.php" method="POST">
			<input type="hidden" name="bookid" value="'.$row['id'].'">
			<input type="submit" class="order" value="Взять">
			</form>';
			echo "</div>";
		}
}

if (!empty($_POST['bookid'])) 
{
	$bookid = mysql_real_escape_string(htmlspecialchars($_POST['bookid']));
	$result = mysql_query("SELECT `NAME` FROM `books` WHERE `id`=".$bookid.";");
	while ($row = mysql_fetch_array($result)) {
		echo '<div class="banner"><p>Вы взяли книгу "'.$row[0].'. Не забудьте вернуть её нам.</p></div>';
	}
	mysql_query("DELETE FROM `books` WHERE `id`=".$bookid.";");
}


##################################################################################################################


if (isset($_REQUEST['readers']))
{
	$result = mysql_query("SELECT * FROM `users` ORDER BY id DESC;");

		echo '<div class="choices">
		<form action="index.php" method="POST">
		<input type="hidden" name="readerid_add">
		<div class="right"><input type="submit" class="choice" value="Добавить"></div>
		</form></div>';

	while ($row = mysql_fetch_assoc($result)) 
	{	
 		echo '<div class="items">';
 		echo '<div class="choices">';

 		echo '<form action="index.php" method="POST">
		<input type="hidden" name="readerid_update" value="'.$row['id'].'">
 		<div class="left"><input type="submit" class="choice" value="Изменить"></div>
 		</form>';

 		echo '<form action="index.php" method="POST">
		<input type="hidden" name="readerid_del" value="'.$row['id'].'">
 		<div class="right"><input type="submit" class="choice" value="Удалить"></div>
 		</form>';

 		echo '</div>';
 		echo '<img src="'.$row['status'].'" class="item reader">';
		echo '<div class="description_aut"><span>Контактный номер:</span><br>'.$row['MOBILE'].'</div>';
		echo '<div class="description_nam"><span>ФИО:</span><br>'.$row['FIO'].'</div>';

		echo "</div>";
	}
}

##################################################################################################################


if (isset($_REQUEST['authors']))
{
	echo '<div class="choices">
		<form action="index.php" method="POST">
		<input type="hidden" name="authorid_add">
		<div class="right"><input type="submit" class="choice" value="Добавить"></div>
		</form></div>';

	$result = mysql_query("SELECT * FROM `autors` ORDER BY ID DESC;");
	while ($row = mysql_fetch_assoc($result)) 
 	{	

 		echo '<div class="items">
 		<style>
 		.description_aut{
 			font-size:20px;
 		}

 		.items{
 			height:120px;
 		}


 		.description_aut span{
 			font-size:25px;
 		}
 		.description_des{
 			font-size:15px;
 		}
 		.description_des span{
 			font-size:25px;
 		}
 		</style>
 		';
 		echo '<div class="choices">';
 		echo '<form action="index.php" method="POST">
		<input type="hidden" name="authorid_update" value="'.$row['id'].'">
 		<div class="left"><input type="submit" class="choice" value="Изменить"></div>
 		</form>';

 		echo '<form action="index.php" method="POST">
		<input type="hidden" name="authorid_del" value="'.$row['id'].'">
 		<div class="right"><input type="submit" class="choice" value="Удалить"></div>
 		</form>';

 		echo '</div>';
		echo '<div class="description_aut"><span>Автор:</span><br>'.$row['FIO'].'</div>';
		echo '<div class="description_des"><span>Биография:</span><br>'.$row['BIO'].'</div>';
		echo "</div>";
	}
}

##################################################################################################################

if (isset($_REQUEST['finedesk']))
{
	echo '<div class="choices">
 		<form action="index.php" method="POST">
		<input type="hidden" name="fineid_add">
 		<div class="right"><input type="submit" class="choice" value="Добавить"></div>
 		</form></div>';

	$result = mysql_query("SELECT * FROM `fines` ORDER BY id DESC;");
	while ($row = mysql_fetch_assoc($result)) 
 	{	

 		$id = $row['id'];
 		$fio="";
 		$sum = $row['SUM'];
 		$normal_fio = mysql_query("SELECT `FIO` FROM `users` WHERE id= ".$row['id_user'].";");
		while ($roww = mysql_fetch_assoc($normal_fio)){
			$fio = $roww['FIO'];
		}
 		$book="";
 		$normal_book = mysql_query("SELECT `NAME` FROM `books` WHERE id= ".$row['id_book'].";");
 		while ($roww = mysql_fetch_assoc($normal_book)){
 			$book = $roww['NAME'];
 		}


 		$bookimg = mysql_query("SELECT `IMAGE` FROM `books` WHERE id=".$row['id_book'].";");
		while ($row = mysql_fetch_array($bookimg)) 
 		{	
 			$image = $row[0];
 		}


 		echo '<div class="items">
 		<style>
		.item{
			margin-left: 3px;
			float: left;
			width: 130px;
			height: 150px;
		}

 		</style>
 		';
 		echo '<div class="choices">
 		<form action="index.php" method="POST">
		<input type="hidden" name="fineid_update" value="'.$id.'">
 		<div class="left"><input type="submit" class="choice" value="Изменить"></div>
 		</form>';

 		echo '<form action="index.php" method="POST">
		<input type="hidden" name="fineid_del" value="'.$id.'">
 		<div class="right"><input type="submit" class="choice" value="Удалить"></div>
 		</form>
 		</div>';

		echo '<img src="'.$image.'" class="item" >
		<div class="description_aut"><span>Пользователь:</span><br>'.$fio.'</div>
		<div class="description_des"><span>Книга:</span><br>'.$book.'</div>
		<div class="description_nam"><span>Сумма:</span><br>'.$sum.'</div>
		</div>';
		}
}


##################################################################################################################


$count = 1;
if (isset($_REQUEST['shelves']))
{

	echo '<div class="choices">
 		<form action="index.php" method="POST">
		<input type="hidden" name="shelves_add">
 		<div class="right"><input type="submit" class="choice" value="Добавить полку"></div>
 		</form>

 		<form action="index.php" method="POST">
		<input type="hidden" name="shelves_del">
 		<div class="right"><input type="submit" class="choice" value="Удалить полку"></div>
 		</form>

 		</div>';


	$result = mysql_query("SELECT DISTINCT shelve FROM `shelves` ORDER BY id DESC;");
	while ($row = mysql_fetch_assoc($result)) 
 	{	
 		$bookname = "";
 		$bookimg = "";
 		$shelve = $row['shelve'];
	 	echo '<div class="shelves">';
 		
		$count = mysql_query("SELECT COUNT(*) FROM `shelves` WHERE shelve=".$shelve.";");
		while ($cou = mysql_fetch_assoc($count)){
			$count = $cou['COUNT(*)'];
		}

 		$booksonshelve = mysql_query("SELECT * FROM `shelves` WHERE shelve= ".$row['shelve'].";");
		while ($roww = mysql_fetch_assoc($booksonshelve))
			{
			$bookss = mysql_query("SELECT * FROM `books` WHERE id= ".$roww['id_book'].";");
			while ($rowww = mysql_fetch_assoc($bookss)){
				$bookid = $rowww['id'];
				$bookimg = $rowww['IMAGE'];
				echo '<form action="index.php" method="POST">
				<input type="hidden" name="on_shelve_del" value="'.$bookid.'">
				<input type="image" name="picture" src="'.$bookimg.'" class="shelveitem" />
				</form>';
				}
 			}
		if ($count < 6 ){
			echo '<form action="index.php" method="POST">
			<input type="hidden" name="on_shelve_add" value="'.$shelve.'">
			<input type="image" name="picture" src="style/img/add.png" class="add right" />
			</form>';
		}
	echo '</div>';
	}
}




##################################################################################################################

if ((!isset($_REQUEST['getbook']))&&(!isset($_REQUEST['shelves']))&&(!isset($_REQUEST['readers']))&&(!isset($_REQUEST['authors']))&&(!isset($_REQUEST['finedesk']))&&(!isset($_REQUEST['givebook']))){
echo '<div id="info">
	<div class="text">
	<h1>Добро пожаловать в городскую биб-<br>лиотеку.</h1>
		<ol>
		<li>Чтобы добавить книгу в базу данных перейдите в меню по ссылке "Подарить книгу".</li>
		<li>После того, как Вы подарите книгу нашей библиотеке, она станет доступна по ссылке "Взять книгу".</li>
		</ol>
		<h2>Приятного чтения!</h2>
	</div>
</div> <!--info-->';
}

#if ((isset($_REQUEST['givebook']))&&($this->isAuth())){
if (isset($_REQUEST['givebook'])){
echo '
<div id="givebook">
	<form action="index.php" method="POST" enctype="multipart/form-data">
		<p>Автор*: </p><input type="text" name="AUTHOR" value=""><br>
		<p>Название*: </p><input type="text" name="NAME" value=""><br>
		<p>Описание*: </p><input type="text" name="DESC" value=""><br>
		<p>Оценка: </p><input type="text" name="OTZIV" value=""><br>
		<p>Обложка*: </p><input type="file" name="filename"><br> 
		<input type="submit" class="btn" value="Отдать книгу">
	</form>
</div> <!--info-->';
}


$fname="";

if($_FILES["filename"]["size"] > 800*3*800)
{
	echo ("Размер файла превышает пару мегабайт");
	exit;
}
// Проверяем загружен ли файл
if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
{
	// Если файл загружен успешно, перемещаем его
	// из временной директории в конечную
	move_uploaded_file($_FILES["filename"]["tmp_name"], "images/".$_FILES["filename"]["name"]);
	$fname = $_FILES["filename"]["name"];
} else {
	$fname = "";
}


$NAME = "";
$AUTHOR = "";
$DESC = "";
$OTZIV = "";
$IMAGE=$fname;


// Проверяем, установлены ли переменные
if ((!empty($_POST['AUTHOR']))&&(!empty($_POST['NAME']))&&(!empty($_POST['DESC']))&&(!empty($_POST['OTZIV']))) 
{
	//преобразование строк
	$NAME = mysql_real_escape_string(htmlspecialchars($_POST['NAME']));
	$AUTHOR = mysql_real_escape_string(htmlspecialchars($_POST['AUTHOR']));
	$DESC = mysql_real_escape_string(htmlspecialchars($_POST['DESC']));
	$OTZIV = mysql_real_escape_string(htmlspecialchars($_POST['OTZIV']));
	$IMAGE=$fname;

	$result = mysql_query("INSERT INTO `books`(`NAME`, `AUTHOR`, `DESCRIPTION`, `IMAGE`, `OTZIV`) VALUES ('".$NAME."','".$AUTHOR."','".$DESC."','images/".$IMAGE."', '".$OTZIV."');");
	 		
}


#############################################READERS##############################################################



if (!empty($_POST['readerid_del'])) 
{
	$readerid = mysql_real_escape_string(htmlspecialchars($_POST['readerid_del']));
	$result = mysql_query("SELECT `FIO` FROM `users` WHERE `id`= ".$readerid.";");
	while ($row = mysql_fetch_array($result)) {
		echo '<div class="banner"><p>Вы изгнали читателя '.$row[0].'. Надеемся на то была причина.</p></div>';
	}

	mysql_query("DELETE FROM `users` WHERE `id`=".$readerid.";");

}


if (!empty($_POST['readerid_update'])) 
{
	$readerid = mysql_real_escape_string(htmlspecialchars($_POST['readerid_update']));
	$result = mysql_query("SELECT * FROM `users` WHERE `id`= ".$readerid.";");
	while ($row = mysql_fetch_assoc($result)) {	
		$fio=$row['FIO'];
		$mob=$row['MOBILE'];
		echo '<div class="banner">
				<style>
				.banner{
					height:500px;
				}
				</style>
				<form action="index.php" method="POST">
					<input type="hidden" name="readerfio_update" value="'.$readerid.'">
					<p>Новое ФИО вместо:<br> '.$fio.'</p>
					<input type="text" name="newfio">
					<input type="submit" class="choice" value="Обновить">
				</form>';
				echo '
				<form action="index.php" method="POST">
					<input type="hidden" name="readermobile_update" value="'.$readerid.'">
					<p>Новый номер вместо:<br> '.$mob.' </p>
					<input type="text" name="newmob">
					<input type="submit" class="choice" value="Обновить">
				</form>';

				echo '
				<form action="index.php" method="POST">
					<input type="hidden" name="readerstatus_update" value="'.$readerid.'">
					<p>Статус:<br></p>
					<select name="newstat">
						<option>Обычный</option>
						<option>VIP</option>
					</select>
					<input type="submit" class="choice" value="Обновить">
				</form>
			</div>';
	}

}


if (isset($_POST['readerid_add'])) 
{
	$fio = "";
	$mob = "";
	echo '<div class="banner">
	<style>
	.banner{
		height: 500px;
	}
	</style>
	<form action="index.php" method="POST">
		<input type="hidden" name="reader_add">
		<p>ФИО:<br></p>
		<input type="text" name="fio">
		<p>Контактный номер:<br></p>
		<input type="text" name="mob">
		<p>Статус:<br></p>
		<select name="status">
			<option>Обычный</option>
			<option>VIP</option>
		</select>
		<input type="submit" class="choice" value="Добавить">
	</form>';
	echo '</div>';
}


if (isset($_POST['reader_add'])){
	if ($_POST['status']=="VIP"){
		$path="style/img/vip.png";
	}
	else{
		$path="style/img/book.png";
	}
	$fio=$_POST['fio'];
	$mob=$_POST['mob'];
	mysql_query("INSERT INTO `users`(`FIO`, `MOBILE`, `status`) VALUES ('".$fio."','".$mob."','".$path."');");
}


if (!empty($_POST['readerfio_update'])) 
{
	$newfio = mysql_real_escape_string(htmlspecialchars($_POST['newfio']));
	$readerid = mysql_real_escape_string(htmlspecialchars($_POST['readerfio_update']));
	mysql_query("UPDATE `users` SET `FIO`='".$newfio."' WHERE `id`=".$readerid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}

if (!empty($_POST['readermobile_update'])) 
{
	$newmob = mysql_real_escape_string(htmlspecialchars($_POST['newmob']));
	$readerid = mysql_real_escape_string(htmlspecialchars($_POST['readermobile_update']));
	mysql_query("UPDATE `users` SET `MOBILE`='".$newmob."' WHERE `id`=".$readerid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}


if (!empty($_POST['readerstatus_update'])) 
{
	if ($_POST['newstat']=="VIP"){
		$path="style/img/vip.png";
	}
	else{
		$path="style/img/book.png";
	}
	$readerid = mysql_real_escape_string(htmlspecialchars($_POST['readerstatus_update']));
	mysql_query("UPDATE `users` SET `status`='".$path."' WHERE `id`=".$readerid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}


################################################AUTHORS#######################################################



if (!empty($_POST['authorid_del'])) 
{
	$authorid = mysql_real_escape_string(htmlspecialchars($_POST['authorid_del']));
	$result = mysql_query("SELECT `FIO` FROM `autors` WHERE `id`= ".$authorid.";");
	while ($row = mysql_fetch_array($result)) {
		echo '<div class="banner"><p>Вы изгнали автора '.$row[0].'. Надеемся на то была причина.</p></div>';
	}

	mysql_query("DELETE FROM `autors` WHERE `id`=".$authorid.";");

}


if (!empty($_POST['authorid_update'])) 
{
	$authorid = mysql_real_escape_string(htmlspecialchars($_POST['authorid_update']));
	$result = mysql_query("SELECT * FROM `autors` WHERE `id`= ".$authorid.";");
	 while ($row = mysql_fetch_assoc($result)) {	
		$fio=$row['FIO'];
		$bio=$row['BIO'];
		echo '<div class="banner">
		<style>
		.banner{
			height:500px;
		}
		</style>
		<form action="index.php" method="POST">
			<input type="hidden" name="authorfio_update" value="'.$authorid.'">
			<p>Новое ФИО вместо:<br> '.$fio.'</p>
			<input type="text" name="newfio">
			<input type="submit" class="choice" value="Обновить">
		</form>';
		echo '
		<form action="index.php" method="POST">
			<input type="hidden" name="authorbio_update" value="'.$authorid.'">
			<p>Новая биография:<br></p>
			<input type="text" name="newbio">
			<input type="submit" class="choice" value="Обновить">
		</form>';
		echo '</div>';
	}

}


if (isset($_POST['authorid_add'])) 
{
	$fio = "";
	$bio = "";
	echo '<div class="banner">
	<style>
	.banner{
		height:500px;
	}
	</style>
	<form action="index.php" method="POST">
		<input type="hidden" name="author_add">
		<p>ФИО:<br></p>
		<input type="text" name="fio">
		<p>Биография:<br></p>
		<input type="text" name="bio"><br>
		<input type="submit" class="choice" value="Добавить">
	</form>';
	echo '</div>';
}

if (isset($_POST['author_add'])){
	$fio=$_POST['fio'];
	$bio=$_POST['bio'];
	mysql_query("INSERT INTO `autors`(`FIO`, `BIO`) VALUES ('".$fio."','".$bio."');");
}



if (!empty($_POST['authorfio_update'])) 
{
	$newfio = mysql_real_escape_string(htmlspecialchars($_POST['newfio']));
	$authorid = mysql_real_escape_string(htmlspecialchars($_POST['authorfio_update']));
	mysql_query("UPDATE `autors` SET `FIO`='".$newfio."' WHERE `id`=".$authorid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}

if (!empty($_POST['authorbio_update'])) 
{
	$newbio = mysql_real_escape_string(htmlspecialchars($_POST['newbio']));
	$authorid = mysql_real_escape_string(htmlspecialchars($_POST['authorbio_update']));
	mysql_query("UPDATE `autors` SET `BIO`='".$newbio."' WHERE `id`=".$authorid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}






################################################FINES##############################################################




if (!empty($_POST['fineid_del'])) 
{
	$fineid = mysql_real_escape_string(htmlspecialchars($_POST['fineid_del']));
	mysql_query("DELETE FROM `fines` WHERE `id`=".$fineid.";");

}

$fineid="";	
if (!empty($_POST['fineid_update'])) 
{
	$fineid = mysql_real_escape_string(htmlspecialchars($_POST['fineid_update']));
	$result = mysql_query("SELECT * FROM `fines` WHERE `id`= ".$fineid.";");
	while ($row = mysql_fetch_assoc($result)) {	
		$userid=$row['id_user'];
		$bookid=$row['id_book'];



		echo '<div class="banner">
		<style>
		.banner{
			height:400px;
		}
		</style>
		<form action="index.php" method="POST">
			<input type="hidden" name="finebook_update" value="'.$fineid.'">
			<p>Новая книга</p>
			<select name="newbook">';
				$books = mysql_query("SELECT * FROM `books` ORDER BY id DESC;");
				 while ($roww = mysql_fetch_assoc($books)) {	
				echo '<option value="'.$roww['id'].'">'.$roww['NAME'].'</option>';
				}
				echo '
			</select>
			<input type="submit" class="choice" value="Обновить">
		</form>';

		echo '
		<form action="index.php" method="POST">
			<input type="hidden" name="fineuser_update" value="'.$fineid.'">
			<select name="newuser">';
				$books = mysql_query("SELECT * FROM `users` ORDER BY id DESC;");
				 while ($roww = mysql_fetch_assoc($books)) {	
				echo '<option value="'.$roww['id'].'">'.$roww['FIO'].'</option>';
				}
				echo '
			</select>
			<input type="submit" class="choice" value="Обновить">
		</form>';
		echo '</div>';
	}
}



if (isset($_POST['fineid_add'])) 
{
	$name = "";
	$book = "";
	echo '<div class="banner">
	<style>
	.banner{
		height:500px;
	}
	</style>
	<form action="index.php" method="POST">
		<input type="hidden" name="fine_add">
		<p>Кто должен:<br></p>
		<select name="fio">';
			$books = mysql_query("SELECT * FROM `users` ORDER BY id DESC;");
			 while ($roww = mysql_fetch_assoc($books)) {	
			echo '<option value="'.$roww['id'].'">'.$roww['FIO'].'</option>';
			}
			echo '
		</select>
		<p>Что должен:<br></p>
		<select name="book">';
			$books = mysql_query("SELECT * FROM `books` ORDER BY id DESC;");
			 while ($roww = mysql_fetch_assoc($books)) {	
			echo '<option value="'.$roww['id'].'">'.$roww['NAME'].'</option>';
			}
			echo '
		</select>
		<p>Сколько должен:<br></p>
		<input type="text" name="summ"><br>
		<input type="submit" class="choice" value="Добавить">
	</form>';
	echo '</div>';
}

if (isset($_POST['fine_add'])){
	$fio=$_POST['fio'];
	$book=$_POST['book'];
	$sum=$_POST['summ'];
	mysql_query("INSERT INTO `fines`(`id_user`, `id_book`, `SUM`) VALUES (".$fio.",".$book.",'".$sum."');");
}


if (!empty($_POST['finebook_update'])) 
{
	$fineid = mysql_real_escape_string(htmlspecialchars($_POST['finebook_update']));
	mysql_query("UPDATE `fines` SET `id_book`='".$_POST['newbook']."' WHERE `id`=".$fineid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}

if (!empty($_POST['fineuser_update'])) 
{
	$fineid = mysql_real_escape_string(htmlspecialchars($_POST['fineuser_update']));
	mysql_query("UPDATE `fines` SET `id_user`='".$_POST['newuser']."' WHERE `id`=".$fineid.";");
	echo '<div class="banner"><p>Успех</p></div>';
}


###################################################SHELVES###########################################################


if (isset($_POST['on_shelve_add'])) 
{
$shelve = $_POST['on_shelve_add'];
echo '<div class="banner">
	<style>
	.banner{
		height:200px;
	}
	</style>
	<form action="index.php" method="POST">
		<input type="hidden" name="shelve_add" value="'.$shelve.'">
		<p>Книга:<br></p>
		<select name="bookonshelve">';
			$books = mysql_query("SELECT * FROM `books` ORDER BY id DESC;");
			while ($roww = mysql_fetch_assoc($books)) {	
			 	$bookid = $roww['id'];
				echo '<option value="'.$roww['id'].'">'.$roww['NAME'].'</option>';
			}
			echo '
		</select><br>
		<input type="submit" class="choice" value="Добавить">
	</form>';
	echo '</div>';
}

if (isset($_POST['shelve_add'])) 
{
	$bookid = $_POST['bookonshelve'];
	$shelve = $_POST['shelve_add'];
	mysql_query("INSERT INTO `shelves`(`id_book`, `shelve`) VALUES (".$bookid.",".$shelve.");");
}

if (isset($_POST['on_shelve_del'])) 
{
	mysql_query("DELETE FROM `shelves` WHERE `id_book`=".$_POST['on_shelve_del'].";");
}




if (isset($_POST['shelves_add'])) 
{
	$count = mysql_query("SELECT * FROM `shelves` ORDER BY `shelve` DESC LIMIT 1;");
	while ($cou = mysql_fetch_assoc($count)){
		$count = $cou['shelve'];
	} 			
	$count = $count+1;
	mysql_query("INSERT INTO `shelves`(`shelve`) VALUES (".$count.");");
}


if (isset($_POST['shelves_del'])) 
{
	echo '<div class="banner">
	<form action="index.php" method="POST">
			<input type="hidden" name="shelve_add" value="'.$shelve.'">
			<p>Полка:<br></p>
			<select name="shelve_fordel">';
				$shelveval = mysql_query("SELECT DISTINCT shelve FROM `shelves` ORDER BY id DESC;");
				 while ($roww = mysql_fetch_assoc($shelveval)) {	
				echo '<option value="'.$roww['shelve'].'">'.$roww['shelve'].'</option>';
				}
				echo '
			</select><br>
			<input type="submit" class="choice" value="Удалить">
		</form>
		</div>';
}

if (isset($_POST['shelve_fordel'])) {
	mysql_query("DELETE FROM `shelves` WHERE `shelve`=".$_POST['shelve_fordel'].";");
}

#############################################################################################################
?>
	
</div>
<!--HEADER ENDED-->
</body>
</html>
