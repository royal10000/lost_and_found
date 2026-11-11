<?php

require_once "includes/sessions.php";
require_once "includes/db_connection.php";

$errors = [];
$email = "";

if (isLoggedIn()) {
    header("Location:index.php");
    exit();
}

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $email = trim($_POST['email'] ?? "");
        $password = trim($_POST['password'] ?? "");
        $remember = $_POST['remember'] ?? "";

        // Validation
        if (empty($email)) {
            $errors['email'] = "Email is required";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required";
        }

        if (empty($errors)) {

            // Check user
            $stmt = $pdo->prepare("
                SELECT *
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $executed = $stmt->execute([$email]);

            if (!$executed) {
                throw new Exception("Login failed");
            }

            $user = $stmt->fetch();

            // User lookup failed
            if (!$user) {

                $errors['email'] = "No account uses this email";

            } elseif (!(int)$user['isActive']) {

                $errors['email'] = "This account is inactive. Please contact admin.";

            } else {

                // Verify password
               
                if (!password_verify($password, $user['password'])) {

                    $errors['password'] = "Incorrect password";

                } else {

                    // Set session
                    setSession(
                        $user['id'],
                        $user['username'],
                        $user['email'],
                        $user['isAdmin']
                    );
                    message("user logged in successfully");

                    // Admin redirect
                    if ($user['isAdmin']) {

                        header("Location:admin/index.php");
                        exit();
                    }

                    // User redirect
                    header("Location:index.php");
                    exit();
                }
            }
        }
    }

} catch (PDOException $e) {

    $errors['database'] = "Database Error: " . $e->getMessage();

} catch (Exception $e) {

    $errors['general'] = $e->getMessage();
}

include "includes/header.php";

?>

<section class="flex items-center justify-center min-h-[80vh] px-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">

        <h2 class="text-3xl font-bold text-center mb-2">
            Welcome Back
        </h2>

        <p class="text-center text-gray-500 mb-8">
            Login to your account
        </p>

        <!-- Database Error -->
        <?php if (isset($errors['database'])): ?>   
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
                <?= htmlspecialchars($errors['database']) ?>
            </div>
        <?php endif; ?>

        <!-- General Error -->
        <?php if (isset($errors['general'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form class="space-y-5" method="POST" action="">

            <!-- Email -->
            <div>

                <label class="text-sm font-medium">
                    Email
                </label>

                <input
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="Enter your email"
                    class="w-full mt-2 p-4 rounded-xl border outline-none focus:ring-2 focus:ring-black"
                />

                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= $errors['email'] ?>
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
                    placeholder="Enter your password"
                    class="w-full mt-2 p-4 rounded-xl border outline-none focus:ring-2 focus:ring-black"
                />

                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= $errors['password'] ?>
                    </p>
                <?php endif; ?>

            </div>

            <!-- Remember -->
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

            <!-- Submit -->
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

<?php include "includes/footer.php"; ?>
