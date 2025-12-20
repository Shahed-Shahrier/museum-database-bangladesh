<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); } 
include_once "auth.php"; 

// Handle Museum Selection for Admin Context (Must be before HTML output)
if(is_super_admin() && isset($_POST['set_admin_museum'])){
    if($_POST['admin_museum_id'] === 'all'){
        unset($_SESSION['admin_museum_id']);
    } else {
        $_SESSION['admin_museum_id'] = intval($_POST['admin_museum_id']);
    }
    // Refresh to clear post data
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Museum DB - Local Frontend</title>
<style>
  :root{ 
    --bg:#f0f2f5; 
    --text:#1e293b; 
    --primary-gradient: linear-gradient(90deg, #3f51b5 0%, #283593 100%);
    --footer-bg: #1a237e;
    --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
  }
  *{ box-sizing:border-box; }
  body{ margin:0; font-family:'Segoe UI', system-ui, sans-serif; background:var(--bg); color:var(--text); display:flex; flex-direction:column; min-height:100vh; }
  
  /* Top Bar */
  .top-bar { background: #00000040; color: #fff; padding: 5px 20px; font-size: 12px; display: flex; justify-content: space-between; }
  .top-bar a { color: #fff; text-decoration: none; margin-left: 10px; }

  /* Hero Header */
  header{ 
    background: url('https://img.freepik.com/free-vector/gradient-blue-background_23-2149333532.jpg') no-repeat center center/cover, var(--primary-gradient);
    background-blend-mode: overlay;
    color:#fff; 
    padding: 40px 20px; 
    text-align: center;
    position: relative;
  }
  header h1{ margin:0; font-size:32px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
  header p { margin: 10px 0 0; font-size: 16px; opacity: 0.9; }
  
  /* Search Bar in Header */
  .search-container { margin-top: 20px; display: flex; justify-content: center; gap: 10px; }
  .search-input { padding: 10px 15px; border-radius: 20px; border: none; width: 300px; outline: none; }
  .search-btn { padding: 10px 20px; border-radius: 20px; border: none; background: #009688; color: white; cursor: pointer; font-weight: bold; }

  /* Navigation (Main Menu) */
  nav{ background:#fff; padding:10px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display:flex; flex-wrap:wrap; gap:15px; justify-content: center; position: sticky; top: 0; z-index: 100; }
  nav a{ color:#333; text-decoration:none; padding:8px 14px; border-radius:4px; font-weight: 500; font-size: 14px; transition: all 0.2s; }
  nav a:hover{ background:#e3f2fd; color:#1565c0; }
  
  main{ max-width:1200px; margin:30px auto; padding:0 20px; flex: 1; width: 100%; }

  /* Card Grid Styles (for Index) */
  .service-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
  .service-card { 
    background: #fff; 
    border-radius: 8px; 
    overflow: hidden; 
    box-shadow: var(--card-shadow); 
    transition: transform 0.2s; 
    position: relative;
    display: flex;
    flex-direction: column;
    height: 160px;
    text-decoration: none;
    color: #fff;
  }
  .service-card:hover { transform: translateY(-5px); }
  .card-content { padding: 15px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; z-index: 2; }
  .card-icon { font-size: 24px; margin-bottom: 10px; background: rgba(255,255,255,0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
  .card-title { font-size: 14px; font-weight: 600; line-height: 1.4; }
  .card-tag { font-size: 10px; text-transform: uppercase; opacity: 0.8; margin-bottom: 5px; }
  
  /* Gradient Variants */
  .grad-yellow { background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%); }
  .grad-blue { background: linear-gradient(135deg, #42A5F5 0%, #1976D2 100%); }
  .grad-green { background: linear-gradient(135deg, #66BB6A 0%, #388E3C 100%); }
  .grad-teal { background: linear-gradient(135deg, #26C6DA 0%, #0097A7 100%); }
  .grad-purple { background: linear-gradient(135deg, #AB47BC 0%, #7B1FA2 100%); }
  .grad-red { background: linear-gradient(135deg, #EF5350 0%, #D32F2F 100%); }

  /* Decorative Curve */
  .service-card::after {
    content: '';
    position: absolute;
    bottom: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
  }

  /* Admin Context Bar */
  .admin-context { background:#fff3cd; color:#856404; padding:10px 20px; display:flex; align-items:center; gap:10px; font-size:0.9em; border-bottom:1px solid #ffeeba; justify-content: center; }
  
  /* Standard Tables & Forms */
  .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: var(--card-shadow); margin-bottom: 20px; color: #333; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
  th { background: #f8f9fa; font-weight: 600; }
  input, select, textarea { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 4px; }
  button { padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
  .btn-danger { background: #ef4444; }
  .btn-secondary { background: #64748b; text-decoration: none; color: white; display: inline-block; }
  .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; background: #d1fae5; color: #065f46; }
  .alert-danger { background: #fee2e2; color: #991b1b; }
</style>
</head>

<body>
<div style="background: linear-gradient(90deg, #1a237e 0%, #283593 100%);">
    <div class="top-bar">
        <div>Ministry of Cultural Affairs | Museum Database Portal</div>
        <div>
            <?php if(isset($_SESSION['user'])): ?>
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']['Username']); ?></strong></span>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <header>
      <h1>Integrated Museum Service Platform</h1>
      <p>Welcome to the one-stop service delivery platform for all museums.</p>
      
      <form action="search.php" method="GET" class="search-container">
        <input type="text" name="q" class="search-input" placeholder="Search services..." required>
        <button type="submit" class="search-btn">Search</button>
      </form>
    </header>
</div>

<nav>
  <a href="index.php">Home</a>
  <a href="museums.php">Museums</a>
  <a href="events.php">Events</a>
  <a href="galleries.php">Galleries</a>
  <a href="artists.php">Artists</a>
  <a href="art_pieces.php">Art Pieces</a>
  
  <?php if(is_logged_in() && !is_admin()): ?>
    <a href="buy_tickets.php">Buy Tickets</a>
  <?php endif; ?>

  <!-- Admin-only links -->
  <?php if(is_admin()): ?>
    <a href="tickets.php">Manage Tickets</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="visitors.php">Visitors</a>
    <?php if(is_super_admin()): ?>
        <a href="create_user.php">Create User</a>
    <?php endif; ?>
  <?php endif; ?>
</nav>

<?php if(is_admin() && isset($conn)): ?>
  <div class="admin-context">
    <span>🔧 <strong>Admin Context:</strong></span>
    <?php if(is_super_admin()): ?>
    <form method="post" style="margin:0; display:inline-block;">
      <select name="admin_museum_id" onchange="this.form.submit()" style="padding:4px; border-radius:4px; border:1px solid #ccc; width:auto; display:inline-block;">
        <option value="all">-- Manage All Museums --</option>
        <?php
            $m_res = $conn->query("SELECT Museum_ID, Name FROM Museum ORDER BY Name");
            while($m = $m_res->fetch_assoc()){
                $selected = (isset($_SESSION['admin_museum_id']) && $_SESSION['admin_museum_id'] == $m['Museum_ID']) ? 'selected' : '';
                echo "<option value='{$m['Museum_ID']}' $selected>" . htmlspecialchars($m['Name']) . "</option>";
            }
        ?>
      </select>
      <input type="hidden" name="set_admin_museum" value="1">
    </form>
    <?php else: ?>
        <?php 
            if(isset($_SESSION['admin_museum_id'])){
                $mid = intval($_SESSION['admin_museum_id']);
                $m_res = $conn->query("SELECT Name FROM Museum WHERE Museum_ID = $mid");
                if($m = $m_res->fetch_assoc()){
                    echo "<span style='font-weight:bold;'>" . htmlspecialchars($m['Name']) . "</span>";
                }
            }
        ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['admin_museum_id'])): ?>
        <span style="margin-left:10px;">⚠️ Editing restricted</span>
    <?php endif; ?>
  </div>
<?php endif; ?>

<main>
