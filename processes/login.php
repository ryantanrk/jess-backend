<?php
require_once '../connection.php';
require_once '../class/person.php';
require_once '../factory/personfactory.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = ["1" => "Send a POST request to this url!"];

session_start();

function login($email, $p)
{
	global $arr;

	$email = $email;
	$pword = $p;

	$paramVariablesArray = [$email];

	$result = sqlProcesses("SELECT * FROM `person` WHERE `email` = ?", "s", $paramVariablesArray);

	if(mysqli_num_rows($result) > 0)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			///get the correct password to compare input with
			if($pword === $user['password'])
			{
				///info to be held on to throughout session is declared here
				$_SESSION["currentUser"] = $user["personID"];	

				//1st in line, person data
				$arr = 
				[
					"personID" => $user["personID"], 
					"type" => $user["type"], 
					"username" => $user["username"],
					"email" => $user["email"],
					"dob" => $user["dob"]
				];

				$personobj = "";
				if ($arr['type'] == 0) {
					$factoryobj = new EditorFactory;
					$personobj = $factoryobj->getNewUser($arr['personID']);
				}
				else if ($arr['type'] == 1) {
					$factoryobj = new AuthorFactory;
					$personobj = $factoryobj->getNewUser($arr['personID']);
				}
				else if ($arr['type'] == 2) {
					$factoryobj = new ReviewerFactory;
					$personobj = $factoryobj->getNewUser($arr['personID']);
				}

				$arr = $personobj;
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

	login($email, md5($password));
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>