<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$edit_product = null;

// LOAD PRODUCT FOR EDIT
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM products WHERE id=$id");
    $edit_product = $res->fetch_assoc();
}

// ADD PRODUCT
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    if ($price <= 0) die("Invalid price");

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/" . $image);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, category, description, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $name, $category, $description, $price, $image);
    $stmt->execute();

    header("Location: manage_products.php");
    exit();
}

// UPDATE PRODUCT
if (isset($_POST['update_product'])) {
    $id = intval($_GET['edit']);
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/" . $image);

        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, description=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("sssdsi", $name, $category, $description, $price, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, description=?, price=? WHERE id=?");
        $stmt->bind_param("sssdi", $name, $category, $description, $price, $id);
    }

    $stmt->execute();
    header("Location: manage_products.php");
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT image FROM products WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if (!empty($row['image']) && file_exists("assets/images/" . $row['image'])) {
            unlink("assets/images/" . $row['image']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: manage_products.php");
    exit();
}

// DELETE ALL
if (isset($_POST['delete_all'])) {
    $res = $conn->query("SELECT image FROM products");
    while ($row = $res->fetch_assoc()) {
        if (!empty($row['image']) && file_exists("assets/images/" . $row['image'])) {
            unlink("assets/images/" . $row['image']);
        }
    }
    $conn->query("DELETE FROM products");

    header("Location: manage_products.php");
    exit();
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { background:#f8fafc; }
.sidebar { background:#0b3c91; }
.card { background:#fff; box-shadow:0 4px 20px rgba(0,0,0,0.05); }
</style>
</head>

<body class="text-gray-800">

<div class="flex min-h-screen">

<!-- ✅ EXACT SAME SIDEBAR -->
<aside class="sidebar w-72 p-6 flex flex-col text-white">
    <h1 class="text-2xl font-bold mb-8">WELCOME, Admin</h1>

    <nav class="space-y-3">
        <a href="admin_dashboard.php" class="block p-3 rounded-lg hover:bg-white/20">Dashboard</a>
        <a href="manage_users.php" class="block p-3 rounded-lg hover:bg-white/20">Users</a>
        <a href="manage_products.php" class="block p-3 rounded-lg bg-white/20">Products</a>
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

<h2 class="text-3xl font-bold mb-6">Manage Products</h2>

<!-- FORM -->
<div class="card p-6 rounded-xl mb-8">
<form method="POST" enctype="multipart/form-data" class="space-y-4">

<input type="text" name="name" required
value="<?php echo $edit_product['name'] ?? ''; ?>"
placeholder="Product Name"
class="w-full p-3 border rounded-lg">

<select name="category" required class="w-full p-3 border rounded-lg">
<?php 
$cats = ['Keychains','Wallets','Bags','Jackets','Action Figures','Collectibles'];
foreach ($cats as $cat):
?>
<option <?php if(($edit_product['category'] ?? '') == $cat) echo 'selected'; ?>>
<?php echo $cat; ?>
</option>
<?php endforeach; ?>
</select>

<textarea name="description" class="w-full p-3 border rounded-lg"><?php echo $edit_product['description'] ?? ''; ?></textarea>

<input type="number" step="0.01" name="price" required
value="<?php echo $edit_product['price'] ?? ''; ?>"
placeholder="Price"
class="w-full p-3 border rounded-lg">

<div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer bg-gray-50 hover:bg-gray-100 transition">

    <p class="text-gray-500">Drag & Drop Image Here</p>
    <p class="text-sm text-gray-400">or click to select file</p>

    <input type="file" name="image" id="fileInput" accept="image/*" class="hidden">

    <img id="preview" class="mx-auto mt-3 hidden rounded-lg" width="120">

</div>

<?php if ($edit_product): ?>
<button type="submit" name="update_product" class="bg-yellow-500 text-white px-6 py-3 rounded-lg">
Update Product
</button>
<?php else: ?>
<button type="submit" name="add_product" class="bg-[#0b3c91] text-white px-6 py-3 rounded-lg">
Add Product
</button>
<?php endif; ?>

</form>
</div>

<!-- TABLE -->
<div class="card p-6 rounded-xl">

<form method="POST" class="mb-4">
<button name="delete_all" onclick="return confirm('Delete all?')" class="bg-red-600 text-white px-4 py-2 rounded-lg">
Delete All
</button>
</form>

<table class="w-full text-sm">
<thead class="border-b">
<tr>
<th class="p-3">ID</th>
<th class="p-3">Name</th>
<th class="p-3">Category</th>
<th class="p-3">Price</th>
<th class="p-3">Image</th>
<th class="p-3">Action</th>
</tr>
</thead>

<tbody>
<?php while ($p = $products->fetch_assoc()): ?>
<tr class="border-b hover:bg-gray-50">
<td class="p-3"><?php echo $p['id']; ?></td>
<td class="p-3"><?php echo $p['name']; ?></td>
<td class="p-3"><?php echo $p['category']; ?></td>
<td class="p-3"><?php echo $p['price']; ?></td>
<td class="p-3">
<?php if ($p['image']): ?>
<img src="assets/images/<?php echo $p['image']; ?>" class="w-12 h-12 rounded">
<?php endif; ?>
</td>
<td class="p-3 space-x-2">

<a href="?edit=<?php echo $p['id']; ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">
Edit
</a>

<a href="?delete=<?php echo $p['id']; ?>" 
onclick="return confirm('Delete?')" 
class="bg-red-500 text-white px-2 py-1 rounded">
Delete
</a>

</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>
</div>

</main>
</div>

<script>
const dropArea = document.getElementById("drop-area");
const fileInput = document.getElementById("fileInput");
const preview = document.getElementById("preview");

// Click to open file
dropArea.addEventListener("click", () => fileInput.click());

// File selected
fileInput.addEventListener("change", function () {
    showPreview(this.files[0]);
});

// Drag over
dropArea.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropArea.classList.add("bg-gray-200");
});

// Drag leave
dropArea.addEventListener("dragleave", () => {
    dropArea.classList.remove("bg-gray-200");
});

// Drop file
dropArea.addEventListener("drop", (e) => {
    e.preventDefault();
    dropArea.classList.remove("bg-gray-200");

    const file = e.dataTransfer.files[0];
    fileInput.files = e.dataTransfer.files;

    showPreview(file);
});

// Preview function
function showPreview(file) {
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove("hidden");
    };
    reader.readAsDataURL(file);
}
</script>

</body>
</html>