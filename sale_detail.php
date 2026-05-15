<?php
// sale_detail.php
// Shows all items in one specific sale

session_start();
include "connect.php";
include "navbar.php";

// Get the sale ID from the URL
$saleID = $_GET['id'];

// SQL: SELECT from multiple tables (Sale_Items joined with Products and Sales and Users)
$sql = "SELECT Sale_Items.QuantitySold, Sale_Items.UnitPrice,
               Products.ProductName,
               Sales.SaleDate, Users.Username
        FROM Sale_Items
        JOIN Products ON Sale_Items.ProductID = Products.ProductID
        JOIN Sales    ON Sale_Items.SaleID    = Sales.SaleID
        JOIN Users    ON Sales.UserID         = Users.UserID
        WHERE Sale_Items.SaleID = '$saleID'";

$result = mysqli_query($conn, $sql);

// Calculate total (mathematical calculation using a loop)
$grandTotal = 0;
$items      = array();

while ($row = mysqli_fetch_assoc($result)) {
    // Calculate line total (math: unit price × quantity)
    $lineTotal    = $row['UnitPrice'] * $row['QuantitySold'];
    $grandTotal   = $grandTotal + $lineTotal;
    $row['LineTotal'] = $lineTotal;
    $items[]          = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sale Detail - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .receipt-items tr {
            animation: slideInLeft 0.4s var(--ease-out) backwards;
        }
        .receipt-total {
            background: linear-gradient(135deg, var(--accent-light), rgba(124, 58, 237, 0.05));
            border: 2px solid var(--accent);
            border-radius: var(--radius-md);
            padding: 28px;
            text-align: right;
            margin: 32px 0;
            animation: scaleIn 0.5s var(--ease-out);
        }
        .receipt-total .label {
            font-size: 14px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .receipt-total .amount {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }
    </style>
</head>
<body>

<div class="box">
    <div class="receipt-header">
        <div style="font-size: 48px;">⚡</div>
        <h3>Joe's Electronics</h3>
        <p style="font-size: 16px;">Sale Receipt #<?php echo $saleID; ?></p>

        <?php if (count($items) > 0) { ?>
            <p>Date: <strong><?php echo $items[0]['SaleDate']; ?></strong> | Cashier: <strong><?php echo $items[0]['Username']; ?></strong></p>
        <?php } ?>
    </div>

    <h3 style="margin-top: 0;">Items</h3>
    <table class="receipt-items">
        <tr>
            <th>Product</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>

        <?php
        foreach ($items as $key => $item) {
            $animDelay = $key * 0.1;
            echo "<tr style='animation-delay: {$animDelay}s'>";
            echo "<td><strong>" . $item['ProductName'] . "</strong></td>";
            echo "<td>₸" . number_format($item['UnitPrice']) . "</td>";
            echo "<td>" . $item['QuantitySold'] . "</td>";
            echo "<td style='font-weight: 700;'>₸" . number_format($item['LineTotal']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <div class="receipt-total">
        <div class="label">Total Amount</div>
        <div class="amount">₸<?php echo number_format($grandTotal); ?></div>
    </div>

    <div class="action-buttons">
        <a href="sales.php" class="btn btn-blue">← Back to Sales</a>
        <button onclick="window.print()" class="btn btn-orange">Print Receipt</button>
    </div>
</div>

</body>
</html>
