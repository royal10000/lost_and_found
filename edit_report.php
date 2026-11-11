<?php
require_once "includes/db_connection.php";
require_once "includes/sessions.php";
require_once "includes/functions.php";

require_login();

$user = currentUser();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    message("Report link is not available.", "warning");
    header("Location:reported_item.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$id, $user['id']]);
$report = $stmt->fetch();

if (!$report) {
    message("You can only edit your own reports.", "warning");
    header("Location:reported_item.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_type = trim($_POST['report_type'] ?? "");
    $title = trim($_POST['title'] ?? "");
    $category_id = trim($_POST['category'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $location = trim($_POST['location'] ?? "");
    $lost_found_date = trim($_POST['date'] ?? "");
    $contact = trim($_POST['contact'] ?? "");
    $imageName = $report['image'];

    if ($report_type !== 'lost' && $report_type !== 'found') {
        $errors['report_type'] = "Please select report type";
    }

    if ($title === '') {
        $errors['title'] = "Item title is required";
    } elseif (strlen($title) < 3) {
        $errors['title'] = "Title must be at least 3 characters";
    }

    if ($category_id === '') {
        $errors['category'] = "Please select category";
    }

    if ($description === '') {
        $errors['description'] = "Description is required";
    } elseif (strlen($description) < 10) {
        $errors['description'] = "Description too short";
    }

    if ($location === '') {
        $errors['location'] = "Location is required";
    }

    if ($lost_found_date === '') {
        $errors['date'] = "Date is required";
    }

    if ($contact === '') {
        $errors['contact'] = "Contact information is required";
    }

    if (isset($_FILES['image'])) {
        $newImage = saveUploadedImage($_FILES['image'], "uploads/", $errors);

        if ($newImage !== null) {
            $imageName = $newImage;
        }
    }

    if (empty($errors)) {
        $update = $pdo->prepare("
            UPDATE reports
            SET category_id = ?,
                report_type = ?,
                title = ?,
                lost_found_date = ?,
                description = ?,
                location = ?,
                contact = ?,
                image = ?,
                status = 'pending'
            WHERE id = ? AND user_id = ?
        ");
        $update->execute([
            $category_id,
            $report_type,
            $title,
            $lost_found_date,
            $description,
            $location,
            $contact,
            $imageName,
            $id,
            $user['id']
        ]);

        message("Report updated. It is pending admin approval again.");
        header("Location:reported_item.php");
        exit();
    }

    $report = array_merge($report, [
        'report_type' => $report_type,
        'title' => $title,
        'category_id' => $category_id,
        'description' => $description,
        'location' => $location,
        'lost_found_date' => $lost_found_date,
        'contact' => $contact,
        'image' => $imageName
    ]);
}

$categories = $pdo->query("SELECT * FROM category ORDER BY categoryName ASC")->fetchAll();

require_once "includes/header.php";
?>

<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <div class="bg-black p-8 text-center">
                <h1 class="text-3xl font-bold text-white">Edit Report</h1>
                <p class="mt-2 text-gray-400">Changes return the report to pending review.</p>
            </div>

            <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                <input type="hidden" name="id" value="<?= (int)$id ?>">

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Report Type</label>
                    <select name="report_type" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                        <option value="">Select Type</option>
                        <option value="lost" <?= $report['report_type'] === 'lost' ? 'selected' : '' ?>>Lost Item</option>
                        <option value="found" <?= $report['report_type'] === 'found' ? 'selected' : '' ?>>Found Item</option>
                    </select>
                    <?php if (isset($errors['report_type'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['report_type']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Item Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($report['title']) ?>" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                    <?php if (isset($errors['title'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['title']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Category</label>
                    <select name="category" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int)$category['id'] ?>" <?= (int)$report['category_id'] === (int)$category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($category['categoryName'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['category']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Description</label>
                    <textarea name="description" rows="5" class="w-full border border-gray-300 rounded-xl px-4 py-3"><?= htmlspecialchars($report['description']) ?></textarea>
                    <?php if (isset($errors['description'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['description']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Location</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($report['location']) ?>" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                    <?php if (isset($errors['location'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['location']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Date</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($report['lost_found_date']) ?>" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                    <?php if (isset($errors['date'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['date']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Contact Information</label>
                    <input type="text" name="contact" value="<?= htmlspecialchars($report['contact']) ?>" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                    <?php if (isset($errors['contact'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['contact']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Replace Image Optional</label>
                    <img src="<?= htmlspecialchars(reportImageSrc($report['image'])) ?>" alt="<?= htmlspecialchars($report['title']) ?>" class="w-full max-h-72 object-cover rounded-2xl mb-3 bg-gray-100">
                    <input type="file" name="image" class="w-full border border-gray-300 rounded-xl px-4 py-3">
                    <?php if (isset($errors['image'])): ?><p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['image']) ?></p><?php endif; ?>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 bg-black text-white py-4 rounded-xl hover:bg-gray-900 transition">Save Changes</button>
                    <a href="reported_item.php" class="flex-1 text-center border border-black text-black py-4 rounded-xl hover:bg-gray-100 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
