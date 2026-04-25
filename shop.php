<?php
session_start();
include 'includes/db.php';

/* ========= AUTH ========= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ========= CART ========= */
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

// ADD
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header("Location: shop.php");
    exit();
}

// REMOVE
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header("Location: shop.php");
    exit();
}

// INCREASE
if (isset($_GET['inc'])) {
    $id = (int)$_GET['inc'];
    $_SESSION['cart'][$id]++;
}

// DECREASE
if (isset($_GET['dec'])) {
    $id = (int)$_GET['dec'];
    $_SESSION['cart'][$id]--;
    if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
}

/* ========= FAVORITES ========= */
if (isset($_POST['favorite_action'])) {
    $pid = (int)$_POST['product_id'];

    if ($_POST['favorite_action'] === 'add') {
        $conn->query("INSERT IGNORE INTO favorites (user_id, product_id) VALUES ($user_id,$pid)");
    } else {
        $conn->query("DELETE FROM favorites WHERE user_id=$user_id AND product_id=$pid");
    }

    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

/* ========= CATEGORIES ========= */
$categories = [
    'Favorites','Recommended','All',
    'Keychains','Wallets','Bags',
    'Jackets','Action Figures','Collectibles'
];

$current_category = $_GET['category'] ?? 'Recommended';
$search = $_GET['search'] ?? '';

/* ========= SAFE CART IDS ========= */
$cart_ids = !empty($_SESSION['cart'])
    ? implode(',', array_map('intval', array_keys($_SESSION['cart'])))
    : "0";

/* ========= PRODUCTS ========= */

// SEARCH ALGORITHM
if (!empty($search)) {

    $search = $conn->real_escape_string($search);

    $products = $conn->query("
        SELECT * FROM products 
        WHERE name LIKE '%$search%' 
        ORDER BY id DESC
    ");

} elseif ($current_category === 'Favorites') {

    $products = $conn->query("  
        SELECT p.* 
        FROM products p
        JOIN favorites f ON p.id = f.product_id
        WHERE f.user_id = $user_id
    ");

} elseif ($current_category === 'Recommended') {

// RECOMMENDATION ALGORITHM
    $products = $conn->query("
        SELECT p.*,

        (
    /* FAVORITES */
    (CASE 
        WHEN p.category IN (
            SELECT DISTINCT pr.category
            FROM products pr
            JOIN favorites f ON pr.id = f.product_id
            WHERE f.user_id = $user_id
        )
        THEN 3 ELSE 0 
    END)

    +

    /* CART */
    (CASE 
        WHEN p.category IN (
            SELECT DISTINCT category
            FROM products
            WHERE id IN ($cart_ids)
        )
        THEN 2 ELSE 0 
    END)

    +

    /* 🔥 PURCHASE HISTORY (ADD THIS HERE) */
    (CASE 
        WHEN p.category IN (
            SELECT DISTINCT pr.category
            FROM products pr
            JOIN order_items oi ON pr.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = $user_id
        )
        THEN 2 ELSE 0
    END)

    +

    /* POPULAR */
    (CASE 
        WHEN pop.product_id IS NOT NULL
        THEN 3 ELSE 0 
    END)

) AS score

        FROM products p

        LEFT JOIN (
            SELECT product_id
            FROM order_items
            GROUP BY product_id
            ORDER BY COUNT(*) DESC
            LIMIT 10
        ) AS pop ON p.id = pop.product_id

        ORDER BY score DESC, RAND()
        LIMIT 20
    ");


} elseif ($current_category === 'All') {

    $products = $conn->query("
        SELECT * FROM products ORDER BY id DESC
    ");

} else {

    $cat = $conn->real_escape_string($current_category);

    $products = $conn->query("
        SELECT * FROM products 
        WHERE category = '$cat'
        ORDER BY id DESC
    ");
}



/* ========= FAVORITES LIST ========= */
$user_favorites = [];
$res = $conn->query("SELECT product_id FROM favorites WHERE user_id = $user_id");

while ($row = $res->fetch_assoc()) {
    $user_favorites[] = $row['product_id'];
}

/* ========= CHECKOUT ========= */
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {

    $total = 0;

    foreach ($_SESSION['cart'] as $pid => $qty) {
        $product = $conn->query("SELECT price FROM products WHERE id=$pid")->fetch_assoc();
        $total += $product['price'] * $qty;
    }

    $_SESSION['temp_cart'] = $_SESSION['cart'];
    $_SESSION['total_amount'] = $total;

    header("Location: esewa_payment.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Shop</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { background:#0b0b0f; color:white; }
.card { background:#1a1a25; border-radius:12px; }

footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #0b0b0f;
    border-top: 1px solid #ffffff1a;
}

/* Add padding to the main content to avoid footer covering it */
main {
    padding-bottom: 80px; /* Adjust this value based on the footer height */
}
</style>
</head>

<body>

<!-- NAV -->
<header class="fixed top-0 w-full z-50 
               backdrop-blur-md 
               bg-white/10 
               border-b border-white/10">

  <div class="max-w-[110rem] mx-auto 
              flex items-center justify-between 
              px-10 py-3">

    <!-- Logo -->
    <img src="assets/images/LOGO.png" alt="logo" class="h-12 md:h-16 object-contain">

    <!-- Nav Links -->
    <nav class="flex items-center gap-8 text-sm font-medium">
      <a href="newstation.php" class="hover:text-gray-300 transition">NewStation</a>
      <a href="customer_home.php" class="hover:text-gray-300 transition">Home</a>
      <a href="shop.php" class="hover:text-gray-300 transition">Shop</a>
      <a href="customer_orders.php" class="hover:text-gray-300 transition">Orders</a>
      <a href="customer_profile.php" class="hover:text-gray-300 transition">Profile</a>
    </nav>

    <div class="flex items-center gap-4">

  <!-- SEARCH BAR -->
 <div class="w-64 relative">

  <div class="flex items-center 
              bg-[#1a1a25] 
              border border-white/10 
              rounded-full 
              px-4 py-2">

    <span class="text-gray-400 mr-2">🔍</span>

    <input 
      type="text" 
      id="liveSearch"
      placeholder="Search Items..."
      class="bg-transparent outline-none text-sm text-white w-full">

  </div>


</div>

  <!-- LOGOUT -->
  <a href="logout.php"
     class="px-5 py-2 rounded-xl 
            bg-white text-black 
            text-sm font-semibold 
            hover:bg-gray-200 transition">
    Logout
  </a>

</div>

  </div>

</header>

<div class="pt-20 flex">

<!-- SIDEBAR -->
<aside class="fixed top-20 left-0 h-[calc(100%-5rem)] w-56 bg-[#111] px-4 py-8 overflow-y-auto z-40 flex flex-col justify-start">

    <!-- CATEGORY LIST -->
    <div class="space-y-4 mt-2">

        <?php foreach($categories as $cat): ?>
        <a href="?category=<?= $cat ?>"
        class="block text-center py-3 rounded-xl text-sm font-medium transition-all
        <?= $cat==$current_category 
            ? 'bg-red-500 text-white shadow-lg scale-105' 
            : 'bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white' ?>">

        <?= $cat ?>
        </a>
        <?php endforeach; ?>

    </div>

    <!-- OPTIONAL BOTTOM SPACE / DESIGN -->
    <div class="text-center text-xs text-gray-500 mt-6">
        Explore Categories 🎌
    </div>

</aside>

<!-- MAIN -->
<main class="flex-1 p-6 ml-56 mr-[18%]">

<h2 class="text-2xl mb-6">
<?= !empty($search) ? "Search Results: " . htmlspecialchars($search) : $current_category ?>
</h2>

<div id="productGrid" class="grid grid-cols-2 md:grid-cols-4 gap-6">

<?php while($p = $products->fetch_assoc()): ?>

<div class="card p-3 cursor-pointer hover:scale-105 transition"
     onclick="openModal(
   '<?= htmlspecialchars($p['name']) ?>',
   '<?= $p['price'] ?>',
   'assets/images/<?= $p['image'] ?>',
   '<?= $p['id'] ?>'
     )">

<img src="assets/images/<?= $p['image'] ?>" 
     class="h-32 w-full object-cover rounded">

<h3><?= $p['name'] ?></h3>
<p class="text-red-400">Rs. <?= $p['price'] ?></p>

<div class="flex justify-between mt-2">

<a href="?add=<?= $p['id'] ?>" class="bg-red-500 px-2 py-1 text-xs rounded">Add</a>

<form method="POST">
<input type="hidden" name="product_id" value="<?= $p['id'] ?>">
<button name="favorite_action"
value="<?= in_array($p['id'],$user_favorites)?'remove':'add' ?>">
<?= in_array($p['id'],$user_favorites)?'❤️':'🤍' ?>
</button>
</form>

</div>

</div>

<?php endwhile; ?>

</div>

</main>

<!-- CART -->
<aside class="fixed top-16 right-0 h-[calc(100%-4rem)] 
w-80 max-w-[28%] min-w-[260px] 
bg-[#111] p-6 overflow-y-auto z-40 shadow-2xl">

<h2 class="text-xl font-semibold mb-6">🛒 Cart</h2>

<?php
$total = 0;

if (!empty($_SESSION['cart'])):
foreach($_SESSION['cart'] as $id=>$qty):
$p = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
$subtotal = $p['price'] * $qty;
$total += $subtotal;
?>

<div class="flex justify-between items-start mb-4 bg-[#1a1a25] p-3 rounded-lg">

  <div>
    <!-- PRODUCT NAME -->
    <p class="text-sm font-semibold"><?= $p['name'] ?></p>

    <!-- PRICE -->
    <p class="text-sm text-gray-400">
      Rs. <?= number_format($p['price'],2) ?>
    </p>

    <!-- QUANTITY -->
    <div class="flex items-center gap-3 mt-2">

      <a href="?dec=<?= $id ?>" 
         class="px-2 py-1 bg-white/10 rounded text-sm">-</a>

      <span class="text-sm font-semibold"><?= $qty ?></span>

      <a href="?inc=<?= $id ?>" 
         class="px-2 py-1 bg-white/10 rounded text-sm">+</a>

    </div>
  </div>

  <!-- REMOVE -->
  <a href="?remove=<?= $id ?>" 
     class="text-red-400 text-sm font-bold hover:text-red-600">
     ✕
  </a>

</div>

<?php endforeach; ?>

<div class="sticky bottom-0 bg-[#111] pt-4">

<hr class="my-5 border-white/10">

<!-- TOTAL -->
<div class="flex justify-between text-base font-semibold mb-4">
  <span>Total</span>
  <span>Rs. <?= number_format($total,2) ?></span>
</div>

<!-- BUTTON -->
<form method="POST">
  <button name="checkout"
          class="block w-full bg-red-500 py-3 rounded-lg font-semibold">
    Checkout with eSewa
  </button>
</form>

<?php else: ?>

<p class="text-gray-400 text-sm">Cart is empty</p>

<?php endif; ?>

</aside>

</div>
<!-- PRODUCT MODAL -->
<div id="productModal" 
     class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50">

  <div class="bg-[#1a1a25] rounded-xl 
            w-[900px] h-[500px] 
            flex overflow-hidden relative shadow-2xl">

    <!-- LEFT IMAGE -->
    <div class="w-1/2 bg-black">
      <img id="modalImage" 
           class="w-full h-full object-cover">
    </div>

    <!-- RIGHT CONTENT -->
    <div class="w-1/2 p-5 flex flex-col justify-between">

      <!-- CLOSE -->
      <button onclick="closeModal()" 
              class="absolute top-2 right-3 text-white text-xl">✕</button>

      <div>
        <!-- TITLE -->
        <h2 id="modalName" class="text-xl font-bold mb-2"></h2>

        <!-- DESCRIPTION (optional) -->
        <p class="text-gray-400 text-sm mb-3">
          Anime merchandise item with premium quality design. Perfect for collectors and fans.
        </p>

        <!-- PRICE -->
        <p id="modalPrice" class="text-red-400 text-lg font-semibold mb-4"></p>

        <!-- EXTRA INFO -->
        <ul class="text-sm text-gray-300 space-y-1">
          <li>⭐ Rating: 4.8</li>
          <li>📦 Category: Anime</li>
          <li>🎌 Imported</li>
        </ul>
      </div>

   <div class="mt-5 space-y-3">

  <!-- FAVORITE BUTTON -->
  <form method="POST" id="modalFavForm">
    <input type="hidden" name="product_id" id="modalProductId">
    
    <button type="submit" name="favorite_action" id="modalFavBtn" value="add"
      class="w-full bg-white/10 hover:bg-white/20 py-2 rounded-lg font-semibold">
      🤍 Add to Favorites
    </button>
  </form>

  <!-- ADD TO CART -->
  <a id="modalAddToCart"
     class="block text-center bg-yellow-500 hover:bg-red-600 py-2 rounded-lg font-semibold cursor-pointer">
     Add to Cart
  </a>

</div>

    </div>

  </div>
</div>



  </div>

</div>

<script>
function openModal(name, price, image, id) {
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPrice').innerText = "Rs. " + price;
    document.getElementById('modalImage').src = image;

    // Add to cart
    document.getElementById('modalAddToCart').href = "?add=" + id;

    // Favorite system
    document.getElementById('modalProductId').value = id;

    // Check if already favorite
    const favorites = <?php echo json_encode($user_favorites); ?>;

    let btn = document.getElementById('modalFavBtn');

    if (favorites.includes(parseInt(id))) {
        btn.innerHTML = "❤️ Remove from Favorites";
        btn.value = "remove";
    } else {
        btn.innerHTML = "🤍 Add to Favorites";
        btn.value = "add";
    }

    document.getElementById('productModal').classList.remove('hidden');
    document.getElementById('productModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('productModal').classList.add('hidden');
}
</script>

<footer class="bg-[#0b0b0f] border-t border-white/10 bottom-0 w-full">
    <div class="text-center py-2 text-xs text-gray-400">
        <div>© 2026 Anime Store. All rights reserved.</div>
        <div class="text-red-400 font-semibold">
            OTAKU STORE • Licensed Merchandise
        </div>
    </div>
</footer>

<script>
const input = document.getElementById("liveSearch");
const grid = document.getElementById("productGrid");

let timeout;

input.addEventListener("keyup", function () {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
        let query = input.value;

        if (query.length === 0) {
            location.reload(); // show default products again
            return;
        }

        fetch("live_products.php?q=" + query)
            .then(res => res.text())
            .then(data => {
                grid.innerHTML = data;
            });

    }, 300);
});
</script>

</body>
</html>