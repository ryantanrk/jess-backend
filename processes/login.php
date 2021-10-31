<?php
require_once '../connection.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];
$message = "";

function login($email, $p)
{
	global $arr;
	global $message;

	$email = $email;
	$pword = $p;

	$paramVariablesArray = [$email];

	$result = sqlProcesses("SELECT * FROM `person` WHERE `email` = ?", "s", $paramVariablesArray);

	if(mysqli_num_rows($result) > 0)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			///get the correct password to compare input with

			if($pword == $user["password"])
			{
				///info to be held on to throughout session is declared here
				$_SESSION["currentUser"] = $user["personID"];	

				//Navigate to appropriate page based on user type
				//Pass the users data to these pages
				if($user["type"] == "0")			
				{
					$message . "Editor";
					// writeLine("Editor");
				}
				else if($user["type"] == "1")		
				{
					// $message += "Author";
					$result = sqlProcesses("SELECT * FROM `document` WHERE `authorID` = ?", "s", [$user["personID"]]);

					if(mysqli_num_rows($result) > 0)
					{
						$arr = [];
						while($document = mysqli_fetch_assoc($result))
						{
							
							array_push($arr, ["documentID" => $document["documentID"], 
									"authorID" => $document["authorID"], 
									"title" => $document["title"],
									"topic" => $document["topic"],
									"dateOfSubmission" => $document["dateOfSubmission"],
									"printDate" => $document["printDate"],
									"pages" => $document["pages"],
									"authorRemarks" => $document["authorRemarks"],
									"editorRemarks" => $document["editorRemarks"],
									"status" => $document["status"]
								]);
						}
					}

				}
				else if($user["type"] == "2")				
				{
					$message . "Reviewer";
				}	
		
			}
			else
				$message . "Wrong password";
		}
	}
	else
		$message . "No such user email";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST['email'];
	$password = $_POST['password'];

	login($email, md5($password));

	echo json_encode($arr, JSON_PRETTY_PRINT);
}

// login("author1@x.com", md5("password"));
// echo json_encode($arr, JSON_PRETTY_PRINT);
?>