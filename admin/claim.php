<?php
require_once "class/reports.php";

$reports = new Report();
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$reportsList = $reports->fetchReports(limit: $limit, page: $page, status: 'claimed');
$startNumber = ($page - 1) * $limit;

require_once "includes/header.php";
?>
<div class="bg-white shadow rounded-l overflow-hidden w-full mx-10 my-12">
    <div class="p-4 border-b">
        <h2 class="text-xl font-semibold">Claimed Items</h2>
    </div>

    <table class="w-full text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3">S.N.</th>
                <!-- <th class="px-4 py-3">Report ID</th> -->
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">User</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Contact</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">View</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($reportsList['reports'] as $index => $report): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $startNumber + $index + 1 ?></td>
                    <td class="px-4 py-3">
                        <a class="text-blue-600 hover:underline" href="single.php?id=<?= (int)$report['id'] ?>">
                            <?= htmlspecialchars($report['title']) ?>
                        </a>
                    </td>
                    <td class="px-4 py-3"><?= htmlspecialchars($report['username']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($report['categoryName']) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($report['contact'] ?? 'N/A') ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars(ucfirst($report['report_type'])) ?></td>
                    <td class="px-4 py-3">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">Claimed</span>
                    </td>
                    <td class="px-4 py-3"><?= htmlspecialchars($report['lost_found_date']) ?></td>
                    <td class="px-4 py-3">
                        <a href="single.php?id=<?= (int)$report['id'] ?>" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($reportsList['pagination']['totalPages'] > 1): ?>
        <div class="flex items-center justify-center gap-2 my-6">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $reportsList['pagination']['totalPages']; $i++): ?>
                <a href="?page=<?= $i ?>" class="px-4 py-2 rounded-lg font-medium transition <?= $page == $i ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $reportsList['pagination']['totalPages']): ?>
                <a href="?page=<?= $page + 1 ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php require_once "includes/footer.php"; ?>
