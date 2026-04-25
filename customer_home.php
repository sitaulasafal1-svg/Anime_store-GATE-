<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");

// Fetch testimonials
$testimonials = $conn->query("
    SELECT t.message, t.created_at, u.username 
    FROM testimonials t 
    JOIN users u ON t.user_id = u.id
    ORDER BY t.id DESC LIMIT 6
");

// Fetch You May Like Recommendations
$user_id_rec = (int)$_SESSION['user_id'];
$cart_ids_rec = !empty($_SESSION['cart']) ? implode(',', array_map('intval', array_keys($_SESSION['cart']))) : "0";

$recommended = $conn->query("
    SELECT p.*,
    (
        (CASE WHEN p.category IN (SELECT DISTINCT pr.category FROM products pr JOIN favorites f ON pr.id = f.product_id WHERE f.user_id = $user_id_rec) THEN 3 ELSE 0 END) +
        (CASE WHEN p.category IN (SELECT DISTINCT category FROM products WHERE id IN ($cart_ids_rec)) THEN 2 ELSE 0 END) +
        (CASE WHEN p.category IN (SELECT DISTINCT pr.category FROM products pr JOIN order_items oi ON pr.id = oi.product_id JOIN orders o ON oi.order_id = o.id WHERE o.user_id = $user_id_rec) THEN 2 ELSE 0 END) +
        (CASE WHEN pop.product_id IS NOT NULL THEN 1 ELSE 0 END)
    ) AS score
    FROM products p
    LEFT JOIN (SELECT product_id FROM order_items GROUP BY product_id ORDER BY COUNT(*) DESC LIMIT 10) AS pop ON p.id = pop.product_id
    ORDER BY score DESC, RAND()
    LIMIT 4
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Anime Store</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
:root{
  --bg:#0b0b0f;
  --glass:rgba(255,255,255,0.08);
}

body{
  background:#0b0b0f;
  color:white;
  font-family: 'Inter', sans-serif;
}

.glass{
  background: var(--glass);
  backdrop-filter: blur(12px);
  border:1px solid rgba(255,255,255,0.1);
}

.btn {
  background: white;
  color: black;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  transition: 0.3s;
}

.btn:hover {
  background: #ddd;
}

.glass {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  color: white;
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

    <!-- Logout -->
     <a href="logout.php" class="px-5 py-2 rounded-xl bg-white text-black text-sm font-semibold hover:bg-gray-200">
        Logout
      </a>

  </div>

</header>

<!-- HERO -->
<section class="relative h-screen flex items-center justify-center text-center">

  <!-- Background -->
  <img src="assets/images/homebackground.jpg"
       class="absolute w-full h-full object-cover">

  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/60"></div>

  <!-- Content -->
  <div class="relative z-10 max-w-2xl px-4">
    
    <h1 class="text-4xl md:text-6xl font-extrabold tracking-wide">
      WELCOME, <?php echo htmlspecialchars($username); ?>
    </h1>

    <h2 class="mt-4 text-2xl md:text-3xl font-bold">
      EXPLORE THE WORLD OF ANIME
    </h2>
    
    <p class="mt-6 text-gray-300 text-lg">
      Premium anime collections for real fans
    </p>

    <a href="shop.php" class="btn mt-8 inline-block">
      Explore Store
    </a>

  </div>

  <!-- Floating cards -->
  <div class="absolute bottom-10 flex flex-wrap justify-center gap-4 z-10">
    <div class="glass px-4 py-2 rounded-xl">🔥 Trending</div>
    <div class="glass px-4 py-2 rounded-xl">🚚 Fast Delivery</div>
    <div class="glass px-4 py-2 rounded-xl">⭐ Top Quality</div>
  </div>

</section>

<!-- PRODUCTS -->
<section class="py-24 px-6 max-w-7xl mx-auto">

  <h2 class="text-4xl font-bold mb-12 text-center">
    Latest Products
  </h2>

  <div class="grid md:grid-cols-4 gap-8">

  <?php while ($product = $products->fetch_assoc()): ?>
    
    <div class="glass p-5 rounded-2xl hover:scale-105 transition">

      <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
           class="w-full h-60 object-cover rounded-xl mb-4">

      <h3 class="text-lg font-semibold">
        <?php echo htmlspecialchars($product['name']); ?>
      </h3>

      <p class="text-gray-300 mb-4">
        Rs. <?php echo htmlspecialchars($product['price']); ?>
      </p>

      <a href="shop.php?product_id=<?php echo $product['id']; ?>" class="btn">
        View
      </a>

    </div>

  <?php endwhile; ?>

  </div>
</section>

<!-- YOU MAY LIKE SECTION -->
<section class="py-16 px-6 max-w-7xl mx-auto border-t border-white/10">
  <div class="flex items-center justify-between mb-10">
    <h2 class="text-3xl font-bold flex items-center gap-2">
      <span class="text-red-500">✨</span> You May Like
    </h2>
    <a href="shop.php" class="text-sm text-gray-400 hover:text-white transition">View All ➔</a>
  </div>

  <div class="grid md:grid-cols-4 gap-6">
  <?php while ($rec = $recommended->fetch_assoc()): ?>
    <div class="glass p-4 rounded-2xl hover:scale-105 transition group relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 z-10"></div>
      <img src="assets/images/<?php echo htmlspecialchars($rec['image']); ?>"
           class="w-full h-56 object-cover rounded-xl mb-4 group-hover:scale-110 transition duration-500">
      <div class="relative z-20">
        <h3 class="text-lg font-semibold truncate">
          <?php echo htmlspecialchars($rec['name']); ?>
        </h3>
        <div class="flex justify-between items-center mt-2">
          <p class="text-red-400 font-bold">
            Rs. <?php echo htmlspecialchars($rec['price']); ?>
          </p>
          <a href="shop.php?product_id=<?php echo $rec['id']; ?>" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-sm transition">
            View
          </a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
  </div>
</section>

<!-- TIMELINE -->
<section class="py-24 text-center">

  <h2 class="text-4xl font-bold mb-16">
    Our Journey
  </h2>

  <div class="max-w-2xl mx-auto border-l border-gray-700 pl-6 space-y-10 text-left">

    <div>
      <h3 class="font-semibold text-lg">Started</h3>
      <p class="text-gray-400">Built by anime lovers</p>
    </div>

    <div>
      <h3 class="font-semibold text-lg">Growing</h3>
      <p class="text-gray-400">Expanding collections globally</p>
    </div>

    <div>
      <h3 class="font-semibold text-lg">Now</h3>
      <p class="text-gray-400">Serving thousands of fans</p>
    </div>

  </div>

</section>

<!-- TESTIMONIALS -->
<section class="py-24 px-6 max-w-6xl mx-auto">

<h2 class="text-4xl text-center mb-12">Reviews</h2>

<div class="grid md:grid-cols-3 gap-8">

<?php while($t = $testimonials->fetch_assoc()): ?>

<div class="glass p-6 rounded-xl">
  <p class="text-gray-300 mb-4">
    "<?php echo htmlspecialchars($t['message']); ?>"
  </p>

  <h4 class="font-semibold">
    <?php echo htmlspecialchars($t['username']); ?>
  </h4>
</div>

<?php endwhile; ?>

</div>

</section>

<section class="py-24 px-10 max-w-[110rem] mx-auto">

  <h2 class="text-3xl font-bold mb-12 text-center">
    What Our Customers Say
  </h2>

  <!-- Reviews -->
  <div class="grid md:grid-cols-3 gap-8 mb-16">

    <?php if($testimonials && $testimonials->num_rows > 0): ?>
      <?php while($t = $testimonials->fetch_assoc()): ?>

        <div class="glass p-6 rounded-2xl">
          <p class="text-gray-300 mb-4 text-sm leading-relaxed">
            "<?php echo htmlspecialchars($t['message']); ?>"
          </p>

          <h4 class="text-white font-semibold text-sm">
            — <?php echo htmlspecialchars($t['username']); ?>
          </h4>

          <span class="text-xs text-gray-500">
            <?php echo date('M d, Y', strtotime($t['created_at'])); ?>
          </span>
        </div>

      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-gray-500 col-span-3 text-center">No reviews yet.</p>
    <?php endif; ?>

  </div>

  <!-- Add Review -->
  <div class="max-w-xl mx-auto">

    <h3 class="text-xl font-semibold mb-4 text-center">
      Share Your Experience
    </h3>

    <form id="testimonialForm" method="POST" class="space-y-4">

      <textarea 
        name="message"
        placeholder="Write your review..."
        class="w-full p-4 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:border-white/30"
        rows="4"
        required>
      </textarea>

      <button 
        type="submit"
        class="w-full py-3 rounded-xl bg-white text-black font-semibold hover:bg-gray-200 transition">
        Submit Review
      </button>

    </form>

    <p id="testimonialMsg" class="text-sm text-gray-400 mt-3 text-center"></p>

  </div>

</section>

<script>
document.getElementById('testimonialForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  const res = await fetch('submit_testimonial.php', {
    method: 'POST',
    body: formData
  });

  const data = await res.json();
  document.getElementById('testimonialMsg').textContent = data.message;

  if (data.status === 'success') e.target.reset();
});
</script>

<!-- FOOTER -->
<footer class="py-10 text-center text-gray-400 border-t border-gray-800">
  © <?php echo date('Y'); ?> Anime Store
</footer>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init();
</script>

</body>
</html>
