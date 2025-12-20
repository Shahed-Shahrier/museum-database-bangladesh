<?php include "config.php"; include "auth.php"; include "header.php"; ?>
<div class="card"><h2>Events</h2>
<?php if(is_admin() && isset($_POST['delete']) && isset($_POST['Event_ID'])){ $id=$_POST['Event_ID']; $st=$conn->prepare('DELETE FROM events WHERE Event_ID=?'); $st->bind_param('i',$id); if(!$st->execute()) echo '<div class="alert">Delete failed: '.h($st->error).'</div>'; else echo '<div class="alert">Deleted ✔</div>'; } ?>
<?php if(is_admin() && isset($_POST['save'])){ 
    $is_edit=($_POST['is_edit']=='1');
    $Name=$_POST['Name'];
    $Date=$_POST['Date'];
    $Description=$_POST['Description'];
    $Type=$_POST['Type'];
    $Museum_ID = !empty($_POST['Museum_ID']) ? intval($_POST['Museum_ID']) : null;

    if($is_edit){ 
        $st=$conn->prepare('UPDATE events SET `Name`=?, `Date`=?, `Description`=?, `Type`=? WHERE Event_ID=?'); 
        $st->bind_param('ssssi',$Name,$Date,$Description,$Type,$_POST['Event_ID']); 
        if($st->execute()) {
            $eid = intval($_POST['Event_ID']);
            $conn->query("DELETE FROM museum_event WHERE Event_ID=$eid");
            if($Museum_ID) {
                $st2 = $conn->prepare('INSERT INTO museum_event (Museum_ID, Event_ID) VALUES (?, ?)');
                $st2->bind_param('ii', $Museum_ID, $eid);
                $st2->execute();
            }
            echo '<div class="alert">Saved ✔</div>';
        } else {
            echo '<div class="alert">Save failed: '.h($st->error).'</div>';
        }
    } else { 
        $st=$conn->prepare('INSERT INTO events (`Name`, `Date`, `Description`, `Type`) VALUES (?, ?, ?, ?)'); 
        $st->bind_param('ssss',$Name,$Date,$Description,$Type); 
        if($st->execute()) {
            $eid = $conn->insert_id;
            if($Museum_ID) {
                $st2 = $conn->prepare('INSERT INTO museum_event (Museum_ID, Event_ID) VALUES (?, ?)');
                $st2->bind_param('ii', $Museum_ID, $eid);
                $st2->execute();
            }
            echo '<div class="alert">Saved ✔</div>';
        } else {
            echo '<div class="alert">Save failed: '.h($st->error).'</div>';
        }
    } 
} ?>
<?php 
$edit=null; 
if(is_admin() && isset($_GET['edit'])){ 
    $id=intval($_GET['edit']); 
    $rs=$conn->prepare('SELECT e.Event_ID, e.Name, e.Date, e.Description, e.Type, me.Museum_ID FROM events e LEFT JOIN museum_event me ON e.Event_ID = me.Event_ID WHERE e.Event_ID=? LIMIT 1'); 
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
<form method="post" class="card"><h3 style="margin-top:.2rem;">Events Form</h3>
<input type="hidden" name="Event_ID" value="<?php echo $edit ? h($edit['Event_ID']) : ''; ?>">
<input type="hidden" name="is_edit" value="<?php echo $edit ? '1' : '0'; ?>">
<input name="Name" placeholder="Name" value="<?php echo $edit ? h($edit["Name"]) : ""; ?>" required>
<input name="Date" placeholder="Date (YYYY-MM-DD)" value="<?php echo $edit ? h($edit["Date"]) : ""; ?>">
<?php if(isset($_SESSION['admin_museum_id'])): ?>
    <input type="hidden" name="Museum_ID" value="<?php echo $_SESSION['admin_museum_id']; ?>">
    <input type="text" value="<?php 
        foreach($museums as $m) if($m['Museum_ID'] == $_SESSION['admin_museum_id']) echo h($m['Name']); 
    ?>" disabled>
<?php else: ?>
<select name="Museum_ID">
    <option value="">-- Select Museum (Optional) --</option>
    <?php foreach($museums as $m): ?>
        <option value="<?php echo $m['Museum_ID']; ?>" <?php if($edit && isset($edit['Museum_ID']) && $edit['Museum_ID'] == $m['Museum_ID']) echo 'selected'; ?>>
            <?php echo h($m['Name']); ?>
        </option>
    <?php endforeach; ?>
</select>
<?php endif; ?>
<label class="muted">Description</label><br>
<textarea name="Description" placeholder="Description"><?php echo $edit ? h($edit["Description"]) : ""; ?></textarea>
<input name="Type" placeholder="Type" value="<?php echo $edit ? h($edit["Type"]) : ""; ?>">
<div style="display:flex; gap:8px; margin-top:10px;"><button type="submit" name="save">Save</button><a class="btn-secondary" style="padding:10px 14px; border-radius:10px;" href="events.php">Cancel</a></div>
</form>
<?php endif; ?>
<div class="card"><h2>All Events</h2>
<?php 
$sql = "SELECT e.Event_ID, e.Name, e.Date, e.Description, e.Type, GROUP_CONCAT(m.Name SEPARATOR ', ') as MuseumName FROM events e LEFT JOIN museum_event me ON e.Event_ID = me.Event_ID LEFT JOIN museum m ON me.Museum_ID = m.Museum_ID";
if(isset($_SESSION['admin_museum_id'])){
    $sql .= " WHERE me.Museum_ID = " . intval($_SESSION['admin_museum_id']);
}
$sql .= " GROUP BY e.Event_ID ORDER BY e.Event_ID DESC";
$res=$conn->query($sql); 
if($res && $res->num_rows){  
    echo "<table><tr><th>Event_ID</th><th>Name</th><th>Date</th><th>Description</th><th>Type</th><th>Museum</th><th>Actions</th></tr>"; 
    while($row=$res->fetch_assoc()){ 
        echo "<tr>"; 
        echo "<td>".h($row['Event_ID'])."</td>";
        echo "<td>".h($row['Name'])."</td>";
        echo "<td>".h($row['Date'])."</td>";
        echo "<td>".h($row['Description'])."</td>";
        echo "<td>".h($row['Type'])."</td>";
        echo "<td>".($row['MuseumName'] ? h($row['MuseumName']) : '<span class="muted">None</span>')."</td>";
        echo "<td>"; 
        if(is_admin()){ 
            echo "<a href=\"events.php?edit=".$row["Event_ID"]."\">Edit</a> "; 
            echo "<form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete()\"><input type=\"hidden\" name=\"Event_ID\" value=\"" . h($row["Event_ID"]) . "\"/><button class=\"btn-danger\" name=\"delete\">Delete</button></form>"; 
        } 
        echo "</td></tr>"; 
    } 
    echo "</table>"; 
} else { 
    echo "<div class='alert'>No records.</div>"; 
} 
?>
</div><?php include "footer.php"; ?>