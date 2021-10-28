<?php
//require_once 'the session key';
require_once '../connection.php';

//Abstract retrieve function initiated. Array of commands unpackaged in to $request

//Create an author object - $authorObject(...)

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