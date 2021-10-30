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

    function isLoggedIn()
    {
        if (isset($_SESSION['currentUser'])) 
            return true;
        else
            return false;
    }

    function sqlProcesses($sqlStatement, $paramString, $paramVariablesArray)
    {
        require_once 'connection.php';
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

    function writeLine($input)
    {
        echo $input . "<br>";
    }

	//function to get a new ID (for any table)
    function getNewID($type) : string 
    {
        global $personTable, $documentTable, $connection;
		//get prefix & query
        $prefix = "";
        $query = "";
        switch ($type) {
            case 0: //editor
                $prefix = "E";
                $query = "SELECT `personID` FROM `$personTable` WHERE `type` = '0'";
                break;
            case 1: //author
                $prefix = "A";
                $query = "SELECT `personID` FROM `$personTable` WHERE `type` = '1'";
                break;
            case 2: //reviewer
                $prefix = "R";
                $query = "SELECT `personID` FROM `$personTable` WHERE `type` = '2'";
                break;
            case 3: //document
                $prefix = "D";
                $query = "SELECT `personID` FROM `$documentTable`";
                break;
        }

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        
        $chosenID = $prefix . "1";
        $maxNum = 1;

        if (mysqli_num_rows($result) != 0) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $personID = $row['personID'];
                $id = substr($personID, 1);

                if ($id > $maxNum) {
                    $maxNum = $id;
                }
            }
            $chosenID = $prefix . ($maxNum + 1);
        }

        return $chosenID;
    }
?>