<?php 

	session_start();
	require_once("pdo.php");
	require_once("utilities.php");

	$salt = 'XyZzy12*_';

	if (isset($_POST['cancel'])) 
	{
		header("Location: login.php");
		return;
	}

	if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass']))
	{
		unset($_SESSION['name']);

		$username = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['pass'];

		if(strlen($username) < 1 || strlen($email) < 1 || strlen($password) < 1)
		{
			$_SESSION['error'] = "All fields are required";
			header("Location: account.php");
			return;
		}
		else
		{
			$checkPass = hash("md5", $salt . $password);
			$sql = "SELECT * FROM users WHERE email = :em";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":em" => $email));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if($row)
			{
				$_SESSION['error'] = "Account already exist";
				header("Location: account.php");
				return;
			}
			else
			{
				$sql = "INSERT INTO users (name, email, password) VALUES (:nm, :em, :pw)";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array(":nm" => $username, ":em" => $email, ":pw" => $checkPass));
				$_SESSION['success'] = "Account created, please enter your credentials";
				header("Location: login.php");
				return;
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width; initial-scale=1">
		<title>Create Account</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<h1>Create an account</h1>
			<?php flashMessage("error"); ?>
			<p>
				<form method="post" action="account.php">
					<label for="name">Username</label>
					<input type="text" name="name" id="name"><br>
					<label for="email">Email</label>
					<input type="text" name="email" id="email"><br>
					<label for="id_1723">Password</label>
					<input type="password" name="pass" id="id_1723"><br>
					<input type="submit" value="Create" onclick="return doValidate();">
					<input type="submit" name="cancel" value="Cancel">
				</form>
			</p>
			<script type="text/javascript">
				function doValidate()
				{
					console.log("Validating ...");
					try
					{
						user = document.getElementById("name").value;
						addr = document.getElementById("email").value;
						pw = document.getElementById("id_1723").value;
						console.log("Validating addr = " + addr + " pw = " + pw);

						if(addr == null || addr == "" || pw == null || pw == "" || user == null || user == "")
						{
							alert("All fields must be filled out");
							return false;
						}
						if(addr.indexOf("@") == -1)
						{
							alert("Invalid email address");
							return false;
						}

						return true;
					}
					catch(e)
					{
						return false;
					}

					return false;
				}
			</script>	
		</div>
	</body>

</html>


