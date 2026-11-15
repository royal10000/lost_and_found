

<?php
require_once __DIR__ . "/../class/user.php";
require_once __DIR__."/../includes/session.php";

if (Session::isLoggedIn()) {
    header("Location: index.php");
    exit;
}


$errors = [];
$username = '';
$email = '';
$term = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['cPassword'] ?? '';
    $term = isset($_POST['term']);
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) $errors['username'] = 'Use 3–30 letters, numbers, or underscores.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['   email'] = 'Enter a valid email address.';
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) $errors['password'] = 'Use at least 8 characters, including a letter and a number.';
    if ($confirmPassword !== $password) $errors['cPassword'] = 'Passwords do not match.';
    if (!$term) $errors['term'] = 'Please agree to the terms and conditions.';
    if (!$errors) {
        try {
            (new User())->signUp($username, $email, $password);
            header('Location: login.php?registered=1');
            exit;
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage() === 'User already exists' ? 'An account with this email already exists.' : 'Unable to create your account. Please try again.';
        }
    }
}
require_once __DIR__ . "/../layouts/header.php";
?>
<section class="flex items-center justify-center min-h-[85vh] px-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">

        <h2 class="text-3xl font-bold text-center mb-2">
            Create Account
        </h2>

        <p class="text-center text-gray-500 mb-8">
            Join Lost & Found community
        </p>

        <!-- General Errors -->
        <?php if (isset($errors['database'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
                <?= htmlspecialchars($errors['database']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form class="space-y-5" method="post" action="">

            <!-- Username -->
            <div>
                <label class="text-sm font-medium">
                    Username
                </label>

                <input
                    name="username"
                    type="text"
                    value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

                <?php if (isset($errors['username'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['username'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label class="text-sm font-medium">
                    Email
                </label>

                <input
                    name="email"`r`n                    type="email"`r`n                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div>
                <label class="text-sm font-medium">
                    Password
                </label>

                <input
                    name="password"
                    type="password"
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="text-sm font-medium">
                    Confirm Password
                </label>

                <input
                    name="cPassword"
                    type="password"
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

                <?php if (isset($errors['cPassword'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['cPassword'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Terms -->
            <div>
                <div class="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        id="term"
                        name="term"
                        <?= !empty($term) ? 'checked' : '' ?> />

                    <label for="term">
                        I agree to the terms and conditions
                    </label>
                </div>

                <?php if (isset($errors['term'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['term'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Submit -->
            <button
                class="w-full bg-black text-white py-4 rounded-xl font-semibold hover:bg-gray-800 transition">
                Sign Up
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?

            <a
                href="login.php"
                class="text-black font-medium hover:underline">
                Login
            </a>
        </p>

    </div>
</section>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>

