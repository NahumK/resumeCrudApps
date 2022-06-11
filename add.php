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

	if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) 
			&& isset($_POST['summary']))
	{
		$firstName = $_POST['first_name'];
		$lastName = $_POST['last_name'];
		$email = $_POST['email'];
		$headline = $_POST['headline'];
		$summary = $_POST['summary'];
		$id = $_SESSION['user_id'];

		if(dataValidation() && validateField("year", "desc", "Position") && validateField("edu_year", "edu_school", "Education"))
		{
			$sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) 
					VALUES (:uid, :fn, :ln, :em, :hl, :sm)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":uid" => $id, ":fn" => $firstName, ":ln" => $lastName, ":em" => $email, ":hl" => $headline, 
									":sm" => $summary));

			$profile_id = $pdo->lastInsertId();
			insertPos($pdo, $profile_id);
			insertEdu($pdo, $profile_id);

			$_SESSION['success'] = "Profile added";
			header("Location: index.php");
			return;
		}
		else
		{
			header("Location: add.php");
			return;
		}
		
	}

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Profile Add</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<?php welcome("Add"); flashMessage("error"); ?>
			<form method="post">
				<p>First Name: <input type="text" name="first_name" size="60"></p>
				<p>Last Name: <input type="text" name="last_name" size="60"></p>
				<p>Email: <input type="text" name="email" size="30"></p>
				<p>
					Headline:<br>
					<input type="text" name="headline" size="80">
				</p>
				<p>
					Summary:<br>
					<textarea name="summary" rows="8" cols="80"></textarea>
				</p>
				<p>
					Education: <input type="submit" id="addEdu" value="+">
					<div id="edu_fields"></div>
				</p>
				<p>
					Position: <input type="submit" id="addPos" value="+">
					<div id="position_fields"></div>
				</p>
				<input type="submit" value="Add">
				<input type="submit" name="cancel" value="Cancel">
			</form>
			<script type="text/javascript">
				countPos = 0;
				countEdu = 0;

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