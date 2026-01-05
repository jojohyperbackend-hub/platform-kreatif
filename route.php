<?php
// routing sederhana berdasarkan ?page=...
$page = $_GET['page'] ?? '';

switch ($page) {
    // admin routes
    case 'approve_challenge':
        include 'admin/admin.php';
        break;
    case 'stats':
        include 'admin/admin.php';
        break;

    // guru routes
    case 'create_challenge':
        include 'guru/guru.php';
        break;
    case 'view_submissions':
        include 'guru/guru.php';
        break;

    // murid routes
    case 'submit_challenge':
        include 'murid/murid.php';
        break;
    case 'view_challenges':
        include 'murid/murid.php';
        break;

    default:
        // default ke dashboard sesuai role
        if (isset($_SESSION['role'])) {
            switch ($_SESSION['role']) {
                case 'admin':
                    include 'admin/admin.php';
                    break;
                case 'guru':
                    include 'guru/guru.php';
                    break;
                case 'murid':
                    include 'murid/murid.php';
                    break;
            }
        } else {
            header("Location: index.php");
        }
        break;
}
?>
