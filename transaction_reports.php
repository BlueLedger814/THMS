<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$summary = $conn->query("
    SELECT
        COUNT(*) AS total_transactions,
        COALESCE(SUM(CASE
            WHEN transaction_type = 'Deposit' THEN amount
            ELSE 0
        END), 0) AS total_deposits,
        COALESCE(SUM(CASE
            WHEN transaction_type = 'Withdrawal' THEN amount
            ELSE 0
        END), 0) AS total_withdrawals
    FROM transactions
")->fetch_assoc();

$accounts = $conn->query("
    SELECT account_number, customer_name, balance
    FROM accounts
    ORDER BY customer_name
");

$total_balance = $conn->query("
    SELECT COALESCE(SUM(balance), 0) AS total_balance
    FROM accounts
")->fetch_assoc()["total_balance"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Reports - THMS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f4f8fd;
            color: #243447;
        }

        h1, h2 {
            color: #1565c0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin: 25px 0;
        }

        .card, .table-container {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(21, 101, 192, 0.10);
        }

        .card h3 {
            margin-top: 0;
            font-size: 15px;
            color: #526579;
        }

        .amount {
            font-size: 23px;
            font-weight: bold;
            color: #1565c0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #e0e7ef;
            text-align: left;
        }

        th {
            background: #1565c0;
            color: white;
        }

        .table-container {
            overflow-x: auto;
        }

        a {yyt
            color: #1565c0;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
        }
    </style>
</head>

<body>

<h1>Transaction Reports</h1>
<p>Summary of deposits, withdrawals, and customer account balances.</p>

<div class="cards">

    <div class="card">
        <h3>Total Transactions</h3>
        <div class="amount">
            <?php echo (int)$summary["total_transactions"]; ?>
        </div>
    </div>

    <div class="card">
        <h3>Total Deposits</h3>
        <div class="amount">
            KES <?php echo number_format($summary["total_deposits"], 2); ?>
        </div>
    </div>

    <div class="card">
        <h3>Total Withdrawals</h3>
        <div class="amount">
            KES <?php echo number_format($summary["total_withdrawals"], 2); ?>
        </div>
    </div>

    <div class="card">
        <h3>Total Account Balances</h3>
        <div class="amount">
            KES <?php echo number_format($total_balance, 2); ?>
        </div>
    </div>

</div>

<div class="table-container">

    <h2>Customer Account Balances</h2>

    <table>
        <tr>
            <th>Account Number</th>
            <th>Customer Name</th>
            <th>Current Balance (KES)</th>
        </tr>

        <?php if ($accounts && $accounts->num_rows > 0): ?>

            <?php while ($row = $accounts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["account_number"]); ?></td>
                    <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
                    <td><?php echo number_format($row["balance"], 2); ?></td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="3">No customer accounts found.</td>
            </tr>

        <?php endif; ?>

    </table>

</div>

<a class="back" href="dashboard.php">Back to Dashboard</a>

</body>
</html>