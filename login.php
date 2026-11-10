<?php
session_start();
require_once "db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, username, password FROM users WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if (password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Incorrect username or password.";
        }

    } else {
        $error = "Incorrect username or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THMS Login</title><style>
    body {
        font-family: Arial, sans-serif;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        width: 320px;
        padding: 30px;
        background-color:white
        border-radius: 12px;
        box-shadow: inset 0 0 15px;
        rgba(21, 101, 192, 0.15);
    }

    h2 {
        text-align: center;
        color: #1565c0;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #b0bec5;
        border-radius: 5px;
        box-sizing: border-box;
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 12px;
        background-color: #1565c0;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #0d47a1;
    }
</style>

</head>
<body><div class="login-container">
    <h2>THMS Login</h2>
<?php if ($error != ""): ?>
    <p style="color: red; text-align: center;">
        <?php echo htmlspecialchars($error); ?>
    </p>
<?php endif; ?>


    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Enter username" required>

        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>