<?php
require_once "includes/db_connection.php";
require_once "includes/sessions.php";
require_once "includes/functions.php";

require_login();

$user = currentUser();
$errors = [];

$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$userStmt->execute([$user['id']]);
$profileUser = $userStmt->fetch();

if (!$profileUser) {
    message("Profile is not available.", "warning");
    header("Location:index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profileImage = saveUploadedImage($_FILES['profile_image'] ?? [], "uploads/profiles/", $errors, 'profile_image');

    if ($profileImage !== null && empty($errors)) {
        $update = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $update->execute([$profileImage, $user['id']]);
        $_SESSION['user']['profile_image'] = $profileImage;
        message("Profile picture updated.");
        header("Location:profile.php");
        exit();
    }

    if (empty($errors)) {
        $errors['profile_image'] = "Please choose an image";
    }
}

$statsStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total_reports,
        SUM(status = 'approved') AS approved_reports,
        SUM(status = 'pending') AS pending_reports,
        SUM(status = 'rejected') AS rejected_reports,
        SUM(status = 'claimed') AS claimed_reports
    FROM reports
    WHERE user_id = ?
");
$statsStmt->execute([$user['id']]);
$stats = $statsStmt->fetch();
require_once "includes/header.php";

?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-black text-white p-8">
                <p class="uppercase tracking-[3px] text-sm text-gray-400 mb-3">
                    Account
                </p>

                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    <img
                        src="<?= htmlspecialchars(profileImageSrc($profileUser['profile_image'])) ?>"
                        alt="<?= htmlspecialchars($profileUser['username']) ?>"
                        class="w-20 h-20 rounded-full object-cover bg-white border-4 border-white">

                    <div>
                        <h1 class="text-4xl font-bold">
                            <?= htmlspecialchars($profileUser['username']) ?>
                        </h1>

                        <p class="text-gray-300 mt-2">
                            <?= htmlspecialchars($profileUser['email']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="p-8 border-b border-gray-100">
                <label class="block mb-2 font-semibold text-gray-700">Profile Picture</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input
                        type="file"
                        name="profile_image"
                        class="flex-1 border border-gray-300 rounded-xl px-4 py-3">

                    <button
                        type="submit"
                        class="px-6 py-3 bg-black text-white rounded-xl hover:bg-gray-800 transition">
                        Upload
                    </button>
                </div>
                <?php if (isset($errors['profile_image'])): ?>
                    <p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['profile_image']) ?></p>
                <?php endif; ?>
            </form>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-5 p-8">
                <div class="rounded-2xl bg-gray-50 p-6 border border-gray-100">
                    <p class="text-gray-500">Total Reports</p>
                    <h2 class="text-3xl font-bold mt-2"><?= (int)$stats['total_reports'] ?></h2>
                </div>

                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <p class="text-green-700">Approved</p>
                    <h2 class="text-3xl font-bold mt-2"><?= (int)$stats['approved_reports'] ?></h2>
                </div>

                <div class="rounded-2xl bg-yellow-50 p-6 border border-yellow-100">
                    <p class="text-yellow-700">Pending</p>
                    <h2 class="text-3xl font-bold mt-2"><?= (int)$stats['pending_reports'] ?></h2>
                </div>

                <div class="rounded-2xl bg-red-50 p-6 border border-red-100">
                    <p class="text-red-700">Rejected</p>
                    <h2 class="text-3xl font-bold mt-2"><?= (int)$stats['rejected_reports'] ?></h2>
                </div>

                <div class="rounded-2xl bg-blue-50 p-6 border border-blue-100">
                    <p class="text-blue-700">Claimed</p>
                    <h2 class="text-3xl font-bold mt-2"><?= (int)$stats['claimed_reports'] ?></h2>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 px-8 pb-8">
                <a
                    href="reported_item.php"
                    class="inline-flex justify-center px-6 py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">
                    View My Reports
                </a>

                <a
                    href="report.php"
                    class="inline-flex justify-center px-6 py-3 border border-black text-black rounded-2xl hover:bg-gray-100 transition">
                    Report New Item
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
