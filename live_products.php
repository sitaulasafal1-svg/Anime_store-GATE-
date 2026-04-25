<?php
include 'includes/db.php';

$q = $_GET['q'] ?? '';
$q = $conn->real_escape_string($q);

$res = $conn->query("
    SELECT * FROM products
    WHERE name LIKE '%$q%'
    ORDER BY id DESC
    LIMIT 20
");

while ($p = $res->fetch_assoc()) {
    echo "
<div class='card p-3 cursor-pointer hover:scale-105 transition'
     onclick=\"openModal(
        '".htmlspecialchars($p['name'])."',
        '".$p['price']."',
        'assets/images/".$p['image']."',
        '".$p['id']."'
     )\">

<img src='assets/images/{$p['image']}' 
     class='h-32 w-full object-cover rounded'>

<h3>{$p['name']}</h3>
<p class='text-red-400'>Rs. {$p['price']}</p>

<div class='flex justify-between mt-2'>

<a href='?add={$p['id']}' class='bg-red-500 px-2 py-1 text-xs rounded'>Add</a>

</div>

</div>
";
}
?>