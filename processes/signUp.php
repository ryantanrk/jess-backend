<?php
require_once '../connection.php';

function signUp($userName, $password, $emailAddress, $role, $dob)
{
	$paramVariablesArray = [$userName, $emailAddress];

	$result = sqlProcesses("SELECT * FROM `person` WHERE `username` = ? AND `email` = ?", "ss", $paramVariablesArray);

	if(mysqli_num_rows($result) > 0)
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
		
		$personID = 'P' . ($totalUsers + 1);

		$paramVariablesArray = [$personID, $role[0], $userName, $password, $emailAddress, $dob];

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

signUp("reviewer4", "password", "reviewer4@x.com", "2-Science", "0/0/0");

?>