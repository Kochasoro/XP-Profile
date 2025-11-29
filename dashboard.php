<?php include 'header.php'; ?>
<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: portfolio"); 
    exit; 
}
?>
<link rel="stylesheet" href="resources/css/dashboard.css">
<script src="resources/js/dashboard.js"></script>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Dashboard</h2>
    </div>

    <div class="table-wrapper">
        <table id="userTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>