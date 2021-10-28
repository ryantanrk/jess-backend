<?php
require_once '../connection.php';

function sqlProcesses($sqlStatement, $paramString, $paramVariablesArray)
{
	$paramVariablesArrayProcessed = array();

	$paramVariablesArrayProcessed[] = & $paramString;

	for($i = 0; $i < strlen($paramString); $i++)
		$paramVariablesArrayProcessed[] = & $paramVariablesArray[$i];

	/* Prepare statement */
	$stmt = $GLOBALS['conn']->prepare($sqlStatement);
	if($stmt === false)
		trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->errno . ' ' . $conn->error, E_USER_ERROR);

	call_user_func_array(array($stmt, 'bind_param'), $paramVariablesArrayProcessed);
	$stmt->execute();

	$result = $stmt->get_result();

	return $result;
}

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
		$paramVariablesArray = [$personID, $role, $userName, $password, $emailAddress, $dob];

		sqlProcesses($sqlStatement, "ssssss", $paramVariablesArray);

		echo "Sign up succeeded<br>";
	}
}

signUp("reviewer4", "password", "editor3@x.com", "2", "0/0/0");

?>