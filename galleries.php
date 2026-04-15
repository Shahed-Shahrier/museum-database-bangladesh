<?php include "config.php"; include "auth.php"; include "header.php"; ?>
<div class="card"><h2>Galleries</h2>
<?php if(is_admin() && isset($_POST['delete']) && isset($_POST['Gallery_ID'])){ $id=$_POST['Gallery_ID']; $st=$conn->prepare('DELETE FROM gallery WHERE Gallery_ID=?'); $st->bind_param('i',$id); if(!$st->execute()) echo '<div class="alert">Delete failed: '.h($st->error).'</div>'; else echo '<div class="alert">Deleted ✔</div>'; } ?>
<?php if(is_admin() && isset($_POST['save'])){ $is_edit=($_POST['is_edit']=='1');
$Name=$_POST['Name'];
$Floor_no=$_POST['Floor_no'];
$Room_no=$_POST['Room_no'];
$Museum_ID=$_POST['Museum_ID'];

// Enforce Admin Context
if(isset($_SESSION['admin_museum_id'])){
    $Museum_ID = $_SESSION['admin_museum_id'];
}

if($is_edit){ $st=$conn->prepare('UPDATE gallery SET `Name`=?, `Floor_no`=?, `Room_no`=?, `Museum_ID`=? WHERE Gallery_ID=?'); $st->bind_param('sisii',$Name,$Floor_no,$Room_no,$Museum_ID,$_POST['Gallery_ID']); } else { $st=$conn->prepare('INSERT INTO gallery (`Name`, `Floor_no`, `Room_no`, `Museum_ID`) VALUES (?, ?, ?, ?)'); $st->bind_param('sisi',$Name,$Floor_no,$Room_no,$Museum_ID); } if(!$st->execute()) echo '<div class="alert">Save failed: '.h($st->error).'</div>'; else echo '<div class="alert">Saved ✔</div>'; } ?>
<?php 
$edit=null; 
if(is_admin() && isset($_GET['edit'])){ 
    $id=intval($_GET['edit']); 
    $rs=$conn->prepare('SELECT Gallery_ID, Name, Floor_no, Room_no, Museum_ID FROM gallery WHERE Gallery_ID=?'); 
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
<form method="post" class="card"><h3 style="margin-top:.2rem;">Galleries Form</h3>
<input type="hidden" name="Gallery_ID" value="<?php echo $edit ? h($edit['Gallery_ID']) : ''; ?>">
<input type="hidden" name="is_edit" value="<?php echo $edit ? '1' : '0'; ?>">
<input name="Name" placeholder="Name" value="<?php echo $edit ? h($edit["Name"]) : ""; ?>" required>
<input name="Floor_no" placeholder="Floor No" value="<?php echo $edit ? h($edit["Floor_no"]) : ""; ?>">
<input name="Room_no" placeholder="Room No" value="<?php echo $edit ? h($edit["Room_no"]) : ""; ?>">

<?php if(isset($_SESSION['admin_museum_id'])): ?>
    <input type="hidden" name="Museum_ID" value="<?php echo $_SESSION['admin_museum_id']; ?>">
    <div style="margin-bottom:10px; padding:8px; background:#f1f5f9; border-radius:4px; color:#475569;">
        Museum: <strong><?php 
            foreach($museums as $m) if($m['Museum_ID'] == $_SESSION['admin_museum_id']) echo h($m['Name']); 
        ?></strong> (Locked)
    </div>
<?php else: ?>
    <select name="Museum_ID" required>
        <option value="">-- Select Museum --</option>
        <?php foreach($museums as $m): ?>
            <option value="<?php echo $m['Museum_ID']; ?>" <?php if($edit && $edit['Museum_ID'] == $m['Museum_ID']) echo 'selected'; ?>>
                <?php echo h($m['Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>

<div style="display:flex; gap:8px; margin-top:10px;"><button type="submit" name="save">Save</button><a class="btn-secondary" style="padding:10px 14px; border-radius:10px;" href="galleries.php">Cancel</a></div>
</form>
<?php endif; ?>
<div class="card"><h2>All Galleries</h2>
<?php 
$sql = "SELECT g.Gallery_ID, g.Name, g.Floor_no, g.Room_no, m.Name as MuseumName FROM gallery g LEFT JOIN museum m ON g.Museum_ID = m.Museum_ID";
if(isset($_SESSION['admin_museum_id'])){
    $sql .= " WHERE g.Museum_ID = " . intval($_SESSION['admin_museum_id']);
}
$sql .= " ORDER BY g.Gallery_ID DESC";

$res=$conn->query($sql); 
if($res && $res->num_rows){ 
    echo "<table><tr><th>ID</th><th>Name</th><th>Floor</th><th>Room</th><th>Museum</th><th>Actions</th></tr>"; 
    while($row=$res->fetch_assoc()){ 
        echo "<tr>"; 
        echo "<td>".h($row['Gallery_ID'])."</td>";
        echo "<td>".h($row['Name'])."</td>";
        echo "<td>".h($row['Floor_no'])."</td>";
        echo "<td>".h($row['Room_no'])."</td>";
        echo "<td>".h($row['MuseumName'])."</td>";
        echo "<td>"; 
        if(is_admin()){ 
            echo "<a href=\"galleries.php?edit=".$row["Gallery_ID"]."\">Edit</a> "; 
            echo "<form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete()\"><input type=\"hidden\" name=\"Gallery_ID\" value=\"" . h($row["Gallery_ID"]) . "\"/><button class=\"btn-danger\" name=\"delete\">Delete</button></form>"; 
        } 
        echo "</td></tr>"; 
    } 
    echo "</table>"; 
} else { 
    echo "<div class='alert'>No records.</div>"; 
} 
?>
</div><?php include "footer.php"; ?>