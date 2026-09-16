<?php

/** @return array<string,mixed>|null */
function hug_attempt_login(PDO $pdo, string $username, string $password): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u AND is_active = 1 LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }

    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role']      = $user['role'];

    $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE user_id = :id')
        ->execute([':id' => $user['user_id']]);

    return $user;
}

function hug_redirect_after_login(string $role): void
{
    if ($role === 'member') {
        header('Location: /public/account.php');
        exit;
    }
    header('Location: /admin/dashboard.php');
    exit;
}

function hug_require_member(): void
{
    session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: /public/login.php');
        exit;
    }
    if (($_SESSION['role'] ?? '') !== 'member') {
        header('Location: /admin/dashboard.php');
        exit;
    }
}
