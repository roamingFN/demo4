<?php
    // connection to the db
    include 'connect.php';

    $email = mysql_real_escape_string($_POST['email']); // $_POST is an array (not a function)
    // mysql_real_escape_string is to prevent sql injection

    $sql = "select customer_email from customer where customer_email='".$email."'"; // Username must enclosed in two quotations

    $query = mysql_query($sql);

    if(mysql_num_rows($query) == 0)
    {
        echo('USER_AVAILABLE');
    }
    else
    {
        echo('USER_EXISTS');
    }
?>