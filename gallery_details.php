<?php
include "config.php";
include "auth.php";
include "header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<div class='alert'>Invalid Gallery ID.</div>";
    include "footer.php";
    exit;
}

// Fetch Gallery Details
$stmt = $conn->prepare("SELECT g.*, m.Name as MuseumName FROM Gallery g JOIN Museum m ON g.Museum_ID = m.Museum_ID WHERE Gallery_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$gallery = $stmt->get_result()->fetch_assoc();

if (!$gallery) {
    echo "<div class='alert'>Gallery not found.</div>";
    include "footer.php";
    exit;
}

// Fetch Art Pieces in this Gallery
$stmt = $conn->prepare("
    SELECT a.*, ar.Name as ArtistName
    FROM Art_Piece a 
    JOIN Artist ar ON a.Artist_ID = ar.Artist_ID
    WHERE a.Gallery_ID = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$art_pieces = $stmt->get_result();
?>

<div class="container">
    <div class="card" style="margin-bottom: 20px;">
        <h1>Gallery: <?php echo h($gallery['Name']); ?></h1>
        <p><strong>Museum:</strong> <?php echo h($gallery['MuseumName']); ?></p>
        <p><strong>Floor:</strong> <?php echo h($gallery['Floor_no']); ?></p>
        <p><strong>Room:</strong> <?php echo h($gallery['Room_no']); ?></p>
        <a href="javascript:history.back()" class="btn-secondary" style="display:inline-block; margin-top:10px; padding:8px 12px; border-radius:6px; text-decoration:none; background:#e2e8f0; color:#334155;">&larr; Back</a>
    </div>

    <h2>Art Pieces in this Gallery</h2>
    <div class="grid">
        <?php if ($art_pieces->num_rows > 0): ?>
            <?php while($row = $art_pieces->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo h($row['Title']); ?></h3>
                    <p><strong>Artist:</strong> <?php echo h($row['ArtistName']); ?></p>
                    <p><strong>Medium:</strong> <?php echo h($row['Medium']); ?></p>
                    <p><strong>Created:</strong> <?php echo h($row['Creation_date']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No art pieces found in this gallery.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>
