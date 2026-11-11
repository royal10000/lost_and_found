<?php


require_once "includes/db_connection.php";
require_once "includes/sessions.php";
require_once "includes/functions.php";

require_login();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    // Inputs
    $report_type = trim($_POST['report_type'] ?? "");
    $title = trim($_POST['title'] ?? "");
    $category_id = trim($_POST['category'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $location = trim($_POST['location'] ?? "");
    $lost_found_date = trim($_POST['date'] ?? "");
    $contact = trim($_POST['contact'] ?? "");

    // Validation
    if (empty($report_type)) {
        $errors['report_type'] = "Please select report type";
    }

    if (empty($title)) {
        $errors['title'] = "Item title is required";
    } elseif (strlen($title) < 3) {
        $errors['title'] = "Title must be at least 3 characters";
    }

    if (empty($category_id)) {
        $errors['category'] = "Please select category";
    }

    if (empty($description)) {
        $errors['description'] = "Description is required";
    } elseif (strlen($description) < 10) {
        $errors['description'] = "Description too short";
    }

    if (empty($location)) {
        $errors['location'] = "Location is required";
    }

    if (empty($lost_found_date)) {
        $errors['date'] = "Date is required";
    }

    if (empty($contact)) {
    $errors['contact'] = "Contact information is required";
} elseif (!preg_match("/^[0-9]{10,15}$/", $contact)) {
    $errors['contact'] = "Phone number must contain only numbers and be 10 to 15 digits";
}

    $imageName = null;

    if (isset($_FILES['image'])) {
        $imageName = saveUploadedImage($_FILES['image'], "uploads/", $errors);
    }

    // If No Errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
    INSERT INTO reports (
        user_id,
        category_id,
        report_type,
        title,
        lost_found_date,
        description,
        location,
        contact,
        image,
        status
    )

    VALUES (
        :user_id,
        :category_id,
        :report_type,
        :title,
        :lost_found_date,
        :description,
        :location,
        :contact,
        :image,
        :status
    )
");
            $result = $stmt->execute([
                ':user_id' => $_SESSION['user']['id'],
                ':category_id' => $category_id,
                ':report_type' => $report_type,
                ':title' => $title,
                ':lost_found_date' => $lost_found_date,
                ':description' => $description,
                ':location' => $location,
                ':contact' => $contact,
                ':image' => $imageName,
                ':status' => 'pending'
            ]);

            if (!$result) {
                throw new Exception("Error while inserting reports into database");
            } else {
                message("report submitted successfully. Please wait for approval");
                header("Location:index.php");
                exit();
            }
        } catch (\Throwable $th) {
            //throw $th;
            message($th->getMessage(), "error");
        }
    }
}

require_once "includes/header.php";

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
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">
                        <option value="">Select Category</option>
                        <?php
                        $query = "SELECT * FROM CATEGORY ORDER BY categoryName ASC";
                        $category_query = $pdo->query($query);
                        while ($category = $category_query->fetch()) : ?>

                            <option value="<?= $category['id'] ?>">
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
                        value="<?= htmlspecialchars($_POST['date'] ?? "") ?>"
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

<?php include "includes/footer.php"; ?>
