<?php
$currentPage = basename($_SERVER['PHP_SELF']);

function adminNavClass(string $page, string $currentPage): string
{
    $base = "block px-6 py-3 hover:bg-gray-800 border-l-4 transition";
    return $page === $currentPage
        ? $base . " bg-gray-800 border-blue-500 text-white"
        : $base . " border-transparent text-gray-300";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="includes/assets/script.js" defer></script>
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">

            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold">
                    Lost & Found
                </h1>
            </div>

            <nav class="flex-1 py-4">

                <a href="index.php" class="<?= adminNavClass('index.php', $currentPage) ?>">
                    Dashboard
                </a>

                <a href="user.php" class="<?= adminNavClass('user.php', $currentPage) ?>">
                    Users
                </a>

                <a href="reports.php" class="<?= adminNavClass('reports.php', $currentPage) ?>">
                    Reports
                </a>

                <a href="category.php" class="<?= adminNavClass('category.php', $currentPage) ?>">
                    Categories
                </a>

                <a href="approved.php" class="<?= adminNavClass('approved.php', $currentPage) ?>">
                    Approved Items
                </a>

                <a href="rejected.php" class="<?= adminNavClass('rejected.php', $currentPage) ?>">
                    Rejected Items
                </a>

                <a href="claim.php" class="<?= adminNavClass('claim.php', $currentPage) ?>">
                    Claimed Items
                </a>

                <a href="../index.php" class="block px-6 py-3 hover:bg-gray-800 border-l-4 border-transparent text-gray-300 transition">
                    View Website
                </a>

            </nav>

            <!-- Sidebar Footer -->
            <div class="border-t border-gray-700 p-4">

                <div class="mb-4">
                    <p class="font-semibold">
                        Admin
                    </p>

                    <p class="text-sm text-gray-400">
                        <?= $_SESSION['user']['email']?>
                    </p>
                </div>

                <a href="../logout.php" class="block text-center bg-red-500 hover:bg-red-600 py-2 rounded-lg">
                    Logout
                </a>

            </div>

        </aside>
