<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "GameSell";
    $conn = "";
    try{
        $conn = mysqli_connect($servername,
                               $username, 
                               $password, 
                               $dbname);
    }
    catch(mysqli_sql_exception){
        echo"Could not connect! <br>";
    }
?> 