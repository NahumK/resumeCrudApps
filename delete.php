<?php 

	session_start();

	require_once("pdo.php");
	require_once("utilities.php");

	checkLoggedIn();

	if(isset($_POST['cancel']))
	{
		header("Location: index.php");
		return;
	}

	if(isset($_POST["delete"]) && isset($_POST['profile_id']))
	{
		$sql = "DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array("pid" => $_POST['profile_id'], ":uid" => $_SESSION['user_id']));
		$row = $stmt->rowCount();

		if($row == 1)
			$_SESSION['success'] = "Profile deleted";
		else
			$_SESSION['error'] = "Could not load profile";

		header("Location: index.php");
		return;
	}

	$id = $fn = $ln = "";
	$sql = "SELECT * FROM Profile WHERE profile_id = :pid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":pid" => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if($row)
	{
		$id = $row["profile_id"];
		$fn = $row["first_name"];
		$ln = $row["last_name"];
	}
	else
	{	
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Profile Delete</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<h1>Deleting Profile</h1>
			<form method="post" action="delete.php">
				<p>First Name: <?= $fn ?></p>
				<p>Last Name: <?= $ln ?></p>
				<input type="hidden" name="profile_id" value="<?= $id ?>">
				<input type="submit" name="delete" value="Delete">
				<input type="submit" name="cancel" value="Cancel">
			</form>
 		</div>
	</body>

</html>