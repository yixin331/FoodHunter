<html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body background="ee.png">
<font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font>
<form method="POST">
<center>
<table border="1">
<tr>
<th>Ingredient Name</th>
<th>Price per unit</th>
<th>QTY</th>
</tr>
<tr>
<td>Beef</td>
<td>6.99</td>
<td><input type="text" name="1" value="" style="width:50;height:30"></td>
</tr>
<tr>
<td>Cabbage</td>
<td>2.98</td>
<td><input type="text" name="2" value="" style="width:50;height:30"></td>
</tr>
<tr>
<td>Spaghetti</td>
<td>3.59</td>
<td><input type="text" name="3" value="" style="width:50;height:30"></td>
</tr>
<tr>
<td>Chicken</td>
<td>4.98</td>
<td><input type="text" name="4" value="" style="width:50;height:30"></td>
</tr>
<tr>
<td>Potato</td>
<td>1.99</td>
<td><input type="text" name="5" value="" style="width:50;height:30"></td>
</tr>
</table>
</center>
<font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font>
<center><input type='submit' value='Confirm' name='sub' style="width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<input type='submit' value='Back' name='back' style="width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double"></center>
</form>






	</body>
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
    
    global $db_conn;
    $arg=$_SERVER["QUERY_STRING"]."<br>";
    $id=substr($arg,0,strlen($arg)-4);
    
    if(isset($_POST['back'])){
        $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$id;
        echo "<script language=\"JavaScript\">";
        echo "location.href='$url';";
        echo "</script>";
    }
    if(isset($_POST['sub'])){
        connectToDB();
        if($_POST['1']>0){
            $pr=$_POST['1']*6.99;
            executePlainSQL("insert into Purchase_info values ('".$id."','Beef',".$_POST['1'].")");
            $result=executePlainSQL("select * from Purchase_price where Ingredient_name='Beef' AND Amount=".$_POST['1']);
            if(($nrows=oci_fetch_all($result, $results))==0){
                executePlainSQL("insert into Purchase_price values ('Beef',".$_POST['1'].",".$pr.")");
            }
            echo "<script Language=\"JavaScript\">";
            echo "alert(\"Totoal price: $pr\\nPurchase succeeds!\");";
            echo "</script>";
            OCICommit($db_conn);
        }
        if($_POST['2']>0){
            $pr=$_POST['2']*2.98;
            executePlainSQL("insert into Purchase_info values ('".$id."','Cabbage',".$_POST['2'].")");
            $result=executePlainSQL("select * from Purchase_price where Ingredient_name='Cabbage' AND Amount=".$_POST['2']);
            if(($nrows=oci_fetch_all($result, $results))==0){
                executePlainSQL("insert into Purchase_price values ('Cabbage',".$_POST['2'].",".$pr.")");
            }
            echo "<script Language=\"JavaScript\">";
            echo "alert(\"Totoal price: $pr\\nPurchase succeeds!\");";
            echo "</script>";
            OCICommit($db_conn);
        }
        if($_POST['3']>0){
            $pr=$_POST['3']*3.59;
            executePlainSQL("insert into Purchase_info values ('".$id."','Spaghetti',".$_POST['3'].")");
            $result=executePlainSQL("select * from Purchase_price where Ingredient_name='Spaghetti' AND Amount=".$_POST['3']);
            if(($nrows=oci_fetch_all($result, $results))==0){
                executePlainSQL("insert into Purchase_price values ('Spaghetti',".$_POST['3'].",".$pr.")");
            }
            echo "<script Language=\"JavaScript\">";
            echo "alert(\"Totoal price: $pr\\nPurchase succeeds!\");";
            echo "</script>";
            OCICommit($db_conn);
        }
        if($_POST['4']>0){
            $pr=$_POST['4']*4.98;
            executePlainSQL("insert into Purchase_info values ('".$id."','Chicken',".$_POST['4'].")");
            $result=executePlainSQL("select * from Purchase_price where Ingredient_name='Chicken' AND Amount=".$_POST['4']);
            if(($nrows=oci_fetch_all($result, $results))==0){
                executePlainSQL("insert into Purchase_price values ('Chicken',".$_POST['4'].",".$pr.")");
            }
            echo "<script Language=\"JavaScript\">";
            echo "alert(\"Totoal price: $pr\\nPurchase succeeds!\");";
            echo "</script>";
            OCICommit($db_conn);
        }
        if($_POST['5']>0){
            $pr=$_POST['5']*1.99;
            executePlainSQL("insert into Purchase_info values ('".$id."','Potato',".$_POST['5'].")");
            $result=executePlainSQL("select * from Purchase_price where Ingredient_name='Potato' AND Amount=".$_POST['5']);
            if(($nrows=oci_fetch_all($result, $results))==0){
            executePlainSQL("insert into Purchase_price values ('Potato',".$_POST['5'].",".$pr.")");
            }
            echo "<script Language=\"JavaScript\">";
            echo "alert(\"Totoal price: $pr\\nPurchase succeeds!\");";
            echo "</script>";
            OCICommit($db_conn);
        }
    }
    disconnectFromDB();
    ?>
</html>

