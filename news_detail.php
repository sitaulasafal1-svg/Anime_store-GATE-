<?php
session_start();
include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: newstation.php");
    exit();
}

$id = (int)$_GET['id'];
$news = $conn->query("SELECT * FROM news WHERE id = $id")->fetch_assoc();

if (!$news) {
    echo "News not found";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($news['title']) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.tailwindcss.com"></script>

<style>
/* ===== GLOBAL ===== */
body{
  margin:0;
  background:#f4f1ea;
  color:#111;
  font-family:"Georgia", serif;
}

/* ===== FULL WIDTH PAPER ===== */
.paper{
  width:100%;
  padding:40px 60px;
}

/* ===== TOP BAR ===== */
.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  border-bottom:2px solid black;
  padding-bottom:10px;
  font-size:14px;
}

/* ===== HEADLINE ===== */
.headline{
  font-size:clamp(40px, 8vw, 100px);
  font-weight:900;
  text-align:center;
  text-transform:uppercase;
  letter-spacing:3px;
  margin:30px 0 10px;
}

/* ===== QUOTE ===== */
.quote{
  text-align:center;
  font-style:italic;
  margin-bottom:20px;
}

/* ===== CONTENT ===== */
.content{
  column-count:3;
  column-gap:60px;
  width:100%;
}

/* ===== TEXT ===== */
.content p{
  font-size:17px;
  line-height:1.9;
  text-align:justify;
}

/* ===== IMAGE ===== */
.news-img{
  width:100%;
  max-width:500px;
  display:block;
  margin:20px auto;
  break-inside:avoid;
  filter:grayscale(100%);
}

/* ===== BACK ===== */
.back{
  display:inline-block;
  margin-bottom:10px;
  font-size:14px;
  text-decoration:underline;
}

/* ===== FOOTER ===== */
.footer{
  margin-top:40px;
  text-align:center;
  font-weight:bold;
  border-top:2px solid black;
  padding-top:10px;
}

.back-btn{
  display:inline-block;
  font-size:16px;
  font-weight:600;
  padding:10px 20px;
  border:2px solid black;
  border-radius:8px;
  margin-bottom:20px;
  transition:.3s;
}

.back-btn:hover{
  background:black;
  color:#f4f1ea;
}

/* ===== RESPONSIVE ===== */
@media (max-width:1100px){
  .content{
    column-count:2;
  }
}

@media (max-width:768px){
  .paper{
    padding:30px 20px;
  }

  .content{
    column-count:1;
  }

  .headline{
    font-size:clamp(28px, 7vw, 50px);
  }
}
</style>
</head>

<body>

<div class="paper">

<!-- BACK -->
<a href="newstation.php" class="back-btn">
  ← Back to News
</a>

<!-- TOP BAR -->
<div class="topbar">
  <span>BREAKING NEWS</span>
  <span>ANIME TIMES</span>
  <span><?= date('M d, Y', strtotime($news['created_at'])) ?></span>
</div>

<!-- HEADLINE -->
<h1 class="headline">
  <?= htmlspecialchars($news['title']) ?>
</h1>

<!-- QUOTE -->
<p class="quote">
  "<?= htmlspecialchars($news['quote'] ?? 'Anime is not just entertainment, it\'s emotion.') ?>"
</p>

<hr class="my-6 border-black">

<!-- CONTENT -->
<div class="content">

  <?php if (!empty($news['image'])): ?>
    <img src="assets/images/<?= htmlspecialchars($news['image']) ?>" class="news-img">
  <?php endif; ?>

  <?php if (!empty($news['image2'])): ?>
  <img src="assets/images/<?= htmlspecialchars($news['image2']) ?>" class="news-img">
<?php endif; ?>

<?php if (!empty($news['image3'])): ?>
  <img src="assets/images/<?= htmlspecialchars($news['image3']) ?>" class="news-img">
<?php endif; ?>

  <p>
    <?= nl2br(htmlspecialchars($news['description'])) ?>
  </p>

</div>

<!-- FOOTER -->
<div class="footer">
  ANIME STORE • NEWS EDITION
</div>

</div>

</body>
</html>