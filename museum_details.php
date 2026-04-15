<?php
include "config.php";
include "auth.php";
include "header.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<div class='alert'>Invalid Museum ID.</div>";
    include "footer.php";
    exit;
}

// Fetch Museum Details
$stmt = $conn->prepare("SELECT * FROM museum WHERE Museum_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$museum = $stmt->get_result()->fetch_assoc();

if (!$museum) {
    echo "<div class='alert'>Museum not found.</div>";
    include "footer.php";
    exit;
}

// Fetch Galleries
$stmt = $conn->prepare("SELECT * FROM gallery WHERE Museum_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$galleries = $stmt->get_result();

// Fetch Artists
$stmt = $conn->prepare("SELECT * FROM artist WHERE Museum_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$artists = $stmt->get_result();

// Fetch Art Pieces (via Gallery)
$stmt = $conn->prepare("
    SELECT a.*, g.Name as GalleryName 
    FROM art_piece a 
    JOIN gallery g ON a.Gallery_ID = g.Gallery_ID 
    WHERE g.Museum_ID = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$art_pieces = $stmt->get_result();

// Fetch Events
$stmt = $conn->prepare("
    SELECT e.* 
    FROM events e 
    JOIN museum_event me ON e.Event_ID = me.Event_ID 
    WHERE me.Museum_ID = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$events = $stmt->get_result();

?>

<div class="container">
    <div class="card" style="margin-bottom: 20px;">
        <h1><?php echo h($museum['Name']); ?></h1>
        <p><strong>City:</strong> <?php echo h($museum['City']); ?></p>
        <p><strong>Type:</strong> <?php echo h($museum['Type']); ?></p>
        <p><strong>Contact:</strong> <?php echo h($museum['Contact_no']); ?></p>
        <p><strong>Email:</strong> <?php echo h($museum['Email']); ?></p>
        <a href="museums.php" class="btn-secondary" style="display:inline-block; margin-top:10px; padding:8px 12px; border-radius:6px; text-decoration:none; background:#e2e8f0; color:#334155;">&larr; Back to Museums</a>
    </div>

    <h2>Galleries</h2>
    <div class="grid">
        <?php if ($galleries->num_rows > 0): ?>
            <?php while($row = $galleries->fetch_assoc()): ?>
                <a href="gallery_details.php?id=<?php echo $row['Gallery_ID']; ?>" style="text-decoration:none; color:inherit; display:block;">
                    <div class="card" style="height:100%;">
                        <h3><?php echo h($row['Name']); ?></h3>
                        <p>Floor: <?php echo h($row['Floor_no']); ?>, Room: <?php echo h($row['Room_no']); ?></p>
                        <span style="color:#2563eb; font-size:0.9em;">View Artworks &rarr;</span>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No galleries found.</p>
        <?php endif; ?>
    </div>

    <h2>Artists</h2>
    <div class="grid">
        <?php if ($artists->num_rows > 0): ?>
            <?php while($row = $artists->fetch_assoc()): ?>
                <a href="artist_details.php?id=<?php echo $row['Artist_ID']; ?>&museum_id=<?php echo $id; ?>" style="text-decoration:none; color:inherit; display:block;">
                    <div class="card" style="height:100%;">
                        <h3><?php echo h($row['Name']); ?></h3>
                        <p><?php echo h($row['Description']); ?></p>
                        <span style="color:#2563eb; font-size:0.9em;">View Artworks in this Museum &rarr;</span>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No artists found.</p>
        <?php endif; ?>
    </div>

    <h2>Art Pieces</h2>
    <div class="grid">
        <?php if ($art_pieces->num_rows > 0): ?>
            <?php while($row = $art_pieces->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo h($row['Title']); ?></h3>
                    <p><strong>Medium:</strong> <?php echo h($row['Medium']); ?></p>
                    <p><strong>Gallery:</strong> <?php echo h($row['GalleryName']); ?></p>
                    <p><strong>Created:</strong> <?php echo h($row['Creation_date']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No art pieces found.</p>
        <?php endif; ?>
    </div>

    <h2>Events</h2>
    <div class="grid">
        <?php if ($events->num_rows > 0): ?>
            <?php while($row = $events->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo h($row['Name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo h($row['Date']); ?></p>
                    <p><?php echo h($row['Description']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>
