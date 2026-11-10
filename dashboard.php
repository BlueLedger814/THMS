<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION["username"] ?? "User", ENT_QUOTES, "UTF-8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>THMS | Dashboard</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fc;
            color: #26364a;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 245px;
            height: 100vh;
            background: #123b72;
            color: white;
            padding: 25px 16px;
            overflow-y: auto;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #c8dcfa;
            margin-bottom: 35px;
        }

        .menu-title {
            font-size: 11px;
            color: #b8cbe6;
            margin: 20px 12px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: block;
            color: #e8f1ff;
            text-decoration: none;
            padding: 14px 12px;
            border-radius: 8px;
            margin-bottom: 6px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2476d4;
            color: white;
        }

        .main {
            margin-left: 245px;
            padding: 25px 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .topbar h1 {
            margin: 0 0 7px;
            font-size: 27px;
        }

        .muted {
            color: #7b899b;
            font-size: 13px;
        }

        .profile {
            background: white;
            padding: 12px 17px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(20, 50, 90, 0.05);
            font-size: 14px;
        }

        .welcome {
            background: linear-gradient(120deg, #1768c4, #43a4ee);
            color: white;
            padding: 27px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 8px 22px rgba(23, 104, 196, 0.16);
        }

        .welcome h2 {
            margin: 0 0 10px;
            font-size: 24px;
        }

        .welcome p {
            margin: 0;
            line-height: 1.6;
            color: #edf6ff;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(25, 60, 100, 0.06);
            border-bottom: 4px solid #2476d4;
        }

        .stat-card:nth-child(2) {
            border-bottom-color: #19a88b;
        }

        .stat-card:nth-child(3) {
            border-bottom-color: #f2ae36;
        }

        .stat-icon {
            font-size: 26px;
            margin-bottom: 14px;
        }

        .stat-card h3 {
            font-size: 13px;
            font-weight: normal;
            color: #7b899b;
            margin: 0 0 12px;
        }

        .stat-value {
            font-size: 27px;
            font-weight: bold;
            color: #183b66;
        }

        .section-title {
            font-size: 19px;
            margin: 0 0 18px;
        }

        .charts {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .chart-card,
        .actions-card,
        .recent-card {
            background: white;
            padding: 23px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(25, 60, 100, 0.06);
            min-width: 0;
        }

        .chart-container {
            position: relative;
            height: 290px;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .action-link {
            display: block;
            padding: 20px;
            background: white;
            border: 1px solid #e5edf7;
            border-radius: 12px;
            text-decoration: none;
            color: #1768c4;
            transition: 0.2s;
        }

        .action-link:hover {
            background: #edf5ff;
            transform: translateY(-2px);
        }

        .action-link strong {
            display: block;
            margin-bottom: 7px;
        }

        .action-link span {
            color: #7b899b;
            font-size: 12px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        th,
        td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #edf1f7;
            font-size: 13px;
        }

        th {
            color: #738198;
            background: #f8faff;
        }

        .empty {
            text-align: center;
            color: #8290a3;
            padding: 30px;
        }

        footer {
            text-align: center;
            color: #8491a3;
            font-size: 12px;
            padding: 25px 0 5px;
        }

        @media (max-width: 1000px) {
            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .charts {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                padding: 18px;
            }

            .stats,
            .actions {
                grid-template-columns: 1fr;
            }

            .topbar h1 {
                font-size: 23px;
            }

            .welcome {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">THMS</div>
    <div class="brand-subtitle">Transaction History Management System</div>

    <div class="menu-title">Main Menu</div>

    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <a href="add_customer.php">👤 Register Customer</a>
    <a href="add_transaction.php">💳 Add Transaction</a>
    <a href="transaction_history.php">📋 Transaction History</a>
    <a href="transaction_reports.php">📊 Transaction Reports</a>

    <div class="menu-title">Account</div>
    <a href="logout.php">🚪 Logout</a>
</aside>

<main class="main">

    <div class="topbar">
        <div>
            <h1>Dashboard</h1>
            <div class="muted">An overview of your transaction management system</div>
        </div>

        <div class="profile">
            👋 Welcome, <strong><?php echo $username; ?></strong>
        </div>
    </div>

    <section class="stats">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <h3>Total Customers</h3>
            <div class="stat-value">—</div>
            <div class="muted">Database connection pending</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💳</div>
            <h3>Total Transactions</h3>
            <div class="stat-value">—</div>
            <div class="muted">Database connection pending</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <h3>Total Transaction Amount</h3>
            <div class="stat-value">—</div>
            <div class="muted">Database connection pending</div>
        </div>
    </section>

    <section class="charts">
        <div class="chart-card">
            <h2 class="section-title">Transaction Overview</h2>
            <p class="muted">Illustrative monthly transaction chart</p>

            <div class="chart-container">
                <canvas id="transactionChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h2 class="section-title">Transaction Types</h2>
            <p class="muted">Illustrative distribution</p>

            <div class="chart-container">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </section>

    <section class="actions-card">
        <h2 class="section-title">Quick Actions</h2>

        <div class="actions">
            <a href="add_customer.php" class="action-link">
                <strong>👤 Register Customer</strong>
                <span>Add a new customer record</span>
            </a>

            <a href="add_transaction.php" class="action-link">
                <strong>💳 Add Transaction</strong>
                <span>Record a customer transaction</span>
            </a>

            <a href="transaction_history.php" class="action-link">
                <strong>📋 View History</strong>
                <span>Review recorded transactions</span>
            </a>

            <a href="transaction_reports.php" class="action-link">
                <strong>📊 View Reports</strong>
                <span>Access transaction reports</span>
            </a>
        </div>
    </section>

    <br>

    <section class="recent-card">
        <h2 class="section-title">Recent Transactions</h2>
        <p class="muted">
            Your recent records will appear here after we connect the database.
        </p>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Account Number</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="5" class="empty">
                            Database connection pending
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <footer>
        THMS &copy; <?php echo date("Y"); ?> | Transaction History Management System
    </footer>

</main>

<script>
    new Chart(document.getElementById("transactionChart"), {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            datasets: [{
                label: "Example Transactions",
                data: [12, 19, 15, 25, 20, 30],
                borderColor: "#2476d4",
                backgroundColor: "rgba(36, 118, 212, 0.12)",
                fill: true,
                tension: 0.4,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById("typeChart"), {
        type: "doughnut",
        data: {
            labels: ["Deposits", "Withdrawals", "Transfers"],
            datasets: [{
                data: [45, 30, 25],
                backgroundColor: ["#2476d4", "#19a88b", "#f2ae36"],
                borderWidth: 2,
                borderColor: "#ffffff"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom"
                }
            }
        }
    });
</script>

</body>
</html>