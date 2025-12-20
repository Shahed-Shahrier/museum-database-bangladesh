<?php include "config.php"; include "auth.php"; include "header.php"; ?>
<div class="card"><h2>Art Pieces</h2>
<?php if(is_admin() && isset($_POST['delete']) && isset($_POST['Art_ID'])){ $id=$_POST['Art_ID']; $st=$conn->prepare('DELETE FROM art_piece WHERE Art_ID=?'); $st->bind_param('i',$id); if(!$st->execute()) echo '<div class="alert">Delete failed: '.h($st->error).'</div>'; else echo '<div class="alert">Deleted ✔</div>'; } ?>
<?php if(is_admin() && isset($_POST['save'])){ $is_edit=($_POST['is_edit']=='1');
$Title=$_POST['Title'];
$Creation_date=$_POST['Creation_date'];
$Acquisition_date=$_POST['Acquisition_date'];
$Medium=$_POST['Medium'];
$Artist_ID=$_POST['Artist_ID'];
$Gallery_ID=$_POST['Gallery_ID'];
if($is_edit){ $st=$conn->prepare('UPDATE art_piece SET `Title`=?, `Creation_date`=?, `Acquisition_date`=?, `Medium`=?, `Artist_ID`=?, `Gallery_ID`=? WHERE Art_ID=?'); $st->bind_param('ssssiii',$Title,$Creation_date,$Acquisition_date,$Medium,$Artist_ID,$Gallery_ID,$_POST['Art_ID']); } else { $st=$conn->prepare('INSERT INTO art_piece (`Title`, `Creation_date`, `Acquisition_date`, `Medium`, `Artist_ID`, `Gallery_ID`) VALUES (?, ?, ?, ?, ?, ?)'); $st->bind_param('ssssii',$Title,$Creation_date,$Acquisition_date,$Medium,$Artist_ID,$Gallery_ID); } if(!$st->execute()) echo '<div class="alert">Save failed: '.h($st->error).'</div>'; else echo '<div class="alert">Saved ✔</div>'; } ?>
<?php 
$edit=null; 
if(is_admin() && isset($_GET['edit'])){ 
    $id=intval($_GET['edit']); 
    // Fetch Museum_ID as well to pre-select the museum dropdown
    $rs=$conn->prepare('SELECT ap.Art_ID, ap.Title, ap.Creation_date, ap.Acquisition_date, ap.Medium, ap.Artist_ID, ap.Gallery_ID, g.Museum_ID FROM art_piece ap LEFT JOIN gallery g ON ap.Gallery_ID = g.Gallery_ID WHERE ap.Art_ID=?'); 
    $rs->bind_param('i',$id); 
    $rs->execute(); 
    $row=$rs->get_result()->fetch_assoc(); 
    if($row){ $edit=$row; } 
} 

// Fetch Museums
$museums_res = $conn->query("SELECT Museum_ID, Name FROM museum ORDER BY Name");
$museums = [];
while($m = $museums_res->fetch_assoc()) { $museums[] = $m; }

// Fetch Artists (with Museum_ID)
$artists_res = $conn->query("SELECT Artist_ID, Name, Museum_ID FROM artist ORDER BY Name");
$all_artists = [];
while($a = $artists_res->fetch_assoc()) { $all_artists[] = $a; }

// Fetch Galleries (with Museum_ID)
$galleries_res = $conn->query("SELECT Gallery_ID, Name, Museum_ID FROM gallery ORDER BY Name");
$all_galleries = [];
while($g = $galleries_res->fetch_assoc()) { $all_galleries[] = $g; }
?>
<?php if(is_admin()): ?>
<form method="post" class="card"><h3 style="margin-top:.2rem;">Art Pieces Form</h3>
<input type="hidden" name="Art_ID" value="<?php echo $edit ? h($edit['Art_ID']) : ''; ?>">
<input type="hidden" name="is_edit" value="<?php echo $edit ? '1' : '0'; ?>">
<input name="Title" placeholder="Title" value="<?php echo $edit ? h($edit["Title"]) : ""; ?>" required>
<input name="Creation_date" placeholder="Creation Date (YYYY-MM-DD)" value="<?php echo $edit ? h($edit["Creation_date"]) : ""; ?>">
<input name="Acquisition_date" placeholder="Acquisition Date (YYYY-MM-DD)" value="<?php echo $edit ? h($edit["Acquisition_date"]) : ""; ?>">
<input name="Medium" placeholder="Medium" value="<?php echo $edit ? h($edit["Medium"]) : ""; ?>">

<label class="muted">Museum</label>
<?php if(isset($_SESSION['admin_museum_id'])): ?>
    <input type="hidden" id="museum_select" value="<?php echo $_SESSION['admin_museum_id']; ?>">
    <input type="text" value="<?php 
        foreach($museums as $m) if($m['Museum_ID'] == $_SESSION['admin_museum_id']) echo h($m['Name']); 
    ?>" disabled>
<?php else: ?>
<select id="museum_select" onchange="updateDropdowns()">
    <option value="">-- Select Museum --</option>
    <?php foreach($museums as $m): ?>
        <option value="<?php echo $m['Museum_ID']; ?>" <?php if($edit && isset($edit['Museum_ID']) && $edit['Museum_ID'] == $m['Museum_ID']) echo 'selected'; ?>>
            <?php echo h($m['Name']); ?>
        </option>
    <?php endforeach; ?>
</select>
<?php endif; ?>

<label class="muted">Artist</label>
<select name="Artist_ID" id="artist_select" required>
    <option value="">-- Select Artist --</option>
</select>

<label class="muted">Gallery</label>
<select name="Gallery_ID" id="gallery_select" required>
    <option value="">-- Select Gallery --</option>
</select>

<div style="display:flex; gap:8px; margin-top:10px;"><button type="submit" name="save">Save</button><a class="btn-secondary" style="padding:10px 14px; border-radius:10px;" href="art_pieces.php">Cancel</a></div>
</form>

<script>
const allArtists = <?php echo json_encode($all_artists); ?>;
const allGalleries = <?php echo json_encode($all_galleries); ?>;
const selectedArtist = "<?php echo $edit ? $edit['Artist_ID'] : ''; ?>";
const selectedGallery = "<?php echo $edit ? $edit['Gallery_ID'] : ''; ?>";

function updateDropdowns() {
    const museumId = document.getElementById('museum_select').value;
    const artistSelect = document.getElementById('artist_select');
    const gallerySelect = document.getElementById('gallery_select');

    // Save current selection if not empty and valid for new museum (handled by logic below)
    // Actually, simpler to just clear and rebuild. If editing, we have selectedArtist/Gallery vars.

    artistSelect.innerHTML = '<option value="">-- Select Artist --</option>';
    gallerySelect.innerHTML = '<option value="">-- Select Gallery --</option>';

    if (!museumId) return;

    // Filter and populate Artists
    allArtists.forEach(artist => {
        if (artist.Museum_ID == museumId || artist.Museum_ID == null) {
            const opt = document.createElement('option');
            opt.value = artist.Artist_ID;
            opt.textContent = artist.Name;
            if (artist.Artist_ID == selectedArtist) opt.selected = true;
            artistSelect.appendChild(opt);
        }
    });

    // Filter and populate Galleries
    allGalleries.forEach(gallery => {
        if (gallery.Museum_ID == museumId) {
            const opt = document.createElement('option');
            opt.value = gallery.Gallery_ID;
            opt.textContent = gallery.Name;
            if (gallery.Gallery_ID == selectedGallery) opt.selected = true;
            gallerySelect.appendChild(opt);
        }
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', updateDropdowns);
</script>
<?php endif; ?>
<div class="card"><h2>All Art Pieces</h2>
<?php 
$sql = "SELECT ap.Art_ID, ap.Title, ap.Creation_date, ap.Acquisition_date, ap.Medium, a.Name as ArtistName, g.Name as GalleryName, m.Name as MuseumName FROM art_piece ap LEFT JOIN artist a ON ap.Artist_ID = a.Artist_ID LEFT JOIN gallery g ON ap.Gallery_ID = g.Gallery_ID LEFT JOIN museum m ON g.Museum_ID = m.Museum_ID";
if(isset($_SESSION['admin_museum_id'])){
    $sql .= " WHERE g.Museum_ID = " . intval($_SESSION['admin_museum_id']);
}
$sql .= " ORDER BY ap.Art_ID DESC";
$res=$conn->query($sql); 
if($res && $res->num_rows){  
    echo "<table><tr><th>ID</th><th>Title</th><th>Created</th><th>Acquired</th><th>Medium</th><th>Artist</th><th>Gallery</th><th>Museum</th><th>Actions</th></tr>"; 
    while($row=$res->fetch_assoc()){ 
        echo "<tr>"; 
        echo "<td>".h($row['Art_ID'])."</td>";
        echo "<td>".h($row['Title'])."</td>";
        echo "<td>".h($row['Creation_date'])."</td>";
        echo "<td>".h($row['Acquisition_date'])."</td>";
        echo "<td>".h($row['Medium'])."</td>";
        echo "<td>".h($row['ArtistName'])."</td>";
        echo "<td>".h($row['GalleryName'])."</td>";
        echo "<td>".h($row['MuseumName'])."</td>";
        echo "<td>"; 
        if(is_admin()){ 
            echo "<a href=\"art_pieces.php?edit=".$row["Art_ID"]."\">Edit</a> "; 
            echo "<form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete()\"><input type=\"hidden\" name=\"Art_ID\" value=\"" . h($row["Art_ID"]) . "\"/><button class=\"btn-danger\" name=\"delete\">Delete</button></form>"; 
        } 
        echo "</td></tr>"; 
    } 
    echo "</table>"; 
} else { 
    echo "<div class='alert'>No records.</div>"; 
} 
?>
</div><?php include "footer.php"; ?>