<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
$loggedInUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

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
    <?php global $route; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PM System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($route !== 'portfolio'): ?>
        <link rel="stylesheet" href="resources/css/page.css">
    <?php endif; ?>
    <script src="resources/js/main.js"></script>

    <script>
    var loggedInUserId = <?php echo json_encode($loggedInUserId); ?>;

</script>
</head>
<body>
<?php if ($route !== 'portfolio'): ?>

    <header>
        <h1>Project Monitoring System</h1>
        <div class="header-buttons">
        <?php
            if ($_SESSION['role'] === 'admin') {
                echo '<button onclick="window.location.href=\'dashboard\'">Dashboard</button>';
            }
        ?>            
            <button onclick="window.location.href='subject'">Subject</button>
            <button onclick="window.location.href='portfolio'">My Project</button>
            <!-- <button onclick="window.location.href='dashboardOld.php'">Settings</button> -->
            <button onclick="confirmLogout()">Logout</button>
        </div>
    </header>
    <?php endif; ?>
