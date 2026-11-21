<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/report.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = (int) Session::currentUser();
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$result = (new Report())->fetchReports($userId, $page);
$items = $result['items'];
$page = $result['page'];
$totalItems = $result['totalItems'];
$totalPages = $result['totalPages'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

require_once __DIR__ . '/../layouts/header.php';
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-10">
            <div>
                <p class="uppercase tracking-[3px] text-sm text-gray-500 mb-3">My Reports</p>
                <h1 class="text-4xl font-bold text-black">Reported Items</h1>
                <p class="text-gray-500 mt-2">
                    You submitted <?= $totalItems ?> report<?= $totalItems === 1 ? '' : 's' ?>.
                </p>
            </div>
            <a href="report.php" class="inline-flex justify-center px-6 py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">
                Report New Item
            </a>
        </div>

        <?php if (($_GET['delete'] ?? '') === 'success'): ?>
            <p class="mb-6 rounded-xl bg-green-100 px-4 py-3 text-green-800" role="status">Report deleted.</p>
        <?php elseif (($_GET['delete'] ?? '') === 'failed'): ?>
            <p class="mb-6 rounded-xl bg-red-100 px-4 py-3 text-red-800" role="alert">Could not delete that report. Please try again.</p>
        <?php endif; ?>

        <?php if ($items): ?>
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-7">
                <?php foreach ($items as $item): ?>
                    <?php
                    $status = $item['status'] ?? 'pending';
                    $statusClasses = [
                        'approved' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        'claimed' => 'bg-blue-100 text-blue-700',
                    ];
                    $type = $item['report_type'] ?? 'lost';
                    $typeClass = $type === 'lost' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600';
                    $imageSrc = !empty($item['image'])
                        ? 'uploads/images/' . rawurlencode(basename($item['image']))
                        : 'assets/img/no-image.svg';
                    ?>
                    <article class="group flex flex-col bg-white rounded-[1.75rem] overflow-hidden border border-gray-200 shadow-sm hover:-translate-y-1 hover:shadow-xl transition duration-300">
                        <a href="report_detail.php?id=<?= (int) $item['id'] ?>" class="relative block h-56 overflow-hidden bg-gray-100 focus:outline-none focus:ring-4 focus:ring-black/20" aria-label="View report: <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/50 to-transparent"></span>
                            <span class="absolute left-5 bottom-4 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide <?= $typeClass ?>">
                                <?= htmlspecialchars(ucfirst($type), ENT_QUOTES, 'UTF-8') ?> item
                            </span>
                        </a>
                        <div class="flex flex-1 flex-col p-6 sm:p-7">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <span class="text-sm text-gray-500 font-medium">
                                    <?= htmlspecialchars($item['categoryName'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="shrink-0 <?= $statusClasses[$status] ?? 'bg-gray-100 text-gray-700' ?> px-3 py-1 rounded-full text-xs font-semibold">
                                    <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-950 leading-tight mb-3 group-hover:text-gray-600 transition">
                                <a href="report_detail.php?id=<?= (int) $item['id'] ?>">
                                    <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h2>
                            <p class="flex items-center gap-2 text-gray-600 mb-6">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18s6-5.686 6-10A6 6 0 1 0 4 8c0 4.314 6 10 6 10Zm0-7a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/></svg>
                                <span class="truncate"><?= htmlspecialchars($item['location'], ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                            <div class="mt-auto flex items-center justify-between gap-3 border-t border-gray-100 pt-5">
                                <a href="report_detail.php?id=<?= (int) $item['id'] ?>" class="inline-flex items-center gap-2 font-semibold text-black hover:text-gray-500 transition">
                                    View report <span aria-hidden="true">→</span>
                                </a>
                                <form method="POST" action="delete_report.php" onsubmit="return confirm('Delete this report?');">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-red-700 bg-red-50 hover:bg-red-100 transition">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="flex justify-center items-center gap-2 mt-10" aria-label="Report pages">
                    <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                        <a href="?page=<?= $pageNumber ?>"
                           aria-current="<?= $pageNumber === $page ? 'page' : 'false' ?>"
                           class="px-4 py-2 rounded-xl <?= $pageNumber === $page ? 'bg-black text-white' : 'bg-white text-black hover:bg-gray-200' ?>">
                            <?= $pageNumber ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white border border-gray-100 rounded-3xl p-10 text-center shadow-sm">
                <h2 class="text-2xl font-bold text-black mb-3">No reports yet</h2>
                <p class="text-gray-500 mb-6">Once you submit a lost or found item, it will appear here with its review status.</p>
                <a href="report.php" class="inline-flex px-6 py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">Create Your First Report</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
