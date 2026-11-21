<?php
require_once __DIR__ . '/../class/report.php';

$reportId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$item = $reportId ? (new Report())->fetchPublicReport($reportId) : null;
if (!$item) {
    http_response_code(404);
    require_once __DIR__ . '/../404.php';
    exit;
}

$imageSrc = !empty($item['image'])
    ? 'uploads/images/' . rawurlencode(basename($item['image']))
    : 'assets/img/no-image.svg';
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="min-h-screen bg-gray-50 py-12 sm:py-16">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        <a href="items.php" class="mb-6 inline-flex items-center gap-2 font-semibold text-gray-600 hover:text-black"><span aria-hidden="true">←</span> Back to reports</a>
        <article class="grid overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-sm md:grid-cols-2">
            <div class="min-h-72 bg-gray-100 md:min-h-[32rem]">
                <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>" class="h-full w-full object-cover">
            </div>
            <div class="p-7 sm:p-10">
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold uppercase <?= $item['report_type'] === 'lost' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' ?>"><?= htmlspecialchars($item['report_type'], ENT_QUOTES, 'UTF-8') ?> item</span>
                <h1 class="mt-5 text-3xl font-bold text-gray-950 sm:text-4xl"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="mt-2 text-gray-500"><?= htmlspecialchars($item['categoryName'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?></p>
                <dl class="mt-8 space-y-6">
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Location</dt><dd class="mt-1 text-gray-800"><?= htmlspecialchars($item['location'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Date</dt><dd class="mt-1 text-gray-800"><?= htmlspecialchars($item['lost_found_date'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Description</dt><dd class="mt-1 whitespace-pre-line leading-relaxed text-gray-800"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                </dl>
                <a href="items.php" class="mt-9 inline-flex rounded-xl bg-black px-5 py-3 font-semibold text-white hover:bg-gray-800">Browse more reports</a>
            </div>
        </article>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
