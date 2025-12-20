<?php if (session_status() === PHP_SESSION_NONE) { session_start(); }
function auth_user(){ return isset($_SESSION['user']) ? $_SESSION['user'] : null; }
function is_logged_in(){ return !!auth_user(); }
function user_role(){ return isset($_SESSION['user']['Role']) ? $_SESSION['user']['Role'] : 'guest'; }
function is_super_admin(){ return is_logged_in() && user_role()==='admin'; }
function is_admin(){ return is_logged_in() && (user_role()==='admin' || user_role()==='museum_admin'); }
function require_login(){ if(!is_logged_in()){ header('Location: login.php'); exit; } }
function require_admin(){ if(!is_admin()){ http_response_code(403); echo '<div style="padding:16px;font-family:sans-serif">403 — Admins only.</div>'; exit; } }
function require_super_admin(){ if(!is_super_admin()){ http_response_code(403); echo '<div style="padding:16px;font-family:sans-serif">403 — Super Admins only.</div>'; exit; } }
?>