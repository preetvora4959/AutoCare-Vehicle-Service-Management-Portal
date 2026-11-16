<?php
function clean($v){ return htmlspecialchars(trim((string)$v), ENT_QUOTES, "UTF-8"); }
function customer_only(){ if(session_status()===PHP_SESSION_NONE) session_start(); if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!=="customer"){ header("Location: /AutoCare-Core-PHP/login.php"); exit(); } }
function admin_only(){ if(session_status()===PHP_SESSION_NONE) session_start(); if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!=="admin"){ header("Location: /AutoCare-Core-PHP/admin/login.php"); exit(); } }
?>