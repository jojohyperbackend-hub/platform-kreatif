<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../middleware.php';
checkAuth('guru');

$guru_id = $_SESSION['user_id'];

/* ===============================
   BUAT CHALLENGE
================================ */
if (isset($_POST['buat_challenge'])) {
    $title = trim($_POST['title']);
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);

    if ($title && $subject && $description) {
        $conn->query("
            INSERT INTO challenge (guru_id, title, subject, description, approved)
            VALUES ($guru_id, '$title', '$subject', '$description', 0)
        ");
    }
}

/* ===============================
   NILAI & FEEDBACK
================================ */
if (isset($_POST['nilai_submission'])) {
    $submission_id = intval($_POST['submission_id']);
    $score = intval($_POST['score']);
    $feedback = trim($_POST['feedback']);

    $conn->query("
        UPDATE submission
        SET score = $score, feedback = '$feedback'
        WHERE id = $submission_id
    ");
}

/* ===============================
   DATA
================================ */

// challenge milik guru
$challenges = $conn->query("
    SELECT * FROM challenge
    WHERE guru_id = $guru_id
    ORDER BY id DESC
");

// submission ke challenge guru
$submissions = $conn->query("
    SELECT s.*, m.username AS murid, c.title
    FROM submission s
    JOIN murid m ON s.murid_id = m.id
    JOIN challenge c ON s.challenge_id = c.id
    WHERE c.guru_id = $guru_id
    ORDER BY s.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Guru</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Framer Motion CDN -->
<script src="https://cdn.jsdelivr.net/npm/framer-motion@10.12.16/dist/framer-motion.umd.js"></script>

<style>
body {
    background:#f4f6f9;
}
.gov-header {
    background:#0b3c91;
    color:white;
    padding:18px 20px;
    border-radius:10px;
}
.card {
    border-radius:14px;
}
.badge-wait {
    background:#fff3cd;
    color:#856404;
}
</style>
</head>

<body class="container py-4">

<!-- HEADER -->
<div class="gov-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">Dashboard Guru</h4>
            <small>Platform Kreatif Mata Pelajaran</small>
        </div>
        <a href="../logout.php" class="btn btn-sm btn-light">Logout</a>
    </div>
</div>

<!-- ===============================
     BUAT CHALLENGE
================================ -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">➕ Buat Challenge Baru</h5>
        <form method="POST">
            <div class="mb-2">
                <label class="form-label">Judul Challenge</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Mata Pelajaran</label>
                <input type="text" name="subject" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi Challenge</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" name="buat_challenge" class="btn btn-primary">
                Ajukan Challenge
            </button>

            <small class="text-muted d-block mt-2">
                * Challenge akan tampil ke murid setelah disetujui admin
            </small>
        </form>
    </div>
</div>

<!-- ===============================
     CHALLENGE SAYA
================================ -->
<h5 class="mb-3">📚 Challenge Saya</h5>

<table class="table table-bordered bg-white shadow-sm">
<thead class="table-light">
<tr>
    <th>Judul</th>
    <th>Mata Pelajaran</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
<?php while ($c = $challenges->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($c['title']) ?></td>
    <td><?= htmlspecialchars($c['subject']) ?></td>
    <td>
        <?php if ($c['approved']): ?>
            <span class="badge bg-success">Disetujui</span>
        <?php else: ?>
            <span class="badge badge-wait">Menunggu Admin</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<!-- ===============================
     SUBMISSION MURID
================================ -->
<h5 class="mt-5 mb-3">📝 Submission Murid</h5>

<?php if ($submissions->num_rows == 0): ?>
<div class="alert alert-info">
    Belum ada submission dari murid.
</div>
<?php endif; ?>

<?php while ($s = $submissions->fetch_assoc()): ?>
<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <h6 class="mb-1"><?= htmlspecialchars($s['title']) ?></h6>
        <small class="text-muted">Murid: <?= htmlspecialchars($s['murid']) ?></small>

        <p class="mt-2">
            <?= nl2br(htmlspecialchars($s['text_submission'])) ?>
        </p>

        <?php if ($s['file_path']): ?>
            <a href="../uploads/<?= $s['file_path'] ?>" target="_blank">
                📎 Lihat File
            </a>
        <?php endif; ?>

        <hr>

        <form method="POST" class="row g-2">
            <input type="hidden" name="submission_id" value="<?= $s['id'] ?>">

            <div class="col-md-2">
                <input type="number"
                       name="score"
                       class="form-control"
                       min="0"
                       max="100"
                       placeholder="Nilai"
                       value="<?= $s['score'] ?>"
                       required>
            </div>

            <div class="col-md-7">
                <input type="text"
                       name="feedback"
                       class="form-control"
                       placeholder="Feedback untuk murid"
                       value="<?= htmlspecialchars($s['feedback']) ?>">
            </div>

            <div class="col-md-3">
                <button type="submit" name="nilai_submission"
                        class="btn btn-success w-100">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>
<?php endwhile; ?>

</body>
</html>
