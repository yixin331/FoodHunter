
<html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body style=" background-color:#FFEBCD">
<form method="POST">
<font color="#FFEBCD"><p>d</p></font>
     <p><img src="kok.gif" width="70" height="40">&nbsp&nbsp&nbsp<font color="black" size="100" face="Phosphate">Current &nbsp&nbsp&nbsp MEMBERS&nbsp&nbsp&nbsp</font>
<img src="kok.gif" width="110" height="40"><img src="kok.gif" width="110" height="40"><img src="kok.gif" width="110" height="40"><img src="kok.gif" width="110" height="40"><img src="kok.gif" width="110" height="40"><img src="kok.gif" width="110" height="40">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="submit" value="Log out" name="logout" style="width:70px;height:35px;color:white;background-color:black;border:3px white double">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="submit" value="My profile" name="profile" style="width:100px;height:35px;color:white;background-color:black;border:3px white double"></p>
    <font color="black" size="5"><label><input name="R" type="radio" value="Haidilao" >Haidilao Hotpot&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
    <label><input name="R" type="radio" value="MissKorea" >Miss Korea BBQ&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
    <label><input name="R" type="radio" value="PacificPoke" >Pacific Poke&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
    <label><input name="R" type="radio" value="CactusClubCafe">Cactus Club Cafe&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
    <label><input name="R" type="radio" value="ChurchChicken" >Church Chicken</label></font><font color="#FFEBCD"><p>d</p></font>

<font size="4"><p><input type="submit" value="Place Order" name="place" style="width:200px;height:35px;color:white;background-color:black;border:3px white double">
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<input type="submit" value="Restaurant information" name="info" style="width:160px;height:35px;color:white;background-color:black;border:3px white double">
<select name="sisisi" style="width:80px;height:35px;color:white;background-color:black">
<option value ="A">Address</option>
<option value ="P">Phone</option>
<option value="T">Type</option>
<option value="Z">Zip</option>
<option value ="O">Open time</option>
<option value ="Av">Average rating</option>
</select>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<input type="submit" value="Delete account" name="daccount" style="width:120px;height:35px;color:white;background-color:black;border:3px white double">&nbsp<input type="submit" value="Change password" name=change style="width:130px;height:35px;color:white;background-color:black;border:3px white double">
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="submit" value="Rating" name="rating"    style="width:150px;height:35px;color:white;background-color:black;border:3px white double">&nbsp
<select name="select" style="width:30px;height:35px;color:white;background-color:black">
<option value ="1">1&nbsp(Bad)</option>
<option value ="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value ="5">5&nbsp(Good)</option>
</select></font>
</form>



<font color="#FFEBCD"><p>d</p></font><font color="#FFEBCD"><p>d</p></font>
<p><img src="add.png" width="1400" height="400"></p>

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
    


    if(isset($_POST['daccount'])){
        if(connectToDB()){
        $arg = $_SERVER["QUERY_STRING"]."<br>";
        $curr = substr($arg,0,strlen($arg)-4);
        $url="https://www.students.cs.ubc.ca/~yixin331/delete.php?".$curr;
        echo "<script LANGUAGE=\"Javascript\">";
        echo "location.href='$url'";
        echo "</script>";
        }
    }


    if(isset($_POST['change'])){
        if(connectToDB()){
            $arg = $_SERVER["QUERY_STRING"]."<br>";
            $curr = substr($arg,0,strlen($arg)-4);
            $url="https://www.students.cs.ubc.ca/~yixin331/reset.php?".$curr;
            echo "<script LANGUAGE=\"Javascript\">";
            echo "location.href='$url'";
            echo "</script>";
            }
    }

    if(isset($_POST['place'])){
        if(connectToDB()){
            $arg = $_SERVER["QUERY_STRING"]."<br>";
            $curr = substr($arg,0,strlen($arg)-4);
            $r=$_POST['R'];
            if($r=="Haidilao"){
                $url="https://www.students.cs.ubc.ca/~yixin331/placeOrder.php?".$curr."+Haidilao";
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";}
            if($r=="MissKorea"){
                $url="https://www.students.cs.ubc.ca/~yixin331/placeOrder.php?".$curr."+MissKorea";
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";}
            if($r=="PacificPoke"){
                $url="https://www.students.cs.ubc.ca/~yixin331/placeOrder.php?".$curr."+PacificPoke";
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";}
            if($r=="CactusClubCafe"){
                $url="https://www.students.cs.ubc.ca/~yixin331/placeOrder.php?".$curr."+CactusClubCafe";
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";}
            if($r=="ChurchChicken"){
                $url="https://www.students.cs.ubc.ca/~yixin331/placeOrder.php?".$curr."+ChurchChicken";
                echo "<script LANGUAGE=\"Javascript\">";
                echo "location.href='$url'";
                echo "</script>";}
            }
    }




    if(isset($_POST['rating'])){
        if(connectToDB()){
        $v=$_POST['select'];
        $r=$_POST['R'];
        $arg=$_SERVER["QUERY_STRING"]."<br>";
        $id=substr($arg,0,strlen($arg)-4);
            if($r=="Haidilao"){
                $result=executePlainSQL("SELECT Customer_ID FROM Review WHERE Restaurant_name='Haidilao Hotpot' AND Customer_ID='".$id."'");
            }
            if($r=="MissKorea"){
                $result=executePlainSQL("SELECT Customer_ID FROM Review WHERE Restaurant_name='Miss Korea BBQ' AND Customer_ID='".$id."'");
            }
            if($r=="PacificPoke"){
                $result=executePlainSQL("SELECT Customer_ID FROM Review WHERE Restaurant_name='Pacific Poke' AND Customer_ID='".$id."'");
            }
            if($r=="CactusClubCafe"){
                $result=executePlainSQL("SELECT Customer_ID FROM Review WHERE Restaurant_name='Cactus Club Cafe' AND Customer_ID='".$id."'");
            }
            if($r=="ChurchChicken"){
                $result=executePlainSQL("SELECT Customer_ID FROM Review WHERE Restaurant_name='Church Chicken' AND Customer_ID='".$id."'");
            }
            
        if(($row = oci_fetch_row($result)) != false){
            if($r=="Haidilao"){
                executePlainSQL("Update Review set Rating=".$v." where Restaurant_name='Haidilao Hotpot' AND Customer_ID='".$id."'");
            }
            if($r=="MissKorea"){
                executePlainSQL("Update Review set Rating=".$v." where Restaurant_name='Miss Korea BBQ' AND Customer_ID='".$id."'");
            }
            if($r=="PacificPoke"){
                executePlainSQL("Update Review set Rating=".$v." where Restaurant_name='Pacific Poke' AND Customer_ID='".$id."'");
            }
            if($r=="CactusClubCafe"){
                executePlainSQL("Update Review set Rating=".$v." where Restaurant_name='Cactus Club Cafe' AND Customer_ID='".$id."'");
            }
            if($r=="ChurchChicken"){
                executePlainSQL("Update Review set Rating=".$v." where Restaurant_name='Church Chicken' AND Customer_ID='".$id."'");
            }
        }
        else{
            if($r=="Haidilao"){
                executePlainSQL("insert into Review values ('Haidilao Hotpot','".$id."',".$v.")");
            }
            if($r=="MissKorea"){
                executePlainSQL("insert into Review values ('Miss Korea BBQ','".$id."',".$v.")");
            }
            if($r=="PacificPoke"){
                executePlainSQL("insert into Review values ('Pacific Poke','".$id."',".$v.")");
            }
            if($r=="CactusClubCafe"){
                executePlainSQL("insert into Review values ('Cactus Club Cafe','".$id."',".$v.")");
            }
            if($r=="ChurchChicken"){
                executePlainSQL("insert into Review values ('Church Chicken','".$id."',".$v.")");
            }
        }OCICommit($db_conn);  disconnectFromDB();}
    }

    if(isset($_POST['logout'])){
        $url="https://www.students.cs.ubc.ca/~yixin331/h.php";
        echo "<script language=\"JavaScript\">";
        echo "var a=window.confirm(\"Sure to log out?\");";
        echo "if (a) {location.href='$url'} else {};";
        echo "</script>";
    }

    if(isset($_POST['profile'])){
        $arg = $_SERVER["QUERY_STRING"]."<br>";
        $curr = substr($arg,0,strlen($arg)-4);
        $url="https://www.students.cs.ubc.ca/~yixin331/user.php?".$curr;
        echo "<script language=\"JavaScript\">";
        echo "location.href='$url'";
        echo "</script>";
    }
    
    if(isset($_POST['info'])){
        if(connectToDB()){
            $v=$_POST['sisisi'];
            $r=$_POST['R'];
        
            if($r=="Haidilao"){
                if($v=="A"){
                    $result=executePlainSQL("select Address from Restaurant_info where Name='Haidilao Hotpot'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Address of Haidilao Hotpot:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="P"){
                    $result=executePlainSQL("select Phone from Restaurant_info where Name='Haidilao Hotpot'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Phone of Haidilao Hotpot:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="T"){
                    $result=executePlainSQL("select Type from Restaurant_info where Name='Haidilao Hotpot'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Type of Haidilao Hotpot:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Z"){
                    $re=executePlainSQL("select Address from Restaurant_info where Name='Haidilao Hotpot'");
                    $address=oci_fetch_row($re);
                    $result=executePlainSQL("select Postcode from Restaurant_address where Address='".$address[0]."'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Zip code of Haidilao Hotpot:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="O"){
                    $result=executePlainSQL("select Open_hours from Restaurant_info where Name='Haidilao Hotpot'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Open time of Haidilao Hotpot:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Av"){
                    $result=executePlainSQL("select AVG(Rating) from Review where Restaurant_name='Haidilao Hotpot'");
                    $row=oci_fetch_row($result);
                    $n=round($row[0],2);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Average rating of Haidilao Hotpot:\\n $n\");";
                    echo "</script>";
                }
            }
            if($r=="MissKorea"){
                if($v=="A"){
                    $result=executePlainSQL("select Address from Restaurant_info where Name='Miss Korea BBQ'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Address of Miss Korea BBQ:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="P"){
                    $result=executePlainSQL("select Phone from Restaurant_info where Name='Miss Korea BBQ'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Phone of Miss Korea BBQ:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="T"){
                    $result=executePlainSQL("select Type from Restaurant_info where Name='Miss Korea BBQ'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Type of Miss Korea BBQ:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Z"){
                    $re=executePlainSQL("select Address from Restaurant_info where Name='Miss Korea BBQ'");
                    $address=oci_fetch_row($re);
                    $result=executePlainSQL("select Postcode from Restaurant_address where Address='".$address[0]."'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Zip code of Miss Korea BBQ:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="O"){
                    $result=executePlainSQL("select Open_hours from Restaurant_info where Name='Miss Korea BBQ'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Open time of Miss Korea BBQ:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Av"){
                    $result=executePlainSQL("select AVG(Rating) from Review where Restaurant_name='Miss Korea BBQ'");
                    $row=oci_fetch_row($result);
                    $n=round($row[0],2);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Average rating of Miss Korea BBQ:\\n $n\");";
                    echo "</script>";
                }
            }
            if($r=="PacificPoke"){
                if($v=="A"){
                    $result=executePlainSQL("select Address from Restaurant_info where Name='Pacific Poke'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Address of Pacific Poke:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="P"){
                    $result=executePlainSQL("select Phone from Restaurant_info where Name='Pacific Poke'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Phone of Pacific Poke:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="T"){
                    $result=executePlainSQL("select Type from Restaurant_info where Name='Pacific Poke'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Type of Pacific Poke:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Z"){
                    $re=executePlainSQL("select Address from Restaurant_info where Name='Pacific Poke'");
                    $address=oci_fetch_row($re);
                    $result=executePlainSQL("select Postcode from Restaurant_address where Address='".$address[0]."'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Zip code of Pacific Poke:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="O"){
                    $result=executePlainSQL("select Open_hours from Restaurant_info where Name='Pacific Poke'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Open time of Pacific Poke:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Av"){
                    $result=executePlainSQL("select AVG(Rating) from Review where Restaurant_name='Pacific Poke'");
                    $row=oci_fetch_row($result);
                    $n=round($row[0],2);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Average rating of Pacific Poke:\\n $n\");";
                    echo "</script>";
                }
            }
            if($r=="CactusClubCafe"){
                if($v=="A"){
                    $result=executePlainSQL("select Address from Restaurant_info where Name='Cactus Club Cafe'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Address of Cactus Club Cafe:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="P"){
                    $result=executePlainSQL("select Phone from Restaurant_info where Name='Cactus Club Cafe'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Phone of Cactus Club Cafe:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="T"){
                    $result=executePlainSQL("select Type from Restaurant_info where Name='Cactus Club Cafe'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Type of Cactus Club Cafe:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Z"){
                    $re=executePlainSQL("select Address from Restaurant_info where Name='Cactus Club Cafe'");
                    $address=oci_fetch_row($re);
                    $result=executePlainSQL("select Postcode from Restaurant_address where Address='".$address[0]."'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Zip code of Cactus Club Cafe:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="O"){
                    $result=executePlainSQL("select Open_hours from Restaurant_info where Name='Cactus Club Cafe'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Open time of Cactus Club Cafe:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Av"){
                    $result=executePlainSQL("select AVG(Rating) from Review where Restaurant_name='Cactus Club Cafe'");
                    $row=oci_fetch_row($result);
                    $n=round($row[0],2);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Average rating of Cactus Club Cafe:\\n $n\");";
                    echo "</script>";
                }
            }
            if($r=="ChurchChicken"){
                if($v=="A"){
                    $result=executePlainSQL("select Address from Restaurant_info where Name='Church Chicken'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Address of Cactus Church Chicken:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="P"){
                    $result=executePlainSQL("select Phone from Restaurant_info where Name='Church Chicken'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Phone of Cactus Church Chicken:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="T"){
                    $result=executePlainSQL("select Type from Restaurant_info where Name='Church Chicken'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Type of Church Chicken:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Z"){
                    $re=executePlainSQL("select Address from Restaurant_info where Name='Church Chicken'");
                    $address=oci_fetch_row($re);
                    $result=executePlainSQL("select Postcode from Restaurant_address where Address='".$address[0]."'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Zip code of Church Chicken:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="O"){
                    $result=executePlainSQL("select Open_hours from Restaurant_info where Name='Church Chicken'");
                    $row=oci_fetch_row($result);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Open time of Church Chicken:\\n $row[0]\");";
                    echo "</script>";
                }
                if($v=="Av"){
                    $result=executePlainSQL("select AVG(Rating) from Review where Restaurant_name='Church Chicken'");
                    $row=oci_fetch_row($result);
                    $n=round($row[0],2);
                    echo "<script Language=\"JavaScript\">";
                    echo "alert(\"Average rating of Church Chicken:\\n $n\");";
                    echo "</script>";
                }
            }
            OCICommit($db_conn);  disconnectFromDB();
        }
    }
    
    
    ?>
</body>


</html>

