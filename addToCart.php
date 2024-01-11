<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Gid = $_POST['Gid'];
    $Gname = $_POST['Gname'];
    $Price = $_POST['Price'];

    // Insert into the Cart table
    $stmt = mysqli_prepare($conn, "INSERT INTO Cart (Uid, Gid, Gname, Price) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $user['Uid'], $Gid, $Gname, $Price);

    if (mysqli_stmt_execute($stmt)) {
        echo "Game added to the cart successfully!";
    } else {
        echo "Error adding to cart: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}
?>
