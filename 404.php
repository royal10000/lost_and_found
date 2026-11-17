<?php require_once "layouts/header.php"; ?>

<section class="py-20 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <div class="bg-white rounded-3xl shadow-md border border-gray-100 p-10">
            <p class="uppercase tracking-[3px] text-sm text-gray-500 mb-3">
                Page unavailable
            </p>

            <h1 class="text-4xl font-bold text-black mb-4">
                Let us get you back to the reports.
            </h1>

            <p class="text-gray-600 mb-8">
                The page you requested is not available, but you can continue browsing current lost and found items.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="items.php" class="px-6 py-3 bg-black text-white rounded-2xl hover:bg-gray-800 transition">
                    Browse Items
                </a>
                <a href="index.php" class="px-6 py-3 border border-black text-black rounded-2xl hover:bg-gray-100 transition">
                    Home
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once "layouts/footer.php"; ?>
