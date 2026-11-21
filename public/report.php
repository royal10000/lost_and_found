<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../class/report.php";

$report = new Report();
$isLoggedIn = Session::isLoggedIn();
if (!$isLoggedIn) {
    header("Location: index.php");
    exit();
}

$errors = [];
$dateBounds = $report->getDateBounds();
$categories = $report->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $report->createReport($_POST, $_FILES['image'] ?? [], (int) ($_SESSION['user']['user_id'] ?? 0));
    if (!$errors) {
        header('Location: report.php?report=submitted');
        exit;
    }
}

require_once __DIR__ . "/../layouts/header.php";
?>


<div class="max-w-4xl mx-auto py-12 px-4">

    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">

        <!-- Header -->
        <div class="bg-black p-8 text-center">
            <h1 class="text-3xl font-bold text-white">
                Report Lost or Found Item
            </h1>

            <p class="mt-2 text-gray-400">
                Help people reconnect with their belongings
            </p>
        </div>

        <!-- Form -->
        <div class="p-8">

            <?php if (isset($errors['general'])) : ?>
                <div role="alert" class="mb-6 bg-red-100 text-red-700 px-4 py-3 rounded-xl">
                    <?= htmlspecialchars($errors['general']) ?>
                    <?php if (empty($_SESSION['user']['user_id'])) : ?>
                        <a href="login.php" class="underline font-semibold">Sign in</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (($_GET['report'] ?? '') === 'submitted') : ?>
                <div role="status" class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded-xl">
                    Report submitted successfully. Please wait for approval.
                </div>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <div role="status" class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded-xl">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6">

                <!-- Report Type -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Report Type
                    </label>

                    <select
                        name="report_type"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">
                        <option value="">Select Type</option>

                        <option value="lost"
                            <?= ($_POST['report_type'] ?? "") === "lost" ? "selected" : "" ?>>
                            Lost Item
                        </option>

                        <option value="found"
                            <?= ($_POST['report_type'] ?? "") === "found" ? "selected" : "" ?>>
                            Found Item
                        </option>

                    </select>

                    <?php if (isset($errors['report_type'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['report_type'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Title -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Item Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="<?= htmlspecialchars($_POST['title'] ?? "") ?>"
                        placeholder="e.g Black Wallet"
                        required
                        minlength="3"
                        maxlength="150"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <?php if (isset($errors['title'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['title'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Category -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Category
                    </label>

                    <select
                        name="category"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">
                        <option value="">Select Category</option>
                        <?php
                        foreach ($categories as $category) : ?>

                            <option value="<?= htmlspecialchars((string) $category['id']) ?>"
                                <?= (string) ($_POST['category'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($category['categoryName'])) ?>
                            </option>

                        <?php endforeach; ?>
                    </select>

                    <?php if (isset($errors['category'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['category'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Description -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        minlength="10"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3"><?= htmlspecialchars($_POST['description'] ?? "") ?></textarea>

                    <?php if (isset($errors['description'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['description'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Location -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        value="<?= htmlspecialchars($_POST['location'] ?? "") ?>"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <?php if (isset($errors['location'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['location'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Date -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Date
                    </label>

                    <input
                        type="date"
                        name="date"
                        min="<?= htmlspecialchars($dateBounds['minimum']) ?>"
                        max="<?= htmlspecialchars($dateBounds['maximum']) ?>"
                        value="<?= htmlspecialchars($_POST['date'] ?? "") ?>"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <?php if (isset($errors['date'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['date'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Contact -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Contact Information
                    </label>

                    <input
                        type="tel"
                        name="contact"
                        value="<?= htmlspecialchars($_POST['contact'] ?? "") ?>"
                        pattern="[0-9]{10,15}"
                        minlength="10"
                        maxlength="15"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <?php if (isset($errors['contact'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['contact'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Image -->
                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Upload Image Optional
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <?php if (isset($errors['image'])) : ?>
                        <p class="text-red-500 text-sm mt-2">
                            <?= $errors['image'] ?>
                        </p>
                    <?php endif; ?>

                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full bg-black text-white py-4 rounded-xl hover:bg-gray-900 transition">
                    Submit Report
                </button>

            </form>

        </div>

    </div>

</div>

<script>
    (() => {
        const dateInput = document.querySelector('input[name="date"]');
        if (!dateInput) return;

        const validateReportDate = (value, minimum, maximum) => {
            if (!value) return 'Please select a date.';

            const parts = value.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (!parts) return 'Please enter a valid date.';

            const [, year, month, day] = parts;
            const parsed = new Date(Date.UTC(Number(year), Number(month) - 1, Number(day)));
            if (
                parsed.getUTCFullYear() !== Number(year) ||
                parsed.getUTCMonth() !== Number(month) - 1 ||
                parsed.getUTCDate() !== Number(day)
            ) {
                return 'Please enter a valid date.';
            }
            if (value < minimum || value > maximum) {
                return 'Choose a date from the last two months through today.';
            }
            return '';
        };

        const updateDateValidity = () => {
            dateInput.setCustomValidity(
                validateReportDate(dateInput.value, dateInput.min, dateInput.max)
            );
        };

        dateInput.addEventListener('input', updateDateValidity);
        dateInput.addEventListener('change', updateDateValidity);
        updateDateValidity();
    })();
</script>

<?php require_once __DIR__ . "/../layouts/footer.php" ?>
