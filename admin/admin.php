<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../middleware.php';
checkAuth('admin');

/* =========================
   ACTION HANDLER
========================= */
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Tambah guru
    if ($action === 'tambah_guru') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $conn->query("INSERT INTO guru (username, password) VALUES ('$username', '$password')");
    }

    // Hapus guru + challenge + submission
    if ($action === 'hapus_guru') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM guru WHERE id=$id");
    }

    // Hapus murid + submission
    if ($action === 'hapus_murid') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM murid WHERE id=$id");
    }

    // Hapus challenge + submission
    if ($action === 'hapus_challenge') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM challenge WHERE id=$id");
    }

    // Hapus submission
    if ($action === 'hapus_submission') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM submission WHERE id=$id");
    }
}

// Approve challenge
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE challenge SET approved=1 WHERE id=$id");
}

/* =========================
   DATA
========================= */
$guru = $conn->query("SELECT * FROM guru");
$murid = $conn->query("SELECT * FROM murid");

$challenge = $conn->query("
    SELECT c.*, g.username AS guru 
    FROM challenge c 
    JOIN guru g ON c.guru_id = g.id
");

$submission = $conn->query("
    SELECT s.*, m.username AS murid, c.title
    FROM submission s
    JOIN murid m ON s.murid_id = m.id
    JOIN challenge c ON s.challenge_id = c.id
");

$total_challenges = $conn->query("SELECT COUNT(*) total FROM challenge")->fetch_assoc()['total'];
$total_submissions = $conn->query("SELECT COUNT(*) total FROM submission")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">

<h3>Admin Dashboard</h3>
<a href="../logout.php" class="btn btn-danger mb-4">Logout</a>

<!-- =========================
     STAT
========================= -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-3">Total Challenge: <b><?= $total_challenges ?></b></div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">Total Submission: <b><?= $total_submissions ?></b></div>
    </div>
</div>

<!-- =========================
     TAMBAH GURU
========================= -->
<h5>Tambah Guru</h5>
<form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="action" value="tambah_guru">
    <div class="col"><input name="username" class="form-control" placeholder="Username" required></div>
    <div class="col"><input name="password" class="form-control" placeholder="Password" required></div>
    <div class="col"><button class="btn btn-primary">Tambah</button></div>
</form>

<!-- =========================
     DATA GURU
========================= -->
<h5>Data Guru</h5>
<table class="table table-bordered">
<tr><th>ID</th><th>Username</th><th>Aksi</th></tr>
<?php while ($g = $guru->fetch_assoc()): ?>
<tr>
<td><?= $g['id'] ?></td>
<td><?= $g['username'] ?></td>
<td>
<form method="POST">
<input type="hidden" name="action" value="hapus_guru">
<input type="hidden" name="id" value="<?= $g['id'] ?>">
<button class="btn btn-sm btn-danger">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

<!-- =========================
     DATA MURID
========================= -->
<h5>Data Murid</h5>
<table class="table table-bordered">
<tr><th>ID</th><th>Username</th><th>Aksi</th></tr>
<?php while ($m = $murid->fetch_assoc()): ?>
<tr>
<td><?= $m['id'] ?></td>
<td><?= $m['username'] ?></td>
<td>
<form method="POST">
<input type="hidden" name="action" value="hapus_murid">
<input type="hidden" name="id" value="<?= $m['id'] ?>">
<button class="btn btn-sm btn-danger">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

<!-- =========================
     CHALLENGE
========================= -->
<h5>Semua Challenge</h5>
<table class="table table-bordered">
<tr>
<th>ID</th><th>Judul</th><th>Guru</th><th>Status</th><th>Aksi</th>
</tr>
<?php while ($c = $challenge->fetch_assoc()): ?>
<tr>
<td><?= $c['id'] ?></td>
<td><?= $c['title'] ?></td>
<td><?= $c['guru'] ?></td>
<td><?= $c['approved'] ? 'Approved' : 'Pending' ?></td>
<td>
<?php if (!$c['approved']): ?>
<a href="?approve=<?= $c['id'] ?>" class="btn btn-sm btn-success">Approve</a>
<?php endif; ?>
<form method="POST" class="d-inline">
<input type="hidden" name="action" value="hapus_challenge">
<input type="hidden" name="id" value="<?= $c['id'] ?>">
<button class="btn btn-sm btn-danger">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

<!-- =========================
     SUBMISSION
========================= -->
<h5>Semua Submission</h5>
<table class="table table-bordered">
<tr>
<th>ID</th><th>Murid</th><th>Challenge</th><th>Nilai</th><th>Aksi</th>
</tr>
<?php while ($s = $submission->fetch_assoc()): ?>
<tr>
<td><?= $s['id'] ?></td>
<td><?= $s['murid'] ?></td>
<td><?= $s['title'] ?></td>
<td><?= $s['score'] ?? '-' ?></td>
<td>
<form method="POST">
<input type="hidden" name="action" value="hapus_submission">
<input type="hidden" name="id" value="<?= $s['id'] ?>">
<button class="btn btn-sm btn-danger">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
