<?php
include "config.php";
include "auth.php";
require_admin(); // Only admins can see the dashboard
include "header.php";
?>

<div class="card">
  <h2>Dashboard</h2>
  <p class="muted">Quick counts from your <strong>MUSEUM_DATABASE</strong>.</p>

  <div class="grid">
  <?php
    $mid = isset($_SESSION['admin_museum_id']) ? intval($_SESSION['admin_museum_id']) : null;

    $items = [
      ["Museums", "SELECT COUNT(*) c FROM Museum" . ($mid ? " WHERE Museum_ID=$mid" : "")],
      ["Events", "SELECT COUNT(DISTINCT e.Event_ID) c FROM Events e JOIN Museum_Event me ON e.Event_ID=me.Event_ID" . ($mid ? " WHERE me.Museum_ID=$mid" : "")],
      ["Galleries", "SELECT COUNT(*) c FROM Gallery" . ($mid ? " WHERE Museum_ID=$mid" : "")],
      ["Artists", "SELECT COUNT(*) c FROM Artist" . ($mid ? " WHERE Museum_ID=$mid" : "")],
      ["Art Pieces", "SELECT COUNT(*) c FROM Art_Piece ap JOIN Gallery g ON ap.Gallery_ID=g.Gallery_ID" . ($mid ? " WHERE g.Museum_ID=$mid" : "")],
      ["Tickets", "SELECT COUNT(*) c FROM Tickets" . ($mid ? " WHERE Museum_ID=$mid" : "")],
      ["Visitors", "SELECT COUNT(*) c FROM bookings b JOIN tickets t ON b.Ticket_Serial=t.Serial" . ($mid ? " WHERE t.Museum_ID=$mid" : "")],
    ];
    
    if(!$mid){
        $items[] = ["Users", "SELECT COUNT(*) c FROM Users"];
    }

    foreach ($items as $item) {
      $label = $item[0];
      $sql   = $item[1];

      $res = $conn->query($sql);
      $count = $res ? $res->fetch_assoc()["c"] : 0;

      echo '<div class="card">';
      echo '<h3>'.$label.'</h3>';
      echo '<div class="muted" style="font-size:32px;font-weight:700;">'.htmlspecialchars($count).'</div>';
      echo '</div>';
    }
  ?>
  </div>

</div>

<?php include "footer.php"; ?>
