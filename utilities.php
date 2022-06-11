<?php 

	function flashMessage($error)
	{
		$color = ($error == "success") ? "green" : "red";
		
		if(isset($_SESSION[$error]))
		{
			echo("<p style='color:" . $color . "'>" . $_SESSION[$error] . "</p>\n");
			unset($_SESSION[$error]);
		}
	}

	function checkLoggedIn()
	{
		if(!isset($_SESSION['name']))
			die("ACCESS DENIED");
	}

	function welcome($file)
	{
		echo("<h1>" . $file . "ing Profile for " . htmlentities(strtoupper($_SESSION['name'])) . "</h1>\n");
	}

	function dataValidation()
	{
		
		if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 
				|| strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
		{
			$_SESSION['error'] = "All fields are required";
			return false;
		}
		if(!str_contains($_POST['email'], "@"))
		{
			$_SESSION['error'] = "Email address must contain @";
			return false;
		}
		return true;
	}

	function validateField($fieldYear, $desc, $field)
	{
		for($i = 1; $i <= 9; $i++)
		{
			if(!(isset($_POST[$fieldYear . "" . $i]) && isset($_POST[$desc . "" . $i]))) continue;

			$year = $_POST[$fieldYear . "" . $i];
			$desc = $_POST[$desc . "" . $i];

			if(strlen($year) == 0 || strlen($desc) == 0)
			{
				$_SESSION["error"] = "All fields are required";
				return false;
			}

			if(!is_numeric($year))
			{
				$_SESSION['error'] = $field . " year must be numeric";
				return false;
			}

		}

		return true;
	}

	function loadPos($pdo, $profile_id)
	{
		$sql = "SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":pid" => $profile_id));
		$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $positions;
	}

	function loadEdu($pdo, $profile_id)
	{
		$sql = "SELECT * FROM Education JOIN Institution 
					ON Education.institution_id = Institution.institution_id WHERE profile_id = :pid ORDER BY rank";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":pid" => $profile_id));
		$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $educations;
	}

	function insertPos($pdo, $profile_id)
	{
		$rank = 1;

		for($i = 1; $i <= 9; $i++)
		{
			if(!(isset($_POST['year' . $i]) && isset($_POST['desc' . $i]))) continue;

			$year = $_POST['year' . $i];
			$desc = $_POST['desc' . $i];

			$sql = "INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rk, :yr, :descr)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":pid" => $profile_id, ":rk" => $rank, ":yr" => $year, ":descr" => $desc));
			$rank++;
		}
	}

	function insertEdu($pdo, $profile_id)
	{
		$rank = 1;

		for($i = 1; $i <= 9; $i++)
		{
			if(!(isset($_POST['edu_year' . $i]) && isset($_POST['edu_school' . $i]))) continue;

			$year = $_POST['edu_year' . $i];
			$school = $_POST['edu_school' . $i];

			//Lookup the school if it is there
			$institution_id = "";
			$sql = "SELECT institution_id FROM Institution WHERE name = :name";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":name" => $school));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if($row !== false)
				$institution_id = $row["institution_id"];
			else
			{
				$sql = "INSERT INTO Institution (name) VALUES (:name)";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array(":name" => $school));
				$institution_id = $pdo->lastInsertId();
			}

			$sql = "INSERT INTO Education (profile_id, rank, year, institution_id) VALUES (:pid, :rank, :year, :iid)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(":pid" => $profile_id, ":rank" => $rank, ":year" => $year, ":iid" => $institution_id));

			$rank++;
		}
	}

?>