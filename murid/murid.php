<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../middleware.php';
checkAuth('murid');

$murid_id = $_SESSION['user_id'];

/* ===============================
   SUBMIT CHALLENGE (1x SAJA)
================================ */
if (isset($_POST['submit'])) {
    $challenge_id = intval($_POST['challenge_id']);
    $text = trim($_POST['text_submission']);

    // Cek apakah sudah pernah submit
    $cek = $conn->query("SELECT id FROM submission 
        WHERE challenge_id=$challenge_id AND murid_id=$murid_id");
    if ($cek->num_rows == 0) {

        $file_path = null;
        if (!empty($_FILES['file']['name'])) {
            $filename = time() . '_' . basename($_FILES['file']['name']);
            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                __DIR__ . '/../uploads/' . $filename
            );
            $file_path = $filename;
        }

        $conn->query("INSERT INTO submission 
            (challenge_id, murid_id, text_submission, file_path)
            VALUES (
                $challenge_id,
                $murid_id,
                '$text',
                " . ($file_path ? "'$file_path'" : "NULL") . "
            )");
    }
}

/* ===============================
   DATA
================================ */
$challenges = $conn->query("
    SELECT c.id, c.title, c.description, c.subject, g.username AS guru
    FROM challenge c
    JOIN guru g ON c.guru_id = g.id
    WHERE c.approved = 1
    ORDER BY c.created_at DESC
");

$submissions = $conn->query("
    SELECT s.*, c.title
    FROM submission s
    JOIN challenge c ON s.challenge_id = c.id
    WHERE s.murid_id = $murid_id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Murid</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Framer Motion -->
<script src="https://cdn.jsdelivr.net/npm/framer-motion@10.12.16/dist/framer-motion.umd.js"></script>

<style>
body {
    background:#f4f6f9;
}
.gov-header {
    background:#0d47a1;
    color:white;
    padding:16px;
    border-radius:8px;
}
.card {
    border-radius:14px;
}
.badge-subject {
    background:#e3f2fd;
    color:#0d47a1;
}
</style>
</head>

<body class="container py-4">

<!-- HEADER -->
<div class="gov-header mb-4">
    <h4 class="mb-0">Dashboard Murid</h4>
    <small>Platform Kreatif Mata Pelajaran</small>
    <a href="../logout.php" class="btn btn-sm btn-light float-end">Logout</a>
</div>

<!-- ===============================
     CHALLENGE AKTIF
================================ -->
<h5 class="mb-3">📌 Challenge Aktif</h5>

<?php while ($c = $challenges->fetch_assoc()): ?>
<?php
$cek_submit = $conn->query("
    SELECT id FROM submission
    WHERE challenge_id={$c['id']} AND murid_id=$murid_id
")->num_rows;
?>

<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($c['title']) ?></h5>
        <span class="badge badge-subject mb-2"><?= $c['subject'] ?></span>
        <p class="mt-2"><?= nl2br(htmlspecialchars($c['description'])) ?></p>
        <small class="text-muted">Guru: <?= $c['guru'] ?></small>

        <?php if ($cek_submit == 0): ?>
        <hr>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="challenge_id" value="<?= $c['id'] ?>">

            <label class="form-label mt-2">Jawaban Teks</label>
            <textarea name="text_submission" class="form-control" rows="3" required></textarea>

            <label class="form-label mt-2">File (PDF / Gambar) – opsional</label>
            <input type="file" name="file" class="form-control">

            <button type="submit" name="submit" class="btn btn-primary mt-3">
                Kirim Submission
            </button>
        </form>
        <?php else: ?>
        <div class="alert alert-success mt-3 mb-0">
            Submission sudah dikirim
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endwhile; ?>

<!-- ===============================
     HASIL & FEEDBACK
================================ -->
<h5 class="mt-5 mb-3">🏆 Nilai & Feedback</h5>

<table class="table table-bordered bg-white shadow-sm">
<thead class="table-light">
<tr>
    <th>Challenge</th>
    <th>Jawaban</th>
    <th>File</th>
    <th>Nilai</th>
    <th>Feedback Guru</th>
</tr>
</thead>
<tbody>
<?php while ($s = $submissions->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($s['title']) ?></td>
    <td><?= nl2br(htmlspecialchars($s['text_submission'])) ?></td>
    <td>
        <?php if ($s['file_path']): ?>
            <a href="../uploads/<?= $s['file_path'] ?>" target="_blank">Lihat</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
    <td><?= $s['score'] ?? '-' ?></td>
    <td><?= htmlspecialchars($s['feedback'] ?? '-') ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</body>
</html>
