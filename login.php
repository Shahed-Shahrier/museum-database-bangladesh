<?php include "config.php"; include "auth.php";
$err=""; if($_SERVER['REQUEST_METHOD']==='POST'){
  $username=trim($_POST['username']??''); $password=$_POST['password']??'';
  if($username===''||$password===''){ $err='Username and password are required.'; }
  else{
    $st=$conn->prepare('SELECT User_ID, Username, PasswordHash, Role, Museum_ID FROM Users WHERE Username=?');
    $st->bind_param('s',$username); $st->execute(); $res=$st->get_result();
    if($row=$res->fetch_assoc()){ 
        if(password_verify($password,$row['PasswordHash'])){ 
            $_SESSION['user']=['User_ID'=>$row['User_ID'],'Username'=>$row['Username'],'Role'=>$row['Role']]; 
            if($row['Role'] === 'museum_admin' && !empty($row['Museum_ID'])){
                $_SESSION['admin_museum_id'] = intval($row['Museum_ID']);
            }
            header('Location: index.php'); exit; 
        } else { $err='Invalid credentials.'; } 
    }
    else { $err='User not found.'; }
  }
} ?>
<?php include "header.php"; ?>
<div class="card" style="max-width:520px;margin:40px auto;">
  <h2>Sign in</h2>
  <?php if($err){ echo '<div class="alert">'.htmlspecialchars($err).'</div>'; } ?>
  <form method="post" class="grid" style="grid-template-columns:1fr;">
    <input name="username" placeholder="Username" required>
    <input name="password" type="password" placeholder="Password" required>
    <button type="submit">Log in</button>
  </form>
    <p class="muted">
    No account yet? <a href="register.php">Register as a guest</a>.<br>
   
  </p>

</div>
<?php include "footer.php"; ?>