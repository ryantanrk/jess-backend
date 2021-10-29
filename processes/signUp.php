<?php
require_once '../connection.php';

function signUp($personID, $userName, $password, $emailAddress, $role, $dob)
{
	$paramVariablesArray = [$userName, $emailAddress];

	$result = sqlProcesses("SELECT * FROM `person` WHERE `username` = ? AND `email` = ?", "ss", $paramVariablesArray);

	if(mysqli_num_rows($result) > 1)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			///get the correct password to compare input with
			print_r($user);
			echo "<br><br>";
		}
		echo "Sign up failed. User already exists<br>";
	}
	else
	{
		$paramVariablesArray = ["*"];
		$result = sqlProcesses("SELECT COUNT(?) FROM `person`", "s", $paramVariablesArray);		
		$value = mysqli_fetch_assoc($result);

		$totalUsers = $value['COUNT(?)'];
		$sqlStatement = "INSERT INTO `person`(`personID`, `type`, `username`, `password`, `email`, `dob`)
						 VALUES (?, ?, ?, ?, ?, ?)";

		$paramVariablesArray = [$personID, $role[0], $userName, md5($password), $emailAddress, $dob];

		sqlProcesses($sqlStatement, "ssssss", $paramVariablesArray);

		echo "Sign up succeeded<br>";

		if($role[0] == "2")
		{
			writeLine("we have a reviewer");
			echo substr($role,2);

			$paramVariablesArray = [$personID, substr($role,2), "available"];
			sqlProcesses("INSERT INTO `reviewer`(`personID`, `areaOfExpertise`, `status`) VALUES (?,?,?)", "sss", $paramVariablesArray);
		}
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$personID = getNewID($type);
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$role = $_POST['type'];
	$dob = $_POST['dob'];

	//sign up
	signUp($personID, $username, $password, $email, $role, $dob);
}
?>