<?php
require_once '../connection.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [];
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
			if($pword === $user["password"])
			{
				///info to be held on to throughout session is declared here
				$_SESSION["currentUser"] = $user["personID"];	

				//1st in line, person data
				array_push($arr, ["personID" => $user["personID"], 
								  "type" => $user["type"], 
								  "username" => $user["username"],
								  "email" => $user["email"],
								  "dob" => $user["dob"]
								]);

				//Next in line to be delivered, user specific data
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
					// $message . "Reviewer";
					$result = sqlProcesses("SELECT * 
											FROM `review` JOIN `document` on document.documentID = review.documentID
											WHERE review.reviewerID = ?", "s", [$user["personID"]]);

					if(mysqli_num_rows($result) > 0)
					{
						while($document = mysqli_fetch_assoc($result))
						{
							
							array_push($arr, [
									"documentID" => $document["documentID"], 
									"rating" => $document["rating"],
									"comment" => $document["comment"],
									"reviewStatus" => $document["reviewStatus"],
									"dateOfReviewCompletion " => $document["dateOfReviewCompletion"],
									"authorID" => $document["authorID"], 
									"title" => $document["title"],
									"topic" => $document["topic"],
									"dateOfSubmission" => $document["dateOfSubmission"],
									"printDate" => $document["printDate"],
									"pages" => $document["pages"],
									"authorRemarks" => $document["authorRemarks"],
									"editorRemarks" => $document["editorRemarks"],
									"reviewDueDate" => $document["reviewDueDate"],
									"editDueDate" => $document["editDueDate"],
									"documentStatus" => $document["documentStatus"]
								]);
						}
					}			
				}	
		
			}
			else
				array_push($arr, "Wrong password");
		}
	}
	else
		array_push($arr, "No such user email");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST['email'];
	$password = $_POST['password'];

	login($email, md5($password));

	echo json_encode($arr, JSON_PRETTY_PRINT);
}

// login("REVIEWER1@X.COM", md5("password"));
// echo json_encode($arr, JSON_PRETTY_PRINT);
?>