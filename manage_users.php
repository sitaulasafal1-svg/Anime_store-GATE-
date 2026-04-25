<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    header("Location: manage_users.php");
    exit();
}

$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { background:#f8fafc; }
.sidebar { background:#0b3c91; }
.card { background:#fff; box-shadow:0 4px 20px rgba(0,0,0,0.05); }
</style>
</head>

<body class="text-gray-800">
<div class="flex min-h-screen">

<!-- ✅ SAME SIDEBAR -->
<aside class="sidebar w-72 p-6 flex flex-col text-white">
    <h1 class="text-2xl font-bold mb-8">WELCOME, Admin</h1>

    <nav class="space-y-3">
        <a href="admin_dashboard.php" class="block p-3 rounded-lg hover:bg-white/20">Dashboard</a>
        <a href="manage_users.php" class="block p-3 rounded-lg bg-white/20">Users</a>
        <a href="manage_products.php" class="block p-3 rounded-lg hover:bg-white/20">Products</a>
        <a href="manage_orders.php" class="block p-3 rounded-lg hover:bg-white/20">Orders</a>
        <a href="manage_news.php" class="block p-3 rounded-lg hover:bg-white/20">News</a>
    </nav>

    <div class="fixed bottom-5 left-5 z-50">
    <a href="logout.php"
    class="bg-white text-black px-6 py-3 rounded-xl font-semibold shadow-md hover:bg-gray-100 transition">

        Logout
    </a>
</div>
</aside>

<!-- MAIN -->
<main class="flex-1 p-8">

<h2 class="text-3xl font-bold mb-6">Manage Users</h2>

<div class="card p-6 rounded-xl overflow-x-auto">

<table class="w-full text-left border-collapse">
<thead>
<tr class="border-b">
<th class="p-3">ID</th>
<th class="p-3">Username</th>
<th class="p-3">Email</th>
<th class="p-3">Role</th>
<th class="p-3">Created</th>
<th class="p-3">Action</th>
</tr>
</thead>

<tbody>
<?php while ($user = $result->fetch_assoc()): ?>
<tr class="border-b hover:bg-gray-50">
<td class="p-3"><?php echo $user['id']; ?></td>
<td class="p-3"><?php echo htmlspecialchars($user['username']); ?></td>
<td class="p-3"><?php echo htmlspecialchars($user['email']); ?></td>
<td class="p-3"><?php echo ucfirst($user['role']); ?></td>
<td class="p-3"><?php echo $user['created_at']; ?></td>
<td class="p-3">
<?php if ($user['id'] !== $_SESSION['user_id']): ?>
<a href="?delete=<?php echo $user['id']; ?>" 
class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600"
onclick="return confirm('Delete this user?');">
Delete
</a>
<?php else: ?>
<span class="text-gray-400">You</span>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

</div>

</main>
</div>

</body>
</html>