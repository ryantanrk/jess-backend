<?php
require_once '../connection.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$arr = [1 => "Send a POST request to this url!"];

function signUp($userName, $password, $emailAddress, $role, $dob)
{
	$paramVariablesArray = [$userName, $emailAddress];

	$result = sqlProcesses("SELECT * FROM `person` WHERE `username` = ? AND `email` = ?", "ss", $paramVariablesArray);

	if(mysqli_num_rows($result) > 1)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			///get the correct password to compare input with
			print_r($user);
		}
		array_push($arr, [
			"error" => "User already exists."
		]);
	}
	else
	{
		$personID = getNewID($role);
		$paramVariablesArray = ["*"];
		// $result = sqlProcesses("SELECT COUNT(?) FROM `person`", "s", $paramVariablesArray);		
		// $value = mysqli_fetch_assoc($result);

		// $totalUsers = $value['COUNT(?)'];
		$sqlStatement = "INSERT INTO `person`(`personID`, `type`, `username`, `password`, `email`, `dob`)
						 VALUES (?,?,?,?,?,?)";

		$paramVariablesArray = [$personID, $role[0], $userName, md5($password), $emailAddress, $dob];

		sqlProcesses($sqlStatement, "ssssss", $paramVariablesArray);
		$message = "Account username " . $userName . " successfully created.";

		if($role[0] == "2")
		{
			$paramVariablesArray = [$personID, substr($role, 2), "pending approval"];
			sqlProcesses("INSERT INTO `reviewer`(`personID`, `areaOfExpertise`, `status`) VALUES (?,?,?)", "sss", $paramVariablesArray);
			$message = "Reviewer account username " . $userName . " successfully created.";
		}
		array_push($arr, [
			"success" => $message
		]);
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$role = $_POST['type']; //contains area of expertise (if reviewer)
	$dob = $_POST['dob'];

	//sign up
	signUp($username, $password, $email, $role, $dob);
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>