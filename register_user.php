<?php
require_once "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username === "" || $password === "") {
        $message = "Please fill in all fields.";
    } elseif (strlen($password) < 8) {
        $message = "Password must have at least 8 characters.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (username, password) VALUES (?, ?)"
        );

        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $message = "User registered successfully!";
        } else {
            $message = "Registration failed. Username may already exist.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>THMS User Registration</title>
</head>
<body>

    <h2>Register THMS User</h2>

    <form method="POST">
        <input type="text" name="username"
               placeholder="Enter username" required>

        <br><br>

        <input type="password" name="password"
               placeholder="Enter password" required>

        <br><br>

        <button type="submit">Register</button>
    </form>

    <p><?php echo htmlspecialchars($message); ?></p>

</body>
</html>