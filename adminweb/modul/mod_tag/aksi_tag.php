<?php
session_start();
include "../../../config/koneksi.php";
include "../../../config/fungsi_seo.php";

$module = $_GET['module'];
$act = $_GET['act'];

// Hapus Tag
if ($module == 'tag' and $act == 'hapus') {
  mysqli_query($conn, "DELETE FROM tag WHERE id_tag='$_GET[id]'");
  header('location:../../media.php?module=' . $module);
}

// Input tag
elseif ($module == 'tag' and $act == 'input') {
  $tag_seo = seo_title($_POST['nama_tag']);
  mysqli_query($conn, "INSERT INTO tag(nama_tag,tag_seo) VALUES('$_POST[nama_tag]','$tag_seo')");
  header('location:../../media.php?module=' . $module);
}

// Update tag
elseif ($module == 'tag' and $act == 'update') {
  $tag_seo = seo_title($_POST['nama_tag']);
  mysqli_query($conn, "UPDATE tag SET nama_tag = '$_POST[nama_tag]', tag_seo='$tag_seo' WHERE id_tag = '$_POST[id]'");
  header('location:../../media.php?module=' . $module);
}
