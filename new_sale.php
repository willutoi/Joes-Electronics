<?php
// new_sale.php
// This page lets cashier or manager record a new sale

session_start();
include "connect.php";
include "navbar.php";

$message = "";
$msgType = "";

// When the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $productID = $_POST['product_id'];
    $quantity  = $_POST['quantity'];
    $today     = date("Y-m-d");

    // SQL: SELECT the chosen product (one table)
    $sql     = "SELECT * FROM Products WHERE ProductID = '$productID'";
    $result  = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);

    // Conditional: check if there is enough stock
    if ($quantity <= 0) {
        $message = "Quantity must be more than 0.";
        $msgType = "red";

    } elseif ($quantity > $product['CurrentStock']) {
        $message = "Not enough stock! Only " . $product['CurrentStock'] . " left.";
        $msgType = "red";

    } else {
        // Calculate total price (simple mathematical calculation)
        $totalAmount = $product['SellingPrice'] * $quantity;

        // SQL: INSERT a new sale into the Sales table
        $sql1 = "INSERT INTO Sales (SaleDate, UserID, TotalAmount) VALUES ('$today', '{$_SESSION['user_id']}', '$totalAmount')";
        mysqli_query($conn, $sql1);

        $saleID = mysqli_insert_id($conn);

        // SQL: INSERT the item into Sale_Items table
        $sql2 = "INSERT INTO Sale_Items (SaleID, ProductID, QuantitySold, UnitPrice) VALUES ('$saleID', '$productID', '$quantity', '{$product['SellingPrice']}')";
        mysqli_query($conn, $sql2);

        // SQL: UPDATE the product stock (subtract what was sold)
        $newStock = $product['CurrentStock'] - $quantity;
        $sql3     = "UPDATE Products SET CurrentStock = '$newStock' WHERE ProductID = '$productID'";
        mysqli_query($conn, $sql3);

        $message = "Sale recorded! Total: ₸" . number_format($totalAmount);
        $msgType = "green";
    }
}

// SQL: SELECT all products grouped by category for the dropdown
$sql      = "SELECT * FROM Products WHERE CurrentStock > 0 ORDER BY Category, ProductName";
$result   = mysqli_query($conn, $sql);

// Group products by category
$grouped = [];
while ($p = mysqli_fetch_assoc($result)) {
    $grouped[$p['Category']][] = $p;
}

// Category icons for labels
$catIcons = [
    'Smartphones' => '📱',
    'Laptops'     => '💻',
    'Tablets'     => '📟',
    'Audio'       => '🎧',
    'Wearables'   => '⌚',
    'Accessories' => '🔌',
    'Cameras'     => '📷',
    'Computers'   => '🖥️',
    'Gaming'      => '🎮',
    'Networking'  => '📡',
    'Smart Home'  => '🏠',
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Sale - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .total-display {
            background: var(--bg-primary);
            border: 2px solid var(--success);
            border-radius: var(--radius-md);
            padding: 24px;
            text-align: center;
            margin: 24px 0;
            transition: all 0.3s var(--ease-out);
        }
        .total-display:hover { transform: scale(1.02); box-shadow: var(--shadow-md); }
        .total-display .amount {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--success), #06d6a0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        /* Category quick-filter chips */
        .cat-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        .cat-chip {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s var(--ease-out);
            user-select: none;
        }
        .cat-chip:hover {
            background: var(--accent-light);
            color: var(--accent);
            border-color: var(--accent);
        }
        .cat-chip.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        /* Product info preview */
        .product-info-bar {
            display: none;
            background: var(--bg-primary);
            border: 2px solid var(--accent);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            margin-bottom: 16px;
            animation: scaleIn 0.3s var(--ease-out);
            align-items: center;
            gap: 16px;
        }
        .product-info-bar.show { display: flex; }
        .product-info-name { font-weight: 700; font-size: 16px; flex: 1; }
        .product-info-price { font-size: 20px; font-weight: 800; color: var(--accent); }
        .product-info-stock { font-size: 13px; color: var(--text-muted); }
    </style>
</head>
<body>

<div class="box">
    <h2>New Sale</h2>

    <?php if ($message != "") { echo "<div class='msg-$msgType'>$message</div>"; } ?>

    <div style="margin-top: 32px;"></div>

    <form method="POST" action="new_sale.php" id="saleForm">
        <h3>Select Product</h3>

        <!-- Category quick-filter chips -->
        <label>Filter by Category</label>
        <div class="cat-chips" id="catChips">
            <span class="cat-chip active" data-cat="all" onclick="filterCat(this, 'all')">🔍 All</span>
            <?php foreach (array_keys($grouped) as $cat):
                $icon = $catIcons[$cat] ?? '📦';
            ?>
            <span class="cat-chip" data-cat="<?= htmlspecialchars($cat) ?>" onclick="filterCat(this, '<?= htmlspecialchars($cat) ?>')"><?= $icon ?> <?= $cat ?></span>
            <?php endforeach; ?>
        </div>

        <!-- Product selected info bar -->
        <div class="product-info-bar" id="productInfoBar">
            <div class="product-info-name" id="infoName">—</div>
            <div class="product-info-price" id="infoPrice">₸0</div>
            <div class="product-info-stock" id="infoStock">Stock: —</div>
        </div>

        <label>Choose Product</label>
        <select name="product_id" id="productSelect" onchange="updatePrice()">
            <option value="">-- Select a product --</option>
            <?php foreach ($grouped as $cat => $items):
                $icon = $catIcons[$cat] ?? '📦';
            ?>
            <optgroup label="<?= $icon ?> <?= htmlspecialchars($cat) ?>" data-cat="<?= htmlspecialchars($cat) ?>">
                <?php foreach ($items as $p): ?>
                <option value="<?= $p['ProductID'] ?>"
                        data-price="<?= $p['SellingPrice'] ?>"
                        data-stock="<?= $p['CurrentStock'] ?>"
                        data-name="<?= htmlspecialchars($p['ProductName']) ?>"
                        data-cat="<?= htmlspecialchars($p['Category']) ?>">
                    <?= htmlspecialchars($p['ProductName']) ?> — ₸<?= number_format($p['SellingPrice']) ?> (Stock: <?= $p['CurrentStock'] ?>)
                </option>
                <?php endforeach; ?>
            </optgroup>
            <?php endforeach; ?>
        </select>

        <label>Quantity</label>
        <input type="number" name="quantity" id="quantityInput" value="1" min="1" max="1" onchange="calcTotal()" oninput="calcTotal()">

        <div class="total-display">
            <p style="margin:0;color:var(--text-secondary);font-size:14px;margin-bottom:8px;">Total Amount</p>
            <div class="amount" id="totalDisplay">₸0</div>
        </div>

        <button type="submit" class="btn btn-green">Record Sale</button>
        <a href="dashboard.php" class="btn btn-blue" style="margin-left:8px;">Cancel</a>
    </form>
</div>

<script>
// Store all original options for filtering
var allOptions = [];
(function() {
    var select = document.getElementById('productSelect');
    var groups = select.querySelectorAll('optgroup');
    groups.forEach(function(g) {
        var cat = g.getAttribute('data-cat');
        var opts = g.querySelectorAll('option');
        opts.forEach(function(o) {
            allOptions.push({ element: o, cat: cat, group: g });
        });
    });
})();

function filterCat(chip, cat) {
    // Update active chip
    document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');

    var select = document.getElementById('productSelect');
    select.value = '';
    hideInfoBar();
    resetTotal();

    var groups = select.querySelectorAll('optgroup');
    groups.forEach(function(g) {
        var groupCat = g.getAttribute('data-cat');
        if (cat === 'all' || groupCat === cat) {
            g.style.display = '';
        } else {
            g.style.display = 'none';
        }
    });
}

function updatePrice() {
    var select = document.getElementById('productSelect');
    var option = select.options[select.selectedIndex];
    var price  = option.getAttribute('data-price');
    var stock  = option.getAttribute('data-stock');
    var name   = option.getAttribute('data-name');

    if (price) {
        document.getElementById('quantityInput').max   = stock;
        document.getElementById('quantityInput').value = 1;
        calcTotal();
        showInfoBar(name, price, stock);
    } else {
        hideInfoBar();
        resetTotal();
    }
}

function calcTotal() {
    var select   = document.getElementById('productSelect');
    var option   = select.options[select.selectedIndex];
    var price    = parseInt(option.getAttribute('data-price')) || 0;
    var quantity = parseInt(document.getElementById('quantityInput').value) || 0;
    document.getElementById('totalDisplay').textContent = '₸' + (price * quantity).toLocaleString();
}

function showInfoBar(name, price, stock) {
    document.getElementById('infoName').textContent  = name;
    document.getElementById('infoPrice').textContent = '₸' + parseInt(price).toLocaleString();
    document.getElementById('infoStock').textContent = 'Stock: ' + stock;
    document.getElementById('productInfoBar').classList.add('show');
}

function hideInfoBar() {
    document.getElementById('productInfoBar').classList.remove('show');
}

function resetTotal() {
    document.getElementById('totalDisplay').textContent = '₸0';
}
</script>

</body>
</html>
