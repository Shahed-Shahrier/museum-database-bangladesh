<?php include "config.php"; include "auth.php"; require_admin(); include "header.php"; ?>
<div class="card"><h2>Tickets</h2>
<?php if(isset($_POST['delete']) && isset($_POST['Serial'])){ $id=$_POST['Serial']; $st=$conn->prepare('DELETE FROM tickets WHERE Serial=?'); $st->bind_param('i',$id); if(!$st->execute()) echo '<div class="alert">Delete failed: '.h($st->error).'</div>'; else echo '<div class="alert">Deleted ✔</div>'; } ?>
<?php if(isset($_POST['save'])){ $is_edit=($_POST['is_edit']=='1');
$Type=$_POST['Type'];
$Price=$_POST['Price'];
$Museum_ID=$_POST['Museum_ID'];
$Event_ID = !empty($_POST['Event_ID']) ? $_POST['Event_ID'] : null;

if($is_edit){ 
    $st=$conn->prepare('UPDATE tickets SET `Type`=?, `Price`=?, `Museum_ID`=?, `Event_ID`=? WHERE Serial=?'); 
    $st->bind_param('sdiii',$Type,$Price,$Museum_ID,$Event_ID,$_POST['Serial']); 
} else { 
    $st=$conn->prepare('INSERT INTO tickets (`Type`, `Price`, `Museum_ID`, `Event_ID`) VALUES (?, ?, ?, ?)'); 
    $st->bind_param('sdii',$Type,$Price,$Museum_ID,$Event_ID); 
} 
if(!$st->execute()) echo '<div class="alert">Save failed: '.h($st->error).'</div>'; else echo '<div class="alert">Saved ✔</div>'; } ?>
<?php $edit=null; if(isset($_GET['edit'])){ $id=intval($_GET['edit']); $rs=$conn->prepare('SELECT Serial, `Type`, Price, Museum_ID, Event_ID FROM tickets WHERE Serial=?'); $rs->bind_param('i',$id); $rs->execute(); $row=$rs->get_result()->fetch_assoc(); if($row){ $edit=$row; } } 
// Fetch Museums
$museums_res = $conn->query("SELECT Museum_ID, Name FROM museum ORDER BY Name");
$museums = [];
while($m = $museums_res->fetch_assoc()) { $museums[] = $m; }

// Fetch Events
$events_res = $conn->query("SELECT Event_ID, Name FROM events ORDER BY Name");
$events = [];
while($e = $events_res->fetch_assoc()) { $events[] = $e; }
?>
<form method="post" class="card"><h3 style="margin-top:.2rem;">Tickets Form</h3>
<input type="hidden" name="Serial" value="<?php echo $edit ? h($edit['Serial']) : ''; ?>">
<input type="hidden" name="is_edit" value="<?php echo $edit ? '1' : '0'; ?>">
<input name="Type" placeholder="Type" value="<?php echo $edit ? h($edit["Type"]) : ""; ?>" required>
<input name="Price" placeholder="Price" value="<?php echo $edit ? h($edit["Price"]) : ""; ?>" required>
<label class="muted">Museum</label>
<?php if(isset($_SESSION['admin_museum_id'])): ?>
    <input type="hidden" name="Museum_ID" value="<?php echo $_SESSION['admin_museum_id']; ?>">
    <input type="text" value="<?php 
        foreach($museums as $m) if($m['Museum_ID'] == $_SESSION['admin_museum_id']) echo h($m['Name']); 
    ?>" disabled>
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

<label class="muted">Event (Optional)</label>
<select name="Event_ID">
    <option value="">-- None (General Ticket) --</option>
    <?php foreach($events as $e): ?>
        <option value="<?php echo $e['Event_ID']; ?>" <?php if($edit && $edit['Event_ID'] == $e['Event_ID']) echo 'selected'; ?>>
            <?php echo h($e['Name']); ?>
        </option>
    <?php endforeach; ?>
</select>

<div style="display:flex; gap:8px; margin-top:10px;"><button type="submit" name="save">Save</button><a class="btn-secondary" style="padding:10px 14px; border-radius:10px;" href="tickets.php">Cancel</a></div>
</form>
<div class="card"><h2>All Tickets</h2>
<?php 
$sql = "SELECT t.Serial, t.Type, t.Price, m.Name as MuseumName, e.Name as EventName 
        FROM tickets t 
        LEFT JOIN museum m ON t.Museum_ID = m.Museum_ID
        LEFT JOIN events e ON t.Event_ID = e.Event_ID";
if(isset($_SESSION['admin_museum_id'])){
    $sql .= " WHERE t.Museum_ID = " . intval($_SESSION['admin_museum_id']);
}
$sql .= " ORDER BY t.Serial DESC";
$res=$conn->query($sql); 
if($res && $res->num_rows){ 
    echo "<table><tr><th>Serial</th><th>Type</th><th>Price</th><th>Museum</th><th>Event</th><th>Actions</th></tr>"; 
    while($row=$res->fetch_assoc()){ 
        echo "<tr>"; 
        echo "<td>".h($row['Serial'])."</td>";
        echo "<td>".h($row['Type'])."</td>";
        echo "<td>৳".h($row['Price'])."</td>";
        echo "<td>".h($row['MuseumName'])."</td>";
        echo "<td>".($row['EventName'] ? h($row['EventName']) : '<span class="muted">-</span>')."</td>";
        echo "<td><a href=\"tickets.php?edit=".$row["Serial"]."\">Edit</a> "; 
        echo "<form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete()\"><input type=\"hidden\" name=\"Serial\" value=\"" . h($row["Serial"]) . "\"/><button class=\"btn-danger\" name=\"delete\">Delete</button></form></td>"; 
        echo "</tr>"; 
    } 
    echo "</table>"; 
} else { 
    echo "<div class='alert'>No records.</div>"; 
} 
?>
</div><?php include "footer.php"; ?>