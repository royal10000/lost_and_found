<?php
require_once __DIR__ . "/../class/user.php";
require_once __DIR__."/../includes/session.php";

// if (Session::isLoggedIn()) {
//     header("Location: index.php");
//     exit;
// }

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if ($password === '') {
        $errors['password'] = 'Enter your password.';
    }

    if (!$errors) {
        try {
            (new User())->logIn($email, $password);
            header('Location: index.php');
            exit;
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage() === 'Invalid email or password.'
                ? 'Email or password is incorrect.'
                : 'Unable to log in right now. Please try again.';
        }
    }
}

require_once __DIR__ . "/../layouts/header.php";
?>
<section class="flex items-center justify-center min-h-[80vh] px-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">
        <h2 class="text-3xl font-bold text-center mb-2">Welcome Back</h2>
        <p class="text-center text-gray-500 mb-8">Login to your account</p>

        <?php if (isset($_GET['registered'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4">Your account was created. Please log in.</div>
        <?php endif; ?>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4"><?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form class="space-y-5" method="POST" action="">
            <div>
                <label for="email" class="text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Enter your email"
                    class="w-full mt-2 p-4 rounded-xl border outline-none focus:ring-2 focus:ring-black" />
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password" class="text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    placeholder="Enter your password"
                    class="w-full mt-2 p-4 rounded-xl border outline-none focus:ring-2 focus:ring-black" />
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full bg-black text-white py-4 rounded-xl font-semibold hover:bg-gray-800 transition">Login</button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account?
            <a href="signup.php" class="text-black font-medium hover:underline">Sign up</a>
        </p>
    </div>
</section>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>