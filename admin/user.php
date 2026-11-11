<?php
require_once "class/User.php";
require_once "includes/header.php";

$users = new User();


if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['toggle'])) {

    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];
    $users->updateUserStatus($id, $column, $value);
    exit();
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$userList = $users->getUsers(limit: $limit, page: $page);
$startNumber = ($page - 1) * $limit;

?>

<div class="flex-1 w-full p-8 bg-gray-100 min-h-screen">

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="p-6 border-b flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Users Management
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Manage all registered users
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left text-gray-600">

                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Reports</th>
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4">Active</th>
                        <!-- <th class="px-6 py-4">Action</th> -->
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($userList['data'] as $key => $row): ?>

                        <tr class="border-b hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium">
                                <?= $startNumber + $key + 1 ?>
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <?= htmlspecialchars($row['username']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['email']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    <?= $row['reports'] ?> Reports
                                </span>
                            </td>

                            <!-- Admin Toggle -->
                            <td class="px-6 py-4">

                                <label class="relative inline-flex items-center cursor-pointer">

                                    <input
                                        type="checkbox"
                                        class="sr-only peer"
                                        data-id="<?= $row['id'] ?>"
                                        data-column="isAdmin"
                                        onchange="toggleUserStatus(this)"
                                        <?= $row['isAdmin'] ? 'checked' : '' ?>>

                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all"></div>

                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>

                                </label>

                            </td>

                            <!-- Active Toggle -->
                            <td class="px-6 py-4">

                                <label class="relative inline-flex items-center cursor-pointer">

                                    <input
                                        type="checkbox"
                                        class="sr-only peer"
                                        data-id="<?= $row['id'] ?>"
                                        data-column="isActive"
                                        onchange="toggleUserStatus(this)"
                                        <?= $row['isActive'] ? 'checked' : '' ?>>

                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition-all"></div>

                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>

                                </label>

                            </td>



                        </tr>


                    <?php endforeach; ?>

                </tbody>

            </table>

            <?php if ($userList['pagination']['totalPages'] >2): ?>
                <div class="flex items-center justify-center gap-2 my-6">

                    <!-- Previous -->
                    <?php if ($page > 1): ?>
                        <a
                            href="?page=<?= $page - 1 ?>"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Previous
                        </a>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <=$userList['pagination']['totalPages']; $i++): ?>
                        <a
                            href="?page=<?= $i ?>"
                            class="
                    px-4 py-2 rounded-lg font-medium transition
                    <?= $page == $i
                            ? 'bg-blue-600 text-white'
                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100'
                    ?>
                ">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next -->
                    <?php if ($page < $userList['pagination']['totalPages']): ?>
                        <a
                            href="?page=<?= $page + 1 ?>"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Next
                        </a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>

<script>
    function toggleUserStatus(el) {
        const id = el.dataset.id;
        const column = el.dataset.column;
        const value = el.checked ? 1 : 0;

        fetch("", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `toggle=1&id=${id}&column=${column}&value=${value}`
            })
            .then(res => res.text())
            .then(data => {
                console.log(data);
            });
    }
</script>