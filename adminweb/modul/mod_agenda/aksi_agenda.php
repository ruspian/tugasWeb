<?php
session_start();
include "../../../config/koneksi.php";
include "../../../config/fungsi_seo.php";
include "../../../config/library.php";

$module = $_GET['module'];
$act = $_GET['act'];

// Hapus agenda
if ($module == 'agenda' and $act == 'hapus') {
  mysqli_query($conn, "DELETE FROM agenda WHERE id_agenda='$_GET[id]'");
  header('location:../../media.php?module=' . $module);
}

// Input agenda
elseif ($module == 'agenda' and $act == 'input') {
  $mulai = $_POST['thn_mulai'] . '-' . $_POST['bln_mulai'] . '-' . $_POST['tgl_mulai'];
  $selesai = $_POST['thn_selesai'] . '-' . $_POST['bln_selesai'] . '-' . $_POST['tgl_selesai'];

  $tema_seo = seo_title($_POST['tema']);

  mysqli_query($conn, "INSERT INTO agenda(tema,
                                  tema_seo, 
                                  isi_agenda,
                                  tempat,
                                  tgl_mulai,
                                  tgl_selesai,
                                  tgl_posting,
                                  pengirim, 
                                  username) 
					                VALUES('$_POST[tema]',
					                       '$tema_seo', 
                                 '$_POST[isi_agenda]',
                                 '$_POST[tempat]',
                                 '$mulai',
                                 '$selesai',
                                 '$tgl_sekarang',
                                 '$_POST[pengirim]',
                                 '$_SESSION[namauser]')");
  header('location:../../media.php?module=' . $module);
}

// Update agenda
elseif ($module == 'agenda' and $act == 'update') {
  $mulai = $_POST['thn_mulai'] . '-' . $_POST['bln_mulai'] . '-' . $_POST['tgl_mulai'];
  $selesai = $_POST['thn_selesai'] . '-' . $_POST['bln_selesai'] . '-' . $_POST['tgl_selesai'];

  $tema_seo = seo_title($_POST['tema']);

  mysqli_query($conn, "UPDATE agenda SET tema        = '$_POST[tema]',
                                 tema_seo    = '$tema_seo',
                                 isi_agenda  = '$_POST[isi_agenda]',
                                 tgl_mulai   = '$mulai',
                                 tgl_selesai = '$selesai',
                                 tempat      = '$_POST[tempat]',  
                                 pengirim    = '$_POST[pengirim]'  
                           WHERE id_agenda   = '$_POST[id]'");
  header('location:../../media.php?module=' . $module);
}
