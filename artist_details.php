<?php
include "config.php";
include "auth.php";
include "header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<div class='alert'>Invalid Artist ID.</div>";
    include "footer.php";
    exit;
}

// Fetch Artist Details
$stmt = $conn->prepare("SELECT * FROM artist WHERE Artist_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();

if (!$artist) {
    echo "<div class='alert'>Artist not found.</div>";
    include "footer.php";
    exit;
}

// Fetch Art Pieces by this Artist
$museum_id = isset($_GET['museum_id']) ? intval($_GET['museum_id']) : 0;

if ($museum_id > 0) {
    $stmt = $conn->prepare("
        SELECT a.*, g.Name as GalleryName, m.Name as MuseumName
        FROM art_piece a 
        JOIN gallery g ON a.Gallery_ID = g.Gallery_ID 
        JOIN museum m ON g.Museum_ID = m.Museum_ID
        WHERE a.Artist_ID = ? AND m.Museum_ID = ?
    ");
    $stmt->bind_param("ii", $id, $museum_id);
} else {
    $stmt = $conn->prepare("
        SELECT a.*, g.Name as GalleryName, m.Name as MuseumName
        FROM art_piece a 
        JOIN gallery g ON a.Gallery_ID = g.Gallery_ID 
        JOIN museum m ON g.Museum_ID = m.Museum_ID
        WHERE a.Artist_ID = ?
    ");
    $stmt->bind_param("i", $id);
}
$stmt->execute();
$art_pieces = $stmt->get_result();
?>

<div class="container">
    <div class="card" style="margin-bottom: 20px;">
        <h1>Artist: <?php echo h($artist['Name']); ?></h1>
        <p><strong>Description:</strong> <?php echo h($artist['Description']); ?></p>
        <a href="javascript:history.back()" class="btn-secondary" style="display:inline-block; margin-top:10px; padding:8px 12px; border-radius:6px; text-decoration:none; background:#e2e8f0; color:#334155;">&larr; Back</a>
    </div>

    <h2>Art Pieces by <?php echo h($artist['Name']); ?><?php if($museum_id > 0) echo " (In Selected Museum)"; ?></h2>
    <div class="grid">
        <?php if ($art_pieces->num_rows > 0): ?>
            <?php while($row = $art_pieces->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo h($row['Title']); ?></h3>
                    <p><strong>Medium:</strong> <?php echo h($row['Medium']); ?></p>
                    <p><strong>Created:</strong> <?php echo h($row['Creation_date']); ?></p>
                    <p><strong>Location:</strong> <?php echo h($row['MuseumName']); ?> (<?php echo h($row['GalleryName']); ?>)</p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No art pieces found for this artist.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>
