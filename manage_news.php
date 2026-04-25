<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/' . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO news (title, description, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $imageName);
    $stmt->execute();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM news WHERE id = $id");
}

$news = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage News</title>
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
        <a href="manage_users.php" class="block p-3 rounded-lg hover:bg-white/20">Users</a>
        <a href="manage_products.php" class="block p-3 rounded-lg hover:bg-white/20">Products</a>
        <a href="manage_orders.php" class="block p-3 rounded-lg hover:bg-white/20">Orders</a>
        <a href="manage_news.php" class="block p-3 rounded-lg bg-white/20">News</a>
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

<h2 class="text-3xl font-bold mb-6">Manage News</h2>

<!-- ADD NEWS -->
<div class="card p-6 rounded-xl mb-8 max-w-3xl">
<form method="POST" enctype="multipart/form-data" class="space-y-4">

<input type="text" name="title" placeholder="News title..." required
class="w-full p-3 border rounded-lg">

<textarea name="description" rows="4" placeholder="Write news..." required
class="w-full p-3 border rounded-lg"></textarea>

<div class="flex justify-between items-center">
<input type="file" name="image">
<button class="bg-[#0b3c91] text-white px-6 py-2 rounded-lg">
Post News
</button>
</div>

</form>
</div>

<!-- NEWS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

<?php if ($news && $news->num_rows > 0): ?>
<?php while ($n = $news->fetch_assoc()): ?>

<div class="card rounded-xl overflow-hidden flex flex-col">

<div class="p-4 border-b flex justify-between text-sm">
<div>
<strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong><br>
<span class="text-gray-500">
<?php echo date('M d, Y', strtotime($n['created_at'])); ?>
</span>
</div>

<a href="?delete=<?php echo $n['id']; ?>"
onclick="return confirm('Delete this news?')"
class="text-red-500">
Delete
</a>
</div>

<div class="p-4 flex-1">
<h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($n['title']); ?></h3>
<p class="text-sm text-gray-600">
<?php echo nl2br(htmlspecialchars($n['description'])); ?>
</p>
</div>

<?php if (!empty($n['image'])): ?>
<img src="assets/images/<?php echo $n['image']; ?>" class="w-full h-48 object-cover">
<?php endif; ?>

</div>

<?php endwhile; ?>
<?php else: ?>
<p>No news yet</p>
<?php endif; ?>

</div>

</main>
</div>
</body>
</html>