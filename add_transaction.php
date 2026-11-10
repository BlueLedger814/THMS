<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

$accounts = $conn->query(
    "SELECT id, account_number, customer_name, balance FROM accounts ORDER BY account_number"
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $account_id = (int) $_POST["account_id"];
    $type = $_POST["transaction_type"];
    $amount = (float) $_POST["amount"];
    $description = trim($_POST["description"]);

    if ($account_id <= 0 || $amount <= 0 ||
        !in_array($type, ["Deposit", "Withdrawal"])) {

        $message = "Please enter valid transaction details.";

    } else {

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare(
                "SELECT balance FROM accounts WHERE id = ? FOR UPDATE"
            );
            $stmt->bind_param("i", $account_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $account = $result->fetch_assoc();
            $stmt->close();

            if (!$account) {
                throw new Exception("Account not found.");
            }

            $balance = (float) $account["balance"];

            if ($type == "Withdrawal" && $amount > $balance) {
                throw new Exception("Insufficient account balance.");
            }

            if ($type == "Deposit") {
                $new_balance = $balance + $amount;
            } else {
                $new_balance = $balance - $amount;
            }

            $stmt = $conn->prepare(
                "UPDATE accounts SET balance = ? WHERE id = ?"
            );
            $stmt->bind_param("di", $new_balance, $account_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare(
                "INSERT INTO transactions
                (account_id, transaction_type, amount, description)
                VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isds",
                $account_id,
                $type,
                $amount,
                $description
            );
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            $message = "Transaction completed successfully!";

        } catch (Exception $e) {
            $conn->rollback();
            $message = $e->getMessage();
        }
    }

    $accounts = $conn->query(
        "SELECT id, account_number, customer_name, balance FROM accounts ORDER BY account_number"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Transaction - THMS</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f8fd;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(21,101,192,0.12);
        }

        h2 {
            color: #1565c0;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #ccd9e8;
            border-radius: 7px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 22px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            color: #1565c0;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Add Transaction</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Customer Account</label>

        <select name="account_id" required>
            <option value="">Select Account</option>

            <?php while ($row = $accounts->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars(
                        $row['account_number'] . " - " .
                        $row['customer_name'] . " | Balance: KES " .
                        number_format($row['balance'], 2)
                    ); ?>
                </option>
            <?php endwhile; ?>

        </select>

        <label>Transaction Type</label>

        <select name="transaction_type" required>
            <option value="">Select Type</option>
            <option value="Deposit">Deposit</option>
            <option value="Withdrawal">Withdrawal</option>
        </select>

        <label>Amount (KES)</label>
        <input type="number" name="amount" min="0.01"
               step="0.01" required>

        <label>Description</label>
        <input type="text" name="description"
               placeholder="Enter transaction description">

        <button type="submit">Submit Transaction</button>

    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>

</div>

</body>
</html>