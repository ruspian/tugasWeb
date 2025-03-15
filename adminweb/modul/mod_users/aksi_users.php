<?php
session_start();
include "../../../config/koneksi.php";

$module = $_GET['module'];
$act = $_GET['act'];

// Input user
if ($module == 'user' and $act == 'input') {
  $pass = md5($_POST['password']);
  mysqli_query($conn, "INSERT INTO users(username,
                                 password,
                                 nama_lengkap,
                                 email, 
                                 no_telp,
                                 id_session) 
	                       VALUES('$_POST[username]',
                                '$pass',
                                '$_POST[nama_lengkap]',
                                '$_POST[email]',
                                '$_POST[no_telp]',
                                '$pass')");
  header('location:../../media.php?module=' . $module);
}

// Update user
elseif ($module == 'user' and $act == 'update') {
  if (empty($_POST['password'])) {
    mysqli_query($conn, "UPDATE users SET nama_lengkap   = '$_POST[nama_lengkap]',
                                  email          = '$_POST[email]',
                                  blokir         = '$_POST[blokir]',  
                                  no_telp        = '$_POST[no_telp]'  
                           WHERE  id_session     = '$_POST[id]'");
  }
  // Apabila password diubah
  else {
    $pass = md5($_POST['password']);
    mysqli_query($conn, "UPDATE users SET password        = '$pass',
                                 nama_lengkap    = '$_POST[nama_lengkap]',
                                 email           = '$_POST[email]',  
                                 blokir          = '$_POST[blokir]',  
                                 no_telp         = '$_POST[no_telp]'  
                           WHERE id_session      = '$_POST[id]'");
  }
  header('location:../../media.php?module=' . $module);
}
