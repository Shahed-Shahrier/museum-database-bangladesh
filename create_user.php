<?php
include 'config.php';
include 'auth.php';

// Only super admins can access this page
require_super_admin();

$msg = '';
$err = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $username=trim($_POST['username']??''); 
  $password=$_POST['password']??''; 
  $role=$_POST['role']??'guest';
  $museum_id = !empty($_POST['museum_id']) ? intval($_POST['museum_id']) : null;

  if($username===''||$password===''){ $err='Username and password required.'; }
  elseif(!in_array($role,['admin','guest','museum_admin'])){ $err='Invalid role.'; }
  elseif($role === 'museum_admin' && !$museum_id){ $err='Museum selection is required for Museum Admin.'; }
  else{
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $st=$conn->prepare('INSERT INTO users (Username, PasswordHash, Role, Museum_ID) VALUES (?,?,?,?)');
    $st->bind_param('sssi',$username,$hash,$role,$museum_id);
    if(!$st->execute()) $err='Create failed: '.$st->error; else $msg='User created ✔ — Username: '.htmlspecialchars($username).' Role: '.htmlspecialchars($role);
  }
}

// Fetch museums for dropdown
$museums_res = $conn->query("SELECT Museum_ID, Name FROM museum ORDER BY Name");
$museums = [];
while($m = $museums_res->fetch_assoc()) { $museums[] = $m; }
?><?php include 'header.php'; ?>
<div class="card" style="max-width:560px;margin:40px auto;">
  <h2>Create User</h2>
  <?php if($msg){ echo '<div class="alert">'.$msg.'</div>'; } ?>
  <?php if($err){ echo '<div class="alert" style="background:#fff1f2;border-color:#fecdd3;color:#7f1d1d;">'.htmlspecialchars($err).'</div>'; } ?>
  <form method="post" class="grid" style="grid-template-columns:1fr;">
    <input name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role" id="role_select" onchange="toggleMuseumSelect()">
        <option value="guest">Guest</option>
        <option value="admin">Super Admin</option>
        <option value="museum_admin">Museum Admin</option>
    </select>
    
    <div id="museum_select_container" style="display:none;">
        <label class="muted">Assign Museum</label>
        <select name="museum_id">
            <option value="">-- Select Museum --</option>
            <?php foreach($museums as $m): ?>
                <option value="<?php echo $m['Museum_ID']; ?>"><?php echo h($m['Name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit">Create</button>
  </form>
  <script>
    function toggleMuseumSelect() {
        const role = document.getElementById('role_select').value;
        const container = document.getElementById('museum_select_container');
        if (role === 'museum_admin') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
  </script>
</div>
<?php include 'footer.php'; ?>