<?php
function GetCheckboxes($table, $key, $Label, $Nilai = '')
{
  global $conn; // Menjadikan $conn bisa diakses dalam fungsi

  $s = "SELECT * FROM $table ORDER BY nama_tag";
  $r = mysqli_query($conn, $s);

  if (!$r) {
    die("Query gagal: " . mysqli_error($conn));
  }

  $_arrNilai = explode(',', $Nilai);
  $str = '';

  while ($w = mysqli_fetch_array($r)) {
    $_ck = (array_search($w[$key], $_arrNilai) === false) ? '' : 'checked';
    $str .= "<input type='checkbox' name='" . $key . "[]' value='{$w[$key]}' $_ck>{$w[$Label]} ";
  }

  return $str;
}

$aksi = "modul/mod_berita/aksi_berita.php";
switch ($aksi) {
  // Tampil Berita
  default:
    echo "<h2>Berita</h2>
          <input type=button value='Tambah Berita' onclick=\"window.location.href='?module=berita&act=tambahberita';\">
          <table>
          <tr><th>no</th><th>judul</th><th>tgl. posting</th><th>aksi</th></tr>";

    $p      = new Paging;
    $batas  = 10;
    $posisi = $p->cariPosisi($batas);

    if ($_SESSION['leveluser'] == 'admin') {
      $tampil = mysqli_query($conn, "SELECT * FROM berita ORDER BY id_berita DESC LIMIT $posisi,$batas");
    } else {
      $tampil = mysqli_query($conn, "SELECT * FROM berita 
                           WHERE username='$_SESSION[namauser]'       
                           ORDER BY id_berita DESC LIMIT $posisi,$batas");
    }

    $no = $posisi + 1;
    while ($r = mysqli_fetch_array($tampil)) {
      $tgl_posting = tgl_indo($r['tanggal']);
      echo "<tr><td>$no</td>
                <td>$r[judul]</td>
                <td>$tgl_posting</td>
		            <td><a href=?module=berita&act=editberita&id=$r[id_berita]>Edit</a> | 
		                <a href=$aksi?module=berita&act=hapus&id=$r[id_berita]>Hapus</a></td>
		        </tr>";
      $no++;
    }
    echo "</table>";

    if ($_SESSION['leveluser'] == 'admin') {
      $jmldata = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM berita"));
    } else {
      $jmldata = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM berita WHERE username='$_SESSION[namauser]'"));
    }
    $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
    $linkHalaman = $p->navHalaman($_GET['halaman'], $jmlhalaman);

    echo "<div id=paging>Hal: $linkHalaman</div><br>";

    break;

  case "tambahberita":
    echo "<h2>Tambah Berita</h2>
          <form method=POST action='$aksi?module=berita&act=input' enctype='multipart/form-data'>
          <table>
          <tr><td width=70>Judul</td>     <td> : <input type=text name='judul' size=60></td></tr>
          <tr><td>Kategori</td>  <td> : 
          <select name='kategori'>
            <option value=0 selected>- Pilih Kategori -</option>";
    $tampil = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");
    while ($r = mysqli_fetch_array($tampil)) {
      echo "<option value=$r[id_kategori]>$r[nama_kategori]</option>";
    }
    echo "</select></td></tr>
          <tr><td>Isi Berita</td>  <td> <textarea name='isi_berita' style='width: 450px; height: 350px;'></textarea></td></tr>
          <tr><td>Gambar</td>      <td> : <input type=file name='fupload' size=40> 
                                          <br>Tipe gambar harus JPG/JPEG dan ukuran lebar maks: 400 px</td></tr>";

    $tag = mysqli_query($conn, "SELECT * FROM tag ORDER BY tag_seo");
    echo "<tr><td>Tag (Label)</td><td> ";
    while ($t = mysqli_fetch_array($tag)) {
      echo "<input type=checkbox value='$t[tag_seo]' name=tag_seo[]>$t[nama_tag] ";
    }

    echo "</td></tr>
          <tr><td colspan=2><input type=submit value=Simpan>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
    break;

  case "editberita":
    $edit = mysqli_query($conn, "SELECT * FROM berita WHERE id_berita='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);

    echo "<h2>Edit Berita</h2>
          <form method=POST enctype='multipart/form-data' action=$aksi?module=berita&act=update>
          <input type=hidden name=id value=$r[id_berita]>
          <table>
          <tr><td width=70>Judul</td>     <td> : <input type=text name='judul' size=60 value='$r[judul]'></td></tr>
          <tr><td>Kategori</td>  <td> : <select name='kategori'>";

    $tampil = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");
    if ($r['id_kategori'] == 0) {
      echo "<option value=0 selected>- Pilih Kategori -</option>";
    }

    while ($w = mysqli_fetch_array($tampil)) {
      if ($r['id_kategori'] == $w['id_kategori']) {
        echo "<option value=$w[id_kategori] selected>$w[nama_kategori]</option>";
      } else {
        echo "<option value=$w[id_kategori]>$w[nama_kategori]</option>";
      }
    }
    echo "</select></td></tr>
          <tr><td>Isi Berita</td>   <td> <textarea name='isi_berita' style='width: 450px; height: 350px;'>$r[isi_berita]</textarea></td></tr>
          <tr><td>Gambar</td>       <td> :  ";
    if ($r['gambar'] != '') {
      echo "<img src='../foto_berita/small_$r[gambar]'>";
    }
    echo "</td></tr>
          <tr><td>Ganti Gbr</td>    <td> : <input type=file name='fupload' size=30> *)</td></tr>
          <tr><td colspan=2>*) Apabila gambar tidak diubah, dikosongkan saja.</td></tr>";

    $d = GetCheckboxes('tag', 'tag_seo', 'nama_tag', $r['tag']);

    echo "<tr><td>Tag (Label)</td><td> $d </td></tr>";

    echo  "<tr><td colspan=2><input type=submit value=Update>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
         </table></form>";
    break;
}
