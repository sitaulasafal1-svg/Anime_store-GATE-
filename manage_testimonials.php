<?php
session_start();
include 'includes/db.php';

// Admin session check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all testimonials
$testimonials_res = $conn->query("
    SELECT t.id, t.message, t.created_at, u.username 
    FROM testimonials t 
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cream: #E3DEC0;
            --dark: #1A1A25;
            --olive: #5A5931;
            --peach: #EACAA5;
        }
        body {
            background-color: var(--cream);
            color: var(--dark);
            font-family: 'Arial', sans-serif;
        }
        table {
            border-collapse: separate;
            border-spacing: 0;
        }
        thead th {
            background-color: var(--olive);
            color: var(--cream);
        }
        tbody tr:nth-child(even) {
            background-color: #f4f0e6;
        }
        tbody tr:hover {
            background-color: #e6dcc5;
        }
    </style>
</head>
<body class="min-h-screen p-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold">Manage Testimonials</h2>
        <a href="admin_dashboard.php" class="px-5 py-2 bg-var(--peach) hover:bg-opacity-80 rounded-lg text-var(--dark) font-semibold shadow-md transition">Back to Dashboard</a>
    </div>

    <?php if ($testimonials_res && $testimonials_res->num_rows > 0): ?>
        <div class="overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full bg-white rounded-lg">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left">#</th>
                        <th class="px-6 py-3 text-left">User</th>
                        <th class="px-6 py-3 text-left">Message</th>
                        <th class="px-6 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while($t = $testimonials_res->fetch_assoc()): ?>
                        <tr class="border-b border-gray-300">
                            <td class="px-6 py-3"><?php echo $i++; ?></td>
                            <td class="px-6 py-3 font-medium"><?php echo htmlspecialchars($t['username']); ?></td>
                            <td class="px-6 py-3"><?php echo htmlspecialchars($t['message']); ?></td>
                            <td class="px-6 py-3 text-gray-600"><?php echo date('Y-m-d', strtotime($t['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-700 mt-4">No testimonials found.</p>
    <?php endif; ?>

</body>
</html>
