<?php
    include("connect.php");

    session_start(); // Resume the session

    // Check if the user is logged in
    if (!isset($_SESSION['company'])) {
        header("Location: Logout.php"); // Redirect to Company login if not logged in
        exit();
    }

    // Access user data from the session
    $company = $_SESSION['company'];

    // Adding Games....
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addGame'])) {
        $Gid = filter_input(INPUT_POST, "Gid", FILTER_SANITIZE_SPECIAL_CHARS);
        $Cid = $company['Cid'];
        $Gname = filter_input(INPUT_POST, "Gname", FILTER_SANITIZE_SPECIAL_CHARS);
        $Price = filter_input(INPUT_POST, "Price", FILTER_SANITIZE_SPECIAL_CHARS);
        $Class1 = filter_input(INPUT_POST, "Class1", FILTER_SANITIZE_SPECIAL_CHARS);
        $Class2 = filter_input(INPUT_POST, "Class2", FILTER_SANITIZE_SPECIAL_CHARS);
        $Info = filter_input(INPUT_POST, "Info", FILTER_SANITIZE_SPECIAL_CHARS);
        $Comment = filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_SPECIAL_CHARS);
        $Score = filter_input(INPUT_POST, "Score", FILTER_SANITIZE_SPECIAL_CHARS);

        // Handle image upload
        $Pic1Data = file_get_contents($_FILES['Pic1']['tmp_name']);
        $Pic2Data = file_get_contents($_FILES['Pic2']['tmp_name']);
        $Pic3Data = file_get_contents($_FILES['Pic3']['tmp_name']);


        // Insert the new game information into the Game table
        $stmt = mysqli_prepare($conn, "INSERT INTO Game (Gid, Cid, Gname, Price, Class1, Class2, Info, Comment, Score, Pic1, Pic2, Pic3) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssssssbbb", $Gid, $Cid, $Gname, $Price, $Class1, $Class2, $Info, $Comment, $Score, $Pic1Data, $Pic2Data, $Pic3Data);

        mysqli_stmt_send_long_data($stmt, 9, $Pic1Data);
        mysqli_stmt_send_long_data($stmt, 10, $Pic2Data);
        mysqli_stmt_send_long_data($stmt, 11, $Pic3Data);

        if (mysqli_stmt_execute($stmt)) {
            echo "Game added successfully!";
        } else {
            echo "Error adding game: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    // Retrieve and display all games for the company
    $allGames = mysqli_query($conn, "SELECT Gid, Gname, Price, Score, Pic1, Pic2, Pic3 FROM Game WHERE Cid = '{$company['Cid']}'");

    // Display all games if the button is clicked
    if (isset($_POST['showAllGames'])) {
        echo "<h2>All Games</h2>";

        while ($row = mysqli_fetch_assoc($allGames)) {
            echo "<p>Gid: {$row['Gid']}, Gname: {$row['Gname']}, Price: {$row['Price']}, Score: {$row['Score']}</p>";

            // Check if the game has an associated image (Pic1)
            if (!empty($row['Pic1'])) {
                // Display the image (Pic1)
                echo '<img src="data:image;base64,' . base64_encode($row['Pic1']) . '" alt="Game Image 1" style="max-width: 300px; max-height: 300px;">';
            }

            // Check if the game has an associated image (Pic2)
            if (!empty($row['Pic2'])) {
                // Display the image (Pic2)
                echo '<img src="data:image;base64,' . base64_encode($row['Pic2']) . '" alt="Game Image 2" style="max-width: 300px; max-height: 300px;">';
            }

            // Check if the game has an associated image (Pic3)
            if (!empty($row['Pic3'])) {
                // Display the image (Pic3)
                echo '<img src="data:image;base64,' . base64_encode($row['Pic3']) . '" alt="Game Image 3" style="max-width: 300px; max-height: 300px;">';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Page</title>
    <link rel="stylesheet" href="Company.css">
</head>
<body>
    <h2>Welcome, <?php echo $company['Cname']; ?>!</h2>
    <p>Company ID: <?php echo $company['Cid']; ?></p>
    <p>Email: <?php echo $company['Cemail']; ?></p>
    
    <h2>Add New Game</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"enctype="multipart/form-data">
        <label for="Gid">Game Id:</label>
        <input type="text" name="Gid" required><br>

        <label for="Gname">Game Name:</label>
        <input type="text" name="Gname" required><br>

        <label for="Price">Price:</label>
        <input type="text" name="Price" required><br>

        <label for="Class1">Class 1:</label>
        <input type="text" name="Class1"><br>

        <label for="Class2">Class 2:</label>
        <input type="text" name="Class2"><br>

        <label for="Info">Information:</label>
        <textarea name="Info"></textarea><br>

        <label for="Comment">Comment:</label>
        <textarea name="Comment"></textarea><br>

        <label for="Score">Score:</label>
        <input type="text" name="Score"><br>

        <label for="Pic1">Image1 Upload:</label>
        <input type="file" name="Pic1" accept="image/*"><br>
        <label for="Pic2">Image2 Upload:</label>
        <input type="file" name="Pic2" accept="image/*"><br>
        <label for="Pic3">Image3 Upload:</label>
        <input type="file" name="Pic3" accept="image/*"><br>

        <input type="submit" name="addGame" value="Add Game">
    </form>

    <h2>Show All Games</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="submit" name="showAllGames" value="Show All Games">
    </form>

    <form method="post" action="Logout.php">
        <input type="submit" value="Logout">
    </form>

    <form method="post" action="Home.php">
    <input type="submit" value="Home">
    </form>
</body>
</html>