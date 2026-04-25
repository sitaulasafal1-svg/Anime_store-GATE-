<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: customer_home.php");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login – Anime Store</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

/* BACKGROUND IMAGE */
body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;

    background: url('assets/images/b5d7096a-1833-4a45-bb89-e3ae10010a04.jpg') no-repeat center center fixed;
    background-size: cover;
    position: relative;
}

/* DARK OVERLAY */
body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(10, 8, 25, 0.85);
    z-index: -1;
}

/* LOGIN CARD */
.login-wrapper {
    width: 880px;
    max-width: 95%;
    height: 480px;
    background: #14142b;
    border-radius: 18px;
    display: flex;
    overflow: hidden;
    box-shadow: 0 35px 90px rgba(0,0,0,0.6);
}

/* LEFT LOGIN */
.login-left {
    width: 45%;
    padding: 45px;
    background: #101024;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-left h2 {
    font-size: 26px;
    margin-bottom: 25px;
}

.input-group {
    margin-bottom: 18px;
}

.input-group input {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
    border: none;
    background: #23234a;
    color: #fff;
    font-size: 15px;
}

.input-group input::placeholder {
    color: #bcbce6;
}

.login-left button {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    background: #ff3d81;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.25s;
}

.login-left button:hover {
    background: #ff5a9a;
}

.login-left p {
    margin-top: 18px;
    font-size: 14px;
}

.login-left a {
    color: #ff3d81;
    text-decoration: none;
    font-weight: 600;
}

/* RIGHT IMAGE */
.login-right {
    width: 55%;
    position: relative;
}

.login-right img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* PERFECT FIT */
}

/* SOFT COLOR GLOW */
.login-right::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top left,
        rgba(0,0,0,0.45),
        rgba(0,0,0,0.1)
    );
}

/* MOBILE */
@media (max-width: 768px) {
    .login-wrapper {
        flex-direction: column;
        height: auto;
    }

    .login-left,
    .login-right {
        width: 100%;
    }

    .login-right {
        height: 220px;
    }
}

</style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-left">
        <h2>Login to your account</h2>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">LOGIN</button>

            <p>Don’t have an account? <a href="register.php">Register</a></p>
        </form>
    </div>

    <div class="login-right">
        <div class="blob one"></div>
        <div class="blob two"></div>
        <img src="assets/images/loginpage.jpg" alt="Login Illustration">
    </div>

</div>


<div id="toast" style="
position: fixed;
top: 30px;
right: 30px;
background: #14142b;
color: white;
padding: 16px 24px;
border-radius: 10px;
box-shadow: 0 10px 30px rgba(0,0,0,0.5);
display: none;
z-index: 999;
font-weight: 500;
transition: 0.4s;
">
✅ Registration successful! Please login.
</div>

<script>
const params = new URLSearchParams(window.location.search);

if (params.get('success') === '1') {
    const toast = document.getElementById('toast');
    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
    }, 2500);

    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}
</script>

</body>
</html>



