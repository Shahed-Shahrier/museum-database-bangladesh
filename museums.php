<?php
include "config.php";
include "auth.php";
include "header.php";

// Admin actions: delete, create, update
if (is_admin()) {
    if (isset($_POST['delete']) && isset($_POST['Museum_ID'])) {
        if(!is_super_admin()){
            echo '<div class="alert alert-danger">Only Super Admins can delete museums.</div>';
        } else {
            $id = $_POST['Museum_ID'];
            $stmt = $conn->prepare('DELETE FROM museum WHERE Museum_ID=?');
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                echo '<div class="alert alert-danger">Delete failed: ' . h($stmt->error) . '</div>';
            } else {
                echo '<div class="alert alert-success">Museum deleted successfully.</div>';
            }
        }
    }

    if (isset($_POST['save'])) {
        $is_edit = ($_POST['is_edit'] == '1');
        $Name = $_POST['Name'];
        $City = $_POST['City'];
        $Contact_no = $_POST['Contact_no'];
        $Email = $_POST['Email'];
        $Type = $_POST['Type'];

        if ($is_edit) {
            $stmt = $conn->prepare('UPDATE museum SET `Name`=?, `City`=?, `Contact_no`=?, `Email`=?, `Type`=? WHERE Museum_ID=?');
            $stmt->bind_param('sssssi', $Name, $City, $Contact_no, $Email, $Type, $_POST['Museum_ID']);
        } else {
            $stmt = $conn->prepare('INSERT INTO museum (`Name`, `City`, `Contact_no`, `Email`, `Type`) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $Name, $City, $Contact_no, $Email, $Type);
        }

        if (!$stmt->execute()) {
            echo '<div class="alert alert-danger">Save failed: ' . h($stmt->error) . '</div>';
        } else {
            echo '<div class="alert alert-success">Museum saved successfully.</div>';
        }
    }
}

$edit_museum = null;
if (is_admin() && isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $rs = $conn->prepare('SELECT Museum_ID, Name, City, Contact_no, Email, Type FROM museum WHERE Museum_ID=?');
    $rs->bind_param('i', $id);
    $rs->execute();
    $row = $rs->get_result()->fetch_assoc();
    if ($row) {
        $edit_museum = $row;
    }
}
?>

<div class="container">
    <h1>Museums</h1>
    <p>Explore the museums that are part of our national collection.</p>

    <?php if (is_admin()): ?>
        <?php if(!isset($_SESSION['admin_museum_id']) || $edit_museum): ?>
        <div class="card mb-4">
            <h3><?php echo $edit_museum ? 'Edit Museum' : 'Add New Museum'; ?></h3>
            <form method="post">
                <input type="hidden" name="Museum_ID" value="<?php echo $edit_museum ? h($edit_museum['Museum_ID']) : ''; ?>">
                <input type="hidden" name="is_edit" value="<?php echo $edit_museum ? '1' : '0'; ?>">
                <div class="form-group">
                    <label for="Name">Name</label>
                    <input name="Name" id="Name" class="form-control" placeholder="Museum Name" value="<?php echo $edit_museum ? h($edit_museum["Name"]) : ""; ?>" required>
                </div>
                <div class="form-group">
                    <label for="City">City</label>
                    <input name="City" id="City" class="form-control" placeholder="City" value="<?php echo $edit_museum ? h($edit_museum["City"]) : ""; ?>">
                </div>
                <div class="form-group">
                    <label for="Contact_no">Contact No</label>
                    <input name="Contact_no" id="Contact_no" class="form-control" placeholder="Contact Number" value="<?php echo $edit_museum ? h($edit_museum["Contact_no"]) : ""; ?>">
                </div>
                <div class="form-group">
                    <label for="Email">Email</label>
                    <input name="Email" id="Email" class="form-control" placeholder="Email Address" value="<?php echo $edit_museum ? h($edit_museum["Email"]) : ""; ?>">
                </div>
                <div class="form-group">
                    <label for="Type">Type</label>
                    <input name="Type" id="Type" class="form-control" placeholder="Type (e.g., Art, History)" value="<?php echo $edit_museum ? h($edit_museum["Type"]) : ""; ?>">
                </div>
                <button type="submit" name="save" class="btn btn-primary">Save</button>
                <?php if ($edit_museum): ?>
                    <a href="museums.php" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="grid">
        <?php
        $sql = "SELECT Museum_ID, Name, City, Contact_no, Email, Type FROM museum";
        if(isset($_SESSION['admin_museum_id'])){
            $sql .= " WHERE Museum_ID = " . intval($_SESSION['admin_museum_id']);
        }
        $sql .= " ORDER BY Name ASC";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card">';
                // Make the title a link to the details page
                echo '<h3><a href="museum_details.php?id=' . $row['Museum_ID'] . '" style="text-decoration:none; color:inherit;">' . h($row['Name']) . '</a></h3>';
                echo '<p><strong>City:</strong> ' . h($row['City']) . '</p>';
                echo '<p><strong>Type:</strong> ' . h($row['Type']) . '</p>';
                if ($row['Contact_no']) echo '<p><strong>Contact:</strong> ' . h($row['Contact_no']) . '</p>';
                if ($row['Email']) echo '<p><strong>Email:</strong> ' . h($row['Email']) . '</p>';
                
                // Add a "View Details" button for clarity
                echo '<a href="museum_details.php?id=' . $row['Museum_ID'] . '" style="display:inline-block; margin-top:10px; text-decoration:none; color:#2563eb;">View Details &rarr;</a>';

                if (is_admin()) {
                    echo '<div class="admin-actions" style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">';
                    echo '<a href="museums.php?edit=' . $row["Museum_ID"] . '" class="btn btn-sm btn-secondary">Edit</a> ';
                    if(is_super_admin()){
                        echo '<form method="post" style="display:inline" onsubmit="return confirm(\'Are you sure you want to delete this museum?\');">';
                        echo '<input type="hidden" name="Museum_ID" value="' . h($row["Museum_ID"]) . '"/>';
                        echo '<button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>';
                        echo '</form>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            }
        } else {
            echo "<p>No museums found.</p>";
        }
        ?>
    </div>
</div>

<?php include "footer.php"; ?>
