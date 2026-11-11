<?php
require_once "includes/sessions.php";
require_once("includes/db_connection.php");


$errors = [];
$username = "";
$email = "";
$success = false;

if (isLoggedIn()) {
    header('Location:index.php');
    exit();
}

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $username  = trim($_POST['username'] ?? "");
        $email     = trim($_POST['email'] ?? "");
        $password  = trim($_POST['password'] ?? "");
        $cPassword = trim($_POST['cPassword'] ?? "");
        $term      = $_POST['term'] ?? "";

        $isadmin = 0;

        // Validation
        if (empty($username)) {
            $errors['username'] = "Username is required";
        }

        if (empty($email)) {
            $errors['email'] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email";
        }

        if (strlen($password) < 6) {
            $errors['password'] = "Password must be at least 6 characters";
        }

        if ($password !== $cPassword) {
            $errors['cPassword'] = "Passwords do not match";
        }

        if (empty($term)) {
            $errors['term'] = "Accept terms and conditions";
        }

        if (empty($errors)) {

            // Check if email already exists
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM users
                WHERE email = ?
            ");

            if (!$stmt->execute([$email])) {
                throw new Exception("Failed to check email");
            }

            $emailExists = $stmt->fetchColumn();

            if ($emailExists > 0) {

                $errors['email'] = "User already exists";
            } else {

                // Count total users
                $stmt = $pdo->query("
                    SELECT COUNT(*) 
                    FROM users
                ");

                if (!$stmt) {
                    throw new Exception("Failed to count users");
                }

                $totalUsers = $stmt->fetchColumn();

                // First registered usersbecomes admin
                if ($totalUsers == 0) {
                    $isadmin = 1;
                }

                // Hash password
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                if (!$hashedPassword) {
                    throw new Exception("Password hashing failed");
                }

                // Insert user
                $stmt = $pdo->prepare("
                    INSERT INTO users
                    (username, email, password, isAdmin)
                    VALUES (?, ?, ?, ?)
                ");

                $inserted = $stmt->execute([
                    $username,
                    $email,
                    $hashedPassword,
                    $isadmin
                ]);

                if (!$inserted) {
                    throw new Exception("User registration failed");
                }

                $userId = $pdo->lastInsertId();
                setSession($userId, $username, $email, $isadmin);
                if ($isadmin) {
                    header("Location:admin");
                    exit();
                }
                message("user registered successfully in successfully");
                header("Location:index.php");
                exit();
            }
        }
    }
} catch (PDOException $e) {

    $errors['database'] = "Database Error: " . $e->getMessage();
} catch (Exception $e) {

    $errors['general'] = $e->getMessage();
}
?>

<?php if ($success): ?>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <script>
        Toastify({
            text: "User created successfully",
            duration: 2000,
            gravity: "top",
            position: "right",
            backgroundColor: "#22c55e"
        }).showToast();
    </script>
<?php endif; ?>
<?php require_once "includes/header.php"; ?>

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
                    value="<?= htmlspecialchars($username) ?>"
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

                <?php if (isset($errors['username'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= $errors['username'] ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label class="text-sm font-medium">
                    Email
                </label>

                <input
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars($email) ?>"
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

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
                    class="w-full mt-2 p-4 rounded-xl border focus:ring-2 focus:ring-black outline-none" />

                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= $errors['password'] ?>
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
                        <?= $errors['cPassword'] ?>
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
                        <?= $errors['term'] ?>
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

<?php include "includes/footer.php"; ?>
