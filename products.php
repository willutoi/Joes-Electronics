<?php
// products.php
// Manager can add, edit, delete and filter products by category

session_start();
include "connect.php";
include "navbar.php";

// Conditional: only managers can access this page
if ($_SESSION['role'] != 'Manager') {
    echo "<div class='box'><p>Access denied. Managers only.</p></div>";
    exit();
}

$message = "";
$msgType = "";

// When the ADD form is submitted
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $name     = $_POST['name'];
    $category = $_POST['category'];
    $buying   = $_POST['buying_price'];
    $selling  = $_POST['selling_price'];
    $stock    = $_POST['stock'];

    if ($selling <= $buying) {
        $message = "Selling price must be higher than buying price!";
        $msgType = "red";
    } else {
        $checkSql    = "SELECT * FROM Products WHERE ProductName = '$name'";
        $checkResult = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            $existing = mysqli_fetch_assoc($checkResult);
            $newStock = $existing['CurrentStock'] + $stock;
            $sql = "UPDATE Products SET Category='$category', BuyingPrice='$buying', SellingPrice='$selling', CurrentStock='$newStock' WHERE ProductName='$name'";
            mysqli_query($conn, $sql);
            $message = "Product '$name' found! Added $stock units (new total: $newStock).";
            $msgType = "green";
        } else {
            $sql = "INSERT INTO Products (ProductName, Category, BuyingPrice, SellingPrice, CurrentStock)
                    VALUES ('$name', '$category', '$buying', '$selling', '$stock')";
            mysqli_query($conn, $sql);
            $message = "Product '$name' added!";
            $msgType = "green";
        }
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $productID = $_POST['product_id'];
    $sql = "DELETE FROM Products WHERE ProductID = '$productID'";
    mysqli_query($conn, $sql);
    $message = "Product deleted.";
    $msgType = "green";
}

// ── FILTERS ──────────────────────────────────────────────
$filterCat    = isset($_GET['cat'])    ? $_GET['cat']    : '';
$filterSearch = isset($_GET['search']) ? $_GET['search'] : '';

// Build WHERE clause
$where = "1=1";
if ($filterCat != '')    $where .= " AND Category = '" . mysqli_real_escape_string($conn, $filterCat) . "'";
if ($filterSearch != '') $where .= " AND ProductName LIKE '%" . mysqli_real_escape_string($conn, $filterSearch) . "%'";

// Fetch filtered products
$sql      = "SELECT * FROM Products WHERE $where ORDER BY Category, ProductName";
$products = mysqli_query($conn, $sql);

$totalValue = 0;
$allRows    = array();
while ($p = mysqli_fetch_assoc($products)) {
    $totalValue += ($p['SellingPrice'] * $p['CurrentStock']);
    $allRows[]   = $p;
}

// All categories for filter buttons
$catSql    = "SELECT DISTINCT Category FROM Products ORDER BY Category";
$catResult = mysqli_query($conn, $catSql);
$categories = [];
while ($c = mysqli_fetch_assoc($catResult)) {
    $categories[] = $c['Category'];
}

// Category icons
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

// Group rows by category for table display
$grouped = [];
foreach ($allRows as $row) {
    $grouped[$row['Category']][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── ADD FORM ── */
        .add-form {
            background: var(--bg-primary);
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px;
            margin: 24px 0;
            transition: all 0.3s var(--ease-out);
        }
        .add-form:hover { box-shadow: var(--shadow-md); }
        .add-form:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-light);
        }
        .form-row-5 {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        @media(max-width:900px){ .form-row-5{ grid-template-columns:1fr 1fr; } }

        /* ── INVENTORY STRIP ── */
        .inv-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-primary);
            border: 2px solid var(--success);
            border-left: 6px solid var(--success);
            border-radius: var(--radius-md);
            padding: 20px 28px;
            margin: 24px 0;
            animation: slideInLeft 0.5s var(--ease-out);
            transition: all 0.3s var(--ease-out);
        }
        .inv-strip:hover { box-shadow: var(--shadow-md); transform: translateX(4px); }
        .inv-strip-label { display: flex; align-items: center; gap: 16px; }
        .inv-strip-icon { font-size: 28px; }
        .inv-strip-value { font-size: 36px; font-weight: 800; color: var(--success); letter-spacing: -1px; }

        /* ── CATEGORY FILTER BAR ── */
        .cat-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
            padding: 20px;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
        }
        .cat-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            text-decoration: none;
            border: 2px solid transparent;
            transition: all 0.25s var(--ease-out);
            white-space: nowrap;
        }
        .cat-btn:hover {
            background: var(--accent-light);
            color: var(--accent);
            border-color: var(--accent);
            transform: translateY(-2px);
        }
        .cat-btn.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        .cat-btn .count {
            background: rgba(255,255,255,0.25);
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 11px;
        }
        .cat-btn:not(.active) .count {
            background: var(--bg-tertiary);
            color: var(--text-muted);
        }

        /* ── SEARCH BAR ── */
        .search-row {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            align-items: center;
        }
        .search-row input {
            flex: 1;
            margin-bottom: 0;
        }

        /* ── CATEGORY GROUP HEADER ── */
        .cat-group-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: linear-gradient(90deg, var(--accent-light), transparent);
            border-left: 4px solid var(--accent);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            margin: 32px 0 8px;
            font-size: 16px;
            font-weight: 800;
            color: var(--text-primary);
        }
        .cat-group-header .icon { font-size: 22px; }
        .cat-group-header .cat-count {
            margin-left: auto;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--bg-tertiary);
            padding: 3px 10px;
            border-radius: 12px;
        }
        .product-row { transition: all 0.3s var(--ease-out); }
    </style>
</head>
<body>

<div class="box">
    <h2>Products</h2>

    <?php if ($message != "") { echo "<div class='msg-$msgType'>$message</div>"; } ?>

    <!-- ── ADD FORM ── -->
    <div class="add-form">
        <h3 style="margin-top:0;">Add / Restock Product</h3>
        <form method="POST" action="products.php">
            <input type="hidden" name="action" value="add">
            <div class="form-row-5">
                <div>
                    <label>Product Name</label>
                    <input type="text" name="name" required placeholder="e.g. iPhone 16 Pro 256GB">
                </div>
                <div>
                    <label>Category</label>
                    <select name="category" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label>Buying Price (₸)</label>
                    <input type="number" name="buying_price" required placeholder="0">
                </div>
                <div>
                    <label>Selling Price (₸)</label>
                    <input type="number" name="selling_price" required placeholder="0">
                </div>
                <div>
                    <label>Stock</label>
                    <input type="number" name="stock" required placeholder="0">
                </div>
            </div>
            <button type="submit" class="btn btn-green">Add / Restock</button>
        </form>
    </div>

    <!-- ── INVENTORY VALUE ── -->
    <div class="inv-strip">
        <div class="inv-strip-label">
            <span class="inv-strip-icon">🗃️</span>
            <div>
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);">Total Inventory Value</div>
                <div style="font-size:13px;color:var(--text-secondary);margin-top:2px;"><?php echo count($allRows); ?> products shown</div>
            </div>
        </div>
        <div class="inv-strip-value">₸<?php echo number_format($totalValue); ?></div>
    </div>

    <!-- ── CATEGORY FILTER ── -->
    <div class="cat-bar">
        <?php
        // Count per category for badges
        $countSql = "SELECT Category, COUNT(*) as cnt FROM Products GROUP BY Category ORDER BY Category";
        $countRes = mysqli_query($conn, $countSql);
        $catCounts = [];
        while ($cc = mysqli_fetch_assoc($countRes)) $catCounts[$cc['Category']] = $cc['cnt'];

        $allCount = array_sum($catCounts);
        $activeClass = ($filterCat == '') ? 'active' : '';
        echo "<a href='products.php" . ($filterSearch ? "?search=".urlencode($filterSearch) : "") . "' class='cat-btn $activeClass'>🔍 All <span class='count'>$allCount</span></a>";
        foreach ($categories as $cat):
            $icon = $catIcons[$cat] ?? '📦';
            $cnt  = $catCounts[$cat] ?? 0;
            $active = ($filterCat == $cat) ? 'active' : '';
            $url = 'products.php?cat=' . urlencode($cat) . ($filterSearch ? '&search='.urlencode($filterSearch) : '');
            echo "<a href='$url' class='cat-btn $active'>$icon $cat <span class='count'>$cnt</span></a>";
        endforeach;
        ?>
    </div>

    <!-- ── SEARCH ── -->
    <form method="GET" action="products.php" class="search-row">
        <?php if ($filterCat): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($filterCat) ?>"><?php endif; ?>
        <input type="text" name="search" placeholder="Search product name..." value="<?= htmlspecialchars($filterSearch) ?>">
        <button type="submit" class="btn btn-blue">Search</button>
        <?php if ($filterSearch || $filterCat): ?>
            <a href="products.php" class="btn btn-blue" style="background:var(--bg-secondary);color:var(--text-secondary);">✕ Clear</a>
        <?php endif; ?>
    </form>

    <!-- ── RESULTS COUNT ── -->
    <p style="margin-bottom:8px;">
        Showing <strong><?= count($allRows) ?></strong> products
        <?php if ($filterCat) echo "in <strong>$filterCat</strong>"; ?>
        <?php if ($filterSearch) echo "matching <strong>\"" . htmlspecialchars($filterSearch) . "\"</strong>"; ?>
    </p>

    <!-- ── GROUPED TABLE ── -->
    <?php if (count($allRows) == 0): ?>
        <p style="padding:40px;text-align:center;color:var(--text-muted);">No products found.</p>
    <?php else: ?>
        <?php foreach ($grouped as $cat => $rows):
            $icon = $catIcons[$cat] ?? '📦';
        ?>
        <div class="cat-group-header">
            <span class="icon"><?= $icon ?></span>
            <?= $cat ?>
            <span class="cat-count"><?= count($rows) ?> items</span>
        </div>
        <table>
            <tr>
                <th>Name</th>
                <th>Buying Price</th>
                <th>Selling Price</th>
                <th>Stock</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($rows as $key => $p):
                $animDelay = $key * 0.03;
                if ($p['CurrentStock'] == 0) $stockStyle = "color:var(--danger);font-weight:800;";
                elseif ($p['CurrentStock'] <= 5) $stockStyle = "color:var(--warning);font-weight:700;";
                else $stockStyle = "color:var(--success);font-weight:600;";
            ?>
            <tr class="product-row" style="animation:fadeInUp 0.3s var(--ease-out) backwards;animation-delay:<?= $animDelay ?>s">
                <td><strong><?= htmlspecialchars($p['ProductName']) ?></strong></td>
                <td>₸<?= number_format($p['BuyingPrice']) ?></td>
                <td style="font-weight:700;color:var(--accent);">₸<?= number_format($p['SellingPrice']) ?></td>
                <td style="<?= $stockStyle ?>"><?= $p['CurrentStock'] ?></td>
                <td>
                    <form method="POST" action="products.php" onsubmit="return confirm('Delete this product?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= $p['ProductID'] ?>">
                        <button type="submit" class="btn btn-red">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
</body>
</html>
