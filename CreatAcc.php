<?php
    include("connect.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $Uid = filter_input(INPUT_POST, "Uid", FILTER_SANITIZE_SPECIAL_CHARS);
        $Uname = filter_input(INPUT_POST, "Uname", FILTER_SANITIZE_SPECIAL_CHARS);
        $Uemail = filter_input(INPUT_POST, "Uemail", FILTER_SANITIZE_EMAIL);
        $Upassword = filter_input(INPUT_POST, "Upassword", FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($Uid) || empty($Uname) || empty($Uemail) || empty($Upassword)) {
            echo "<p class='error-message'>Please fill in all fields";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO User (Uid, Uname, Uemail, Upassword) 
                                           VALUES (?, ?, ?, ?)");

            // Adjust the types based on your database schema
            mysqli_stmt_bind_param($stmt, "ssss", $Uid, $Uname, $Uemail, $Upassword);

            if (mysqli_stmt_execute($stmt)) {
                echo "<p class='error-message'>You are now registered!";
            } else {
                echo "<p class='error-message'>Registration failed. Please try again later.";
                // Optionally, provide more information for debugging:
                // echo "Error: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="Nstyle.css">

    <script>
        function goToLogin() {
            window.location.href = 'Login.php';
        }
    </script>
</head>
<body>
    <div class="card">
        <h4 class="title">Create Account</h4>
        <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="field">
                <input autocomplete="off" id="logID" placeholder="ID" class="input-field" name="Uid" type="text" required>
            </div>

            <div class="field">
                <input autocomplete="off" id="logName" placeholder="Name" class="input-field" name="Uname" type="text" required>
            </div>
            <div class="field">
                <svg class="input-icon" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                    <path d="M207.8 20.73c-93.45 18.32-168.7 93.66-187 187.1c-27.64 140.9 68.65 266.2 199.1 285.1c19.01 2.888 36.17-12.26 
                    36.17-31.49l.0001-.6631c0-15.74-11.44-28.88-26.84-31.24c-84.35-12.98-149.2-86.13-149.2-174.2c0-102.9 88.61-185.5 193.4-175.4c91.54 
                    8.869 158.6 91.25 158.6 183.2l0 16.16c0 22.09-17.94 40.05-40 40.05s-40.01-17.96-40.01-40.05v-120.1c0-8.847-7.161-16.02-16.01-16.02l-31.98 
                    .0036c-7.299 0-13.2 4.992-15.12 11.68c-24.85-12.15-54.24-16.38-86.06-5.106c-38.75 13.73-68.12 48.91-73.72 89.64c-9.483 69.01 43.81 128 110.9 
                    128c26.44 0 50.43-9.544 69.59-24.88c24 31.3 65.23 48.69 109.4 37.49C465.2 369.3 496 324.1 495.1 277.2V256.3C495.1 107.1 361.2-9.332 207.8 
                    20.73zM239.1 304.3c-26.47 0-48-21.56-48-48.05s21.53-48.05 48-48.05s48 21.56 48 48.05S266.5 304.3 239.1 304.3z"></path>
                </svg>
                <input autocomplete="off" id="logemail" placeholder="Email" class="input-field" name="Uemail" type="email" required>
            </div>
            <div class="field">
                <svg class="input-icon" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                    <path d="M80 192V144C80 64.47 144.5 0 224 0C303.5 0 368 64.47 368 144V192H384C419.3 192 448 220.7 448 256V448C448 483.3 419.3 512 384 
                    512H64C28.65 512 0 483.3 0 448V256C0 220.7 28.65 192 64 192H80zM144 192H304V144C304 99.82 268.2 64 224 64C179.8 64 144 99.82 144 144V192z"></path>
                </svg>

                <input autocomplete="off" id="logpass" placeholder="Password" class="input-field" name="Upassword" type="password" required>
            </div>
            <div class="button-container">
                <button class="btn" type="submit">Create</button>
                <a href="#" class="btn-link" onclick="goToLogin()">Already have a Account?</a>
            </div>
        </form>
    </div>
</body>
</html>
