<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["customer_name"]);
    $account = trim($_POST["account_number"]);
    $balance = $_POST["balance"];

    if ($name === "" || $account === "" ||
        !is_numeric($balance) || $balance < 0) {

        $message = "Please enter valid customer details.";

    } else {
        $stmt = $conn->prepare(
            "INSERT INTO accounts (account_number, customer_name, balance)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("ssd", $account, $name, $balance);

        if ($stmt->execute()) {
            $message = "Customer account created successfully!";
        } else {
            $message = "Could not create account. Check whether the account number already exists.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Customer - THMS</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f8fd;
            padding: 30px;
        }

        .container {
            max-width: 450px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(21,101,192,0.12);
        }

        h2 {
            text-align: center;
            color: #1565c0;
        }

        label {
            display: block;
            margin-top: 18px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ccd9e8;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 22px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 7px;
        }

        .message {
            text-align: center;
            color: #1565c0;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Register New Customer</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Customer Full Name</label>
        <input type="text" name="customer_name" required>

        <label>Account Number</label>
        <input type="text" name="account_number" required>

        <label>Opening Balance (KES)</label>
        <input type="number" name="balance"
               min="0" step="0.01" required>

        <button type="submit">Register Customer</button>

    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>

</div>

</body>
</html>