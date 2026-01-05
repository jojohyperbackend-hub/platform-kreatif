<?php
require_once __DIR__ . '/config/config.php';

// Fungsi untuk cek login & role
function checkAuth($role) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header("Location: index.php");
        exit();
    }

    if ($_SESSION['role'] !== $role) {
        // redirect sesuai role sebenarnya
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin/admin.php");
                break;
            case 'guru':
                header("Location: guru/guru.php");
                break;
            case 'murid':
                header("Location: murid/murid.php");
                break;
            default:
                header("Location: index.php");
        }
        exit();
    }
}
?>
