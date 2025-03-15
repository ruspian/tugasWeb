<?php
include "../../../config/koneksi.php";
include "../../../config/fungsi_seo.php";
include "../../../config/fungsi_thumb.php";

$module = $_GET['module'];
$act = $_GET['act'];

// Input album
if ($module == 'album' and $act == 'input') {
  $lokasi_file = $_FILES['fupload']['tmp_name'];
  $nama_file   = $_FILES['fupload']['name'];
  $acak           = rand(000000, 999999);
  $nama_file_unik = $acak . $nama_file;

  $album_seo = seo_title($_POST['jdl_album']);

  // Apabila ada gambar yang diupload
  if (!empty($lokasi_file)) {
    UploadAlbum($nama_file_unik);
    mysqli_query($conn, "INSERT INTO album(jdl_album,
                                    album_seo,
                                    gbr_album) 
                            VALUES('$_POST[jdl_album]',
                                   '$album_seo',
                                   '$nama_file_unik')");
  } else {
    mysqli_query($conn, "INSERT INTO album(jdl_album,
                                    album_seo) 
                            VALUES('$_POST[jdl_album]',
                                   '$album_seo')");
  }
  header('location:../../media.php?module=' . $module);
}

// Update album
elseif ($module == 'album' and $act == 'update') {
  $lokasi_file = $_FILES['fupload']['tmp_name'];
  $nama_file   = $_FILES['fupload']['name'];
  $acak           = rand(000000, 999999);
  $nama_file_unik = $acak . $nama_file;

  $album_seo = seo_title($_POST['jdl_album']);

  // Apabila gambar tidak diganti
  if (empty($lokasi_file)) {
    mysqli_query($conn, "UPDATE album SET jdl_album     = '$_POST[jdl_album]',
                                  album_seo     = '$album_seo'
                             WHERE id_album = '$_POST[id]'");
  } else {
    UploadAlbum($nama_file_unik);
    mysqli_query($conn, "UPDATE album SET jdl_album  = '$_POST[jdl_album]',
                                   album_seo = '$album_seo',
                                   gbr_album    = '$nama_file_unik'   
                             WHERE id_album = '$_POST[id]'");
  }
  header('location:../../media.php?module=' . $module);
}
