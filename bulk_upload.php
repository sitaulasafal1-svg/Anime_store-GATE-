<?php
include 'includes/db.php';

if (isset($_POST['upload'])) {

    if (!empty($_FILES['csv']['tmp_name'])) {

        $file = fopen($_FILES['csv']['tmp_name'], "r");
        fgetcsv($file); // skip header

        while (($row = fgetcsv($file)) !== FALSE) {

            $name = $conn->real_escape_string($row[0]);
            $price = (float)$row[1];
            $image = $conn->real_escape_string($row[2]);
            $category = $conn->real_escape_string($row[3]);

            // Check if image exists
            if (!file_exists("assets/images/" . $image)) {
                echo "❌ Missing image: $image <br>";
                continue;
            }

            $conn->query("
                INSERT INTO products (name, price, image, category)
                VALUES ('$name', '$price', '$image', '$category')
            ");
        }

        echo "<br>✅ Upload completed!";
    } else {
        echo "❌ Please select a CSV file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Bulk Upload</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white flex items-center justify-center h-screen">

<div class="bg-[#1a1a25] p-8 rounded-xl text-center">

<h1 class="text-xl mb-4 font-bold">Bulk Upload Products</h1>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="csv" 
       class="mb-4 text-sm" required>

<br>

<button name="upload"
        class="bg-red-500 px-5 py-2 rounded hover:bg-red-600">
    Upload CSV
</button>

</form>

</div>

</body>
</html>