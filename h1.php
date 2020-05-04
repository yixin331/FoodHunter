<html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body background="ee.png">

<p><font color="Black" size="150">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Log in as</font></p>




<font size="5">
<form method="POST" action="h1.php">
<center><label><input name="Fruit" type="radio" value=" Employee" color="black" size="5">Employee&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
<label><input name="Fruit" type="radio" value=" Customer" size="5">Customer</label></center><p></p>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp ID：
<input type="text" name="id" value="" style="width:250;height:30"/>
<p></p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  Password:
<input type="password" name="password" value="" style="width:250;height:30"/>
<input type="hidden" id="selectRequest" name="selectRequest"><p></p>
<center><input type="submit" name="selectSubmit" value="Continue" style="width:170px;height:25px;color:red;background-color:#FFd39b;border:3px orange double">&nbsp&nbsp&nbsp&nbsp&nbsp</form>
<form method="POST" style="display: inline;"><input type="submit" value="Sign up now" name="signSubmit" style="width:170px;height:25px;color:red;background-color:#FFd39b;border:3px orange double">&nbsp&nbsp&nbsp&nbsp&nbsp<input type="hidden" id="signRequest" name="signRequest"></form>
<form method="POST" style="display: inline;"><input type="submit" value="&nbsp&nbsp Back &nbsp&nbsp" name="back" style="width:170px;height:25px;color:red;background-color:#FFd39b;border:3px orange double"><input type="hidden" id="backRequest" name="backRequest"></form></center>
</font>



<p><font color="#FFEBCD" size="50">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspHello</font></p>
</font>


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

    function handleSelectRequest() {
        global $db_conn;
        $a = $_POST['id'];
        $b = $_POST['password'];
        $c=$_POST['Fruit'];
        if($c==" Customer"){
            if(strlen($a)<=6 && ctype_digit($b) &&strlen($b)<=6){
            $result = executePlainSQL("SELECT Name FROM Customer WHERE Login_ID='".$a."' AND Password=".$b."");

            
            if (($row = oci_fetch_row($result)) != false) {
                echo ("<script>window.alert('Welcome back, customer: $row[0]')</script>");
                $url="https://www.students.cs.ubc.ca/~yixin331/h2.php?".$a;
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";
                OCICommit($db_conn);
            }
            echo "<script>alert('Customer not found')</script>";
        }else{
            echo "<script>alert('Customer not found')</script>";
        }
        }
        if($c==" Employee"){
        if(ctype_digit($a) &&strlen($a)<=6 && ctype_digit($b) &&strlen($b)<=6){
        $result = executePlainSQL("SELECT Name FROM Employee WHERE ID=".$a." AND Password=".$b."");
        
        if (($row = oci_fetch_row($result)) != false) {
            $result1 = executePlainSQL("SELECT * FROM Manager WHERE Employee_ID=".$a."");
            if (($row1 = oci_fetch_row($result1)) != false) {
                $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$a;
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";
                OCICommit($db_conn);
            
            }else{
                $url="https://www.students.cs.ubc.ca/~yixin331/employee.php?".$a;
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";
                OCICommit($db_conn);
            }
        }
        echo "<script>alert('Employee not found')</script>";
    }
        echo "<script>alert('Employee not found')</script>";
        }
    }


    function handleBackRequest() {
        global $db_conn;
       
        $url="https://www.students.cs.ubc.ca/~yixin331/h.php";
        echo "<script LANGUAGE=\"Javascript\">";
        echo "location.href='$url'";
        echo "</script>";
    }

    function handleSignRequest() {
        global $db_conn;
       
        $url="https://www.students.cs.ubc.ca/~yixin331/signup.php";
        echo "<script LANGUAGE=\"Javascript\">";
        echo "location.href='$url'";
        echo "</script>";
    }

    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest() {
        if (connectToDB()) {
            if (array_key_exists('backRequest', $_POST)) {
                handleBackRequest();
            } else if (array_key_exists('signRequest', $_POST)) {
                handleSignRequest();
            }else if(array_key_exists('selectRequest', $_POST)){
                 handleSelectRequest();
       
            }

            disconnectFromDB();
        }
    }
    if (isset($_POST['back']) || isset($_POST['signSubmit'])||isset($_POST['selectSubmit'])) {
      
        handlePOSTRequest();
    } 
    ?>

	</body>
</html>

