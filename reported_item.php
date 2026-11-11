<?php
require_once "includes/header.php";
require_once "includes/functions.php";

require_login();

$user = currentUser();
$page = (int)($_GET['page'] ?? 1);

if ($page < 1) {
    $page = 1;
}

$limit = 6;
$offset = ($page - 1) * $limit;

$countStmt = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM reports
    WHERE user_id = ?
");
$countStmt->execute([$user['id']]);
$totalItems = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalItems / $limit));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$stmt = $pdo->prepare("
    SELECT
        reports.*,
        category.categoryName
    FROM reports
    LEFT JOIN category
    ON reports.category_id = category.id
    WHERE reports.user_id = ?
    ORDER BY reports.created_at DESC
    LIMIT " . (int)$limit . "
    OFFSET " . (int)$offset
);
$stmt->execute([$user['id']]);
$items = $stmt->fetchAll();
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-10">
            <div>
                <p class="uppercase tracking-[3px] text-sm text-gray-500 mb-3">
                    My Reports
                </p>

                <h1 class="text-4xl font-bold text-black">
                    Reported Items
                </h1>

                <p class="text-gray-500 mt-2">
                    You submitted <?= $totalItems ?> report<?= $totalItems === 1 ? '' : 's' ?>.
                </p>
            </div>

            <a
                href="report.php"
                class="inline-flex justify-center px-6 py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">
                Report New Item
            </a>
        </div>

        <?php if ($items): ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <?php foreach ($items as $item): ?>
                    <?php
                    $statusClasses = [
                        'approved' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        'claimed' => 'bg-blue-100 text-blue-700'
                    ];

                    $typeClass = $item['report_type'] === 'lost'
                        ? 'bg-red-100 text-red-600'
                        : 'bg-green-100 text-green-600';
                    ?>

                    <article class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-xl transition border border-gray-100">
                        <div class="h-48 bg-gray-100 overflow-hidden">
                            <img
                                src="<?= htmlspecialchars(reportImageSrc($item['image'])) ?>"
                                alt="<?= htmlspecialchars($item['title']) ?>"
                                class="w-full h-full object-cover">
                        </div>

                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="<?= $typeClass ?> px-3 py-1 rounded-full text-sm">
                                    <?= htmlspecialchars(ucfirst($item['report_type'])) ?>
                                </span>

                                <span class="<?= $statusClasses[$item['status']] ?? 'bg-gray-100 text-gray-700' ?> px-3 py-1 rounded-full text-sm">
                                    <?= htmlspecialchars(ucfirst($item['status'])) ?>
                                </span>
                            </div>

                            <h2 class="text-xl font-semibold text-black mb-2">
                                <?= htmlspecialchars($item['title']) ?>
                            </h2>

                            <p class="text-gray-500 mb-1">
                                <?= htmlspecialchars($item['categoryName'] ?? 'Uncategorized') ?>
                            </p>

                            <p class="text-gray-600 mb-5">
                                <?= htmlspecialchars($item['location']) ?>
                            </p>

                            <div class="grid grid-cols-3 gap-2">
                                <a
                                    href="single.php?id=<?= (int)$item['id'] ?>"
                                    class="text-center py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">
                                    View
                                </a>

                                <a
                                    href="edit_report.php?id=<?= (int)$item['id'] ?>"
                                    class="text-center py-3 border border-black text-black rounded-2xl hover:bg-gray-100 transition">
                                    Edit
                                </a>

                                <form method="POST" action="delete_report.php" onsubmit="return confirm('Delete this report?');">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button
                                        type="submit"
                                        class="w-full py-3 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div>

            <?php renderPagination($page, $totalPages); ?>

        <?php else: ?>
            <div class="bg-white border border-gray-100 rounded-3xl p-10 text-center shadow-sm">
                <h2 class="text-2xl font-bold text-black mb-3">
                    No reports yet
                </h2>

                <p class="text-gray-500 mb-6">
                    Once you submit a lost or found item, it will appear here with its review status.
                </p>

                <a
                    href="report.php"
                    class="inline-flex px-6 py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">
                    Create Your First Report
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once "includes/footer.php"; ?>
