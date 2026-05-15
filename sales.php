<?php
// sales.php
// This page shows all past sales and allows searching by Sale ID using binary search

session_start();
include "connect.php";
include "navbar.php";

// SQL: SELECT from multiple tables (Sales joined with Users)
// JOIN means we combine Sales and Users to also get the username
$sql = "SELECT Sales.SaleID, Sales.SaleDate, Sales.TotalAmount, Users.Username
        FROM Sales
        JOIN Users ON Sales.UserID = Users.UserID
        ORDER BY Sales.SaleID ASC";

$result = mysqli_query($conn, $sql);

// Put sales into a one-dimensional array so we can do binary search on it
$salesArray = array(); // One-dimensional array to store all sales
while ($row = mysqli_fetch_assoc($result)) {
    $salesArray[] = $row;
}

// BINARY SEARCH function
// Searches for a sale by ID in a sorted array (array must be sorted by SaleID)
function binarySearch($arr, $targetID) {
    $left  = 0;
    $right = count($arr) - 1;

    // Loop: keep searching until left and right meet
    while ($left <= $right) {
        // Math: find the middle index
        $mid = intval(($left + $right) / 2);

        // Conditional: check if we found it
        if ($arr[$mid]['SaleID'] == $targetID) {
            return $mid; // Found!
        } elseif ($arr[$mid]['SaleID'] < $targetID) {
            $left = $mid + 1; // Search right side
        } else {
            $right = $mid - 1; // Search left side
        }
    }
    return -1; // Not found
}

// When the search form is submitted, use binary search to find the sale
$searchResult = null;
$searchMessage = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_id'])) {
    $searchID = intval($_POST['search_id']);
    
    // Use binary search to find the sale
    $foundIndex = binarySearch($salesArray, $searchID);
    
    // Conditional: check if sale was found
    if ($foundIndex != -1) {
        $searchResult = $salesArray[$foundIndex];
        $searchMessage = "Sale found!";
    } else {
        $searchMessage = "Sale ID #$searchID not found!";
    }
}

// Count total sales (mathematical calculation)
$totalSales = count($salesArray);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-box {
            background: var(--bg-primary);
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px;
            margin-bottom: 40px;
            transition: all 0.3s var(--ease-out);
        }
        .search-box:hover {
            box-shadow: var(--shadow-md);
        }
        .search-box:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-light), var(--shadow-md);
            transform: translateY(-4px);
        }
        .search-result {
            background: var(--bg-primary);
            border: 2px solid var(--success);
            border-radius: var(--radius-md);
            padding: 28px;
            margin-bottom: 40px;
            animation: scaleIn 0.4s var(--ease-out);
        }
        .sale-row {
            transition: all 0.3s var(--ease-out);
        }
        /* Hero banner for sales count */
        .sales-hero-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, var(--text-primary) 0%, #2d2d5e 100%);
            border-radius: var(--radius-lg);
            padding: 32px 40px;
            margin-bottom: 40px;
            animation: fadeInUp 0.5s var(--ease-out);
        }
        .sales-hero-number {
            font-size: 72px;
            font-weight: 800;
            color: white;
            letter-spacing: -3px;
            line-height: 1;
        }
        .sales-hero-sublabel {
            font-size: 14px;
            font-weight: 500;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .sales-hero-right {
            text-align: right;
        }
        .sales-hero-tag {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            color: white;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

<div class="box">
    <div class="sales-hero-banner">
        <div class="sales-hero-left">
            <div class="sales-hero-number"><?php echo $totalSales; ?></div>
            <div class="sales-hero-sublabel">total sales recorded</div>
        </div>
        <div class="sales-hero-right">
            <span class="sales-hero-tag">All time</span>
            <p style="margin:0; font-size:13px; color:var(--text-muted);">Use binary search below to find a specific sale by ID instantly.</p>
        </div>
    </div>

    <div style="margin-top: 32px;"></div>

    <div class="search-box">
        <h3 style="margin-top: 0;">Search Sale by ID (Binary Search)</h3>
        <form method="POST" action="sales.php">
            <label>Enter Sale ID</label>
            <input type="number" name="search_id" placeholder="e.g. 1, 2, 3..." required>
            <button type="submit" class="btn btn-blue">Search</button>
        </form>
    </div>

    <?php if ($searchMessage != "") { ?>
        <div class="<?php echo $searchResult ? 'msg-green' : 'msg-red'; ?>">
            <?php echo $searchMessage; ?>
        </div>
    <?php } ?>

    <?php if ($searchResult) { ?>
        <div class="search-result">
            <h4 style="font-size: 20px; font-weight: 700; margin-bottom: 16px;">Sale #<?php echo $searchResult['SaleID']; ?></h4>
            <p><strong>Date:</strong> <?php echo $searchResult['SaleDate']; ?></p>
            <p><strong>Cashier:</strong> <?php echo $searchResult['Username']; ?></p>
            <p><strong>Total:</strong> <span style="font-size: 24px; font-weight: 800; color: var(--success);">₸<?php echo number_format($searchResult['TotalAmount']); ?></span></p>
            <a href="sale_detail.php?id=<?php echo $searchResult['SaleID']; ?>" class="btn btn-green" style="margin-top: 16px;">View Details</a>
        </div>
    <?php } ?>

    <div style="margin-top: 48px;"></div>

    <h3>All Sales</h3>
    <table>
        <tr>
            <th>Sale #</th>
            <th>Date</th>
            <th>Cashier</th>
            <th>Total</th>
            <th>Details</th>
        </tr>

        <?php
        foreach ($salesArray as $key => $sale) {
            $animDelay = $key * 0.03;
            echo "<tr style='animation: fadeInUp 0.3s var(--ease-out) backwards; animation-delay: {$animDelay}s'>";
            echo "<td><strong>#" . $sale['SaleID'] . "</strong></td>";
            echo "<td>" . $sale['SaleDate'] . "</td>";
            echo "<td>" . $sale['Username'] . "</td>";
            echo "<td style='font-weight: 800; font-size: 16px; color: var(--accent);'>₸" . number_format($sale['TotalAmount']) . "</td>";
            echo "<td><a href='sale_detail.php?id=" . $sale['SaleID'] . "' class='btn btn-blue'>View</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
