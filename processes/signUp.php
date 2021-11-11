<?php
require_once '../connection.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];

function signUp($userName, $password, $emailAddress, $role, $dob)
{
	global $arr;
	$paramVariablesArray = [$userName, $emailAddress];

	//check for existing username/email
	$result = sqlProcesses("SELECT * FROM `person` WHERE `username` = ? OR `email` = ?", "ss", $paramVariablesArray);

	if(mysqli_num_rows($result) == 1)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			$un = $user['username'];
			$em = $user['email'];
			$arr = [
				"error" => "User already exists."
			];

			if ($un == $userName) {
				$arr['username'] = "match";
			}
			if ($em == $emailAddress) {
				$arr['email'] = "match";
			}
		}
	}
	else
	{
		$personID = getNewID($role[0]);
		$paramVariablesArray = ["*"];
		
		$sqlStatement = "INSERT INTO `person`(`personID`, `type`, `username`, `password`, `email`, `dob`)
						 VALUES (?,?,?,?,?,?)";

		$paramVariablesArray = [$personID, $role[0], $userName, md5($password), $emailAddress, $dob];

		sqlProcesses($sqlStatement, "ssssss", $paramVariablesArray);
		$message = "Account username " . $userName . " successfully created.";

		$arr = [
			"success" => $message,
			"type" => $role[0]
		];

		if($role[0] == "2")
		{
			$paramVariablesArray = [$personID, substr($role, 2), "pending approval"];
			sqlProcesses("INSERT INTO `reviewerspecific` (`personID`, `areaOfExpertise`, `status`) VALUES (?,?,?)", "sss", $paramVariablesArray);
			$message = "Reviewer account username " . $userName . " successfully created.";
			$arr['areaOfExpertise'] = substr($role, 2);
		}
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

signUp("ryan", "password", "j18026290@student.newinti.edu.my", "2-Maths", "2001-11-21");

echo json_encode($arr, JSON_PRETTY_PRINT);
?>