<?php include "config.php"; include "header.php"; ?>
<div class="card"><h2>Link Museums ↔ Events</h2><p class="muted">Create or remove collaborations between museums and events.</p>
<?php
if(isset($_POST['link'])){ $museum_id=intval($_POST['Museum_ID']); $event_id=intval($_POST['Event_ID']); $st=$conn->prepare("INSERT IGNORE INTO Museum_Event (Museum_ID, Event_ID) VALUES (?,?)"); $st->bind_param("ii",$museum_id,$event_id); if(!$st->execute()) echo '<div class="alert">Link failed: '.h($st->error).'</div>'; else echo '<div class="alert">Linked ✔</div>'; }
if(isset($_POST['unlink'])){ $museum_id=intval($_POST['Museum_ID']); $event_id=intval($_POST['Event_ID']); $st=$conn->prepare("DELETE FROM Museum_Event WHERE Museum_ID=? AND Event_ID=?"); $st->bind_param("ii",$museum_id,$event_id); if(!$st->execute()) echo '<div class="alert">Unlink failed: '.h($st->error).'</div>'; else echo '<div class="alert">Unlinked ✔</div>'; }
$museums=$conn->query("SELECT Museum_ID, Name FROM Museum ORDER BY Name"); $events=$conn->query("SELECT Event_ID, Name FROM Events ORDER BY Name");
?>
<form class="inline" method="post">
  <select name="Museum_ID" required><option value="">Select Museum</option>
  <?php while($m=$museums->fetch_assoc()){ echo '<option value="'.$m['Museum_ID'].'">'.h($m['Name']).' (#'.$m['Museum_ID'].')</option>'; } ?></select>
  <select name="Event_ID" required><option value="">Select Event</option>
  <?php while($e=$events->fetch_assoc()){ echo '<option value="'.$e['Event_ID'].'">'.h($e['Name']).' (#'.$e['Event_ID'].')</option>'; } ?></select>
  <button type="submit" name="link">Link</button>
</form></div>
<div class="card"><h2>Current Collaborations</h2>
<?php
$sql="SELECT me.Museum_ID, m.Name AS Museum, me.Event_ID, e.Name AS EventName, e.Date FROM Museum_Event me JOIN Museum m ON me.Museum_ID=m.Museum_ID JOIN Events e ON me.Event_ID=e.Event_ID ORDER BY e.Date DESC, m.Name ASC";
$res=$conn->query($sql);
if($res && $res->num_rows){ echo "<table><tr><th>Museum</th><th>Event</th><th>Date</th><th>Actions</th></tr>";
while($row=$res->fetch_assoc()){
  echo "<tr><td>".h($row['Museum'])."</td><td>".h($row['EventName'])."</td><td>".h($row['Date'])."</td>";
  echo "<td><form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete('Unlink this museum & event?')\"><input type=\"hidden\" name=\"Museum_ID\" value=\"" . h($row['Museum_ID']) . "\"/><input type=\"hidden\" name=\"Event_ID\" value=\"" . h($row['Event_ID']) . "\"/><button class=\"btn-danger\" name=\"unlink\">Unlink</button></form></td></tr>";
}
echo "</table>"; } else { echo "<div class='alert'>No links yet. Add some above.</div>"; } ?>
</div><?php include "footer.php"; ?>