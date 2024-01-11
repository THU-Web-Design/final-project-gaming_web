<?php
    include("connect.php");

    session_start(); // Resume the session

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        header("Location: Login.php"); // Redirect to login if not logged in
        exit();
    }

    // Access user data from the session
    $user = $_SESSION['user'];

        // Handle game deletion
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteGame'])) {
        $deleteGid = filter_input(INPUT_POST, "deleteGame", FILTER_SANITIZE_SPECIAL_CHARS);

        // Delete the game from User_Game_Store
        $deleteGameStmt = mysqli_prepare($conn, "DELETE FROM User_Game_Store WHERE Uid = ? AND Gid = ?");
        mysqli_stmt_bind_param($deleteGameStmt, "ss", $user['Uid'], $deleteGid);

        if (mysqli_stmt_execute($deleteGameStmt)) {
            echo "遊戲已刪除！";
            // Refresh the page after deletion
            header("Refresh:0");
        } else {
            echo "Error deleting game: " . mysqli_stmt_error($deleteGameStmt);
        }

        mysqli_stmt_close($deleteGameStmt);
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #171a21;
            margin: 0;
            padding: 20px;
            color: #ffffff;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header img {
            max-width: 100px;
            height: auto;
            border-radius: 50%;
            margin-right: 10px;
        }

        header h1 {
            margin-top: 10px;
            font-size: 24px;
        }

        header a {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
        }

        section {
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            padding: 10px;
            margin: 20px;
        }

        h2 {
            color: #66c0f4;
            border-bottom: 2px solid #66c0f4;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        li img {
            max-width: 50px;
            height: auto;
            margin-right: 10px;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            color: #a4a4a4;
        }
    </style>
</head>
<body>
    <header>
        <div>
            <img src="path/to/avatar.jpg" alt="My Avatar">
            <h1><?php echo $user['Uname']; ?></h1>
        </div>
        <a href="logout.php">Logout</a>
    </header>

    <form method="post" action="Home.php">
        <input type="submit" value="Home">
    </form>

    <section>
    <h2>My Games</h2>
    <ul>
        <?php
            // Fetch user's games from User_Game_Store
            $userGamesQuery = mysqli_query($conn, "SELECT G.Gid, G.Gname, G.Pic1
                                                  FROM User_Game_Store UGS
                                                  JOIN Game G ON UGS.Gid = G.Gid
                                                  WHERE UGS.Uid = '{$user['Uid']}'");

            while ($game = mysqli_fetch_assoc($userGamesQuery)) {
                echo '<li>';
                echo '<img src="data:image;base64,' . base64_encode($game['Pic1']) . '" alt="' . $game['Gname'] . '" style="max-width: 50px; max-height: 50px;">';
                echo $game['Gname'];
                echo '</li>';
            }
        ?>
    </ul>
    </section>

    <section>
    <h2>My Games</h2>
    <?php
    // Retrieve games owned by the user
    $userGames = mysqli_query($conn, "SELECT Gid, Gname FROM User_Game_Store WHERE Uid = '{$user['Uid']}'");

    // Display user's games
    while ($game = mysqli_fetch_assoc($userGames)) {
        echo '<div class="user-game">';
        echo '<h3>' . $game['Gname'] . '</h3>';
        echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
        echo '<input type="hidden" name="deleteGame" value="' . $game['Gid'] . '">';
        echo '<input type="submit" name="delete" value="Delete">';
        echo '</form>';
        echo '</div>';
    }
    ?>
    </section>

    <footer>
        &copy; 2023 My Steam Profile
    </footer>
</body>
</html>
