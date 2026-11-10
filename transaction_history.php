<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT 
            transactions.id,
            accounts.customer_name,
            accounts.account_number,
            transactions.transaction_type,
            transactions.amount,
            transactions.description,
            transactions.transaction_date
        FROM transactions
        INNER JOIN accounts
        ON transactions.account_id = accounts.id
        ORDER BY transactions.transaction_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History - THMS</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f8fd;
            padding: 25px;
        }

        h2 {
            color: #1565c0;
            text-align: center;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1565c0;
            color: white;
        }

        .deposit {
            color: green;
            font-weight: bold;
        }

        .withdrawal {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

<h2>Transaction History</h2>

<div class="container">

<table>
    <tr>
        <th>ID</th>
        <th>Customer Name</th>
        <th>Account Number</th>
        <th>Type</th>
        <th>Amount (KES)</th>
        <th>Description</th>
        <th>Date & Time</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($row = $result->fetch_assoc()): ?>

        <tr>
            <td><?php echo $row["id"]; ?></td>
            <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
            <td><?php echo htmlspecialchars($row["account_number"]); ?></td>

            <td class="<?php echo strtolower($row["transaction_type"]); ?>">
                <?php echo htmlspecialchars($row["transaction_type"]); ?>
            </td>

            <td><?php echo number_format($row["amount"], 2); ?></td>

            <td><?php echo htmlspecialchars($row["description"] ?? ""); ?></td>

            <td><?php echo htmlspecialchars($row["transaction_date"]); ?></td>
        </tr>

        <?php endwhile; ?>

    <?php else: ?>

        <tr>
            <td colspan="7">No transactions recorded yet.</td>
        </tr>

    <?php endif; ?>

</table>

</div>

<p><a href="dashboard.php">Back to Dashboard</a></p>

</body>
</html>