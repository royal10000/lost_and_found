<?php
require_once "config/database.php";

class CategoryAdmin extends Database
{
    public function pdo(): PDO
    {
        return $this->conn;
    }
}

$db = new CategoryAdmin();
$pdo = $db->pdo();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['categoryName'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'create' || $action === 'update') {
        if ($name === '') {
            $errors['categoryName'] = "Category name is required";
        } elseif (strlen($name) < 2) {
            $errors['categoryName'] = "Category name is too short";
        }
    }

    if (empty($errors)) {
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO category (categoryName) VALUES (?)");
                $stmt->execute([$name]);
                header("Location:category.php");
                exit();
            }

            if ($action === 'update' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE category SET categoryName = ? WHERE id = ?");
                $stmt->execute([$name, $id]);
                header("Location:category.php");
                exit();
            }

            if ($action === 'delete' && $id > 0) {
                $usage = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE category_id = ?");
                $usage->execute([$id]);

                if ((int) $usage->fetchColumn() > 0) {
                    $errors['delete'] = "This category is used by reports and cannot be deleted.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
                    $stmt->execute([$id]);
                    header("Location:category.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $errors['database'] = "Category name already exists or could not be saved.";
        }
    }
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$total = (int) $pdo->query("SELECT COUNT(*) FROM category")->fetchColumn();
$totalPages = max(1, (int) ceil($total / $limit));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$stmt = $pdo->prepare("
    SELECT
        category.*,
        COUNT(reports.id) AS report_count
    FROM category
    LEFT JOIN reports ON reports.category_id = category.id
    GROUP BY category.id
    ORDER BY category.categoryName ASC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll();
$editId = (int) ($_GET['edit'] ?? 0);

require_once "includes/header.php";
?>

<div class="flex-1 w-full p-8 bg-gray-100 min-h-screen">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
        </div>

        <div class="p-6 border-b bg-gray-50">
            <?php if (isset($errors['database'])): ?>
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-xl"><?= htmlspecialchars($errors['database']) ?>
                </div>
            <?php endif; ?>
            <?php if (isset($errors['delete'])): ?>
                <div class="mb-4 bg-yellow-100 text-yellow-700 px-4 py-3 rounded-xl">
                    <?= htmlspecialchars($errors['delete']) ?></div>
            <?php endif; ?>

            <form method="POST" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="action" value="create">
                <input type="text" name="categoryName" placeholder="New category name"
                    class="flex-1 border rounded-xl px-4 py-3">
                <button class="px-6 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800">Add Category</button>
            </form>
            <?php if (isset($errors['categoryName'])): ?>
                <p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($errors['categoryName']) ?></p>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">S.N.</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Reports</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $index => $category): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4"><?= $offset + $index + 1 ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <?php if ($editId === (int) $category['id']): ?>
                                    <form method="POST" class="flex gap-2">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                        <input type="text" name="categoryName"
                                            value="<?= htmlspecialchars($category['categoryName']) ?>"
                                            class="border rounded-lg px-3 py-2">
                                        <button class="px-3 py-2 bg-blue-600 text-white rounded-lg">Save</button>
                                        <a href="category.php" class="px-3 py-2 border rounded-lg">Cancel</a>
                                    </form>
                                <?php else: ?>
                                    <?= htmlspecialchars(ucfirst($category['categoryName'])) ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4"><?= (int) $category['report_count'] ?></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="?edit=<?= (int) $category['id'] ?>"
                                        class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                                    <form method="POST" onsubmit="return confirm('Delete this category?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                        <form method="POST" onsubmit="return confirm('Delete this category?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">

                                            <button class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-center gap-2 my-6">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>"
                            class="px-4 py-2 rounded-lg font-medium transition <?= $page == $i ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>