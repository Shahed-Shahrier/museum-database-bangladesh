<?php
include "config.php";
include "auth.php";
include "header.php";

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<div class="container">
    <h2 style="margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">Search Results for "<?php echo h($q); ?>"</h2>

    <?php if($q === ''): ?>
        <div class="alert">Please enter a search term.</div>
    <?php else: ?>
        
        <?php
        $term = "%" . $q . "%";
        
        // 1. Search Museums
        $stmt = $conn->prepare("SELECT * FROM Museum WHERE Name LIKE ? OR City LIKE ? OR Type LIKE ?");
        $stmt->bind_param('sss', $term, $term, $term);
        $stmt->execute();
        $res_museum = $stmt->get_result();
        
        // 2. Search Events
        $stmt = $conn->prepare("SELECT * FROM Events WHERE Name LIKE ? OR Description LIKE ?");
        $stmt->bind_param('ss', $term, $term);
        $stmt->execute();
        $res_event = $stmt->get_result();
        
        // 3. Search Artists
        $stmt = $conn->prepare("SELECT * FROM Artist WHERE Name LIKE ? OR Description LIKE ?");
        $stmt->bind_param('ss', $term, $term);
        $stmt->execute();
        $res_artist = $stmt->get_result();
        
        // 4. Search Art Pieces
        $stmt = $conn->prepare("SELECT * FROM Art_Piece WHERE Title LIKE ? OR Medium LIKE ?");
        $stmt->bind_param('ss', $term, $term);
        $stmt->execute();
        $res_art = $stmt->get_result();
        
        $found_any = false;
        ?>

        <!-- Museums -->
        <?php if($res_museum && $res_museum->num_rows > 0): $found_any = true; ?>
            <h3 style="margin-top: 30px; color: #1a237e;">🏛️ Museums</h3>
            <div class="grid">
                <?php while($row = $res_museum->fetch_assoc()): ?>
                    <div class="card">
                        <h3><a href="museum_details.php?id=<?php echo $row['Museum_ID']; ?>" style="text-decoration:none; color:inherit;"><?php echo h($row['Name']); ?></a></h3>
                        <p><strong>City:</strong> <?php echo h($row['City']); ?></p>
                        <p><strong>Type:</strong> <?php echo h($row['Type']); ?></p>
                        <a href="museum_details.php?id=<?php echo $row['Museum_ID']; ?>" style="color:#2563eb; text-decoration:none;">View Details &rarr;</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <!-- Events -->
        <?php if($res_event && $res_event->num_rows > 0): $found_any = true; ?>
            <h3 style="margin-top: 30px; color: #1a237e;">📅 Events</h3>
            <div class="grid">
                <?php while($row = $res_event->fetch_assoc()): ?>
                    <div class="card">
                        <h3><?php echo h($row['Name']); ?></h3>
                        <p><strong>Date:</strong> <?php echo h($row['Date']); ?></p>
                        <p><?php echo h($row['Description']); ?></p>
                        <p><strong>Type:</strong> <?php echo h($row['Type']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <!-- Artists -->
        <?php if($res_artist && $res_artist->num_rows > 0): $found_any = true; ?>
            <h3 style="margin-top: 30px; color: #1a237e;">🎨 Artists</h3>
            <div class="grid">
                <?php while($row = $res_artist->fetch_assoc()): ?>
                    <div class="card">
                        <h3><a href="artist_details.php?id=<?php echo $row['Artist_ID']; ?>" style="text-decoration:none; color:inherit;"><?php echo h($row['Name']); ?></a></h3>
                        <p><?php echo h($row['Description']); ?></p>
                        <a href="artist_details.php?id=<?php echo $row['Artist_ID']; ?>" style="color:#2563eb; text-decoration:none;">View Artworks &rarr;</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <!-- Art Pieces -->
        <?php if($res_art && $res_art->num_rows > 0): $found_any = true; ?>
            <h3 style="margin-top: 30px; color: #1a237e;">🖼️ Art Pieces</h3>
            <div class="grid">
                <?php while($row = $res_art->fetch_assoc()): ?>
                    <div class="card">
                        <h3><?php echo h($row['Title']); ?></h3>
                        <p><strong>Medium:</strong> <?php echo h($row['Medium']); ?></p>
                        <p><strong>Created:</strong> <?php echo h($row['Creation_date']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <?php if(!$found_any): ?>
            <div class="alert alert-warning">No results found for "<?php echo h($q); ?>". Try a different keyword.</div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
