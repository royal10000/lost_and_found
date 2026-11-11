<?php
define("UPLOADS", "../uploads");
require_once "class/Dashboard.php";
require_once "class/Reports.php";
try {
    //code...
    $dashboard = new Dashboard();
    $report = new Report();
    $result = $dashboard->totalUser();
    $pendingCount = $dashboard->pendingReport();
    $approvedCount = $dashboard->approvedReport();
    $rejectedCount = $dashboard->rejectedReport();
    $claimedCount = $dashboard->claimedReport();
    $recentReports = $report->RecentReports();
} catch (\Throwable $th) {
    //throw $th;
    echo $th->getMessage();
}
require_once "includes/header.php";

?>
<!-- Content Area -->
<div class="flex-1 flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow">

        <div class="px-8 py-5 flex justify-between items-center">

            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Dashboard
                </h2>

                <p class="text-sm text-gray-500">
                    Welcome back, Administrator
                </p>
            </div>

            <div class="flex items-center gap-4">

                <input type="text" placeholder="Search..."
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <img src="https://via.placeholder.com/40" class="rounded-full" alt="Admin">

            </div>

        </div>

    </header>

    <!-- Main Content -->
    <main class="flex-1 p-8">

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">
                    Total Users
                </h3>

                <p class="text-4xl font-bold mt-3">
                    <?= $result['total'] ?>
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">
                    Pending Reports
                </h3>

                <p class="text-4xl font-bold mt-3">
                    <?= $pendingCount['pending'] ?>

                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">
                    Approved Items
                </h3>

                <p class="text-4xl font-bold mt-3">
                    <?= $approvedCount['approved'] ?>

                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">
                    Rejected Items
                </h3>

                <p class="text-4xl font-bold mt-3">
                    <?= $rejectedCount['rejected'] ?>

                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-gray-500">
                    Claimed Items
                </h3>

                <p class="text-4xl font-bold mt-3">
                    <?= $claimedCount['claimed'] ?>

                </p>
            </div>

        </div>

        <!-- Recent Reports -->
        <div class="bg-white rounded-xl shadow">

            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">
                    Recent Reports
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="text-left p-4">ID</th>
                            <th class="text-left p-4">Item</th>
                            <th class="text-left p-4">Image</th>
                            <th class="text-left p-4">User</th>
                            <th class="text-left p-4">Category</th>
                            <th class="text-left p-4">Status</th>
                            <th class="text-left p-4">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($recentReports as $key => $row): ?>

                            <tr class="border-t hover:bg-gray-50">

                                <td class="p-4">
                                    <?= $key + 1; ?>
                                </td>

                                <td>
                                    <?= strlen($row['title']) > 20
                                        ? substr($row['title'], 0, 20) . '...'
                                        : $row['title']; ?>
                                </td>
                                <td class="p-4">
                                    <img src="<?= UPLOADS . '/' . htmlspecialchars($row['image']); ?>"
                                        alt="<?= htmlspecialchars($row['title']); ?>"
                                        class="w-16 h-16 object-cover rounded-lg">
                                </td>

                                <td class="p-4">
                                    <?= ucfirst(htmlspecialchars($row['username'])); ?>
                                </td>
                                <td class="p-4">
                                    <?= (htmlspecialchars($row['categoryName'])); ?>
                                </td>

                                <td class="p-4">

                                    <?php if ($row['status'] === 'pending'): ?>

                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">
                                            Pending
                                        </span>

                                    <?php elseif ($row['status'] === 'approved'): ?>

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                            Approved
                                        </span>

                                    <?php elseif ($row['status'] === 'rejected'): ?>

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                            Rejected
                                        </span>

                                    <?php else: ?>

                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                            Claimed
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="p-4">

                                    <a href="single.php?id=<?= $row['id']; ?>"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                        View
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>
</div>
</main>

<?php require_once "includes/footer.php" ?>