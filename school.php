<?php 
	
	session_start();

	if(!isset($_GET['term']))
		die("Missing required parameter");

	if(!isset($_SESSION['user_id']))
		die("Must be logged in");

	require_once("pdo.php");

	header("Content-type: application/json; charset=utf-8");

	$term = $_GET['term'];
	$sql = "SELECT name FROM Institution WHERE name LIKE :prefix";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":prefix" => $term . "%"));

	$institutions = array();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		$institutions[] = $row["name"];

	echo(json_encode($institutions, JSON_PRETTY_PRINT)); 

?>