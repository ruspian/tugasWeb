<?php
$aksi="modul/mod_album/aksi_album.php";
switch($aksi){
  // Tampil Album
  default:
    echo "<h2>Album</h2>
          <input type=button value='Tambah Album' 
          onclick=\"window.location.href='?module=album&act=tambahalbum';\">
          <table>
          <tr><th>no</th><th>judul album</th><th>aksi</th></tr>"; 
    $tampil=mysqli_query($conn, "SELECT * FROM album ORDER BY id_album DESC");
    $no=1;
    while ($r=mysqli_fetch_array($tampil)){
       echo "<tr><td>$no</td>
             <td>$r[jdl_album]</td>
             <td><a href=?module=album&act=editalbum&id=$r[id_album]>Edit</a>
             </td></tr>";
      $no++;
    }
    echo "</table>";
    break;
  
  // Form Tambah Album
  case "tambahalbum":
    echo "<h2>Tambah Album</h2>
          <form method=POST action='$aksi?module=album&act=input' enctype='multipart/form-data'>
          <table>
          <tr><td>Judul Album</td><td> : <input type=text name='jdl_album'></td></tr>
          <tr><td>Gambar</td><td> : <input type=file name='fupload' size=40></td></tr>
          <tr><td colspan=2><input type=submit name=submit value=Simpan>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
     break;
  
  // Form Edit Album  
  case "editalbum":
    $edit=mysqli_query($conn, "SELECT * FROM album WHERE id_album='$_GET[id]'");
    $r=mysqli_fetch_array($edit);

    echo "<h2>Edit Album</h2>
          <form method=POST enctype='multipart/form-data' action=$aksi?module=album&act=update>
          <input type=hidden name=id value='$r[id_album]'>
          <table>
          <tr><td>Judul Album</td><td> : <input type=text name='jdl_album' value='$r[jdl_album]'></td></tr>
          <tr><td>Gambar</td><td>    : <img src='../img_album/kecil_$r[gbr_album]'></td></tr>
          <tr><td>Ganti Gbr</td><td> : <input type=file name='fupload' size=30> *)</td></tr>
          <tr><td colspan=2><input type=submit value=Update>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
    break;  
}
?>
