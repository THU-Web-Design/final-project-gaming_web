<?php
    include("connect.php");

    // Check if the user is logged in
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: Login.php"); // Redirect to login if not logged in
        exit();
    }

    // Access user data from the session
    $user = $_SESSION['user'];

    // Retrieve cart items for the user
    $cartItems = mysqli_query($conn, "SELECT Gid, Gname, Price FROM Cart WHERE Uid = '{$user['Uid']}'");

    // Handle buying items
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buyItem'])) {
        $Gid = filter_input(INPUT_POST, "Gid", FILTER_SANITIZE_SPECIAL_CHARS);
        $Gname = filter_input(INPUT_POST, "Gname", FILTER_SANITIZE_SPECIAL_CHARS);
        $Price = filter_input(INPUT_POST, "Price", FILTER_SANITIZE_SPECIAL_CHARS);

        // Move the game from the cart to the User_Game_Store table
        $moveToStoreStmt = mysqli_prepare($conn, "INSERT INTO User_Game_Store (Uid, Gid, Gname) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($moveToStoreStmt, "sss", $user['Uid'], $Gid, $Gname);

        if (mysqli_stmt_execute($moveToStoreStmt)) {
            // Item moved to User_Game_Store, now remove it from the Cart
            $removeFromCartStmt = mysqli_prepare($conn, "DELETE FROM Cart WHERE Uid = ? AND Gid = ?");
            mysqli_stmt_bind_param($removeFromCartStmt, "ss", $user['Uid'], $Gid);

            if (mysqli_stmt_execute($removeFromCartStmt)) {
                header("Location: BuySuccess.php");
                exit();
            } else {
                echo "Error removing item from cart: " . mysqli_stmt_error($removeFromCartStmt);
            }

            mysqli_stmt_close($removeFromCartStmt);
        } else {
            echo "Error moving item to User_Game_Store: " . mysqli_stmt_error($moveToStoreStmt);
        }

        mysqli_stmt_close($moveToStoreStmt);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteItem'])) {
        $Gid = filter_input(INPUT_POST, "Gid", FILTER_SANITIZE_SPECIAL_CHARS);

        // Remove the game from the cart
        $removeFromCartStmt = mysqli_prepare($conn, "DELETE FROM Cart WHERE Uid = ? AND Gid = ?");
        mysqli_stmt_bind_param($removeFromCartStmt, "ss", $user['Uid'], $Gid);

        if (mysqli_stmt_execute($removeFromCartStmt)) {
            // Item removed from the cart, refresh the page
            header("Location: Cart.php");
            exit();
        } else {
            echo "Error removing item from cart: " . mysqli_stmt_error($removeFromCartStmt);
        }

        mysqli_stmt_close($removeFromCartStmt);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="CartStyle.css">
</head>

<body>
    <header>
        <h1>Shopping Cart</h1>
        <div>
            <!-- Display username -->
            <label>Welcome, <?php echo $user['Uname']; ?>!</label>
            <!-- Button to go back to the home page -->
            <form method="post" action="Home.php">
                <input type="submit" value="Back to Home">
            </form>
        </div>
    </header>

    <section>
        <?php
            // Display cart items
            while ($cartItem = mysqli_fetch_assoc($cartItems)) {
                echo '<div class="game-item">';
                echo '<div class="game-info">';
                echo '<h2>' . $cartItem['Gname'] . '</h2>';
                echo '<p>Price: $' . $cartItem['Price'] . '</p>';
                echo '</div>';
                
                // Add "Buy" button
                echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="Gid" value="' . $cartItem['Gid'] . '">';
                echo '<input type="hidden" name="Gname" value="' . $cartItem['Gname'] . '">';
                echo '<input type="hidden" name="Price" value="' . $cartItem['Price'] . '">';
                echo '<button class="animated-button" type="submit" name="buyItem">'; // Add name attribute for identification
                echo '<svg viewBox="0 0 24 24" class="arr-2" xmlns="http://www.w3.org/2000/svg">';
                echo '<path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"></path>';
                echo '</svg>';
                echo '<span class="text">BUY</span>';
                echo '<span class="circle"></span>';
                echo '<svg viewBox="0 0 24 24" class="arr-1" xmlns="http://www.w3.org/2000/svg">';
                echo '<path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"></path>';
                echo '</svg>';
                echo '</button>';
                echo '</form>';

                // Add "Delete" button
                echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="Gid" value="' . $cartItem['Gid'] . '">';
                echo '<button type="submit" class="button" name="deleteItem">Delete</button>';
                echo '</form>';

                echo '</div>';
            }
        ?>
    </section>

    <footer>
        <p>&copy; 2023 遊戲商店. All rights reserved.</p>
    </footer>
</body>
</html>
