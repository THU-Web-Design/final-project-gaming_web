<?php
    include("connect.php");

    // Check if anyone is logged in
    session_start();

    if (!isset($_SESSION['user']) && !isset($_SESSION['company'])) {
        header("Location: Login.php"); // Redirect to login if not logged in
        exit();
    }

    // Access user or company data from the session
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
    $company = isset($_SESSION['company']) ? $_SESSION['company'] : null;

    $allGames = mysqli_query($conn, "SELECT Gid, Gname, Price, Pic1 FROM Game");

    // Logout functionality
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
        // Destroy the session and redirect to logout page
        session_destroy();
        header("Location: Logout.php");
        exit();
    }

    // Handle adding to cart
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart'])) {
        if ($user) {
            // User is logged in, proceed with adding to the cart
            $Gid = filter_input(INPUT_POST, "Gid", FILTER_SANITIZE_SPECIAL_CHARS);
            $Gname = filter_input(INPUT_POST, "Gname", FILTER_SANITIZE_SPECIAL_CHARS);
            $Price = filter_input(INPUT_POST, "Price", FILTER_SANITIZE_SPECIAL_CHARS);
    
            // Insert into the Cart table
            $stmt = mysqli_prepare($conn, "INSERT INTO Cart (Uid, Gid, Gname, Price) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $user['Uid'], $Gid, $Gname, $Price);
    
            if (mysqli_stmt_execute($stmt)) {
                echo "加入購物車成功！";
            } else {
                echo "Error adding to cart: " . mysqli_stmt_error($stmt);
            }
    
            mysqli_stmt_close($stmt);
        } else {
            // Redirect to login if user is not logged in
            header("Location: Login.php");
            exit();
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['infoButton'])) {
        $selectedGameId = filter_input(INPUT_POST, "selectedGameId", FILTER_SANITIZE_SPECIAL_CHARS);
        $selectedGame = getGameInfo($selectedGameId);

        // Redirect to GamePage.php with selected game information
        header("Location: GamePage.php?Gid={$selectedGame['Gid']}&Gname={$selectedGame['Gname']}&Class1={$selectedGame['Class1']}&Class2={$selectedGame['Class2']}&Price={$selectedGame['Price']}&Info={$selectedGame['Info']}");
        exit();
    }

    // Function to retrieve game information by Gid
    function getGameInfo($gameId) {
        global $conn;
        $result = mysqli_query($conn, "SELECT * FROM Game WHERE Gid = '$gameId'");
        return mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gamingpass</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div>
            <?php
                if ($user) {
                    echo '<form method="post" action="User.php">';
                    echo '<input type="submit" class="back-to-user-btn" value="返回個人頁面">';
                    echo '</form>';
                }
            ?>

            <span class="buying-btn" onclick="GotoCart()">購物車</span>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="submit" name="logout" class="logout-btn" value="登出">
            </form>
        </div>
        <h1>Gamingpass</h1>
    </header>

    <section>
    <?php
        while ($row = mysqli_fetch_assoc($allGames)) {
            $gameId = $row['Gid'];
            $isInCart = isGameInCart($user['Uid'], $gameId);
            $isInUserGameStore = isGameInUserGameStore($user['Uid'], $gameId);

            echo '<div class="game">';
            echo '<img src="data:image;base64,' . base64_encode($row['Pic1']) . '" alt="Game Image" style="max-width: 150px; max-height: 150px;">';
            echo '<h2>' . $row['Gname'] . '</h2>';
            echo '<p>價格: $' . $row['Price'] . '</p>';

            // Display warning if the game is in cart or User_Game_Store
            if ($isInCart || $isInUserGameStore) {
                echo '<p style="color: red;">This game is already in your cart or library</p>';
            } else {
                echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="selectedGameId" value="' . $row['Gid'] . '">';
                echo '<input type="submit" name="infoButton" value="Info">';
                echo '</form>';

                //The "Add to Cart" button
                echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="Gid" value="' . $row['Gid'] . '">';
                echo '<input type="hidden" name="Gname" value="' . $row['Gname'] . '">';
                echo '<input type="hidden" name="Price" value="' . $row['Price'] . '">';
                echo '<input type="submit" name="addToCart" value="加入購物車">';
                echo '</form>';
            }
            echo '</div>';
            
        }
        

        // Function to check if a game is in the cart
        function isGameInCart($userId, $gameId) {
            global $conn;
            $result = mysqli_query($conn, "SELECT * FROM Cart WHERE Uid = '$userId' AND Gid = '$gameId'");
            return mysqli_num_rows($result) > 0;
        }

        // Function to check if a game is in User_Game_Store
        function isGameInUserGameStore($userId, $gameId) {
            global $conn;
            $result = mysqli_query($conn, "SELECT * FROM User_Game_Store WHERE Uid = '$userId' AND Gid = '$gameId'");
            return mysqli_num_rows($result) > 0;
        }
    ?>
    </section>

    <footer>
        <p>&copy; 2023 遊戲商店. All rights reserved.</p>
    </footer>

    <script>
        function GotoCart() {
            window.location.href = 'Cart.php';
        }
    </script>
</body>
</html>
