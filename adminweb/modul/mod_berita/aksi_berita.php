<?php
session_start();
include "../../../config/koneksi.php";
include "../../../config/library.php";
include "../../../config/fungsi_thumb.php";
include "../../../config/fungsi_seo.php";

$module = $_GET['module'];
$act = $_GET['act'];

// Hapus berita
if ($module == 'berita' and $act == 'hapus') {
  mysqli_query($conn, "DELETE FROM berita WHERE id_berita='$_GET[id]'");
  header('location:../../media.php?module=' . $module);
}

// Input berita
elseif ($module == 'berita' and $act == 'input') {
  $lokasi_file    = $_FILES['fupload']['tmp_name'];
  $tipe_file      = $_FILES['fupload']['type'];
  $nama_file      = $_FILES['fupload']['name'];
  $acak           = rand(1, 99);
  $nama_file_unik = $acak . $nama_file;

  if (!empty($_POST['tag_seo'])) {
    $tag_seo = $_POST['tag_seo'];
    $tag = implode(',', $tag_seo);
  }
  $judul_seo      = seo_title($_POST['judul']);

  // Apabila ada gambar yang diupload
  if (!empty($lokasi_file)) {
    UploadImage($nama_file_unik);

    mysqli_query($conn, "INSERT INTO berita(judul,
                                    judul_seo,
                                    id_kategori,
                                    username,
                                    isi_berita,
                                    jam,
                                    tanggal,
                                    hari,
                                    tag, 
                                    gambar) 
                            VALUES('$_POST[judul]',
                                   '$judul_seo',
                                   '$_POST[kategori]',
                                   '$_SESSION[namauser]',
                                   '$_POST[isi_berita]',
                                   '$jam_sekarang',
                                   '$tgl_sekarang',
                                   '$hari_ini',
                                   '$tag',
                                   '$nama_file_unik')");
  } else {
    mysqli_query($conn, "INSERT INTO berita(judul,
                                    judul_seo, 
                                    id_kategori,
                                    username,
                                    isi_berita,
                                    jam,
                                    tanggal,
                                    tag, 
                                    hari) 
                            VALUES('$_POST[judul]',
                                   '$judul_seo',
                                   '$_POST[kategori]',
                                   '$_SESSION[namauser]',
                                   '$_POST[isi_berita]',
                                   '$jam_sekarang',
                                   '$tgl_sekarang',
                                   '$tag',
                                   '$hari_ini')");
  }

  $jml = count($tag_seo);
  for ($i = 0; $i < $jml; $i++) {
    mysqli_query($conn, "UPDATE tag SET count=count+1 WHERE tag_seo='$tag_seo[$i]'");
  }
  header('location:../../media.php?module=' . $module);
}

// Update berita
elseif ($module == 'berita' and $act == 'update') {
  $lokasi_file    = $_FILES['fupload']['tmp_name'];
  $tipe_file      = $_FILES['fupload']['type'];
  $nama_file      = $_FILES['fupload']['name'];
  $acak           = rand(1, 99);
  $nama_file_unik = $acak . $nama_file;

  if (!empty($_POST['tag_seo'])) {
    $tag_seo = $_POST['tag_seo'];
    $tag = implode(',', $tag_seo);
  }

  $judul_seo      = seo_title($_POST['judul']);

  // Apabila gambar tidak diganti
  if (empty($lokasi_file)) {
    mysqli_query($conn, "UPDATE berita SET judul       = '$_POST[judul]',
                                   judul_seo   = '$judul_seo', 
                                   id_kategori = '$_POST[kategori]',
                                   tag         = '$tag',
                                   isi_berita  = '$_POST[isi_berita]'  
                             WHERE id_berita   = '$_POST[id]'");
  } else {
    UploadImage($nama_file_unik);
    mysqli_query($conn, "UPDATE berita SET judul       = '$_POST[judul]',
                                   judul_seo   = '$judul_seo', 
                                   id_kategori = '$_POST[kategori]',
                                   tag         = '$tag',
                                   isi_berita  = '$_POST[isi_berita]',
                                   gambar      = '$nama_file_unik'   
                             WHERE id_berita   = '$_POST[id]'");
  }
  header('location:../../media.php?module=' . $module);
}
