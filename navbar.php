<?php
// navbar.php
// This is included at the top of every page to show the navigation bar

// If user is not logged in, send them to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!-- Link to external CSS file (Type 1 - External CSS) -->
<link rel="stylesheet" href="style.css">

<div class="navbar">
    <a href="about.php" style="text-decoration:none; color:inherit;"><span>Joe's Electronics</span></a>
    <div>
        <a href="dashboard.php" data-tooltip="Overview">Dashboard</a>
        <a href="new_sale.php" data-tooltip="Create">New Sale</a>
        <a href="sales.php" data-tooltip="History">Sales</a>

        <?php
        if ($_SESSION['role'] == 'Manager') {
            echo '<a href="products.php" data-tooltip="Inventory">Products</a>';
            echo '<a href="report.php" data-tooltip="Analytics">Report</a>';
        }
        ?>

        <a href="logout.php" data-tooltip="Exit">Logout (<?php echo $_SESSION['username']; ?>)</a>
    </div>
</div>
