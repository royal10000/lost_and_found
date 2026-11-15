<?php
require_once __DIR__ . "/../includes/session.php";
$isLoggedIn = Session::isLoggedIn();
$sessionUser = $_SESSION["user"] ?? [];
$username = $sessionUser["username"] ?? "";
$email = $sessionUser["email"] ?? "";
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lost & Found</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body class="bg-gray-100 text-gray-900 overflow-x-hidden">
    <!-- ================= NAVBAR ================= -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="index.php" class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <div
                            class="w-11 h-11 sm:w-12 sm:h-12 bg-black rounded-2xl rotate-6"></div>

                        <div
                            class="absolute inset-0 flex items-center justify-center -rotate-6">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M17 9V7a5 5 0 00-10 0v2m-2 0h14a1 1 0 011 1v9a1 1 0 01-1 1H5a1 1 0 01-1-1v-9a1 1 0 011-1z" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h1 class="text-lg sm:text-2xl font-extrabold text-black whitespace-nowrap">
                            Lost<span class="text-gray-500">&</span>Found
                        </h1>

                        <p class="hidden sm:block text-xs text-gray-500 -mt-1">
                            Community Recovery Platform
                        </p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden xl:flex items-center gap-8 font-medium">
                    <a href="index.php" class="hover:text-gray-500 transition"> Home </a>

                    <a href="lost_page.php" class="hover:text-gray-500 transition">
                        Lost Items
                    </a>

                    <a href="found.php" class="hover:text-gray-500 transition">
                        Found Items
                    </a>

                    <a href="contact.php" class="hover:text-gray-500 transition"> Contact </a>
                </nav>

                <!-- Desktop Buttons -->

                <div class="hidden xl:flex items-center gap-3">
                    <?php if ($isLoggedIn): ?>
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-100 transition">
                                <span class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-semibold"><?= htmlspecialchars(strtoupper(substr($username, 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="text-left leading-tight">
                                    <span class="block text-sm font-semibold"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="block text-xs text-gray-500"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                            </button>
                            <div class="absolute right-0 top-full w-52 bg-white border rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200 z-50">
                                <div class="p-4 border-b leading-tight">
                                    <p class="font-semibold"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-sm text-gray-500 break-all mt-1"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <div class="p-2">
                                    <a href="profile.php" class="block px-4 py-3 rounded-xl hover:bg-gray-100 transition">Profile</a>
                                    <a href="reported_item.php" class="block px-4 py-3 rounded-xl hover:bg-gray-100 transition">Reported Items</a>
                                    <a href="logout.php" class="block px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition">Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="px-5 py-2.5 rounded-xl border border-black text-black hover:bg-gray-100 transition">Login</a>
                    <?php endif; ?>
                    <a href="report.php" class="px-5 py-2.5 rounded-xl bg-black text-white hover:bg-gray-800 transition">Report Item</a>
                </div>
                <!-- Mobile Button -->
                <button id="menuBtn" type="button" aria-label="Open navigation menu" aria-controls="mobileMenu" aria-expanded="false" class="xl:hidden shrink-0 p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-black">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-8 h-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden xl:hidden pb-6 max-h-[calc(100vh-5rem)] overflow-y-auto">

                <div class="flex flex-col gap-2 border-t pt-4 font-medium">

                    <!-- Navigation -->
                    <a href="index.php"
                        class="px-4 py-3 rounded-xl hover:bg-gray-100 transition">
                        Home
                    </a>

                    <a href="lost_page.php"
                        class="px-4 py-3 rounded-xl hover:bg-gray-100 transition">
                        Lost Items
                    </a>

                    <a href="found.php"
                        class="px-4 py-3 rounded-xl hover:bg-gray-100 transition">
                        Found Items
                    </a>

                    <a href="contact.php"
                        class="px-4 py-3 rounded-xl hover:bg-gray-100 transition">
                        Contact
                    </a>

                    <?php if (!$isLoggedIn): ?>

                        <!-- Login Button -->
                        <a href="login.php"
                            class="mt-2 px-5 py-3 rounded-xl border border-black text-center text-black hover:bg-gray-100 transition">
                            Login
                        </a>

                    <?php else: ?>

                        <!-- User Info -->


                        <!-- Profile Links -->
                        <a href="profile.php"
                            class="px-4 py-3 rounded-xl hover:bg-gray-100 transition">
                            Profile
                        </a>

                        <a href="reported_item.php"
                            class="px-4 py-3 rounded-xl hover:bg-gray-100 transition">
                            Reported Items
                        </a>

                        <a href="logout.php"
                            class="px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                            Logout
                        </a>
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-2xl mt-2">

                            <!-- Avatar -->
                            <span class="w-11 h-11 rounded-full bg-black text-white flex items-center justify-center font-semibold"><?= htmlspecialchars(strtoupper(substr($username, 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>

                            <!-- User Details -->
                            <div class="leading-tight overflow-hidden">

                                <p class="font-semibold text-sm">
                                    <?= htmlspecialchars($username) ?>
                                </p>

                                <p class="text-xs text-gray-500 truncate">
                                    <?= htmlspecialchars($email) ?>
                                </p>

                            </div>

                        </div>
                    <?php endif; ?>

                    <!-- Report Button -->
                    <a
                        href="report.php"
                        class="mt-2 px-5 py-3 rounded-xl bg-black text-center text-white hover:bg-gray-800 transition">
                        Report Item
                    </a>

                </div>

            </div>
        </div>
    </header>
