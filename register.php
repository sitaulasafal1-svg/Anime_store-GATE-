<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'customer';

    $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already exists!');</script>";
    } else {
        $sql = $conn->prepare(
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $sql->bind_param("ssss", $username, $email, $password, $role);

        if ($sql->execute()) {
            echo "<script>
window.location='login.php?success=1';
</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register – Anime Store</title>

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
    background: url('assets/images/registerpage.jpg') no-repeat center center fixed;
    background-size: cover;
    position: relative;
}

/* OVERLAY */
body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(10, 8, 25, 0.85);
    z-index: -1;
}

/* CARD */
.register-wrapper {
    width: 880px;
    max-width: 95%;
    height: 500px;
    background: #14142b;
    border-radius: 18px;
    display: flex;
    overflow: hidden;
    box-shadow: 0 35px 90px rgba(0,0,0,0.6);
}

/* LEFT FORM */
.register-left {
    width: 45%;
    padding: 45px;
    background: #101024;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.register-left h2 {
    font-size: 26px;
    margin-bottom: 25px;
}

.input-group {
    margin-bottom: 16px;
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

.register-left button {
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

.register-left button:hover {
    background: #ff5a9a;
}

.register-left p {
    margin-top: 18px;
    font-size: 14px;
}

.register-left a {
    color: #ff3d81;
    text-decoration: none;
    font-weight: 600;
}

/* RIGHT IMAGE */
.register-right {
    width: 55%;
    position: relative;
}

.register-right img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.register-right::after {
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
    .register-wrapper {
        flex-direction: column;
        height: auto;
    }

    .register-left,
    .register-right {
        width: 100%;
    }

    .register-right {
        height: 220px;
    }
}
</style>
</head>

<body>

<div class="register-wrapper">

    <div class="register-left">
        <h2>Create your account</h2>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">REGISTER</button>

            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>

    <div class="register-right">
        <img src="assets/images/download (1).jpg" alt="Register Illustration">
    </div>

</div>

</body>
</html>
