<?php
require_once __DIR__ . '/../class/report.php';

$type = $_GET['type'] ?? '';
$search = trim($_GET['search'] ?? '');
$location = trim($_GET['location'] ?? '');
$requestedPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$results = (new Report())->searchPublicReports($type, $search, $location, $requestedPage);
$items = $results['items'];
$page = $results['page'];
$totalItems = $results['totalItems'];
$totalPages = $results['totalPages'];
$pageTitle = $type === 'lost' ? 'Lost Items' : ($type === 'found' ? 'Found Items' : 'Browse Items');

require_once __DIR__ . '/../layouts/header.php';
?>

<main class="min-h-screen bg-gray-50 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="mb-9">
            <p class="text-xs font-bold uppercase tracking-[3px] text-gray-500 mb-3">Community reports</p>
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-950"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mt-3 text-gray-600">Showing <?= $totalItems ?> approved report<?= $totalItems === 1 ? '' : 's' ?>.</p>
        </div>

        <form method="GET" action="items.php" class="mb-10 grid gap-3 rounded-3xl border border-gray-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-4 sm:p-5">
            <input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Name, details, or location" class="rounded-xl border border-gray-200 px-4 py-3 focus:border-black focus:outline-none">
            <input type="search" name="location" value="<?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?>" placeholder="Filter by location" class="rounded-xl border border-gray-200 px-4 py-3 focus:border-black focus:outline-none">
            <select name="type" class="rounded-xl border border-gray-200 px-4 py-3 focus:border-black focus:outline-none">
                <option value="" <?= $type === '' ? 'selected' : '' ?>>Lost and found</option>
                <option value="lost" <?= $type === 'lost' ? 'selected' : '' ?>>Lost items</option>
                <option value="found" <?= $type === 'found' ? 'selected' : '' ?>>Found items</option>
            </select>
            <button type="submit" class="rounded-xl bg-black px-5 py-3 font-semibold text-white hover:bg-gray-800 transition">Search reports</button>
        </form>

        <?php if ($items): ?>
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($items as $item): ?>
                    <?php
                    $imageSrc = !empty($item['image'])
                        ? 'uploads/images/' . rawurlencode(basename($item['image']))
                        : 'assets/img/no-image.svg';
                    $typeClass = $item['report_type'] === 'lost' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700';
                    ?>
                    <article class="group flex flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <a href="item_detail.php?id=<?= (int) $item['id'] ?>" class="h-56 overflow-hidden bg-gray-100" aria-label="View <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        </a>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide <?= $typeClass ?>"><?= htmlspecialchars(ucfirst($item['report_type']), ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="text-sm text-gray-500"><?= htmlspecialchars($item['categoryName'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-950"><a href="item_detail.php?id=<?= (int) $item['id'] ?>" class="hover:text-gray-600"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></a></h2>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-gray-600"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-auto pt-5 text-sm text-gray-500">📍 <?= htmlspecialchars($item['location'], ENT_QUOTES, 'UTF-8') ?></p>
                            <a href="item_detail.php?id=<?= (int) $item['id'] ?>" class="mt-5 rounded-xl bg-black px-4 py-3 text-center font-semibold text-white hover:bg-gray-800 transition">View details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-10 flex flex-wrap justify-center gap-2" aria-label="Report pages">
                    <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                        <?php $pageQuery = http_build_query(['search' => $search, 'location' => $location, 'type' => $type, 'page' => $pageNumber]); ?>
                        <a href="?<?= htmlspecialchars($pageQuery, ENT_QUOTES, 'UTF-8') ?>" aria-current="<?= $pageNumber === $page ? 'page' : 'false' ?>" class="rounded-xl px-4 py-2 <?= $pageNumber === $page ? 'bg-black text-white' : 'bg-white text-gray-800 hover:bg-gray-200' ?>"><?= $pageNumber ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="rounded-3xl border border-gray-200 bg-white px-6 py-16 text-center">
                <h2 class="text-2xl font-bold text-gray-900">No matching reports</h2>
                <p class="mt-2 text-gray-600">Try a different word or location.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
