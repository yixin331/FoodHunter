  <html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body background="ee.png">
<font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font>
<p><font color="Black" size="7"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Reset your password: </font></p>

<font size="5">
<form method="POST" style="display:inline;">
<center>
<p>Your old password:
<input type="password" name="oldPassword" value="" style="width:250;height:30"/></p>
<p>Your new password:
<input type="password" name="newPassword1" value="" style="width:250;height:30"/></p>
<p>Confirm your new password:
<input type="password" name="newPassword2" value="" style="width:250;height:30"/></p></center><font color="#FFEBCD"><p>d</p></font>
<center><input type="submit" name="updateSubmit" value="Continue" style="width:200px;height:35px;color:white;background-color:black;border:3px white double">
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="hidden" id="updateRequest" name="updateRequest">
</form>
<form method="POST" style="display:inline;">
        <input type="submit" value="&nbsp&nbsp Back &nbsp&nbsp" name="back" style="width:200px;height:35px;color:white;background-color:black;border:3px white double">
        <input type="hidden" id="backRequest" name="backRequest"></center>
        
<p><center><font color="Black" size="3"> Remember: Your password has to be number with maximum length of 6! </font></center></p>
<font color="#FFEBCD"><p>d</p></font>
        <p><center><img src="cat.gif" width="400" height="200"></center></p>
</form></font>

</p>        





<?php

    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

    function debugAlertMessage($message) {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
        }

        return $statement;
    }

    function executeBoundSQL($cmdstr, $list) {
        global $db_conn, $success;
        $statement = OCIParse($db_conn, $cmdstr);

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }

        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                //echo $val;
                //echo "<br>".$bind."<br>";
                OCIBindByName($statement, $bind, $val);
                unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                echo htmlentities($e['message']);
                echo "<br>";
                $success = False;
            }
        }
    }

    function printResult($result) { //prints results from a select statement
        echo "<br>Retrieved data from table demoTable:<br>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function connectToDB() {
        global $db_conn;

        // Your username is ora_(CWL_ID) and the password is a(student number). For example,
        // ora_platypus is the username and a12345678 is the password.
        $db_conn = OCILogon("ora_yixin331", "a36851582", "dbhost.students.cs.ubc.ca:1522/stu");

        if ($db_conn) {
            debugAlertMessage("Database is Connected");
            return true;
        } else {
            debugAlertMessage("Cannot connect to Database");
            $e = OCI_Error(); // For OCILogon errors pass no handle
            echo htmlentities($e['message']);
            return false;
        }
    }

    function disconnectFromDB() {
        global $db_conn;

        debugAlertMessage("Disconnect from Database");
        OCILogoff($db_conn);
    }


    function handleUpdateRequest() {
        global $db_conn;
        $a = $_POST['oldPassword'];
        $b = $_POST['newPassword1'];
        $c = $_POST['newPassword2'];

        $arg = $_SERVER["QUERY_STRING"]."<br>";
        $curr = substr($arg,0,strlen($arg)-4);
        
        if(ctype_digit($a) &&strlen($a)<=6){

        
            $result = executePlainSQL("SELECT Name FROM Customer WHERE Login_ID='".$curr."' AND Password=".$a."");

            if (($row = oci_fetch_row($result)) != false) {
                if(ctype_digit($b) &&strlen($b)<=6){
                if($b==$c){
                    executePlainSQL("UPDATE Customer SET password='" . $b . "' WHERE Login_ID='".$curr."' AND password='" . $a . "'");
                    $url="https://www.students.cs.ubc.ca/~yixin331/h2.php?".$curr;
                    echo "<script LANGUAGE=\"Javascript\">";
                    echo "location.href='$url'";
                    echo "</script>";
                    OCICommit($db_conn);
                }else{
                    echo "<script LANGUAGE=\"Javascript\">";
                    echo "alert('New passwords are not same!');";
                    echo "</script>";
                }
            }else{
                echo "<script LANGUAGE=\"Javascript\">";
                echo "alert('Your password has to be number with maximum length of 6!');";
                echo "</script>";
            }                
            }else{
               echo "<script LANGUAGE=\"Javascript\">";
               echo "alert('Your old password is wrong!');";
               echo "</script>";
            }
        }else{
            echo "<script LANGUAGE=\"Javascript\">";
            echo "alert('Your old password is wrong!');";
            echo "</script>";
        }
    }

    function handleBackRequest() {
        global $db_conn;
        $arg = $_SERVER["QUERY_STRING"]."<br>";
        $curr = substr($arg,0,strlen($arg)-4);
        $url="https://www.students.cs.ubc.ca/~yixin331/h2.php?".$curr;
        echo "<script LANGUAGE=\"Javascript\">";
        echo "location.href='$url'";
        echo "</script>";
    }



    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest() {
        if (connectToDB()) {
            if (array_key_exists('updateRequest', $_POST)) {
                handleUpdateRequest();
            }else if(array_key_exists('backRequest', $_POST)){
                handleBackRequest();
           }

            disconnectFromDB();
        }
    }


    if (isset($_POST['updateSubmit'])||isset($_POST['back'])) {
        handlePOSTRequest();
    } 
    ?>

	</body>
</html>

