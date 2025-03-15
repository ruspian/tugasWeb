<?php
include "../../../config/koneksi.php";
include "../../../config/fungsi_thumb.php";
include "../../../config/fungsi_seo.php";

$module = $_GET['module'];
$act = $_GET['act'];

// Hapus gallery
if ($module == 'galerifoto' and $act == 'hapus') {
  mysqli_query($conn, "DELETE FROM gallery WHERE id_gallery='$_GET[id]'");
  header('location:../../media.php?module=' . $module);
}

// Input gallery
elseif ($module == 'galerifoto' and $act == 'input') {
  $lokasi_file    = $_FILES['fupload']['tmp_name'];
  $tipe_file      = $_FILES['fupload']['type'];
  $nama_file      = $_FILES['fupload']['name'];
  $acak           = rand(000000, 999999);
  $nama_file_unik = $acak . $nama_file;

  $gallery_seo      = seo_title($_POST['jdl_gallery']);

  // Apabila ada gambar yang diupload
  if (!empty($lokasi_file)) {
    UploadGallery($nama_file_unik);
    mysqli_query($conn, "INSERT INTO gallery(jdl_gallery,
                                    gallery_seo,
                                    id_album,
                                    keterangan,
                                    gbr_gallery) 
                            VALUES('$_POST[jdl_gallery]',
                                   '$gallery_seo',
                                   '$_POST[album]',
                                   '$_POST[keterangan]',
                                   '$nama_file_unik')");
  } else {
    mysqli_query($conn, "INSERT INTO gallery(jdl_gallery,
                                    gallery_seo,
                                    id_album,
                                    keterangan) 
                            VALUES('$_POST[jdl_gallery]',
                                   '$gallery_seo',
                                   '$_POST[album]',
                                   '$_POST[keterangan]')");
  }
  header('location:../../media.php?module=' . $module);
}

// Update gallery
elseif ($module == 'galerifoto' and $act == 'update') {
  $lokasi_file    = $_FILES['fupload']['tmp_name'];
  $tipe_file      = $_FILES['fupload']['type'];
  $nama_file      = $_FILES['fupload']['name'];
  $acak           = rand(000000, 999999);
  $nama_file_unik = $acak . $nama_file;

  $gallery_seo      = seo_title($_POST['jdl_gallery']);

  // Apabila gambar tidak diganti
  if (empty($lokasi_file)) {
    mysqli_query($conn, "UPDATE gallery SET jdl_gallery  = '$_POST[jdl_gallery]',
                                   gallery_seo   = '$gallery_seo', 
                                   id_album = '$_POST[album]',
                                   keterangan  = '$_POST[keterangan]'  
                             WHERE id_gallery   = '$_POST[id]'");
  } else {
    UploadGallery($nama_file_unik);
    mysqli_query($conn, "UPDATE gallery SET jdl_gallery  = '$_POST[jdl_gallery]',
                                   gallery_seo   = '$gallery_seo', 
                                   id_album = '$_POST[album]',
                                   keterangan  = '$_POST[keterangan]',  
                                   gbr_gallery      = '$nama_file_unik'   
                             WHERE id_gallery   = '$_POST[id]'");
  }
  header('location:../../media.php?module=' . $module);
}
