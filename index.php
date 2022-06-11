<?php 

	session_start();
	require_once("pdo.php");
	require_once("utilities.php");

	function displayContent($pdo)
	{
		if(!isset($_SESSION['name']))
			echo("<p><a href='login.php'>Please log in</a></p>\n");
		else
			echo("<p><a href='logout.php'>Logout</a></p>\n");

		$sql = "SELECT Profile.profile_id, Profile.first_name, Profile.last_name, Profile.headline FROM users JOIN Profile ON users.user_id = Profile.user_id";
		$stmt = $pdo->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);	

		if($row)
		{
			echo("<table border='1'>\n");
			echo("<thead><tr><th>Name</th><th>Headline</th>");
			if(isset($_SESSION['name']))
				echo("<th>Action</th>");
			echo("</tr></thead>\n");
			echo("<tbody>\n");
			do
			{
				$name = htmlentities($row["first_name"]) . " " . htmlentities($row["last_name"]);
				$headline = htmlentities($row["headline"]);
				$id = $row["profile_id"];

				echo("<tr><td><a href='view.php?profile_id=" . $id . "'>$name</td><td>$headline</td>");
						
				if(isset($_SESSION['name']))
					echo("<td><a href='edit.php?profile_id=" . $id . "'>Edit </a><a href='delete.php?profile_id=" . $id . 
							"'>Delete</a></td>");
				echo("</tr>\n");

			}while($row = $stmt->fetch(PDO::FETCH_ASSOC));

			echo("</tbody></table>\n");
		}
		else
			echo("<p>No Rows Found</p>\n");
		
		
		if(isset($_SESSION['name']))
			echo("<p><a href='add.php'>Add New Entry</a></p>\n");
		
	}

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=1, initial-scale=1">
		<title>Resume Registry</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<h1>Resume Registry</h1>
			<?php flashMessage("error"); flashMessage("success"); displayContent($pdo); ?>
		</div>
	</body>

</html>