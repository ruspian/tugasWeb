<?php
$aksi = "modul/mod_profil/aksi_profil.php";
$act = isset($_GET['act']) ? $_GET['act'] : ""; 

switch ($act) {
  // Tampil Komentar
  default:
    $sql  = mysqli_query($conn, "SELECT * FROM modul WHERE id_modul='37'");
    $r    = mysqli_fetch_array($sql);

    echo "<h2>Profil</h2>
          <form method=POST enctype='multipart/form-data' action=$aksi?module=profil&act=update>
          <input type=hidden name=id value=$r[id_modul]>
          <table>
          <tr><td><img src=../foto_banner/$r[gambar]></td></tr>
         <tr><td>Ganti Foto : <input type=file size=30 name=fupload></td></tr>
         <tr><td><textarea name='isi' style='width: 500px; height: 300px;'>$r[static_content]</textarea></td></tr>
         <tr><td><input type=submit value=Update></td></tr>
         </form></table>";
    break;
}
