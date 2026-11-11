<?php require_once "includes/functions.php" ?>
<!-- ================= FOOTER ================= -->
<footer class="bg-black text-white pt-16 sm:pt-20 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div
            class="grid sm:grid-cols-2 lg:grid-cols-4 gap-12 pb-14 border-b border-white/10">
            <!-- Brand -->
            <div>
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-5">
                    Lost<span class="text-gray-400">&</span>Found
                </h2>

                <p class="text-gray-400 leading-relaxed">
                    Helping communities reconnect with lost belongings.
                </p>
            </div>

            <!-- Platform -->
            <div>
                <h3 class="text-2xl font-semibold mb-6">Platform</h3>

                <ul class="space-y-4 text-gray-400">
                    <li>
                        <a href="items.php" class="hover:text-white transition">Browse Items</a>
                    </li>
                    <li>
                        <a href="report.php" class="hover:text-white transition">Report Item</a>
                    </li>
                    <li>
                        <a href="found.php" class="hover:text-white transition">Found Items</a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-2xl font-semibold mb-6">Support</h3>

                <ul class="space-y-4 text-gray-400">
                    <li>
                        <a href="contact.php" class="hover:text-white transition">Help Center</a>
                    </li>
                    <li>
                        <a href="contact.php" class="hover:text-white transition">Privacy Policy</a>
                    </li>
                    <li><a href="contact.php" class="hover:text-white transition">Terms</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-2xl font-semibold mb-6">Contact</h3>

                <ul class="space-y-4 text-gray-400">
                    <li>support@lostfound.com</li>
                    <li>+1 (555) 123-4567</li>
                    <li>New York, USA</li>
                </ul>
            </div>
        </div>

        <!-- Bottom -->
        <div class="pt-8 text-center">
            <p class="text-gray-500 text-sm">
                © 2026 Lost & Found. All rights reserved.
            </p>
        </div>
    </div>
</footer>
<?php

if (isset($_SESSION['toast'])) {
    displayToast();
}

?>
<!-- ================= MOBILE MENU SCRIPT ================= -->
<script src="assets/js/script.js"></script>
</body>

</html>
