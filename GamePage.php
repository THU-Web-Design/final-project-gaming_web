<?php
    include("connect.php");

    $Gid = $_GET['Gid'];
    $Gname = $_GET['Gname'];
    $Class1 = $_GET['Class1'];
    $Class2 = $_GET['Class2'];
    $Price = $_GET['Price'];
    $Info = $_GET['Info'];

    // Retrieve image data from the database
    $result = mysqli_query($conn, "SELECT Pic1, Pic2, Pic3 FROM Game WHERE Gid = '$Gid'");
    $row = mysqli_fetch_assoc($result);
    $Pic1 = $row['Pic1'];
    $Pic2 = $row['Pic2'];
    $Pic3 = $row['Pic3'];

    // Check if addToCart button is clicked
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['addToCart'])) {
            // Handle adding to cart
            session_start();

            if (isset($_SESSION['user'])) {
                $user = $_SESSION['user'];
                $Gid = filter_input(INPUT_POST, "Gid", FILTER_SANITIZE_SPECIAL_CHARS);
                $Gname = filter_input(INPUT_POST, "Gname", FILTER_SANITIZE_SPECIAL_CHARS);
                $Price = filter_input(INPUT_POST, "Price", FILTER_SANITIZE_SPECIAL_CHARS);
    
                // Insert into the Cart table
                $stmt = mysqli_prepare($conn, "INSERT INTO Cart (Uid, Gid, Gname, Price) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssss", $user['Uid'], $Gid, $Gname, $Price);
    
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: Cart.php");
                } else {
                    echo "Error adding to cart: " . mysqli_stmt_error($stmt);
                }
    
                mysqli_stmt_close($stmt);
            } else {
                // Redirect to login if the user is not logged in
                header("Location: Login.php");
                exit();
            }
        } elseif (isset($_POST['addComment'])) {
            // Handle adding comments
            $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_SPECIAL_CHARS);

            // Update the Game table with the comment
            $updateCommentStmt = mysqli_prepare($conn, "UPDATE Game SET Comment = ? WHERE Gid = ?");
            
            // Check for errors in the prepared statement
            if (!$updateCommentStmt) {
                die("Error in prepared statement: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($updateCommentStmt, "ss", $comment, $Gid);

            if (mysqli_stmt_execute($updateCommentStmt)) {
                echo "Comment added successfully!";
            } else {
                echo "Error adding comment: " . mysqli_stmt_error($updateCommentStmt);
            }

            mysqli_stmt_close($updateCommentStmt);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $Gname; ?> - Steam商店</title>
    <link rel="stylesheet" href="GameCs.css">
</head>
<body>
    <header>
        <h1>Steam商店</h1>
        <div class="user-actions">
            <a href="Home.php"><button>返回首頁</button></a>
            <button onclick="gotoCart()">購物車</button>
            <button class="logout" onclick="logout()">登出</button>
        </div>
    </header>

    <section>
        <div class="game-info">
            <div class="game-images">
                <img src="data:image;base64,<?php echo base64_encode($Pic1); ?>" alt="遊戲截圖1">
                <img src="data:image;base64,<?php echo base64_encode($Pic2); ?>" alt="遊戲截圖2">
                <img src="data:image;base64,<?php echo base64_encode($Pic3); ?>" alt="遊戲截圖3">
            </div>
            <h2><?php echo $Gname; ?></h2>
            <p><strong>類型:</strong> <?php echo $Class1; ?></p>
            <p><strong>類型:</strong> <?php echo $Class2; ?></p>
            <p><strong>價格:</strong> <?php echo $Price; ?></p>

            <!-- 添加底線樣式 -->
            <div class="underline">
                <p class="description"><strong>描述:</strong> <?php echo $Info; ?></p>
            </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="Gid" value="<?php echo $Gid; ?>">
                <input type="hidden" name="Gname" value="<?php echo $Gname; ?>">
                <input type="hidden" name="Price" value="<?php echo $Price; ?>">
                <button type="submit" name="addToCart">加入購物車</button>
            </form>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="Gid" value="<?php echo $Gid; ?>">
                <textarea name="comment" placeholder="在此輸入評論"></textarea>
                <button type="submit" name="addComment">新增評論</button>
            </form>
        </div>
    </section>

    <script>
        function gotoCart() {
            window.location.href = 'Cart.php';
        }

        function logout() {
            window.location.href = 'Logout.php';
        }
    </script>
</body>
</html>
