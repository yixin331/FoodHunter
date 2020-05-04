<html>
    <head>
        <title>Food Hunter</title>

    </head>

<body  background="jja.png">
        

<?php

    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
    $popular;
    $highest_name;
    $avg_rating;
    


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
       
        $url="https://www.students.cs.ubc.ca/~yixin331/h.php";
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
        global $popular;
        global $highest_name;
        global $avg_rating;

        if(connectToDB()){
        $popular = executePlainSQL("SELECT distinct r1.name FROM restaurant_info r1 WHERE NOT EXISTS((SELECT c.Login_ID FROM Customer c) MINUS (SELECT o.Customer_ID FROM Customer_order o WHERE o.rest_name=r1.name))");
        $highest_name = executePlainSQL("SELECT distinct r1.name FROM restaurant_info r1,review r2 WHERE r1.name=r2.restaurant_name GROUP BY r1.name HAVING avg(r2.rating) >= ALL(SELECT avg(r3.rating) FROM review r3 GROUP BY r3.restaurant_name)");
        $avg_rating = executePlainSQL("SELECT distinct r1.type AS Type, avg(r2.rating) AS Rating FROM restaurant_info r1,review r2 WHERE r1.name=r2.restaurant_name GROUP BY r1.type");
        disconnectFromDB();
        }
    }

    performInfo();
    if (isset($_POST['back'])) {
        handlePOSTRequest();
    }
    ?>
<font color="#FFEBCD"><p>d</p></font>
<p><center><font color="Black" size="20"> Let's see how the restaurants go!</font></center></p><font color="#FFEBCD"><p>d</p></font>
<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp How is the rating between different types of restaurant?</font></p>
<p><font color="Black" size="5"><?php  if (($nrows = oci_fetch_all($avg_rating, $avs)) != 0) {
            if ($nrows > 0) {
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($avs as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($avs as $data) {
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br></center>";}} ?></font></p>
<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Congratulation!<font color="#FFA07A" size="5"> <?php  if(($nrows = oci_fetch_all($highest_name, $hs)) != 0){
    for ($i = 0; $i < $nrows; $i++) {
        foreach ($hs as $data) {
            if($i==$nrows-1){
                echo "$data[$i]";
            }else{
                echo "$data[$i], ";
            }
        }}} ?> </font>got the hightest rating! </font></p>
<p><font color="Black" size="5"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Amazing! Everyone has been to <font color="#FFA07A" size="5"><?php if (($nrows = oci_fetch_all($popular, $populars)) != 0){
    for ($i = 0; $i < $nrows; $i++) {
        foreach ($populars as $data) {
            if($i==$nrows-1){
                echo "$data[$i]";
            }else{
                echo "$data[$i], ";
            }
        }
}} ?> </font>before!</font></p>
<font color="#FFEBCD"><p>d</p></font>
<form method="POST" style="display: inline;">
<center><input type="submit" value="&nbsp&nbsp Back &nbsp&nbsp" name="back" style="width:200px;height:35px;color:white;background-color:black;border:3px white double"><input type="hidden" id="backRequest" name="backRequest"></center>
</form>
<font color="#FFEBCD"><p>d</p></font>
<p><center><img src="meme.gif" width="600" height="100"></center></p>
	</body>
</html>

