<?php
function displayToast(): void
{
    if (!isset($_SESSION['toast'])) {
        return;
    }

    $toast = $_SESSION['toast'];

    $background = match ($toast['type']) {
        'success' => 'linear-gradient(to right, #00b09b, #96c93d)',
        'error' => 'linear-gradient(to right, #ff416c, #ff4b2b)',
        'warning' => 'linear-gradient(to right, #f7971e, #ffd200)',
        default => '#333'
    };

?>

    <script>
        Toastify({
            text: "<?= htmlspecialchars($toast['message']) ?>",
            duration: 1500,
            gravity: "top",
            position: "right",
            close: true,
            stopOnFocus: true,
            style: {
                background: "<?= $background ?>"
            }
        }).showToast();
    </script>

<?php

    unset($_SESSION['toast']);
}

function require_login()
{
    if (!isset($_SESSION['user'])) {
        header("Location:login.php");
        exit();
    }
}

function require_admin()
{
    require_login();

    if (empty($_SESSION['user']['is_admin'])) {
        header("Location:/lost_and_found/index.php");
        exit();
    }
}



function searchReports(
    PDO $pdo,
    string $type = "",
    string $search = "",
    string $category = "",
    string $location = "",
    int $limit = 6,
    int $offset = 0,
    string $status = "approved"
): array {
    $sql = "
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

    WHERE 1
";

    $params = [];

    if (!empty($status)) {
        $sql .= " AND reports.status = ?";
        $params[] = $status;
    }

    // Type
    if (!empty($type)) {

        $sql .= " AND reports.report_type = ?";
        $params[] = $type;
    }

    // Search
    if (!empty($search)) {

        $sql .= " AND reports.title LIKE ?";
        $params[] = "%$search%";
    }

    // Category
    if (!empty($category)) {

        $sql .= " AND reports.category_id = ?";
        $params[] = $category;
    }

    // Location
    if (!empty($location)) {

        $sql .= " AND reports.location LIKE ?";
        $params[] = "%$location%";
    }

    // Order
    $sql .= " ORDER BY reports.id DESC";

    // Pagination
    $sql .= " LIMIT " . (int)$limit;
    $sql .= " OFFSET " . (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function countReports(
    PDO $pdo,
    string $type = "",
    string $search = "",
    string $category = "",
    string $location = "",
    string $status = "approved"
): int {

    $sql = "
        SELECT COUNT(*) as total
        FROM reports
        WHERE 1
    ";

    $params = [];

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }

    if (!empty($type)) {

        $sql .= " AND report_type = ?";
        $params[] = $type;
    }

    if (!empty($search)) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$search%";
    }

    if (!empty($category)) {
        $sql .= " AND category_id = ?";
        $params[] = $category;
    }

    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetch();

    return (int)$result['total'];
}

function reportImageSrc(?string $image): string
{
    $image = trim((string)$image);

    if ($image !== '') {
        $fileName = basename($image);
        $filePath = __DIR__ . '/../uploads/' . $fileName;

        if (is_file($filePath)) {
            return 'uploads/' . rawurlencode($fileName);
        }
    }

    return 'assets/img/no-image.svg';
}

function profileImageSrc(?string $image): string
{
    $image = trim((string)$image);

    if ($image !== '' && $image !== 'default.png') {
        $fileName = basename($image);
        $filePath = __DIR__ . '/../uploads/profiles/' . $fileName;

        if (is_file($filePath)) {
            return 'uploads/profiles/' . rawurlencode($fileName);
        }
    }

    return 'assets/img/default-avatar.svg';
}

function saveUploadedImage(array $file, string $uploadDir, array &$errors, string $field = 'image'): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $errors[$field] = "Image upload failed";
        return null;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes, true)) {
        $errors[$field] = "Only JPG, JPEG, PNG allowed";
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $errors[$field] = "Image size must be less than 5MB";
        return null;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $imageName = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
    $uploadPath = rtrim($uploadDir, "/\\") . DIRECTORY_SEPARATOR . $imageName;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $errors[$field] = "Failed to upload image";
        return null;
    }

    return $imageName;
}

function renderPagination(int $currentPage, int $totalPages, array $queryParams = []): void
{
    if ($totalPages <= 1) {
        return;
    }

    $currentPage = max(1, min($currentPage, $totalPages));

?>
    <nav class="flex flex-wrap justify-center items-center gap-3 mt-12" aria-label="Pagination">
        <?php if ($currentPage > 1): ?>
            <?php
            $previousParams = array_merge($queryParams, ['page' => $currentPage - 1]);
            ?>
            <a
                href="?<?= htmlspecialchars(http_build_query($previousParams)) ?>"
                class="px-5 py-3 rounded-xl border bg-white text-black hover:bg-gray-100 transition">
                Previous
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php
            $pageParams = array_merge($queryParams, ['page' => $i]);
            ?>
            <a
                href="?<?= htmlspecialchars(http_build_query($pageParams)) ?>"
                class="px-5 py-3 rounded-xl border transition <?= $currentPage === $i
                    ? 'bg-black text-white'
                    : 'bg-white text-black hover:bg-gray-100'
                ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <?php
            $nextParams = array_merge($queryParams, ['page' => $currentPage + 1]);
            ?>
            <a
                href="?<?= htmlspecialchars(http_build_query($nextParams)) ?>"
                class="px-5 py-3 rounded-xl border bg-white text-black hover:bg-gray-100 transition">
                Next
            </a>
        <?php endif; ?>
    </nav>
<?php
}
?>
