<?php
    // Assuming you have the necessary connection and session handling code here
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        header("Location: Login.php"); // Redirect to login if not logged in
        exit();
    }

    // Access user data from the session
    $user = $_SESSION['user'];

    // Sample success message (customize as needed)
    $successMessage = "Congratulations! You have successfully bought the game.";

    // Handle going back to the cart page
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['backToCart'])) {
        header("Location: Cart.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Success</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Buy Success</h1>
    </header>

    <section>
    <p><?php echo $successMessage; ?></p>
    <form method="post" action="Cart.php">
        <input type="submit" name="backToCart" value="Back to Cart">
    </form>
    </section>

    <footer>
        <p>&copy; 2023 Game Store. All rights reserved.</p>
    </footer>
</body>
</html>
