<?php

require_once '../connection.php';

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
			print_r($user);
			echo "<br><br>";
	
			if($pword == $user["password"])
			{
				echo "User authenticated - " . $user["email"] . ":" . $user["password"]. "<br>";
				$_SESSION["currentUser"] = $user["personID"];	///info to be held on to throughout session is declared here
				

				//Navigate to appropriate page based on user type
				//Pass the users data to these pages
				if($user["type"] == "0")			
				{
					writeLine("Editor");
					header("Location: editorSection.php");
				}
				else if($user["type"] == "1")		
				{
					writeLine("Author");
					header("Location: authorSection.php");
				}
				else if($user["type"] == "2")				
				{
					writeLine("Reviewer");
					header("Location: reviewerSection.php");
				}								
			}
			else
				//Echo JSON object back to user
				echo "wrong password";
		}

	}
	else
		echo "no such user email";
}

login("reviewer1@x.com", "password");

?>