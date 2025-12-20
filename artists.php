<?php include "config.php"; include "auth.php"; include "header.php"; ?>
<div class="card"><h2>Artists</h2>
<?php if(is_admin() && isset($_POST['delete']) && isset($_POST['Artist_ID'])){ $id=$_POST['Artist_ID']; $st=$conn->prepare('DELETE FROM artist WHERE Artist_ID=?'); $st->bind_param('i',$id); if(!$st->execute()) echo '<div class="alert">Delete failed: '.h($st->error).'</div>'; else echo '<div class="alert">Deleted ✔</div>'; } ?>
<?php if(is_admin() && isset($_POST['save'])){ $is_edit=($_POST['is_edit']=='1');
$Name=$_POST['Name'];
$Description=$_POST['Description'];
$Museum_ID = !empty($_POST['Museum_ID']) ? $_POST['Museum_ID'] : null;

// Enforce Admin Context
if(isset($_SESSION['admin_museum_id'])){
    $Museum_ID = $_SESSION['admin_museum_id'];
}

if($is_edit){ $st=$conn->prepare('UPDATE artist SET `Name`=?, `Description`=?, `Museum_ID`=? WHERE Artist_ID=?'); $st->bind_param('ssii',$Name,$Description,$Museum_ID,$_POST['Artist_ID']); } else { $st=$conn->prepare('INSERT INTO artist (`Name`, `Description`, `Museum_ID`) VALUES (?, ?, ?)'); $st->bind_param('ssi',$Name,$Description,$Museum_ID); } if(!$st->execute()) echo '<div class="alert">Save failed: '.h($st->error).'</div>'; else echo '<div class="alert">Saved ✔</div>'; } ?>
<?php 
$edit=null; 
if(is_admin() && isset($_GET['edit'])){ 
    $id=intval($_GET['edit']); 
    $rs=$conn->prepare('SELECT Artist_ID, Name, Description, Museum_ID FROM artist WHERE Artist_ID=?'); 
    $rs->bind_param('i',$id); 
    $rs->execute(); 
    $row=$rs->get_result()->fetch_assoc(); 
    if($row){ $edit=$row; } 
} 

// Fetch museums for dropdown
$museums_res = $conn->query("SELECT Museum_ID, Name FROM museum ORDER BY Name");
$museums = [];
while($m = $museums_res->fetch_assoc()) { $museums[] = $m; }
?>
<?php if(is_admin()): ?>
<form method="post" class="card"><h3 style="margin-top:.2rem;">Artists Form</h3>
<input type="hidden" name="Artist_ID" value="<?php echo $edit ? h($edit['Artist_ID']) : ''; ?>">
<input type="hidden" name="is_edit" value="<?php echo $edit ? '1' : '0'; ?>">
<input name="Name" placeholder="Name" value="<?php echo $edit ? h($edit["Name"]) : ""; ?>" required>
<label class="muted">Description</label><br>
<textarea name="Description" placeholder="Description"><?php echo $edit ? h($edit["Description"]) : ""; ?></textarea>

<?php if(isset($_SESSION['admin_museum_id'])): ?>
    <input type="hidden" name="Museum_ID" value="<?php echo $_SESSION['admin_museum_id']; ?>">
    <div style="margin-bottom:10px; padding:8px; background:#f1f5f9; border-radius:4px; color:#475569;">
        Museum: <strong><?php 
            foreach($museums as $m) if($m['Museum_ID'] == $_SESSION['admin_museum_id']) echo h($m['Name']); 
        ?></strong> (Locked)
    </div>
<?php else: ?>
    <select name="Museum_ID">
        <option value="">-- No Museum --</option>
        <?php foreach($museums as $m): ?>
            <option value="<?php echo $m['Museum_ID']; ?>" <?php if($edit && $edit['Museum_ID'] == $m['Museum_ID']) echo 'selected'; ?>>
                <?php echo h($m['Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>

<div style="display:flex; gap:8px; margin-top:10px;"><button type="submit" name="save">Save</button><a class="btn-secondary" style="padding:10px 14px; border-radius:10px;" href="artists.php">Cancel</a></div>
</form>
<?php endif; ?>
<div class="container">
    <h2>All Artists</h2>
    <div class="grid">
        <?php 
        $sql = "SELECT a.Artist_ID, a.Name, a.Description, a.Museum_ID, m.Name as MuseumName FROM artist a LEFT JOIN museum m ON a.Museum_ID = m.Museum_ID";
        if(isset($_SESSION['admin_museum_id'])){
            $sql .= " WHERE a.Museum_ID = " . intval($_SESSION['admin_museum_id']);
        }
        $sql .= " ORDER BY a.Name ASC";
        
        $res=$conn->query($sql); 
        if($res && $res->num_rows){ 
            while($row=$res->fetch_assoc()){ 
                echo '<div class="card">';
                echo '<h3><a href="artist_details.php?id=' . $row['Artist_ID'] . '" style="text-decoration:none; color:inherit;">' . h($row['Name']) . '</a></h3>';
                echo '<p class="muted" style="font-size:0.9em;">Museum: ' . ($row['MuseumName'] ? h($row['MuseumName']) : 'None') . '</p>';
                echo '<p>' . h($row['Description']) . '</p>';
                echo '<a href="artist_details.php?id=' . $row['Artist_ID'] . '" style="display:inline-block; margin-top:10px; text-decoration:none; color:#2563eb;">View Artworks &rarr;</a>';
                
                if(is_admin()){ 
                    echo '<div class="admin-actions" style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">';
                    echo "<a href=\"artists.php?edit=".$row["Artist_ID"]."\" class=\"btn-secondary\" style=\"font-size:0.8em; padding:4px 8px;\">Edit</a> "; 
                    echo "<form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete()\"><input type=\"hidden\" name=\"Artist_ID\" value=\"" . h($row["Artist_ID"]) . "\"/><button class=\"btn-danger\" style=\"font-size:0.8em; padding:4px 8px;\" name=\"delete\">Delete</button></form>"; 
                    echo '</div>';
                } 
                echo '</div>';
            } 
        } else { 
            echo "<div class='alert'>No records.</div>"; 
        } 
        ?>
    </div>
</div><?php include "footer.php"; ?>