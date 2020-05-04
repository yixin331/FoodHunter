<html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body background="ee.png">
        

<?php

    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
    $name;
    $phone;
    $address;
    $orders;


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
            if(array_key_exists('backRequest', $_POST)){
                handleBackRequest();
           }

            disconnectFromDB();
        }
    }

    function performInfo(){
        global $db_conn;
        global $name;
        global $phone;
        global $address;
        global $orders;

        $arg = $_SERVER["QUERY_STRING"]."<br>";
        $curr = substr($arg,0,strlen($arg)-4);

        if(connectToDB()){
        $name = oci_fetch_row(executePlainSQL("SELECT Name FROM Customer WHERE Login_ID='".$curr."'"))[0];
        $phone = oci_fetch_row(executePlainSQL("SELECT Phone FROM Customer WHERE Login_ID='".$curr."'"))[0];
        $address = oci_fetch_row(executePlainSQL("SELECT Address FROM Customer WHERE Login_ID='".$curr."'"))[0];
        $orders = executePlainSQL("SELECT * FROM Customer_order WHERE Customer_ID='".$curr."'");
        disconnectFromDB();
        }
    }

    performInfo();
    if (isset($_POST['back'])) {
        handlePOSTRequest();
    }
    ?>
<font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font>
<center><p><font color="Black" size="20"> &nbsp&nbsp&nbsp Welcome back user: <?php  echo $name ?></font></p></center>

<font size="5">
<form method="POST" style="display: inline;">

<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Your phone: <?php  echo $phone ?></font></p>
<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Your address: <?php  echo $address ?></font></p>
<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp You have ordered: </font></p>
<p><font color="Black" size="5"><?php  if (($nrows = oci_fetch_all($orders, $temp)) != 0) {
            if ($nrows > 0) {
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($temp as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($temp as $data) {
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br></center>";}} ?></font></p>
<center>
<font color="#FFEBCD"><p>d</p></font>
<form method="POST" style="display: inline;"><input type="submit" value="&nbsp&nbsp Back &nbsp&nbsp" name="back" style="width:200px;height:35px;color:white;background-color:black;border:3px white double"><input type="hidden" id="backRequest" name="backRequest"></form></center>
</font>
	</body>
</html>

