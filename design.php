<html>
    <head>
        <title>Food Hunter</title>

    </head>

    <body background="kale.png">


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
    function executePlainSQL($cmdstr) { 
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
    
    
    $arg=$_SERVER["QUERY_STRING"]."<br>";
    $array=explode('+',$arg);
    $res=substr($array[1],0,strlen($array[1])-4);

    global $db_conn;
    if($res=="Haidilao"){
        connectToDB();
        $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Haidilao Hotpot' AND Dish_name=Name");
        if (($nrows = oci_fetch_all($result, $results)) != 0) {
            if ($nrows > 0) {
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($results as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "<th>New Price</th>\n";
                echo "<th>Delete</th>\n";
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($results as $data) {
                        //echo "<br>";
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "<td><input type=\"text\" name=\"".$i."\" value=\"\" style=\"width:75;height:30\"></td>\n";
                    echo "<td><input type=\"radio\" name=\"weeks\" value=\"".($i+1)."\" style=\"width:50;height:30\"></td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br>";
                echo "<br>";
                echo "<center>";
                echo "<br>";
                echo "<br>";
                echo "<input type='submit' value='Confirm' name='sub' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
                echo "<input type='submit' value='Back' name='back' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\"></form>";
                echo "</center>";
            }
            if(!empty($_POST)){
                if(!empty($_POST['back'])){
                   $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$array[0];
                    echo "<script language=\"JavaScript\">";
                    echo "var a=window.confirm(\"Sure to give up designing?\");";
                    echo "if (a) {location.href='$url'} else {};";
                    echo "</script>";}
                 if(isset($_POST['sub'])){
                 $dishna;
                 $type;
                 $version;
                     $price;
                    $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Haidilao Hotpot' AND Dish_name=Name");
                    if (($nrows = oci_fetch_all($result, $results)) != 0) {
                        if ($nrows > 0) {
                            $j=0;
                                foreach ($results as $data) {
                                    if($j==0){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $dishna[$i]=$data[$i];
                                    }
                                        $j++;     }
                                    else if($j==1){
                                        for ($i = 0; $i < $nrows; $i++) {
                                            $version[$i]=$data[$i];     }
                                        $j++;     }else if($j==2){
                                        for ($i = 0; $i < $nrows; $i++) {
                                            $type[$i]=$data[$i];   }
                                        $j++;     }else if($j==3){
                                        for ($i = 0; $i < $nrows; $i++) {
                                            $price[$i]=$data[$i];
                                        }
                                        $j++;
                                    }}}}
                     for($i=0;$i<$nrows;$i++){
                         if(!empty($_POST[$i])&&!is_numeric($_POST[$i])){
                             echo "<script language=\"JavaScript\">";
                             echo "alert(\"Please enter a positive integer!\");";
                             echo "location.replace(document.referrer);";
                             echo "</script>";
                         }
                         if(is_numeric($_POST[$i])&&$_POST[$i]<0){
                             echo "<script language=\"JavaScript\">";
                             echo "alert(\"Please enter a positive integer!\");";
                             echo "location.replace(document.referrer);";
                             echo "</script>";
                         }
                         if($_POST[$i]>0){
                             executePlainSQL("Update Consist_of set Price=".$_POST[$i]." where Dish_name='".$dishna[$i]."' AND Menu_rest_name='Haidilao Hotpot'");
                             executePlainSQL("insert into Design values ('".$array[0]."','Haidilao Hotpot','".$type[$i]."',".$version[$i].",NULL)");
                         }
                     }
                     if($_POST['weeks']>0){
                         $_POST['weeks']=$_POST['weeks']-1;
                         executePlainSQL("delete from Dish where Name='".$dishna[$_POST['weeks']]."'");
                         executePlainSQL("insert into Design values ('".$array[0]."','Haidilao Hotpot','".$type[$_POST['weeks']]."',".$version[$_POST['weeks']].",NULL)");
                     }
                if(!empty($_POST)){
                    $url="https://www.students.cs.ubc.ca/~yixin331/design.php?".$array[0]."+".$res;
                    echo "<script LANGUAGE=\"Javascript\">";
                    echo "location.href='$url'";
                    echo "</script>";
                }

                 }
            }
        }
        OCICommit($db_conn);
    }
    if($res=="MissKorea"){
        connectToDB();
        $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Miss Korea BBQ' AND Dish_name=Name");
        if (($nrows = oci_fetch_all($result, $results)) != 0) {
            if ($nrows > 0) {
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($results as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "<th>New Price</th>\n";
                echo "<th>Delete</th>\n";
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($results as $data) {
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "<td><input type=\"text\" name=\"".$i."\" value=\"\" style=\"width:75;height:30\"></td>\n";
                    echo "<td><input type=\"radio\" name=\"weeks\" value=\"".($i+1)."\" style=\"width:50;height:30\"></td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br>";
                echo "<br>";
                echo "<center>";
                echo "<br>";
                echo "<br>";
                echo "<input type='submit' value='Confirm' name='sub' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
                echo "<input type='submit' value='Back' name='back' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\"></form>";
                echo "</center>";
            }
            if(!empty($_POST)){
                if(!empty($_POST['back'])){
                    $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$array[0];
                    echo "<script language=\"JavaScript\">";
                    echo "var a=window.confirm(\"Sure to give up designing?\");";
                    echo "if (a) {location.href='$url'} else {};";
                    echo "</script>";}
                if(isset($_POST['sub'])){
                    $dishna;
                    $type;
                    $version;
                    $price;
                    $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Miss Korea BBQ' AND Dish_name=Name");
                    if (($nrows = oci_fetch_all($result, $results)) != 0) {
                        if ($nrows > 0) {
                            $j=0;
                            foreach ($results as $data) {
                                if($j==0){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $dishna[$i]=$data[$i];
                                    }
                                    $j++;   }
                                else if($j==1){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $version[$i]=$data[$i];   }
                                    $j++;
                                }else if($j==2){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $type[$i]=$data[$i];   }
                                    $j++;
                                }else if($j==3){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $price[$i]=$data[$i];  }
                                    $j++;
                                }}}}
                    for($i=0;$i<$nrows;$i++){
                        if(!empty($_POST[$i])&&!is_numeric($_POST[$i])){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if(is_numeric($_POST[$i])&&$_POST[$i]<0){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if($_POST[$i]>0){
                            executePlainSQL("Update Consist_of set Price=".$_POST[$i]." where Dish_Name='".$dishna[$i]."' AND Menu_rest_name='Miss Korea BBQ'");
                            executePlainSQL("insert into Design values ('".$array[0]."','Miss Korea BBQ','".$type[$i]."',".$version[$i].",NULL)");
                        }
                    }
                    if($_POST['weeks']>0){
                        $_POST['weeks']=$_POST['weeks']-1;
                        executePlainSQL("delete from Consist_of where Dish_name='".$dishna[$_POST['weeks']]."' AND Menu_rest_name='Miss Korea BBQ'");
                        executePlainSQL("insert into Design values ('".$array[0]."','Miss Korea BBQ','".$type[$_POST['weeks']]."',".$version[$_POST['weeks']].",NULL)");
                    }
                    if(!empty($_POST)){
                        $url="https://www.students.cs.ubc.ca/~yixin331/design.php?".$array[0]."+".$res;
                        echo "<script LANGUAGE=\"Javascript\">";
                        echo "location.href='$url'";
                        echo "</script>";
                    }
                    
                }
            }
        }
        OCICommit($db_conn);
    }
    if($res=="PacificPoke"){
        connectToDB();
        $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Pacific Poke' AND Dish_name=Name");
        if (($nrows = oci_fetch_all($result, $results)) != 0) {
            if ($nrows > 0) {
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($results as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "<th>New Price</th>\n";
                echo "<th>Delete</th>\n";
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($results as $data) {
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "<td><input type=\"text\" name=\"".$i."\" value=\"\" style=\"width:75;height:30\"></td>\n";
                    echo "<td><input type=\"radio\" name=\"weeks\" value=\"".($i+1)."\" style=\"width:50;height:30\"></td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br>";
                echo "<br>";
                echo "<center>";
                echo "<br>";
                echo "<br>";
                echo "<input type='submit' value='Confirm' name='sub' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
                echo "<input type='submit' value='Back' name='back' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\"></form>";
                echo "</center>";
            }
            if(!empty($_POST)){
                if(!empty($_POST['back'])){
                    $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$array[0];
                    echo "<script language=\"JavaScript\">";
                    echo "var a=window.confirm(\"Sure to give up designing?\");";
                    echo "if (a) {location.href='$url'} else {};";
                    echo "</script>";}
                if(isset($_POST['sub'])){
                    $dishna;
                    $type;
                    $version;
                    $price;
                    $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Pacific Poke' AND Dish_name=Name");
                    if (($nrows = oci_fetch_all($result, $results)) != 0) {
                        if ($nrows > 0) {
                            $j=0;
                            foreach ($results as $data) {
                                if($j==0){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $dishna[$i]=$data[$i];     }
                                    $j++;        }
                                else if($j==1){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $version[$i]=$data[$i];    }
                                    $j++;
                                }else if($j==2){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $type[$i]=$data[$i];         }
                                    $j++;
                                }else if($j==3){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $price[$i]=$data[$i];   }
                                    $j++;
                                }}}}
                    for($i=0;$i<$nrows;$i++){
                        if(!empty($_POST[$i])&&!is_numeric($_POST[$i])){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if(is_numeric($_POST[$i])&&$_POST[$i]<0){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if($_POST[$i]>0){
                            executePlainSQL("Update Consist_of set Price=".$_POST[$i]." where Dish_Name='".$dishna[$i]."' AND Menu_rest_name='Pacific Poke'");
                            executePlainSQL("insert into Design values ('".$array[0]."','Pacific Poke','".$type[$i]."',".$version[$i].",NULL)");
                        }
                    }
                    if($_POST['weeks']>0){
                        $_POST['weeks']=$_POST['weeks']-1;
                        executePlainSQL("delete from Consist_of where Dish_name='".$dishna[$_POST['weeks']]."' AND Menu_rest_name='Pacific Poke'");
                        executePlainSQL("insert into Design values ('".$array[0]."','Pacific Poke','".$type[$_POST['weeks']]."',".$version[$_POST['weeks']].",NULL)");
                    }
                    if(!empty($_POST)){
                        $url="https://www.students.cs.ubc.ca/~yixin331/design.php?".$array[0]."+".$res;
                        echo "<script LANGUAGE=\"Javascript\">";
                        echo "location.href='$url'";
                        echo "</script>";
                    }
                    
                }
            }
        }
        OCICommit($db_conn);
    }
    if($res=="CactusClubCafe"){
        connectToDB();
        $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Cactus Club Cafe' AND Dish_name=Name");
        if (($nrows = oci_fetch_all($result, $results)) != 0) {
            if ($nrows > 0) {
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($results as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "<th>New Price</th>\n";
                echo "<th>Delete</th>\n";
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($results as $data) {
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "<td><input type=\"text\" name=\"".$i."\" value=\"\" style=\"width:75;height:30\"></td>\n";
                    echo "<td><input type=\"radio\" name=\"weeks\" value=\"".($i+1)."\" style=\"width:50;height:30\"></td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br>";
                echo "<br>";
                echo "<center>";
                echo "<br>";
                echo "<br>";
                echo "<input type='submit' value='Confirm' name='sub' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
                echo "<input type='submit' value='Back' name='back' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\"></form>";
                echo "</center>";
            }
            if(!empty($_POST)){
                if(!empty($_POST['back'])){
                    $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$array[0];
                    echo "<script language=\"JavaScript\">";
                    echo "var a=window.confirm(\"Sure to give up designing?\");";
                    echo "if (a) {location.href='$url'} else {};";
                    echo "</script>";}
                if(isset($_POST['sub'])){
                    $dishna;
                    $type;
                    $version;
                    $price;
                    $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Cactus Club Cafe' AND Dish_name=Name");
                    if (($nrows = oci_fetch_all($result, $results)) != 0) {
                        if ($nrows > 0) {
                            $j=0;
                            foreach ($results as $data) {
                                if($j==0){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $dishna[$i]=$data[$i];
                                    }
                                    $j++;  }
                                else if($j==1){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $version[$i]=$data[$i];  }
                                    $j++;
                                }else if($j==2){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $type[$i]=$data[$i]; }
                                    $j++;
                                }else if($j==3){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $price[$i]=$data[$i];      }
                                    $j++;
                                }}}}
                    for($i=0;$i<$nrows;$i++){
                        if(!empty($_POST[$i])&&!is_numeric($_POST[$i])){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if(is_numeric($_POST[$i])&&$_POST[$i]<0){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if($_POST[$i]>0){
                            executePlainSQL("Update Consist_of set Price=".$_POST[$i]." where Dish_Name='".$dishna[$i]."' AND Menu_rest_name='Cactus Club Cafe'");
                            executePlainSQL("insert into Design values ('".$array[0]."','Cactus Club Cafe','".$type[$i]."',".$version[$i].",NULL)");
                        }
                    }
                    if($_POST['weeks']>0){
                        $_POST['weeks']=$_POST['weeks']-1;
                        executePlainSQL("delete from Consist_of where Dish_name='".$dishna[$_POST['weeks']]."' AND Menu_rest_name='Cactus Club Cafe'");
                        executePlainSQL("insert into Design values ('".$array[0]."','Cactus Club Cafe','".$type[$_POST['weeks']]."',".$version[$_POST['weeks']].",NULL)");
                    }
                    if(!empty($_POST)){
                        $url="https://www.students.cs.ubc.ca/~yixin331/design.php?".$array[0]."+".$res;
                        echo "<script LANGUAGE=\"Javascript\">";
                        echo "location.href='$url'";
                        echo "</script>";
                    }
                    
                }
            }
        }
        OCICommit($db_conn);
    }
    if($res=="ChurchChicken"){
        connectToDB();
        $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Church Chicken' AND Dish_name=Name");
        if (($nrows = oci_fetch_all($result, $results)) != 0) {
            if ($nrows > 0) {
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo ("<center>");
                echo "<form method=\"POST\">";
                echo "<table border=\"1\">\n";
                echo "<tr>\n";
                foreach ($results as $key => $val) {
                    echo "<th>$key</th>\n";
                }
                echo "<th>New Price</th>\n";
                echo "<th>Delete</th>\n";
                echo "</tr>\n";
                
                for ($i = 0; $i < $nrows; $i++) {
                    echo "<tr>\n";
                    foreach ($results as $data) {
                        echo "<td>$data[$i]</td>\n";
                    }
                    echo "<td><input type=\"text\" name=\"".$i."\" value=\"\" style=\"width:75;height:30\"></td>\n";
                    echo "<td><input type=\"radio\" name=\"weeks\" value=\"".($i+1)."\" style=\"width:50;height:30\"></td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                echo "<br>";
                echo "<br>";
                echo "<center>";
                echo "<br>";
                echo "<br>";
                echo "<input type='submit' value='Confirm' name='sub' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
                echo "<input type='submit' value='Back' name='back' style=\"width:80px;height:25px;color:red;background-color:#FFd39b;border:3px orange double\"></form>";
                echo "</center>";
            }
            if(!empty($_POST)){
                if(!empty($_POST['back'])){
                    $url="https://www.students.cs.ubc.ca/~yixin331/manager.php?".$array[0];
                    echo "<script language=\"JavaScript\">";
                    echo "var a=window.confirm(\"Sure to give up designing?\");";
                    echo "if (a) {location.href='$url'} else {};";
                    echo "</script>";}
                if(isset($_POST['sub'])){
                    $dishna;
                    $type;
                    $version;
                    $price;
                    $result = executePlainSQL("SELECT Dish_name,Menu_version,Menu_type,Price FROM Consist_of,Dish WHERE Menu_rest_name='Church Chicken' AND Dish_name=Name");
                    if (($nrows = oci_fetch_all($result, $results)) != 0) {
                        if ($nrows > 0) {
                            $j=0;
                            foreach ($results as $data) {
                                if($j==0){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $dishna[$i]=$data[$i];
                                    }
                                    $j++;}
                                else if($j==1){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $version[$i]=$data[$i];
                                    }
                                    $j++; }else if($j==2){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $type[$i]=$data[$i];
                                    }
                                    $j++;
                                }else if($j==3){
                                    for ($i = 0; $i < $nrows; $i++) {
                                        $price[$i]=$data[$i];  }
                                    $j++;
                                }}}}
                    for($i=0;$i<$nrows;$i++){
                        if(!empty($_POST[$i])&&!is_numeric($_POST[$i])){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if(is_numeric($_POST[$i])&&$_POST[$i]<0){
                            echo "<script language=\"JavaScript\">";
                            echo "alert(\"Please enter a positive integer!\");";
                            echo "location.replace(document.referrer);";
                            echo "</script>";
                        }
                        if($_POST[$i]>0){
                            executePlainSQL("Update Consist_of set Price=".$_POST[$i]." where Dish_Name='".$dishna[$i]."' AND Menu_rest_name='Church Chicken'");
                            executePlainSQL("insert into Design values ('".$array[0]."','Church Chicken','".$type[$i]."',".$version[$i].",NULL)");
                        }
                    }
                    if($_POST['weeks']>0){
                        $_POST['weeks']=$_POST['weeks']-1;
                        executePlainSQL("delete from Consist_of where Dish_name='".$dishna[$_POST['weeks']]."' AND Menu_rest_name='Church Chicken'");
                        executePlainSQL("insert into Design values ('".$array[0]."','Church Chicken','".$type[$_POST['weeks']]."',".$version[$_POST['weeks']].",NULL)");
                    }
                    if(!empty($_POST)){
                        $url="https://www.students.cs.ubc.ca/~yixin331/design.php?".$array[0]."+".$res;
                        echo "<script LANGUAGE=\"Javascript\">";
                        echo "location.href='$url'";
                        echo "</script>";
                    }
                    
                }
            }
        }
        OCICommit($db_conn);
    }
    ?>
	</body>
</html>

