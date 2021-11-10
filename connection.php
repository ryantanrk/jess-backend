<?php
    require_once 'config.php';
    
    //document is only called when user is logged in
    $GLOBALS['conn'] = mysqli_connect($server, $connectUser, $connectPass, $database);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    //if user is logged in but account got locked, sign out (update the active values too)
    //attempt to connect to database
	$connection = new mysqli($server, $connectUser, $connectPass);
	if (!$connection)
	{
		die("Database connection failed: " . mysqli_error($connection));
	}

	//attempt to select the database
	$db = mysqli_select_db($connection, $database);
	if(!$db)
	{
		die("Database selection failed: " . mysqli_error($connection));
	}

    //Extraction method
    function retrieveDocumentFromDatabaseInCorrectState($documentID)
    {
        $result = sqlProcesses("SELECT `documentStatus` FROM `document` WHERE `documentID` = ?", "s", [$documentID]);
        $sqlArray = mysqli_fetch_assoc($result);

        $documentObject;

        //discernment
        if($sqlArray['documentStatus'] != "published")
            $documentObject = new Document(new ManuscriptState, $documentID);
        else
            $documentObject = new Document(new JournalState, $documentID);

        return $documentObject;
    }

    //Extraction method
    function sqlProcesses($sqlStatement, $paramString, $paramVariablesArray)
    {
        global $connection;
        $paramVariablesArrayProcessed = array();

        $paramVariablesArrayProcessed[] = & $paramString;

        for($i = 0; $i < strlen($paramString); $i++)
            $paramVariablesArrayProcessed[] = & $paramVariablesArray[$i];

        /* Prepare statement */
        $stmt = $GLOBALS['conn']->prepare($sqlStatement);
        if($stmt === false)
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $connection->errno . 
            ' ' . $connection->error, E_USER_ERROR);

        call_user_func_array(array($stmt, 'bind_param'), $paramVariablesArrayProcessed);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result;
    }

	//function to get a new ID (for any table)
    function getNewID($type) : string 
    {
        global $personTable, $documentTable, $connection;
		//get prefix & query
        $prefix = "";
        $query = "";
        $result = "";

        if ($type < 3 && $type >= 0) {
            switch ($type) {
                case 0: //editor
                    $prefix = "E";
                    $query = "SELECT COUNT(*) AS total FROM `$personTable` WHERE `type` = ?";
                    break;
                case 1: //author
                    $prefix = "A";
                    $query = "SELECT COUNT(*) AS total FROM `$personTable` WHERE `type` = ?";
                    break;
                case 2: //reviewer
                    $prefix = "R";
                    $query = "SELECT COUNT(*) AS total FROM `$personTable` WHERE `type` = ?";
                    break;
            }
            $result = sqlProcesses($query, "s", [$type]);
        }
        else if ($type == 3) {
            //document
            $prefix = "D";
            $query = "SELECT COUNT(?) AS total FROM `$documentTable`";
            $result = sqlProcesses($query, "s", ["*"]);
        }
        
        $chosenID = $prefix . "1"; //default

        if (mysqli_num_rows($result) != 0) {
            $total = mysqli_fetch_assoc($result);
            $chosenID = $prefix . ($total['total'] + 1);
        }

        return $chosenID;
    }
?>