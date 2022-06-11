<?php 

	session_start();

	require_once("pdo.php");
	require_once("utilities.php");

	checkLoggedIn();

	if(!isset($_GET['profile_id']))
	{
		$_SESSION['error'] = "Missing profile_id";
		header("Location: index.php");
		return;
	}

	if(isset($_POST['cancel']))
	{
		header("Location: index.php");
		return;
	}

	$fn = $ln = $em = $hl = $sm = $id = "";

	if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) 
			&& isset($_POST['summary']))
	{

		$firstName = $_POST['first_name'];
		$lastName = $_POST['last_name'];
		$email = $_POST['email'];
		$headline = $_POST['headline'];
		$summary = $_POST['summary'];
		$pId = $_POST['profile_id'];
		$uId = $_SESSION['user_id'];

		if(dataValidation() && validateField("year", "desc", "Position") && validateField("edu_year", "edu_school", "Education"))
		{
			$sql = "UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sm 
					WHERE profile_id = :id AND user_id = :uId";
			$stmt = $pdo->prepare($sql);
			$check = $stmt->execute(array(":fn" => $firstName, ":ln" => $lastName, ":em" => $email, ":hl" => $headline, 
									":sm" => $summary, ":id" => $pId, ":uId" => $uId));

			if($check !== FALSE)
			{
				//Clear out old positions entries
				deleteEntries("Position", $pdo, $pId);
				insertPos($pdo, $pId);

				//Clear out old eductions entries
				deleteEntries("Education", $pdo, $pId);
				insertEdu($pdo, $pId);
				
				$_SESSION['success'] = "Profile updated";
			}
			else
				$_SESSION['error'] = "Could not load profile";
		
			header("Location: index.php");
			return;
		}
		else
		{
			header("Location: edit.php?profile_id=" . $_GET['profile_id']);
			return;
		}

	}

	function deleteEntries($field, $pdo, $pId)
	{
		$sql = "DELETE FROM " . $field . " WHERE profile_id = :pid";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":pid" => $pId));
	}

	$sql = "SELECT * FROM Profile WHERE profile_id = :id AND user_id = :uid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":id" => $_GET['profile_id'], ":uid" => $_SESSION['user_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if(!$row)
	{	
		$_SESSION['error'] = "Could not load profile";
		header("Location: index.php");
		return;
	}
	else
	{
		$fn = $row["first_name"];
		$ln = $row["last_name"];
		$em = $row["email"];
		$hl = $row["headline"];
		$sm = $row["summary"];
		$id = $row["profile_id"];
	}

	function displayPos($pdo)
	{
		$positions = loadPos($pdo, $_GET['profile_id']);
		$rank = 0;

		for($i = 0; $i < count($positions); $i++)
		{
			$year = $positions[$i]["year"];
			$description = $positions[$i]["description"];
			$rank = $positions[$i]["rank"];

			echo("<div id='position$rank'><p>Year: <input type='text' name='year" . $rank . "' value='$year'>");
			echo(" <input type='button' value='-' onclick='$(\"#position" . $rank . "\").remove(); return false;'></p>\n");
			echo("<textarea name='desc" . $rank . "' rows='8' cols='80'>$description</textarea></div>\n");
		}

		return $rank;
	}

	function displayEdu($pdo)
	{
		$educations = loadEdu($pdo, $_GET['profile_id']);
		$rank = 0;

		for($i = 0; $i < count($educations); $i++)
		{
			$year = $educations[$i]["year"];
			$school = $educations[$i]["name"];
			$rank = $educations[$i]["rank"];

			echo("<div id='edu$rank'><p>Year: <input type='text' name='edu_year$rank' value='$year'>");
			echo(" <input type='button' value='-' onclick='$(\"#edu" . $rank . "\").remove(); return false;'></p>\n");
			echo("<p>School: <input type='text' size='80' name='edu_school$rank' class='school' value='$school'></p></div>\n");
		}

		return $rank;
	}

	$countPos = 0;
	$countEdu = 0;

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Profile Edit</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<?php welcome("Edit"); flashMessage("error"); ?>
			<form method="post">
				<p>First Name: <input type="text" name="first_name" size="60" value="<?= $fn ?>"></p>
				<p>Last Name: <input type="text" name="last_name" size="60" value="<?= $ln ?>"></p>
				<p>Email: <input type="text" name="email" size="30" value="<?= $em ?>"></p>
				<p>
					Headline:<br>
					<input type="text" name="headline" size="80" value="<?= $hl ?>">
				</p>
				<p>
					Summary:<br>
					<textarea name="summary" rows="8" cols="80"><?= $sm ?></textarea>
				</p>
				<p>
					Education: <input type="submit" id="addEdu" value="+">
					<div id="edu_fields"><?php $countEdu = displayEdu($pdo); ?></div>
				</p>
				<p>
					Position: <input type="submit" id="addPos" value="+">
					<div id="position_fields"><?php $countPos = displayPos($pdo); ?></div>
				</p>
				<p>
					<input type="hidden" name="profile_id" value="<?= $id ?>">
					<input type="submit" value="Save">
					<input type="submit" name="cancel" value="Cancel">
				</p>
			</form>

			<script type="text/javascript">
				countPos = <?= $countPos ?>;
				countEdu = <?= $countEdu ?>;

				$(document).ready(
					function(){
						console.log("Document ready called");

						//Handle Positions
						$("#addPos").click(
							function(event){
								event.preventDefault();
								if(countPos >= 9)
								{
									alert("Maximum of nine position entries exceeded");
									return;
								}

								countPos++;
								console.log("Adding position " + countPos);

								$("#position_fields").append(
									"<div id='position" + countPos + "'>" + 
									"<p>Year: <input type='text' name='year" + countPos + "' value=''>" + 
									" <input type='button' value='-' onclick='$(\"#position" + countPos + "\").remove();" +
										"return false;'></p>" + 
									"<textarea name='desc" + countPos + "' rows='8' cols='80'></textarea></div>"
								);
							}
						);

						//Handle Educations
						$("#addEdu").click(
							function(event){
								event.preventDefault();
								if(countEdu >= 9)
								{
									alert("Maximum of nine education entries exceeded");
									return;
								}

								countEdu++;
								console.log("Adding education " + countEdu);

								$("#edu_fields").append(
									"<div id='edu" + countEdu + "'>" + 
									"<p>Year: <input type='text' name='edu_year" + countEdu + "' value=''>" + 
									" <input type='button' value='-' onclick='$(\"#edu" + countEdu + "\").remove();" +
										"return false;'></p>" + 
									"<p>School: <input type='text' size='80' name='edu_school" + countEdu + "' class='school' value=''></p></div>"
								);

								$(".school").autocomplete(
									{
										source: "school.php"
									}
								);
							}
						);
					}
				);
			</script>
		</div>
	</body>

</html>