<?php
include 'includes/db.php';

$search = $_GET['q'] ?? '';

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

if ($news && $news->num_rows > 0):

while ($n = $news->fetch_assoc()):
?>

<a href="news_detail.php?id=<?= $n['id'] ?>" class="news-card block">

  <?php if (!empty($n['image'])): ?>
    <img src="assets/images/<?= htmlspecialchars($n['image']) ?>">
  <?php endif; ?>

  <span class="see-btn">See more</span>

  <div class="overlay">
    <h3><?= htmlspecialchars($n['title']) ?></h3>
    <span><?= date('M d, Y', strtotime($n['created_at'])) ?></span>
  </div>

</a>

<?php endwhile; else: ?>

<p class="text-center text-gray-400 col-span-4">No results found</p>

<?php endif; ?>