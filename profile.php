<?php
include "config.php";
include "auth.php";
require_login();
include "header.php";
?>

<div class="card" style="max-width:800px; margin:40px auto;">
  <h2>Your Profile</h2>

  <p><strong>Username:</strong> <?php echo h($_SESSION['user']['Username']); ?></p>
  <p><strong>Role:</strong> <?php echo h($_SESSION['user']['Role']); ?></p>
  <p><strong>User ID:</strong> <?php echo h($_SESSION['user']['User_ID']); ?></p>

  <hr>

  <h3>Your Purchased Tickets</h3>
  <?php
  $user_id = $_SESSION['user']['User_ID'];
  $sql = "SELECT b.Booking_ID, b.Purchase_Date, b.Quantity, t.Type, t.Price, m.Name as MuseumName 
          FROM bookings b
          JOIN tickets t ON b.Ticket_Serial = t.Serial
          JOIN museum m ON t.Museum_ID = m.Museum_ID
          WHERE b.User_ID = ?
          ORDER BY b.Purchase_Date DESC";
  
  $stmt = $conn->prepare($sql);
  if ($stmt) {
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $res = $stmt->get_result();

      if ($res && $res->num_rows > 0) {
          echo "<table>";
          echo "<tr><th>Date</th><th>Museum</th><th>Ticket Type</th><th>Price</th><th>Qty</th><th>Total</th></tr>";
          while ($row = $res->fetch_assoc()) {
              $total = $row['Price'] * $row['Quantity'];
              echo "<tr>";
              echo "<td>" . h($row['Purchase_Date']) . "</td>";
              echo "<td>" . h($row['MuseumName']) . "</td>";
              echo "<td>" . h($row['Type']) . "</td>";
              echo "<td>" . h($row['Price']) . "</td>";
              echo "<td>" . h($row['Quantity']) . "</td>";
              echo "<td>" . h($total) . "</td>";
              echo "</tr>";
          }
          echo "</table>";
      } else {
          echo "<p>You haven't purchased any tickets yet.</p>";
      }
  } else {
      echo "<p class='alert'>Error fetching tickets: " . h($conn->error) . "</p>";
  }
  ?>

</div>

<?php include "footer.php"; ?>
