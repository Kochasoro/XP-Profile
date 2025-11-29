<?php
session_start();


if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "You must log in first.";
    exit;
}

if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=security");
    exit();
}

if (isset($_SESSION['login_time']) && time() - $_SESSION['login_time'] > $_SESSION['expire_time']) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
} else {
    $_SESSION['login_time'] = time(); 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 50%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <a href="logout.php">Logout</a>
    <h1>Welcome to the Dashboard</h1>
    <h2>Session Data</h2>
    <table>
        <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($_SESSION as $key => $value) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($key) . "</td>";
                echo "<td>" . htmlspecialchars(is_array($value) ? json_encode($value) : $value) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>