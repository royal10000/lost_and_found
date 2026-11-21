<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="min-h-screen bg-gray-50 py-14 sm:py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
        <div class="mb-10 text-center">
            <p class="text-xs font-bold uppercase tracking-[3px] text-gray-500 mb-3">Get in touch</p>
            <h1 class="text-4xl font-bold text-gray-950 sm:text-5xl">Contact details</h1>
            <p class="mt-4 text-gray-600">Reach the Lost &amp; Found support team using the details below.</p>
        </div>

        <section class="grid gap-5 sm:grid-cols-3">
            <article class="rounded-3xl border border-gray-200 bg-white p-7 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-xl" aria-hidden="true">✉</div>
                <h2 class="mt-5 font-bold text-gray-950">Email</h2>
                <a href="mailto:support@lostfound.com" class="mt-2 inline-block break-all text-gray-600 hover:text-black">support@lostfound.com</a>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-7 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-xl" aria-hidden="true">☎</div>
                <h2 class="mt-5 font-bold text-gray-950">Phone</h2>
                <a href="tel:+15551234567" class="mt-2 inline-block text-gray-600 hover:text-black">+1 (555) 123-4567</a>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-7 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-xl" aria-hidden="true">⌖</div>
                <h2 class="mt-5 font-bold text-gray-950">Location</h2>
                <p class="mt-2 text-gray-600">New York, USA</p>
            </article>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
