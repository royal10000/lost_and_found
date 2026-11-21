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
                        <a href="lost_item.php" class="hover:text-white transition">Lost Items</a>
                    </li>
                    <li>
                        <a href="found_item.php" class="hover:text-white transition">Found Items</a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-2xl font-semibold mb-6">Support</h3>

                <ul class="space-y-4 text-gray-400">
                    <li>
                        <a href="contact.php" class="hover:text-white transition">Contact details</a>
                    </li>
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

<!-- ================= MOBILE MENU SCRIPT ================= -->
<script>
    (() => {
        const messages = document.querySelectorAll('[role="alert"], [role="status"], .text-red-500.text-sm.mt-1, .text-red-500.text-sm.mt-2');
        messages.forEach((message) => {
            const isError = message.matches('[role="alert"], .text-red-500');
            const text = message.textContent.trim().replace(/\s+/g, ' ');
            if (!text) {
                message.remove();
                return;
            }

            if (typeof Toastify === 'function') {
                Toastify({
                    text,
                    duration: 3000,
                    close: true,
                    gravity: 'top',
                    position: 'right',
                    stopOnFocus: false,
                    style: {
                        background: isError ? '#b91c1c' : '#15803d',
                        borderRadius: '12px',
                        boxShadow: '0 12px 28px rgba(0, 0, 0, 0.16)'
                    }
                }).showToast();
            } else {
                const toast = document.createElement('div');
                toast.textContent = text;
                toast.setAttribute('role', isError ? 'alert' : 'status');
                toast.className = `fixed right-4 top-4 z-[100] max-w-sm rounded-xl px-5 py-4 text-white shadow-xl ${isError ? 'bg-red-700' : 'bg-green-700'}`;
                document.body.appendChild(toast);
                window.setTimeout(() => toast.remove(), 3000);
            }

            message.remove();
        });
    })();
</script>
<script>
    document.querySelectorAll('.toastify').forEach((toast) => {
        // Keep any Toastify notification that another page script created to three seconds.
        window.setTimeout(() => {
            toast.style.transition = 'opacity 250ms ease';
            toast.style.opacity = '0';
            window.setTimeout(() => toast.remove(), 250);
        }, 3000);
    });
</script>
<script src="assets/js/script.js"></script>
</body>

</html>
