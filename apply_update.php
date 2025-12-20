<?php
include 'config.php';

$sql = file_get_contents('update_tickets_event.sql');
if ($conn->multi_query($sql)) {
    echo "Schema updated successfully.";
} else {
    echo "Error updating schema: " . $conn->error;
}
?>
