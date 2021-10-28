<?php
require_once '../document.php';
require_once '../person.php';

function sqlProcesses($sqlStatement, $paramString, $paramVariablesArray)
{
	require_once '../connection.php';
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

$DocumentToPublish = "D0001";
$sqlStatement = "UPDATE `document` SET `type`=?, `printDate`=?, `journalIssue`=?, `status`=? WHERE `documentID`= ?";
$paramVariablesArray = ["1", date("Y-m-d"), "journalIssueX", "Published", $DocumentToPublish];
sqlProcesses($sqlStatement, "sssss", $paramVariablesArray);

?>