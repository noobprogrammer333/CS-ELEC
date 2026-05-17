<?php

require_once __DIR__ . '/php/Auth.php';

redirectIfAuthenticated();

$error = '';
$notice = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($email === '' || $password === '' || $confirmPassword === '') {
        $error = 'Please complete all registration fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $authClient = new SupabaseAuthClient();
        $result = $authClient->signUp($email, $password);

        if ($result['ok'] && is_array($result['data'])) {
            if (!empty($result['data']['access_token'])) {
                storeAuthSession($result['data']);
                header('Location: index.php');
                exit;
            }

            $notice = 'Account created. Check your email if Supabase requires confirmation, then log in.';
        } else {
            $error = $result['error'] ?: 'Unable to create account.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account | NoteAI Lessons</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
    <link rel="stylesheet" href="static/css/style.css">
  </head>
  <body class="auth-body">
    <main class="auth-shell">
      <section class="auth-card glass-panel neon-glow">
        <div class="brand-icon auth-icon">
          <i data-lucide="notebook"></i>
        </div>
        <p class="text-uppercase text-primary fw-semibold small mb-2">Start your vault</p>
        <h1 class="auth-title neon-text">Create account</h1>
        <p class="auth-copy">Save dictated lesson notes and ask your AI assistant later.</p>

        <?php if ($error !== ''): ?>
          <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($notice !== ''): ?>
          <div class="alert alert-warning"><?= htmlspecialchars($notice) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
          <label for="email">Email address</label>
          <input
            id="email"
            name="email"
            type="email"
            value="<?= htmlspecialchars($email) ?>"
            placeholder="student@example.com"
            required
          >

          <label for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            placeholder="At least 6 characters"
            required
          >

          <label for="confirm_password">Confirm password</label>
          <input
            id="confirm_password"
            name="confirm_password"
            type="password"
            placeholder="Repeat your password"
            required
          >

          <button type="submit" class="auth-submit">Create Account</button>
        </form>

        <p class="auth-switch">
          Already registered?
          <a href="login.php">Log in</a>
        </p>
      </section>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>window.lucide && window.lucide.createIcons();</script>
  </body>
</html>
