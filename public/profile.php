<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/user.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = (int) Session::currentUser();
$userModel = new User();
$user = $userModel->getUser($userId);
if (!$user) {
    Session::logOut();
    header('Location: login.php');
    exit;
}

$_SESSION['user']['profile_image'] = $user['profile_image'] ?? null;
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        $error = 'Your session expired. Refresh the page and try again.';
    } else {
        try {
            $filename = $userModel->updateProfileImage($userId, $_FILES['profile_image'] ?? []);
            $_SESSION['user']['profile_image'] = $filename;
            $user = $userModel->getUser($userId);
            $success = 'Your profile image has been updated.';
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

$sessionProfileImage = $_SESSION['user']['profile_image'] ?? '';
$profileFilename = basename((string) ($sessionProfileImage !== '' ? $sessionProfileImage : ($user['profile_image'] ?? '')));
$profilePath = __DIR__ . '/uploads/profiles/' . $profileFilename;
$profileImage = $profileFilename !== '' && is_file($profilePath)
    ? 'uploads/profiles/' . rawurlencode($profileFilename) . '?v=' . filemtime($profilePath)
    : 'assets/img/default-avatar.svg';

$labels = [
    'id' => 'Account ID',
    'username' => 'Username',
    'email' => 'Email address',
    'isAdmin' => 'Administrator',
    'is_admin' => 'Administrator',
    'isActive' => 'Account active',
    'is_active' => 'Account active',
    'created_at' => 'Joined',
    'updated_at' => 'Last updated',
];

require_once __DIR__ . '/../layouts/header.php';
?>

<main class="min-h-screen bg-gray-50 py-12 sm:py-16">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        <div class="mb-8 flex flex-col items-start gap-5 sm:flex-row sm:items-center">
            <img id="profileHeaderPreview" src="<?= htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Profile image for <?= htmlspecialchars($user['username'] ?? 'user', ENT_QUOTES, 'UTF-8') ?>"
                 onerror="this.onerror=null;this.src='assets/img/default-avatar.svg'"
                 class="h-24 w-24 rounded-full border-4 border-white bg-white object-cover shadow-md sm:h-28 sm:w-28">
            <div>
                <p class="text-xs font-bold uppercase tracking-[3px] text-gray-500 mb-3">Your account</p>
                <h1 class="text-4xl font-bold text-gray-950">Profile</h1>
                <p class="mt-2 text-gray-600">View your account details and update your profile image.</p>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php elseif ($success !== ''): ?>
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800" role="status">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm sm:p-9">
                <h2 class="text-xl font-bold text-gray-950">Profile image</h2>
                <div class="mt-7 flex flex-col items-center text-center">
                    <img id="profilePreview" src="<?= htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8') ?>"
                         alt="Current profile image"
                         onerror="this.onerror=null;this.src='assets/img/default-avatar.svg'"
                         class="h-40 w-40 rounded-2xl border border-gray-200 bg-gray-100 object-cover shadow-sm">
                    <p class="mt-4 text-sm text-gray-500">JPG, PNG, GIF, or WebP · up to 5 MB</p>
                </div>

                <form method="POST" enctype="multipart/form-data" class="mt-7 space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="profile_image" class="block text-sm font-semibold text-gray-700">Choose a new image</label>
                    <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" required
                           class="block w-full rounded-xl border border-gray-200 p-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:font-semibold">
                    <button type="submit" class="w-full rounded-xl bg-black px-5 py-3 font-semibold text-white transition hover:bg-gray-800">Update profile image</button>
                </form>
            </section>

            <section class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm sm:p-9">
                <h2 class="text-xl font-bold text-gray-950">Account details</h2>
                <dl class="mt-6 divide-y divide-gray-100">
                    <?php foreach ($user as $field => $value): ?>
                        <?php
                        if ($field === 'profile_image' || preg_match('/password|pass_hash|secret|token|salt/i', $field)) {
                            continue;
                        }
                        $label = $labels[$field] ?? ucwords(str_replace(['_', '-'], ' ', (string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $field)));
                        if (in_array($field, ['isAdmin', 'is_admin', 'isActive', 'is_active'], true)) {
                            $value = $value ? 'Yes' : 'No';
                        }
                        ?>
                        <div class="grid gap-1 py-4 sm:grid-cols-[9rem_1fr] sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></dt>
                            <dd class="break-words text-sm font-semibold text-gray-900"><?= htmlspecialchars((string) ($value ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </section>
        </div>
    </div>
</main>

<script>
    document.getElementById('profile_image')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (file && file.type.startsWith('image/')) {
            const previewUrl = URL.createObjectURL(file);
            document.getElementById('profilePreview').src = previewUrl;
            document.getElementById('profileHeaderPreview').src = previewUrl;
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
