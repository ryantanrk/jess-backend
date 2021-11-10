<?php
require_once '../connection.php';
require_once '../class/person.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];

function publishDocument($editorID, $documentID, $printDate, $journalIssue) {
	global $arr;
	$editor = getPersonFromID($editorID);

	$sql = "SELECT `documentStatus` FROM `document` WHERE `documentID` = ?";
	$result = sqlProcesses($sql, "s", [$documentID]);

	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if ($row['documentStatus'] === "paid") {
			$docAttributes = [
				"documentStatus" => "published",
				"printDate" => $printDate,
				"journalIssue" => $journalIssue
			];

			foreach ($docAttributes as $key => $value) {
				$editor->setAuthorizedDocumentAttribute($documentID, $key, $value);
			}
			$arr = ["message" => "published document: " . $documentID];
		}
		else if ($row['documentStatus'] === "published") {
			$arr = ["error" => "document is already published"];
		}
		else {
			$arr = ["error" => "document is not in a state to be published"];
		}
	}
	else {
		$arr = ["error" => "no such document found"];
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$editorID = $_POST['editorID'];
	$documentID = $_POST['documentID'];
	$printDate = date('Y-m-d');
	$journalIssue = $_POST['journalIssue'];

	publishDocument($editorID, $documentID, $printDate, $journalIssue);
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>