<?php
require_once "includes/db_connection.php";
require_once "includes/sessions.php";
require_once "includes/functions.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    message("That item link is not available. Please browse current reports.", "warning");
    header("Location:items.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT
        reports.*,
        category.categoryName,
        users.username,
        users.email
    FROM reports
    LEFT JOIN category
    ON reports.category_id = category.id
    LEFT JOIN users
    ON reports.user_id = users.id
    WHERE reports.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    message("That item is not available anymore. Please browse current reports.", "warning");
    header("Location:items.php");
    exit();
}

$viewer = currentUser();
$isOwner = $viewer && (int)$viewer['id'] === (int)$item['user_id'];
$isAdmin = $viewer && !empty($viewer['is_admin']);

if ($item['status'] !== 'approved' && !$isOwner && !$isAdmin) {
    message("That item is waiting for approval and is not public yet.", "warning");
    header("Location:items.php");
    exit();
}

$backUrl = $item['report_type'] === 'lost' ? 'lost_page.php' : 'found.php';
$backLabel = $item['report_type'] === 'lost' ? 'Lost' : 'Found';
$typeClass = $item['report_type'] === 'lost' ? 'bg-red-500' : 'bg-green-500';
$dateLabel = $item['report_type'] === 'lost' ? 'Date Lost' : 'Date Found';
$locationLabel = $item['report_type'] === 'lost' ? 'Lost Location' : 'Found Location';

require_once "includes/header.php";
?>

<section class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <a
            href="<?= $backUrl ?>"
            class="inline-flex items-center gap-2 text-gray-600 hover:text-black transition mb-8">
            &larr; Back to <?= $backLabel ?> Items
        </a>

        <div class="grid lg:grid-cols-2 gap-10">
            <div class="space-y-5">
                <div class="bg-white rounded-[32px] overflow-hidden shadow-xl">
                    <img
                        src="<?= htmlspecialchars(reportImageSrc($item['image'])) ?>"
                        alt="<?= htmlspecialchars($item['title']) ?>"
                        class="w-full h-[500px] object-cover">
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-4 mb-5">
                    <span class="<?= $typeClass ?> text-white px-5 py-2 rounded-full text-sm font-medium shadow">
                        <?= htmlspecialchars(ucfirst($item['report_type'])) ?>
                    </span>

                    <span class="bg-gray-200 text-gray-700 px-5 py-2 rounded-full text-sm">
                        <?= htmlspecialchars(ucfirst($item['categoryName'] ?? 'Uncategorized')) ?>
                    </span>
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold text-black leading-tight">
                    <?= htmlspecialchars(ucfirst($item['title'])) ?>
                </h1>

                <p class="text-gray-600 text-lg leading-relaxed mt-6">
                    <?= nl2br(htmlspecialchars(ucfirst($item['description']))) ?>
                </p>

                <div class="grid sm:grid-cols-2 gap-5 mt-10">
                    <div class="bg-white rounded-3xl p-5 shadow-md border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-black text-white flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.6 7-11a7 7 0 10-14 0c0 6.4 7 11 7 11z" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-gray-500 text-sm">
                                    <?= $locationLabel ?>
                                </p>

                                <h3 class="font-bold text-lg">
                                    <?= htmlspecialchars(ucfirst($item['location'])) ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-5 shadow-md border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-black text-white flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v16H4V6a1 1 0 011-1z" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-gray-500 text-sm">
                                    <?= $dateLabel ?>
                                </p>

                                <h3 class="font-bold text-lg">
                                    <?= htmlspecialchars(date('M d, Y', strtotime($item['lost_found_date']))) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100 mt-10">
                    <div class="flex items-center justify-between flex-wrap gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-black text-white flex items-center justify-center text-2xl font-bold uppercase">
                                <?= htmlspecialchars(substr($item['username'] ?? 'U', 0, 1)) ?>
                            </div>

                            <div>
                                <h3 class="text-xl font-bold">
                                    <?= htmlspecialchars(ucfirst($item['username'] ?? 'Unknown user')) ?>
                                </h3>

                                <p class="text-gray-500">
                                    Reported by community member
                                </p>
                            </div>
                        </div>

                        <a
                            href="contact.php"
                            class="bg-black text-white px-8 py-4 rounded-2xl hover:bg-gray-800 transition font-semibold">
                            Contact Reporter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
