<?php
// login.php
// This page lets the user log in with username and password

session_start();
include "connect.php";

$error = "";

// When the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL statement: SELECT user from Users table (one table)
    $sql    = "SELECT * FROM Users WHERE Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $sql);

    // Conditional: check if a matching user was found
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Save user info in session so we remember who is logged in
        $_SESSION['user_id']  = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role']     = $user['Role'];

        // Send to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Wrong username or password
        $error = "Wrong username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .input-group {
            position: relative;
            margin-bottom: 8px;
        }
        .input-group input {
            position: relative;
            z-index: 1;
        }
        .input-group label {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            transition: all 0.3s var(--ease-out);
            color: var(--text-muted);
            font-weight: 500;
            background: transparent;
            padding: 0 4px;
        }
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: var(--accent);
            background: var(--bg-primary);
        }
        .input-group input:focus + label {
            color: var(--accent);
        }
        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-primary);
            border: 2px solid var(--accent);
            border-radius: var(--radius-sm);
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14px;
            color: var(--accent);
            box-shadow: var(--shadow-lg);
            transform: translateX(400px);
            transition: transform 0.4s var(--ease-out);
            z-index: 9999;
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast.success {
            border-color: var(--success);
            color: var(--success);
        }
        .toast.error {
            border-color: var(--danger);
            color: var(--danger);
        }
    </style>
</head>
<body>

<div class="toast" id="toast"></div>

<div class="login-wrapper">
    <div class="login-box">
        <div style="font-size: 56px;">⚡</div>
        <div class="login-title">Joe's Electronics</div>

        <?php if ($error != "") { 
            echo "<div class='msg-red'>$error</div>"; 
        } ?>

        <form method="POST" action="login.php" id="loginForm">
            <div class="input-group">
                <input type="text" name="username" id="username" placeholder=" " autocomplete="off" required>
                <label for="username">Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Password</label>
            </div>

            <button type="submit" class="btn btn-blue" style="width:100%; padding:14px; font-size:15px; margin-top: 16px;">
                Sign In
            </button>
        </form>

        <p style="font-size:12px; color:var(--text-muted); margin-top:24px;">
            manager / admin123 &nbsp;|&nbsp; cashier / cashier123
        </p>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = 'Signing in...';
    btn.style.opacity = '0.7';
    
    // Show toast notification
    showToast('Signing in...', 'info');
});

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast ' + type;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
</script>

</body>
</html>
