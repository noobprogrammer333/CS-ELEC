<?php

require_once __DIR__ . '/php/Auth.php';

redirectIfAuthenticated();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $authClient = new SupabaseAuthClient();
        $result = $authClient->signInWithPassword($email, $password);

        if ($result['ok'] && is_array($result['data'])) {
            storeAuthSession($result['data']);
            header('Location: index.php');
            exit;
        }

        $error = $result['error'] ?: 'Unable to log in. Please check your credentials.';
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | NoteAI Lessons</title>
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
          <i data-lucide="brain-circuit"></i>
        </div>
        <p class="text-uppercase text-primary fw-semibold small mb-2">Welcome back</p>
        <h1 class="auth-title neon-text">Log in to NoteAI</h1>
        <p class="auth-copy">Access your speech notes and AI lesson assistant.</p>

        <?php if ($error !== ''): ?>
          <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
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
            placeholder="Your password"
            required
          >

          <button type="submit" class="auth-submit">Log In</button>
        </form>

        <p class="auth-switch">
          No account yet?
          <a href="register.php">Create one</a>
        </p>
      </section>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>window.lucide && window.lucide.createIcons();</script>
  </body>
</html>
