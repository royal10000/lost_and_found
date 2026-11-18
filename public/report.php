<?php
require_once __DIR__ . "/../includes/session.php";

/** Accept only a real YYYY-MM-DD date from the last two calendar months through today. */
function isValidReportDate(string $date): bool
{
    $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $dateErrors = DateTimeImmutable::getLastErrors();

    if (!$parsedDate || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $parsedDate->format('Y-m-d') !== $date) {
        return false;   
    }

    $today = new DateTimeImmutable('today');
    $earliestDate = $today->modify('-2 months');

    return $parsedDate >= $earliestDate && $parsedDate <= $today;
}

$errors = [];
$today = new DateTimeImmutable('today');
$minimumReportDate = $today->modify('-2 months')->format('Y-m-d');
$maximumReportDate = $today->format('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = trim($_POST['report_type'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if (!in_array($reportType, ['lost', 'found'], true)) {
        $errors['report_type'] = 'Please select Lost Item or Found Item.';
    }
    if ($title === '') {
        $errors['title'] = 'Please enter an item title.';
    }
    if ($category === '' || !ctype_digit($category)) {
        $errors['category'] = 'Please select a category.';
    }
    if ($description === '') {
        $errors['description'] = 'Please enter a description.';
    }
    if ($location === '') {
        $errors['location'] = 'Please enter a location.';
    }
    if (!isValidReportDate($date)) {
        $errors['date'] = 'Choose a valid date from the last two months through today.';
    }
    if ($contact === '') {
        $errors['contact'] = 'Please enter contact information.';
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

            <?php if (!empty($success)) : ?>
                <div class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded-xl">
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
                        $query = "SELECT * FROM CATEGORY ORDER BY categoryName ASC";
                        $category_query = $pdo->query($query);
                        while ($category = $category_query->fetch()) : ?>

                            <option value="<?= htmlspecialchars((string) $category['id']) ?>"
                                <?= (string) ($_POST['category'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($category['categoryName'])) ?>
                            </option>

                        <?php endwhile; ?>
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
                        min="<?= htmlspecialchars($minimumReportDate) ?>"
                        max="<?= htmlspecialchars($maximumReportDate) ?>"
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

            const parsed = new Date(`${value}T00:00:00`);
            if (Number.isNaN(parsed.getTime()) || parsed.toISOString().slice(0, 10) !== value) {
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

<?php require_once __DIR__ ."/../layouts/footer.php" ?>
