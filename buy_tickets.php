<?php
include 'config.php';
include 'auth.php';
require_login(); // Users must be logged in to buy
include 'header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$msg = '';
$err = '';
$show_payment = false;

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $serial = $_POST['ticket_serial'];
        $qty = intval($_POST['quantity']);
        if ($qty > 0) {
            if (isset($_SESSION['cart'][$serial])) {
                $_SESSION['cart'][$serial] += $qty;
            } else {
                $_SESSION['cart'][$serial] = $qty;
            }
            $msg = "Added to cart.";
        }
    } elseif (isset($_POST['remove_from_cart'])) {
        $serial = $_POST['ticket_serial'];
        unset($_SESSION['cart'][$serial]);
        $msg = "Removed from cart.";
    } elseif (isset($_POST['checkout'])) {
        if (!empty($_SESSION['cart'])) {
            $show_payment = true;
        } else {
            $err = "Cart is empty.";
        }
    } elseif (isset($_POST['confirm_payment'])) {
        if (!empty($_SESSION['cart'])) {
            $user_id = $_SESSION['user']['User_ID'];
            $conn->begin_transaction();
            $success = true;
            
            // Prepare statement
            $stmt = $conn->prepare("INSERT INTO bookings (User_ID, Ticket_Serial, Quantity) VALUES (?, ?, ?)");
            
            foreach ($_SESSION['cart'] as $serial => $qty) {
                $stmt->bind_param("iii", $user_id, $serial, $qty);
                if (!$stmt->execute()) {
                    $success = false;
                    $err = "Failed to book ticket ID $serial: " . $stmt->error;
                    break;
                }
            }
            
            if ($success) {
                $conn->commit();
                $_SESSION['cart'] = [];
                $msg = "Payment successful! Tickets booked.";
            } else {
                $conn->rollback();
            }
        } else {
            $err = "Cart is empty.";
        }
    } elseif (isset($_POST['cancel_payment'])) {
        $show_payment = false;
    }
}

// Fetch all tickets for display and cart lookup
$tickets = [];
$sql = "SELECT t.Serial, t.Type, t.Price, m.Name as MuseumName, e.Name as EventName 
        FROM tickets t 
        JOIN museum m ON t.Museum_ID = m.Museum_ID 
        LEFT JOIN events e ON t.Event_ID = e.Event_ID
        ORDER BY m.Name, t.Type";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $tickets[$row['Serial']] = $row;
    }
}

// Group tickets by Museum
$museums = [];
foreach ($tickets as $t) {
    $museums[$t['MuseumName']][] = $t;
}
?>

<div class="container">
    <?php if ($show_payment): ?>
        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <h2>Payment Gateway (Demo)</h2>
            <p>Please enter your payment details to complete the purchase.</p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $serial => $qty) {
                    if(isset($tickets[$serial])) {
                        $total += $tickets[$serial]['Price'] * $qty;
                    }
                }
                ?>
                <h3>Total Amount: ৳<?php echo number_format($total, 2); ?></h3>
            </div>
            <form method="post">
                <div style="margin-bottom: 10px;">
                    <label>Card Number</label>
                    <input type="text" placeholder="0000 0000 0000 0000" required style="width: 100%; padding: 8px;">
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label>Expiry</label>
                        <input type="text" placeholder="MM/YY" required style="width: 100%; padding: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <label>CVV</label>
                        <input type="text" placeholder="123" required style="width: 100%; padding: 8px;">
                    </div>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" name="confirm_payment" style="flex: 1; background: #059669;">Pay Now</button>
                    <button type="submit" name="cancel_payment" class="btn-secondary" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            
            <!-- Available Tickets -->
            <div class="card">
                <h2>Available Tickets</h2>
                <?php if ($msg) echo "<div class='alert'>$msg</div>"; ?>
                <?php if ($err) echo "<div class='alert alert-danger'>$err</div>"; ?>

                <?php if (!empty($museums)): ?>
                    <?php foreach ($museums as $museumName => $museumTickets): ?>
                        <h3 style="margin-top: 20px; border-bottom: 2px solid #eee; padding-bottom: 5px;"><?php echo h($museumName); ?></h3>
                        <table style="margin-bottom: 20px;">
                            <tr>
                                <th>Ticket Type</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($museumTickets as $ticket): ?>
                            <tr>
                                <td>
                                    <?php 
                                    if ($ticket['EventName']) {
                                        echo "<strong>Event:</strong> " . h($ticket['EventName']) . " - " . h($ticket['Type']);
                                    } else {
                                        echo h($ticket['Type']);
                                    }
                                    ?>
                                </td>
                                <td>৳<?php echo h($ticket['Price']); ?></td>
                                <td>
                                    <form method="post" style="display:flex; gap:5px;">
                                        <input type="hidden" name="ticket_serial" value="<?php echo $ticket['Serial']; ?>">
                                        <input type="number" name="quantity" value="1" min="1" style="width:60px;">
                                        <button type="submit" name="add_to_cart" class="btn-secondary" style="background:#2563eb;">Add</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tickets available.</p>
                <?php endif; ?>
            </div>

            <!-- Cart -->
            <div class="card" style="height:fit-content;">
                <h3>🛒 Your Cart</h3>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <table style="font-size:0.9em;">
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $serial => $qty): 
                            if(!isset($tickets[$serial])) continue;
                            $t = $tickets[$serial];
                            $subtotal = $t['Price'] * $qty;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo h($t['MuseumName']); ?></strong><br>
                                <?php 
                                if ($t['EventName']) {
                                    echo "Event: " . h($t['EventName']) . "<br>";
                                }
                                echo h($t['Type']); 
                                ?>
                            </td>
                            <td><?php echo $qty; ?></td>
                            <td>৳<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="ticket_serial" value="<?php echo $serial; ?>">
                                    <button type="submit" name="remove_from_cart" style="background:none; border:none; color:red; cursor:pointer;">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="border-top:2px solid #eee;">
                            <td colspan="2"><strong>Total</strong></td>
                            <td colspan="2"><strong>৳<?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </table>
                    <form method="post" style="margin-top:15px;">
                        <button type="submit" name="checkout" style="width:100%; background:#059669;">Checkout</button>
                    </form>
                <?php else: ?>
                    <p class="muted">Your cart is empty.</p>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
