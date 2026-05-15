<?php
// report.php
// Manager can see the daily sales and stock report here

session_start();
include "connect.php";
include "navbar.php";

// Conditional: only managers can see the report
if ($_SESSION['role'] != 'Manager') {
    echo "<div class='box'><p>Access denied. Managers only.</p></div>";
    exit();
}

// Get the chosen date (default = today)
$reportDate = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// SQL: SELECT from multiple tables - sales with cashier name for chosen date
$sql = "SELECT Sales.SaleID, Sales.TotalAmount, Users.Username
        FROM Sales
        JOIN Users ON Sales.UserID = Users.UserID
        WHERE Sales.SaleDate = '$reportDate'";

$result = mysqli_query($conn, $sql);

// Calculate total revenue using a loop (math + loop)
$totalRevenue = 0;
$salesList    = array();

while ($row = mysqli_fetch_assoc($result)) {
    $totalRevenue = $totalRevenue + $row['TotalAmount']; // Math: add to total
    $salesList[]  = $row;
}

// Count how many sales (mathematical calculation)
$totalSales = count($salesList);

// SQL: SELECT from multiple tables - what products were sold today
$sql2 = "SELECT Products.ProductName,
                SUM(Sale_Items.QuantitySold) AS TotalSold,
                Sale_Items.UnitPrice,
                SUM(Sale_Items.UnitPrice * Sale_Items.QuantitySold) AS Revenue
         FROM Sale_Items
         JOIN Products ON Sale_Items.ProductID = Products.ProductID
         JOIN Sales    ON Sale_Items.SaleID    = Sales.SaleID
         WHERE Sales.SaleDate = '$reportDate'
         GROUP BY Products.ProductID, Products.ProductName, Sale_Items.UnitPrice";

$soldItems = mysqli_query($conn, $sql2);

// SQL: SELECT all current stock (one table)
$sqlStock = "SELECT * FROM Products ORDER BY CurrentStock ASC";
$stockResult = mysqli_query($conn, $sqlStock);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .date-selector {
            background: var(--bg-primary);
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
            transition: all 0.3s var(--ease-out);
        }
        .date-selector:hover {
            box-shadow: var(--shadow-md);
        }
        .date-selector:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-light);
        }
        .report-table tr {
            animation: fadeInUp 0.4s var(--ease-out) backwards;
        }
        .stock-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        /* Wide two-column banner */
        .report-stats-banner {
            display: flex;
            background: var(--bg-primary);
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin: 24px 0;
            animation: scaleIn 0.5s var(--ease-out);
        }
        .report-stat-half {
            flex: 1;
        }
        .report-stat-inner {
            padding: 32px 40px;
            text-align: center;
            position: relative;
            transition: background 0.3s var(--ease-out);
        }
        .report-stat-half:hover .report-stat-inner {
            background: var(--bg-secondary);
        }
        .report-stat-emoji {
            font-size: 28px;
            display: block;
            margin-bottom: 12px;
        }
        .report-stat-val {
            font-size: 44px;
            font-weight: 800;
            letter-spacing: -2px;
            color: var(--text-primary);
            line-height: 1;
        }
        .report-stat-lbl {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            margin-top: 8px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Daily Report</h2>

    <div style="margin-top: 32px;"></div>

    <div class="date-selector">
        <form method="GET" action="report.php">
            <label>Choose Date:</label>
            <input type="date" name="date" value="<?php echo $reportDate; ?>" style="width:auto; display:inline; margin-right:8px;">
            <button type="submit" class="btn btn-blue">Show Report</button>
            <button type="button" onclick="window.print()" class="btn btn-orange" style="margin-left:8px;">Print</button>
        </form>
    </div>

    <div class="report-top">
        <div style="font-size: 40px;">⚡</div>
        <h3 style="color: var(--text-primary); margin: 8px 0;">Joe's Electronics</h3>
        <p>Daily Sales & Stock Report</p>
        <p><strong>Date: <?php echo date("d F Y", strtotime($reportDate)); ?></strong></p>
    </div>

    <div style="margin-top: 32px;"></div>

    <div class="report-stats-banner">
        <div class="report-stat-half">
            <div class="report-stat-inner" style="border-right: 2px solid var(--border);">
                <span class="report-stat-emoji">📋</span>
                <div class="report-stat-val"><?php echo $totalSales; ?></div>
                <div class="report-stat-lbl">Total Sales</div>
            </div>
        </div>
        <div class="report-stat-half">
            <div class="report-stat-inner">
                <span class="report-stat-emoji">💰</span>
                <div class="report-stat-val" style="color: var(--success);">₸<?php echo number_format($totalRevenue); ?></div>
                <div class="report-stat-lbl">Total Revenue</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 48px;"></div>

    <h3>Products Sold Today</h3>
    <?php if (mysqli_num_rows($soldItems) == 0) { echo "<p>No sales on this date.</p>"; } ?>
    <table class="report-table">
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Unit Price</th>
            <th>Qty Sold</th>
            <th>Revenue</th>
        </tr>
        <?php
        $num = 1;
        while ($item = mysqli_fetch_assoc($soldItems)) {
            $delay = $num * 0.05;
            echo "<tr style='animation-delay: {$delay}s'>";
            echo "<td>" . $num . "</td>";
            echo "<td><strong>" . $item['ProductName'] . "</strong></td>";
            echo "<td>₸" . number_format($item['UnitPrice']) . "</td>";
            echo "<td>" . $item['TotalSold'] . "</td>";
            echo "<td style='font-weight: 700; color: var(--success);'>₸" . number_format($item['Revenue']) . "</td>";
            echo "</tr>";
            $num = $num + 1;
        }
        ?>
        <tfoot>
            <tr>
                <td colspan="4">TOTAL REVENUE</td>
                <td style="font-size: 18px; color: var(--accent);">₸<?php echo number_format($totalRevenue); ?></td>
            </tr>
        </tfoot>
    </table>

    <br>

    <h3>Current Stock Levels</h3>
    <table class="report-table">
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Current Stock</th>
            <th>Status</th>
        </tr>
        <?php
        $num = 1;
        while ($p = mysqli_fetch_assoc($stockResult)) {
            $delay = $num * 0.05;
            if ($p['CurrentStock'] == 0) {
                $status = "<span class='stock-badge' style='background:var(--danger-bg); color:var(--danger);'>● Out of Stock</span>";
                $rowColor = "var(--danger-bg)";
                $stockColor = "var(--danger)";
            } elseif ($p['CurrentStock'] <= 5) {
                $status = "<span class='stock-badge' style='background:var(--warning-bg); color:var(--warning);'>● Low Stock</span>";
                $rowColor = "var(--warning-bg)";
                $stockColor = "var(--warning)";
            } else {
                $status = "<span class='stock-badge' style='background:var(--success-bg); color:var(--success);'>● OK</span>";
                $rowColor = "transparent";
                $stockColor = "var(--success)";
            }
            echo "<tr style='animation-delay: {$delay}s; background: {$rowColor};'>";
            echo "<td>" . $num . "</td>";
            echo "<td><strong>" . $p['ProductName'] . "</strong></td>";
            echo "<td style='color: {$stockColor}; font-weight: 800;'>" . $p['CurrentStock'] . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
            $num = $num + 1;
        }
        ?>
    </table>
</div>

</body>
</html>
