<?php
//require_once 'the session key';
require_once '../connection.php';
//headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

//Abstract retrieve function initiated. Array of commands unpackaged in to $request

//Create an author object - $authorObject(...)
$arr = [1 => "Send a POST request to this url!"];
if ($_SERVER['REQUEST_METHOD'] = "POST") {
	//create author object
	$factoryobj = new AuthorFactory;
}

if($request[0] == "mainPage")
{
	$authorID = "A1";
	$paramVariablesArray = [$authorID];
	$result = sqlProcesses("SELECT * FROM `document` WHERE `authorID` = ?", "s", $paramVariablesArray);

	if(mysqli_num_rows($result) > 0)
	{
		while($documents = mysqli_fetch_assoc($result))
		{
			print_r($documents);
		}

	}
	else
		echo "no such user";
}

if($request[0] == "updateAuthorData")
{

}

if($request[0] == "upload")
{

}

if($request[0] == "modify")
{

}

if($request[0] == "pay")
{

}

if($request[0] == "sign_out")
{

}

if($request[0] == "getContent")
{

}
?>