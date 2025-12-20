<?php include "config.php"; include "auth.php"; require_admin(); include "header.php"; ?>
<div class="card"><h2>Visitor Bookings</h2>
<?php if(isset($_POST['delete']) && isset($_POST['Booking_ID'])){ 
    $id=$_POST['Booking_ID']; 
    $st=$conn->prepare('DELETE FROM bookings WHERE Booking_ID=?'); 
    $st->bind_param('i',$id); 
    if(!$st->execute()) echo '<div class="alert">Delete failed: '.h($st->error).'</div>'; 
    else echo '<div class="alert">Deleted ✔</div>'; 
} ?>

<div class="card"><h2>All Bookings</h2>
<?php 
$sql = "SELECT b.Booking_ID, u.Username as VisitorName, b.Purchase_Date as Date, t.Type as TicketType, m.Name as MuseumName, b.Quantity 
        FROM bookings b 
        JOIN users u ON b.User_ID = u.User_ID 
        JOIN tickets t ON b.Ticket_Serial = t.Serial 
        JOIN museum m ON t.Museum_ID = m.Museum_ID";

if(isset($_SESSION['admin_museum_id'])){
    $sql .= " WHERE t.Museum_ID = " . intval($_SESSION['admin_museum_id']);
}
$sql .= " ORDER BY b.Purchase_Date DESC";

$res=$conn->query($sql); 
if($res && $res->num_rows){ 
    echo "<table><tr><th>ID</th><th>Visitor</th><th>Date</th><th>Ticket Type</th><th>Qty</th><th>Museum</th><th>Actions</th></tr>"; 
    while($row=$res->fetch_assoc()){ 
        echo "<tr>"; 
        echo "<td>".h($row['Booking_ID'])."</td>";
        echo "<td>".h($row['VisitorName'])."</td>";
        echo "<td>".h($row['Date'])."</td>";
        echo "<td>".h($row['TicketType'])."</td>";
        echo "<td>".h($row['Quantity'])."</td>";
        echo "<td>".h($row['MuseumName'])."</td>";
        echo "<td>"; 
        echo "<form method=\"post\" style=\"display:inline\" onsubmit=\"return confirmDelete()\"><input type=\"hidden\" name=\"Booking_ID\" value=\"" . h($row["Booking_ID"]) . "\"/><button class=\"btn-danger\" name=\"delete\">Delete</button></form></td>"; 
        echo "</tr>"; 
    } 
    echo "</table>"; 
} else { 
    echo "<div class='alert'>No records.</div>"; 
} 
?>
</div><?php include "footer.php"; ?>