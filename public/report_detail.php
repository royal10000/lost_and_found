<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/report.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$reportId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$item = $reportId ? (new Report())->fetchReport($reportId, (int) Session::currentUser()) : null;
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
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <a href="reported_item.php" class="inline-flex items-center gap-2 mb-6 text-sm font-semibold text-gray-600 hover:text-black">
            <span aria-hidden="true">←</span> Back to my reports
        </a>
        <article class="overflow-hidden rounded-[2rem] bg-white border border-gray-200 shadow-sm">
            <div class="grid md:grid-cols-2">
                <div class="min-h-72 md:min-h-[30rem] bg-gray-100">
                    <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
                         class="w-full h-full object-cover">
                </div>
                <div class="p-7 sm:p-10 lg:p-12">
                    <div class="flex flex-wrap gap-2 mb-5">
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">
                            <?= htmlspecialchars(ucfirst($item['report_type']), ENT_QUOTES, 'UTF-8') ?> item
                        </span>
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">
                            <?= htmlspecialchars(ucfirst($item['status']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-950 mb-3">
                        <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    <p class="text-gray-500 mb-8">
                        <?= htmlspecialchars($item['categoryName'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <dl class="space-y-5">
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Location</dt>
                            <dd class="mt-1 text-gray-800"><?= htmlspecialchars($item['location'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Date</dt>
                            <dd class="mt-1 text-gray-800"><?= htmlspecialchars($item['lost_found_date'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Description</dt>
                            <dd class="mt-1 whitespace-pre-line leading-relaxed text-gray-800"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                    </dl>
                    <a href="reported_item.php" class="mt-9 inline-flex rounded-xl bg-black px-5 py-3 font-semibold text-white hover:bg-gray-800 transition">
                        Return to reports
                    </a>
                </div>
            </div>
        </article>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
