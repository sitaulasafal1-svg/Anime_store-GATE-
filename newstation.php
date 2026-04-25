<?php 
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);

    $news = $conn->query("
        SELECT * FROM news 
        WHERE title LIKE '%$search_safe%' 
        OR description LIKE '%$search_safe%' 
        ORDER BY created_at DESC
    ");
} else {
    $news = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NewStation | Anime Store</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.tailwindcss.com"></script>

<style>
:root{
  --bg:#0b0b0f;
  --glass:rgba(255,255,255,0.06);
}

body{
  background:#0b0b0f;
  color:white;
  font-family:Inter,sans-serif;
}

/* ===== GLASS ===== */
.glass{
  background:var(--glass);
  backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,0.08);
}

/* ===== NAV ===== */
.nav{
  backdrop-filter: blur(12px);
  background: rgba(0,0,0,0.6);
}

/* ===== NEWS CARD (NEW DESIGN) ===== */
.news-card{
  position:relative;
  break-inside:avoid;
  margin-bottom:16px;
  border-radius:16px;
  overflow:hidden;
  cursor:pointer;
  transition:.3s;
}

.news-card:hover{
  transform:scale(1.03);
}

/* IMAGE */
.news-card img{
  width:100%;
  height:auto;
  display:block;
}

/* GRADIENT OVERLAY */
.news-card::after{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(to top, rgba(0,0,0,.85), transparent 60%);
}

/* TEXT ON IMAGE */
.overlay{
  position:absolute;
  bottom:0;
  left:0;
  right:0;
  padding:12px;
  z-index:2;
}

.overlay h3{
  font-size:14px;
  font-weight:600;
  color:white;
  line-height:1.3;
}

.overlay span{
  font-size:11px;
  opacity:.7;
}

/* HIDE DESCRIPTION */
.overlay p{
  display:none;
}

/* SEE BUTTON */
.see-btn{
  position:absolute;
  bottom:10px;
  left:10px;
  z-index:3;
  background:rgba(255,255,255,0.9);
  color:black;
  font-size:11px;
  padding:4px 10px;
  border-radius:999px;
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

    <!-- RIGHT SIDE -->
<div class="flex items-center gap-4">

  <!-- SEARCH -->
  <form method="GET" class="flex items-center">
   <input type="text" id="searchInput"
  placeholder="Search news..."
  class="px-4 py-2 rounded-lg bg-white/20 text-white text-sm 
         placeholder-gray-300 outline-none w-48 focus:w-64 transition-all">

    <button class="ml-2 px-3 py-2 bg-white text-black rounded-lg text-sm">
      🔍
    </button>
  </form>

  <!-- Logout -->
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

<main class="pt-28 px-6 max-w-7xl mx-auto">

<!-- HEADER -->
<div class="mb-16 text-center">
  <h1 class="text-5xl font-extrabold mb-3">
    Anime News
  </h1>
  <p class="text-gray-400">
    Latest drops, leaks & anime updates
  </p>
</div>

<!-- MASONRY -->
<div id="newsContainer" class="columns-1 sm:columns-2 lg:columns-4 gap-8">

<?php if ($news && $news->num_rows > 0): ?>
<?php while ($n = $news->fetch_assoc()): ?>

<a href="news_detail.php?id=<?= $n['id'] ?>" class="news-card block">

  <?php if (!empty($n['image'])): ?>
    <img src="assets/images/<?= htmlspecialchars($n['image']) ?>" alt="news">
  <?php endif; ?>

  <!-- BUTTON -->
  <span class="see-btn">See more</span>

  <!-- TEXT -->
  <div class="overlay">
    <h3><?= htmlspecialchars($n['title']) ?></h3>
    <span><?= date('M d, Y', strtotime($n['created_at'])) ?></span>
  </div>

</a>

<?php endwhile; ?>
<?php else: ?>

<p class="text-center text-gray-500">No news available.</p>

<?php endif; ?>

</div>

</main>

<!-- FOOTER -->
<footer class="text-center text-gray-500 py-10 mt-16 border-t border-gray-800">
  © <?= date('Y') ?> Anime Store
</footer>

<script>
const input = document.getElementById('searchInput');
const container = document.getElementById('newsContainer');

input.addEventListener('keyup', function () {

    let query = this.value;

    fetch('search_news.php?q=' + encodeURIComponent(query))
        .then(res => res.text())
        .then(data => {
            container.innerHTML = data;
        });
});
</script>

</body>
</html>