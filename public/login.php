<?php require_once __DIR__.("/../layouts/header.php")?>'
fa'
<section class="flex items-center justify-center min-h-[80vh] px-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">

        <h2 class="text-3xl font-bold text-center mb-2">
            Welcome Back
        </h2>

        <p class="text-center text-gray-500 mb-8">
            Login to your account
        </p>

        <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
            Database error message
        </div>

        <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
            General error message
        </div>

        <form class="space-y-5" method="POST" action="">

            <div>

                <label class="text-sm font-medium">
                    Email
                </label>

                <input
                    name="email"
                    type="email"
                    placeholder="Enter your email"
                    class="w-full mt-2 p-4 rounded-xl border outline-none focus:ring-2 focus:ring-black"
                />

                <p class="text-red-500 text-sm mt-1">
                    Email error message
                </p>

            </div>

            <div>

                <label class="text-sm font-medium">
                    Password
                </label>

                <input
                    name="password"
                    type="password"
                    placeholder="Enter your password"
                    class="w-full mt-2 p-4 rounded-xl border outline-none focus:ring-2 focus:ring-black"
                />

                <p class="text-red-500 text-sm mt-1">
                    Password error message
                </p>

            </div>

            <div class="flex items-center justify-between text-sm">

                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="remember"
                    />
                    Remember me
                </label>

                <a
                    href="contact.php"
                    class="text-black hover:underline"
                >
                    Forgot password?
                </a>

            </div>

            <button
                type="submit"
                class="w-full bg-black text-white py-4 rounded-xl font-semibold hover:bg-gray-800 transition"
            >
                Login
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">

            Don't have an account?

            <a
                href="signup.php"
                class="text-black font-medium hover:underline"
            >
                Sign up
            </a>

        </p>

    </div>

</section>
<?php require_once __DIR__.("/../layouts/footer.php")?>
