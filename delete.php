  <html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body background="ee.png"><p><font color="#FFEBCD" size="100">.</font></p>
<p><center><font color="Black" size="60"> Are you sure to delete the account?</font></center></p>
<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspConfirm your information to delete the account:</font></p>



<font size="5">
<form method="POST">
<center><p>
ID：
<input type="text" name="id" value="" style="width:250;height:30"/>
<p></p>  Password:
<input type="password" name="password" value="" style="width:250;height:30"/></p></center>
<p><center><input type="submit" name="deleteSubmit" value="Continue" style="width:200px;height:35px;color:white;background-color:black;border:3px white double">
<input type="hidden" id="deleteRequest" name="deleteRequest"></center></p>
</form>
<p>
<center><form method="POST">
        <input type="submit" value="&nbsp&nbsp Back &nbsp&nbsp" name="back" style="width:200px;height:35px;color:white;background-color:black;border:3px white double">
        <input type="hidden" id="backRequest" name="backRequest"></center>
<p><font color="#FFEBCD" size="100">.</font></p>
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


    function handleDeleteRequest() {
        global $db_conn;
        $a = $_POST['id'];
        $b = $_POST['password'];

        $arg = $_SERVER["QUERY_STRING"]."<br>";
        $curr = substr($arg,0,strlen($arg)-4);
        if($a==$curr){
            if(ctype_digit($b) &&strlen($b)<=6){
            $result = executePlainSQL("SELECT Name FROM Customer WHERE Login_ID='".$a."' AND Password=".$b."");
    
            
            if (($row = oci_fetch_row($result)) != false) {
                executePlainSQL("DELETE FROM Customer WHERE Login_ID='".$a."' AND Password=".$b."");
                $url="https://www.students.cs.ubc.ca/~yixin331/h1.php";
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";
                OCICommit($db_conn);
            }else{
                echo "<script LANGUAGE=\"Javascript\">";
                echo "alert('Password not correct!');";
                echo "</script>";
            }
               
        }else{
            echo "<script LANGUAGE=\"Javascript\">";
               echo "alert('Password not correct!');";
               echo "</script>";
        }
        }else{
            echo "<script LANGUAGE=\"Javascript\">";
            echo "alert('Username not matched!');";
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
            if(array_key_exists('deleteRequest', $_POST)){
                handleDeleteRequest();
            }else if(array_key_exists('backRequest', $_POST)){
                handleBackRequest();
           }

            disconnectFromDB();
        }
    }


    if (isset($_POST['deleteSubmit'])||isset($_POST['back'])) {
        handlePOSTRequest();
    } 

    ?>

	</body>
</html>

