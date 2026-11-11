<?php require_once "includes/header.php";
require_once "includes/functions.php";

$page = (int)($_GET['page'] ?? 1);

if ($page < 1) {
    $page = 1;
}

$limit = 6;

$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');

$totalItems = countReports($pdo, "lost", $search);
$totalPages = max(1, (int)ceil($totalItems / $limit));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$items = searchReports(
    $pdo,
    "lost",
    $search,
    "",
    "",
    $limit,
    $offset
);

?>
<section class="min-h-screen bg-gradient-to-b from-gray-50 to-white">

    <!-- Hero -->
    <div class="relative overflow-hidden bg-black">

        <div class="absolute inset-0 opacity-10">
            <div class="absolute w-72 h-72 bg-white rounded-full blur-3xl top-0 -left-20"></div>
            <div class="absolute w-96 h-96 bg-gray-400 rounded-full blur-3xl bottom-0 right-0"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-20">

            <div class="max-w-3xl">

                <span class="inline-flex items-center gap-2 bg-white/10 border border-white/10 text-gray-200 px-5 py-2 rounded-full text-sm backdrop-blur">
                    🔍 Community Lost & Found
                </span>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white leading-tight mt-6">
                    Lost
                    <span class="text-gray-400">&</span>
                    Found
                    <br>
                    Items
                </h1>

                <p class="text-gray-300 text-lg sm:text-xl mt-6 leading-relaxed max-w-2xl">
                    Browse recently reported lost belongings and help reconnect people with what matters most.
                </p>

                <!-- Search lost items -->
                <form
                    method="GET"
                    class="mt-10 flex flex-col sm:flex-row gap-4">

                    <div class="relative flex-1">

                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                            🔎
                        </span>

                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Search lost items..."
                            class="w-full bg-white text-black rounded-2xl py-4 pl-14 pr-5 outline-none shadow-xl">

                    </div>

                    <button
                        type="submit"
                        class="bg-white text-black font-semibold px-8 py-4 rounded-2xl hover:bg-gray-200 transition">
                        Search
                    </button>

                </form>

            </div>

        </div>

    </div>

    <?php

    ?> <!-- Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-20">

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

            <?php if ($items): ?>

                <?php foreach ($items as $item): ?>

                    <div class="group bg-white rounded-[30px] overflow-hidden shadow-lg hover:shadow-2xl transition duration-500 border border-gray-100">

                        <!-- Image -->
                        <div class="relative overflow-hidden">

                            <img
                                src="<?= htmlspecialchars(reportImageSrc($item['image'])) ?>"
                                alt="<?= htmlspecialchars($item['title']) ?>"
                                class="w-full h-72 object-cover group-hover:scale-110 transition duration-700">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                            <span class="absolute top-5 left-5 bg-red-500 text-white text-sm font-medium px-4 py-2 rounded-full shadow-lg">
                                <?= htmlspecialchars(ucfirst($item['report_type'])) ?>
                            </span>

                            <span class="absolute bottom-5 right-5 bg-white/90 backdrop-blur text-black text-sm px-4 py-2 rounded-full shadow">
                                <?= htmlspecialchars($item['categoryName']) ?>
                            </span>

                        </div>

                        <!-- Content -->
                        <div class="p-7">

                            <h2 class="text-2xl font-bold text-black">
                                <?= htmlspecialchars($item['title']) ?>
                            </h2>

                            <p class="text-gray-500 mt-1">
                                <?= htmlspecialchars($item['description']) ?>
                            </p>

                            <!-- Info -->
                            <div class="mt-6 space-y-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        📍
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Location
                                        </p>

                                        <p class="font-medium text-black">
                                            <?= htmlspecialchars($item['location']) ?>
                                        </p>
                                    </div>

                                </div>

                                <div class="flex items-center gap-3">

                                    <div class="w-11 h-11 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        📅
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Date
                                        </p>

                                        <p class="font-medium text-black">
                                            <?= date('M d, Y', strtotime($item['lost_found_date'])) ?>
                                        </p>
                                    </div>

                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="mt-8 flex items-center justify-between">

                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full bg-black text-white flex items-center justify-center font-semibold">
                                        <?= strtoupper(substr($item['username'], 0, 1)) ?>
                                    </div>

                                    <div>
                                        <p class="font-medium text-black">
                                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['username'] == $item['username']) {
                                                echo "Yourself";
                                            } else {
                                                echo  htmlspecialchars($item['username']);
                                            }
                                            ?>
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            Reporter
                                        </p>
                                    </div>

                                </div>

                                <a
                                    href="single.php?id=<?= $item['id'] ?>"
                                    class="bg-black text-white px-6 py-3 rounded-2xl hover:bg-gray-800 transition">
                                    Details
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-span-full text-center py-20">

                    <h2 class="text-3xl font-bold text-gray-700">
                        No items found
                    </h2>

                </div>

            <?php endif; ?>

        </div>

    </div>
    <?php

    ?>

    <?php renderPagination($page, $totalPages, ['search' => $search]); ?>

</section>
<?php require_once "includes/footer.php"; ?>
