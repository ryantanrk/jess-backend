<?php
require_once '../connection.php';
require_once '../class/person.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = ["1" => "Send a POST request to this url!"];

function login($email, $p)
{
	global $arr;

	$email = $email;
	$pword = md5($p);

	$paramVariablesArray = [$email];

	$result = sqlProcesses("SELECT `personID`, `type`, `password` FROM `person` WHERE `email` = ?", "s", $paramVariablesArray);

	if(mysqli_num_rows($result) > 0)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			///get the correct password to compare input with
			if($pword === $user['password'])
			{
				//1st in line, person data
				$arr = 
				[
					"personID" => $user["personID"], 
					"type" => $user["type"]
				];

				$personobj = getPersonFromID($arr['personID']);
				$person_arr = $personobj->getPersonData();
				$person_arr['type'] = $arr['type'];

				$arr = $person_arr;
			}
			else
				$arr = ["error" => "Wrong password"];
		}
	}
	else
		$arr = ["error" => "No such user email"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST['email'];
	$password = $_POST['password'];

	login($email, $password);
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>