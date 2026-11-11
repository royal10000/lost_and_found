<?php
require_once "class/reports.php";

$reports = new Report();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeReportStatus'])) {
    $status = $_POST['status'] ?? '';
    $reports->updateStatus($id, $status);
    header("Location:single.php?id=" . $id);
    exit();
}

$report = $reports->findReport($id);

if (!$report) {
    require_once "includes/header.php";
    ?>
    <div class="flex-1 p-8">
        <div class="bg-white rounded-xl shadow p-8">
            <h1 class="text-2xl font-bold mb-3">Report unavailable</h1>
            <p class="text-gray-600 mb-6">This report does not exist or was removed.</p>
            <a href="reports.php" class="inline-flex px-5 py-3 rounded-lg bg-gray-900 text-white">Back to Reports</a>
        </div>
    </div>
    <?php
    require_once "includes/footer.php";
    exit();
}

$image = trim((string) $report['image']);
$imageSrc = $image !== '' && is_file(__DIR__ . '/../uploads/' . basename($image))
    ? '../uploads/' . rawurlencode(basename($image))
    : '../assets/img/no-image.svg';

require_once "includes/header.php";
?>

<div class="flex-1 p-8 bg-gray-100 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Report Details</h1>
            <p class="text-gray-500">View item information and reporter details.</p>
        </div>
        <a href="reports.php" class="px-5 py-3 rounded-lg border bg-white hover:bg-gray-50">Back to Reports</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow overflow-hidden">
            <img src="<?= htmlspecialchars($imageSrc) ?>" alt="<?= htmlspecialchars($report['title']) ?>"
                class="w-full h-80 object-cover bg-gray-100">

            <div class="p-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    <!-- <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                    </span> -->
                    <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">
                        <?= htmlspecialchars(ucfirst($report['report_type'])) ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">
                        <?= htmlspecialchars(ucfirst($report['status'])) ?>
                    </span>
                </div>

                <h2 class="text-3xl font-bold mb-4"><?= htmlspecialchars($report['title']) ?></h2>
                <p class="text-gray-600 leading-relaxed mb-6"><?= nl2br(htmlspecialchars($report['description'])) ?></p>

                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-gray-500">Category</p>
                        <p class="font-semibold"><?= htmlspecialchars($report['categoryName'] ?? 'Uncategorized') ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-gray-500">Location</p>
                        <p class="font-semibold"><?= htmlspecialchars($report['location']) ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-gray-500">Lost/Found Date</p>
                        <p class="font-semibold"><?= htmlspecialchars($report['lost_found_date']) ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-gray-500">Contact</p>
                        <p class="font-semibold"><?= htmlspecialchars($report['contact'] ?? 'Not provided') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-xl font-bold mb-4">Reporter</h3>
                <div class="space-y-3 text-sm">
                    <p><span class="text-gray-500">Username:</span>
                        <?= htmlspecialchars($report['username'] ?? 'Unknown') ?></p>
                    <p><span class="text-gray-500">Email:</span> <?= htmlspecialchars($report['email'] ?? 'Unknown') ?>
                    </p>
                    <p><span class="text-gray-500">Role:</span> <?= $report['isAdmin'] ? 'Admin' : 'User' ?></p>
                    <p><span class="text-gray-500">Joined:</span>
                        <?= htmlspecialchars(date('M d, Y', strtotime($report['user_created_at']))) ?></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-xl font-bold mb-4">Approval</h3>

                <?php if ($report['status'] === 'pending'): ?>
                    <div class="grid gap-3">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= (int) $report['id'] ?>">
                            <input type="hidden" name="changeReportStatus" value="1">
                            <input type="hidden" name="status" value="approved">
                            <button
                                class="w-full px-5 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700">Approve</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= (int) $report['id'] ?>">
                            <input type="hidden" name="changeReportStatus" value="1">
                            <input type="hidden" name="status" value="rejected">
                            <button
                                class="w-full px-5 py-3 rounded-lg bg-red-600 text-white hover:bg-red-700">Reject</button>
                        </form>
                    </div>
                <?php elseif ($report['status'] === 'approved'): ?>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= (int) $report['id'] ?>">
                        <input type="hidden" name="changeReportStatus" value="1">
                        <input type="hidden" name="status" value="claimed">
                        <button class="w-full px-5 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Mark as
                            Claimed</button>
                    </form>
                <?php elseif ($report['status'] === 'claimed'): ?>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                        Claimed
                    </span>

                <?php else: ?>
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                        Rejected
                    </span>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>