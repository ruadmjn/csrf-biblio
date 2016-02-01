<?php
	//Соединение с базой данных
	mysql_connect("127.0.0.1", "admin", "1") or die('Ошибка соединения с MySQL!');
	mysql_select_db("city_biblio") or die ('Ошибка соединения с базой данных MySQL!');
	//настройка кодировки
	// Выполняем запрос
	mysql_query('SET NAMES utf8');

?>