<?php require_once("includes/db_connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php require_once("includes/header.php"); ?>

<?php


$search = trim($_GET['search'] ?? "");
$location = trim($_GET['location'] ?? "");
$type = trim($_GET['type'] ?? "");
$category = trim($_GET['category'] ?? "");


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$limit = 6;
$offset = ($page - 1) * $limit;

$totalItems = countReports(
    $pdo,
    $type,
    $search,
    $category,
    $location
);

$totalPages = max(1, (int)ceil($totalItems / $limit));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$items = searchReports(
    $pdo,
    $type,
    $search,
    $category,
    $location,
    $limit,
    $offset
);

?>

<section class="py-20 bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto px-6">

        <!-- Title -->
        <div class="mb-10">

            <h1 class="text-4xl font-bold text-black">
                Search Results
            </h1>

            <p class="text-gray-500 mt-2">
                Found <?= $totalItems ?> item<?= $totalItems === 1 ? '' : 's' ?>
            </p>

        </div>

        <!-- Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if ($items): ?>

                <?php foreach ($items as $item): ?>

                    <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition">

                        <!-- Image -->
                        <div class="h-48 overflow-hidden">

                            <img
                                src="<?= htmlspecialchars(reportImageSrc($item['image'])) ?>"
                                alt="<?= htmlspecialchars($item['title']) ?>"
                                class="w-full h-full object-cover hover:scale-110 transition duration-500">

                        </div>

                        <!-- Content -->
                        <div class="p-6">

                            <!-- Status -->
                            <span class="<?= $item['report_type'] === 'lost'
                                                ? 'bg-red-100 text-red-600'
                                                : 'bg-green-100 text-green-600' ?>
                                px-3 py-1 rounded-full text-sm">

                                <?= ucfirst($item['report_type']) ?>

                            </span>

                            <!-- Title -->
                            <h3 class="text-xl font-semibold mt-4 mb-2">

                                <?= htmlspecialchars($item['title']) ?>

                            </h3>

                            <!-- Location -->
                            <p class="text-gray-600 mb-4">

                                <?= htmlspecialchars($item['location']) ?>

                            </p>

                            <!-- Button -->
                            <a
                                href="single.php?id=<?= $item['id'] ?>"
                                class="block text-center w-full py-3 bg-black text-white rounded-2xl hover:opacity-90 transition">

                                View Details

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-span-full text-center py-20">

                    <h2 class="text-3xl font-bold mb-3">
                        No Items Found
                    </h2>

                    <p class="text-gray-500">
                        Try another keyword or location.
                    </p>

                </div>

            <?php endif; ?>

        </div>

        <?php
        renderPagination($page, $totalPages, [
            'search' => $search,
            'location' => $location,
            'type' => $type,
            'category' => $category
        ]);
        ?>

    </div>

</section>

<?php require_once("includes/footer.php"); ?>
