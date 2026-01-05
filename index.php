<?php
require_once __DIR__ . '/config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    switch ($role) {
        case 'admin':
            $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
            break;
        case 'guru':
            $sql = "SELECT * FROM guru WHERE username='$username' AND password='$password'";
            break;
        case 'murid':
            $sql = "SELECT * FROM murid WHERE username='$username'";
            break;
        default:
            $sql = '';
    }

    if ($sql) {
        $result = $conn->query($sql);
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $role;

            // redirect ke dashboard
            switch ($role) {
                case 'admin':
                    header("Location: admin/admin.php");
                    break;
                case 'guru':
                    header("Location: guru/guru.php");
                    break;
                case 'murid':
                    header("Location: murid/murid.php");
                    break;
            }
            exit();
        } else {
            $error = "Login gagal. Cek username/password.";
        }
    } else {
        $error = "Role tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Kreatif 2026</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Framer Motion CDN -->
    <script src="https://cdn.jsdelivr.net/npm/framer-motion@10.12.16/dist/framer-motion.umd.js"></script>
    <style>
        body {
            background: #f0f4f8;
        }
        .card {
            border-radius: 12px;
        }
        .btn-primary {
            background-color: #005a9c;
            border: none;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm w-50">
        <h2 class="mb-3 text-center text-primary">Login Platform Kreatif</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password (Admin & Guru)</label>
                <input type="password" name="password" class="form-control">
                <small class="text-muted">Murid tidak perlu password</small>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="murid">Murid</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
