<?php

require_once '../connection.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$randomArray = ["oi" => "oi"];

function login($email, $p)
{
	$email = $email;
	$pword = $p;

	$paramVariablesArray = [$email];

	$result = sqlProcesses("SELECT * FROM `person` WHERE `email` = ?", "s", $paramVariablesArray);

	if(mysqli_num_rows($result) > 0)
	{
		while($user = mysqli_fetch_assoc($result))
		{
			///get the correct password to compare input with
			// print_r($user);
	
			if($pword == $user["password"])
			{
				// echo "User authenticated - " . $user["email"] . ":" . $user["password"]. "<br>";
				$_SESSION["currentUser"] = $user["personID"];	///info to be held on to throughout session is declared here
				

				//Navigate to appropriate page based on user type
				//Pass the users data to these pages
				if($user["type"] == "0")			
				{
					echo json_encode(["Editor"]);
					// writeLine("Editor");
				}
				else if($user["type"] == "1")		
				{
					echo json_encode(["Author"]);
					// writeLine("Author");
				}
				else if($user["type"] == "2")				
				{
					echo json_encode(["Reviewer"]);
					// writeLine("Reviewer");
				}	
		
			}
			else
				//Echo JSON object back to user
				echo json_encode(["wrong password"]);
				// echo "wrong password";
		}

	}
	else
		echo json_encode(["no such user email"]);
		// echo "no such user email";
}
// echo json_encode($randomArray);

login("author1@x.com", "password");

// print_r($randomArray);
?>