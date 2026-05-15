<?php
// dashboard.php
// This is the main page shown after login
// It shows today's sales and stock levels

session_start();
include "connect.php";
include "navbar.php";

// Get today's date
$today = date("Y-m-d");

// SQL: SELECT sales from today (one table)
//sql statement
$sql         = "SELECT * FROM Sales WHERE SaleDate = '$today'";
$result      = mysqli_query($conn, $sql);
$salesCount  = mysqli_num_rows($result); // Count how many sales today

// Calculate total revenue (mathematical calculation using a loop)
$totalRevenue = 0;
while ($row = mysqli_fetch_assoc($result)) {
    // Add each sale amount to the total (loop + math)
    $totalRevenue = $totalRevenue + $row['TotalAmount'];
}

// SQL: SELECT all products (one table)
$sqlProducts = "SELECT * FROM Products ORDER BY ProductName";
$allProducts = mysqli_query($conn, $sqlProducts);

// Put all products into an array so we can sort them
$productNames  = array(); // One-dimensional array for product names
$productStocks = array(); // One-dimensional array for stock numbers

while ($p = mysqli_fetch_assoc($allProducts)) {
    // Add each product to the arrays (loop structure)
    $productNames[]  = $p['ProductName'];
    $productStocks[] = $p['CurrentStock'];
}

// BUBBLE SORT - sort products from lowest stock to highest
// This helps us see which products are running out first
//LOOP
$n = count($productStocks);
for ($i = 0; $i < $n - 1; $i++) {
    for ($j = 0; $j < $n - $i - 1; $j++) {
        // Conditional: if current stock is bigger than next, swap them
        if ($productStocks[$j] > $productStocks[$j + 1]) {
            // Swap the stock numbers
            $tempStock          = $productStocks[$j];
            $productStocks[$j]  = $productStocks[$j + 1];
            $productStocks[$j + 1] = $tempStock;

            // Swap the names too so they stay matched
            $tempName          = $productNames[$j];
            $productNames[$j]  = $productNames[$j + 1];
            $productNames[$j + 1] = $tempName;
        }
    }
}

// Count total number of products (mathematical calculation)
$totalProducts = count($productNames);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stock-bar {
            height: 6px;
            background: var(--bg-tertiary);
            border-radius: 3px;
            overflow: hidden;
            margin-top: 8px;
        }
        .stock-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 1s var(--ease-out);
        }
        table tbody tr {
            animation: fadeInUp 0.4s var(--ease-out) backwards;
        }
        /* Horizontal metric cards */
        .dash-metrics {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin: 32px 0;
        }
        .dash-metric-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 20px 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s var(--ease-out);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .dash-metric-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--accent), #7c3aed);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.3s var(--ease-out);
        }
        .dash-metric-card:hover {
            transform: translateX(8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent);
        }
        .dash-metric-card:hover::before {
            transform: scaleY(1);
        }
        .dash-metric-icon {
            width: 52px; height: 52px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: var(--shadow-md);
        }
        .dash-metric-body {
            flex: 1;
        }
        .dash-metric-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .dash-metric-number {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--text-primary);
            line-height: 1;
        }
        .dash-metric-arrow {
            font-size: 20px;
            color: var(--text-muted);
            transition: all 0.3s var(--ease-out);
        }
        .dash-metric-card:hover .dash-metric-arrow {
            color: var(--accent);
            transform: translateX(4px);
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['username']; ?> — <?php echo date("l, d M Y"); ?></p>

    <div class="dash-metrics">
        <a href="sales.php" class="dash-metric-card">
            <div class="dash-metric-icon" style="background: linear-gradient(135deg, #4361ee, #7c3aed);">🧾</div>
            <div class="dash-metric-body">
                <div class="dash-metric-label">Sales Today</div>
                <div class="dash-metric-number"><?php echo $salesCount; ?></div>
            </div>
            <div class="dash-metric-arrow">→</div>
        </a>
        <a href="report.php" class="dash-metric-card">
            <div class="dash-metric-icon" style="background: linear-gradient(135deg, #2ec4b6, #06d6a0);">₸</div>
            <div class="dash-metric-body">
                <div class="dash-metric-label">Revenue Today</div>
                <div class="dash-metric-number">₸<?php echo number_format($totalRevenue); ?></div>
            </div>
            <div class="dash-metric-arrow">→</div>
        </a>
        <a href="products.php" class="dash-metric-card">
            <div class="dash-metric-icon" style="background: linear-gradient(135deg, #ff9f1c, #ff6b35);">📦</div>
            <div class="dash-metric-body">
                <div class="dash-metric-label">Total Products</div>
                <div class="dash-metric-number"><?php echo $totalProducts; ?></div>
            </div>
            <div class="dash-metric-arrow">→</div>
        </a>
    </div>

    <div style="margin-top: 48px;"></div>

    <h3>Stock Levels (lowest first)</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Stock</th>
            <th>Status</th>
        </tr>

        <?php
        for ($i = 0; $i < count($productNames); $i++) {
            $stock = $productStocks[$i];
            $maxStock = max($productStocks) ?: 1;
            $barWidth = ($stock / $maxStock) * 100;

            if ($stock == 0) {
                $status = "<span class='badge badge-danger'>● Out of Stock</span>";
                $rowColor = "var(--danger-bg)";
                $stockColor = "var(--danger)";
            } elseif ($stock <= 5) {
                $status = "<span class='badge badge-warning'>● Low Stock</span>";
                $rowColor = "var(--warning-bg)";
                $stockColor = "var(--warning)";
            } else {
                $status = "<span class='badge badge-success'>● OK</span>";
                $rowColor = "transparent";
                $stockColor = "var(--success)";
            }

            $animDelay = $i * 0.05;
            echo "<tr style='animation-delay: {$animDelay}s; background: {$rowColor};'>";
            echo "<td><strong>" . $productNames[$i] . "</strong></td>";
            echo "<td style='color: {$stockColor}; font-weight: 800;'>" . $stock . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<script>
document.querySelectorAll('.stat-card .number').forEach(el => {
    const target = parseInt(el.textContent.replace(/[^0-9]/g, ''));
    if (!target) return;
    let current = 0;
    const step = Math.ceil(target / 30);
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        el.textContent = el.textContent.replace(/[0-9,]+/, current.toLocaleString());
    }, 30);
});
</script>

</body>
</html>
