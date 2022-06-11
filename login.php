<?php 

	session_start();
	require_once("pdo.php");
	require_once("utilities.php");

	$salt = 'XyZzy12*_';

	if(isset($_POST['cancel']))
	{
		header("Location: index.php");
		return;
	}

	if(isset($_POST['email']) && isset($_POST['pass']))
	{
		unset($_SESSION['name']);

		$email = $_POST['email'];
		$password = $_POST['pass'];

		if(strlen($email) < 1 || strlen($password) < 1)
		{
			$_SESSION['error'] = "Email and password are required";
			header("Location: login.php");
			return;
		}
		else
		{
			$check = hash("md5", $salt . $password);
			$sql = "SELECT user_id, name FROM users WHERE email = :em AND password = :pw";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":em" => $email, ":pw" => $check));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if($row)
			{
				$_SESSION['name'] = $row["name"];
				$_SESSION['user_id'] = $row["user_id"];
				header("Location: index.php");
				return;
			}
			else
			{
				$_SESSION['error'] = "Incorrect email or password";
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
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Login Page</title>
		<?php require_once("head.php"); ?>
	</head>

	<body>
		<div class="container">
			<h1>Please Log In</h1>
			<?php flashMessage("error"); flashMessage("success"); ?>
			<p>
				<form method="post" action="login.php">
					<label for="email">Email</label>
					<input type="text" name="email" id="email"><br>
					<label for="id_1723">Password</label>
					<input type="password" name="pass" id="id_1723"><br>
					<input type="submit" value="Log In" onclick="return doValidate();">
					<input type="submit" name="cancel" value="Cancel">
				</form>	
				<a href="account.php">Create an account</a>
			</p>
			<script type="text/javascript">
				function doValidate()
				{
					console.log("Validating ...");
					try
					{
						addr = document.getElementById("email").value;
						pw = document.getElementById("id_1723").value;
						console.log("Validating addr = " + addr + " pw = " + pw);

						if(addr == null || addr == "" || pw == null || pw == "")
						{
							alert("Both fields must be filled out");
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