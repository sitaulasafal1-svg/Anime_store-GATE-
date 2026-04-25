<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'];

// UPDATE PROFILE
if (isset($_POST['update_profile'])) {

    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_name = time() . "_" . basename($_FILES['profile_image']['name']);
        $target = "uploads/profile/" . $image_name;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $target);

        $conn->query("UPDATE users 
            SET username='$username', email='$email', profile_image='$target' 
            WHERE id=$user_id");
    } else {
        $conn->query("UPDATE users 
            SET username='$username', email='$email' 
            WHERE id=$user_id");
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hashed' WHERE id=$user_id");
    }

    $_SESSION['username'] = $username;

    header("Location: customer_profile.php");
    exit();
}

// USER DATA
$user = $conn->query("SELECT username, email, created_at, profile_image FROM users WHERE id = $user_id")->fetch_assoc();

// STATS
$stats = $conn->query("
    SELECT COUNT(*) AS total_orders, SUM(total_price) AS total_spent 
    FROM orders WHERE user_id = $user_id
")->fetch_assoc();

// RECENT ORDERS
$recent_orders = $conn->query("
    SELECT o.id, o.status, o.created_at, o.total_price,
           GROUP_CONCAT(p.name SEPARATOR '|') AS products,
           GROUP_CONCAT(p.image SEPARATOR '|') AS images
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = $user_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
  background: url('assets/images/pfpbg.jpg') no-repeat center center/cover;
}

/* DARK OVERLAY */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: -1;
}

/* GLASS CARD */
.card {
    background: rgba(10, 15, 35, 0.75);
    backdrop-filter: blur(14px);
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 
        0 10px 40px rgba(0,0,0,0.7),
        0 0 20px rgba(138,43,226,0.15);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

/* PROFILE SPECIAL */
.profile-card {
    background: linear-gradient(135deg, #0b1a3a, #101a40);
    box-shadow:
        0 15px 50px rgba(0,0,0,0.8),
        0 0 30px rgba(147,51,234,0.4);
}

/* BUTTON */
.btn {
    background: linear-gradient(135deg, #7c3aed, #9333ea);
    box-shadow: 0 0 15px rgba(147,51,234,0.6);
    transition: 0.3s;
}
.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 0 25px rgba(147,51,234,0.9);
}
</style>
</head>

<body class="text-white">

<!-- NAVBAR -->
<header class="bg-black/40 backdrop-blur-md border-b border-white/10">
  <div class="max-w-7xl mx-auto flex justify-between items-center px-8 py-3">

    <img src="assets/images/LOGO.png" class="h-14">

    <nav class="flex gap-8 text-sm">
      <a href="newstation.php">NewStation</a>
      <a href="customer_home.php">Home</a>
      <a href="shop.php">Shop</a>
      <a href="customer_orders.php">Orders</a>
      <a href="#" class="font-semibold">Profile</a>
    </nav>

    <a href="logout.php" class="bg-white text-black px-4 py-2 rounded-lg">Logout</a>
  </div>
</header>

<!-- MAIN -->
<main class="max-w-6xl mx-auto px-6 py-20">

  <!-- PROFILE CARD -->
  <div class="card profile-card mb-12 px-12 py-8 flex items-center justify-between">

    <div class="flex items-center gap-6">

      <img src="<?php echo !empty($user['profile_image']) 
        ? $user['profile_image'] 
        : 'assets/images/default-avatar.png'; ?>"
        class="w-24 h-24 rounded-full object-cover border-4 border-purple-500 shadow-[0_0_25px_rgba(147,51,234,0.8)]">

      <div>
        <h2 class="text-3xl font-bold"><?php echo $user['username']; ?></h2>
        <p class="text-gray-300"><?php echo $user['email']; ?></p>
        <p class="text-gray-400 text-sm">
          Joined <?php echo date("F Y", strtotime($user['created_at'])); ?>
        </p>
      </div>

    </div>

    <button onclick="openModal()" class="btn px-6 py-3 rounded-lg font-semibold">
      Edit Profile
    </button>

  </div>

  <!-- STATS GRID -->
  <div class="grid md:grid-cols-3 gap-6 mb-12">

    <div class="card p-6 text-center">
      <h3 class="text-gray-400 text-sm">Orders</h3>
      <p class="text-3xl font-bold"><?php echo $stats['total_orders'] ?? 0; ?></p>
    </div>

    <div class="card p-6 text-center">
      <h3 class="text-gray-400 text-sm">Total Spent</h3>
      <p class="text-3xl font-bold text-green-400">
        Rs. <?php echo number_format($stats['total_spent'] ?? 0,2); ?>
      </p>
    </div>

    <div class="card p-6 text-center">
      <h3 class="text-gray-400 text-sm">Status</h3>
      <p class="text-2xl font-semibold text-purple-400">Active</p>
    </div>

  </div>

  <!-- RECENT ORDERS -->
  <?php while($o = $recent_orders->fetch_assoc()): 

$names = explode('|', $o['products']);
$imgs  = explode('|', $o['images']);
?>

<div class="flex items-center gap-4 p-4 mb-3 rounded-xl 
            bg-[#0f172a]/70 backdrop-blur-md border border-white/10 
            shadow-lg hover:shadow-xl transition">

  <!-- IMAGE -->
  <img src="assets/images/<?php echo $imgs[0]; ?>" 
       class="w-16 h-16 rounded-lg object-cover border border-white/10">

  <!-- DETAILS -->
  <div class="flex-1">

    <!-- PRODUCT NAME -->
    <p class="font-semibold text-sm">
      <?php echo $names[0]; ?>
    </p>

    <!-- MORE ITEMS -->
    <?php if(count($names) > 1): ?>
      <p class="text-xs text-gray-500">
        +<?php echo count($names)-1; ?> more items
      </p>
    <?php endif; ?>

    <!-- DATE -->
    <p class="text-gray-400 text-xs">
      <?php echo date("d M Y", strtotime($o['created_at'])); ?>
    </p>

  </div>

  <!-- PRICE -->
  <div class="text-right">
    <p class="text-green-400 font-semibold text-sm">
      Rs. <?php echo number_format($o['total_price'],2); ?>
    </p>
  </div>

</div>

<?php endwhile; ?>

  </div>

</main>

<!-- MODAL -->
<div id="editModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center">

  <div class="card p-6 w-full max-w-md">

    <h2 class="mb-4 text-lg font-bold">Edit Profile</h2>

    <form method="POST" enctype="multipart/form-data">

      <input type="file" name="profile_image" class="mb-3">

      <input type="text" name="username"
             value="<?php echo $user['username']; ?>"
             class="w-full mb-3 p-2 bg-black border border-white/10 rounded">

      <input type="email" name="email"
             value="<?php echo $user['email']; ?>"
             class="w-full mb-3 p-2 bg-black border border-white/10 rounded">

      <input type="password" name="password"
             placeholder="New password"
             class="w-full mb-3 p-2 bg-black border border-white/10 rounded">

      <div class="mt-6 flex justify-end gap-3">

        <button type="button" onclick="closeModal()" 
          class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20">
          Cancel
        </button>

        <button type="submit" name="update_profile"
          class="btn px-4 py-2 rounded-lg font-semibold">
          Save Changes
        </button>

      </div>

    </form>

  </div>

</div>

<!-- FOOTER -->
<footer class="bg-[#0b0b0f] border-t border-white/10 fixed bottom-0 w-full">
  <div class="text-center py-2 text-xs text-gray-400">
    © 2026 Anime Store • OTAKU STORE
  </div>
</footer>

<script>
function openModal(){
  document.getElementById('editModal').classList.remove('hidden');
  document.getElementById('editModal').classList.add('flex');
}
function closeModal(){
  document.getElementById('editModal').classList.add('hidden');
}
</script>

</body>
</html>