<?php 

	session_start();

	require_once("pdo.php");
	require_once("utilities.php");

	$fn = $ln = $em = $hl = $sm = "";

	$pId = $_GET['profile_id'];

	$sql = "SELECT * FROM Profile WHERE profile_id = :pid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":pid" => $pId));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if($row)
	{
		$fn = htmlentities($row["first_name"]);
		$ln = htmlentities($row["last_name"]);
		$em = htmlentities($row["email"]);
		$hl = htmlentities($row["headline"]);
		$sm = htmlentities($row["summary"]);
	}
	else
	{
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}

	function displayPos($pdo, $pId)
	{
		$positions = loadPos($pdo, $pId);
		$len = count($positions);

		if($len > 0)
		{
			echo("<p>Position</p>\n<ul>\n");

			for($i = 0; $i < $len; $i++)
				echo("<li>" . $positions[$i]["year"] . ": " . $positions[$i]["description"] . "</li>\n");
			
			echo("</ul>\n");
		}
	}

	function displayEdu($pdo, $pId)
	{
		$educations = loadEdu($pdo, $pId);
		$len = count($educations);

		if($len > 0)
		{
			echo("<p>Education</p>\n<ul>\n");

			for($i = 0; $i < $len; $i++)
				echo("<li>" . $educations[$i]["year"] . ": " . $educations[$i]["name"] . "</li>\n");
			
			echo("</ul>\n");
		}
	}

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Profile View</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<h1>Profile information</h1>
			<p>First Name: <?= $fn ?></p>
			<p>Last Name: <?= $ln ?></p>
			<p>Email: <?= $em ?></p>
			<p>
				Headline:<br>
				<?= $hl ?>
			</p>
			<p>
				Summary:<br>
				<?= $sm ?>
			</p>
			<?php displayEdu($pdo, $pId); displayPos($pdo, $pId); ?>
			<a href="index.php">Done</a>
		</div>
	</body>

</html>