<?php
$aksi = "modul/mod_galerifoto/aksi_galerifoto.php";
switch ($aksi) {
  // Tampil Galeri Foto
  default:
    echo "<h2>Galeri Foto</h2>
          <input type=button value='Tambah Galeri Foto' onclick=\"window.location.href='?module=galerifoto&act=tambahgalerifoto';\">
          <table>
          <tr><th>no</th><th>judul foto</th><th>album</th><th>aksi</th></tr>";

    $p      = new Paging;
    $batas  = 10;
    $posisi = $p->cariPosisi($batas);

    $tampil = mysqli_query($conn, "SELECT * FROM gallery,album WHERE gallery.id_album=album.id_album ORDER BY id_gallery DESC LIMIT $posisi,$batas");

    $no = $posisi + 1;
    while ($r = mysqli_fetch_array($tampil)) {
      echo "<tr><td>$no</td>
                <td>$r[jdl_gallery]</td>
                <td>$r[jdl_album]</td>
		            <td><a href=?module=galerifoto&act=editgalerifoto&id=$r[id_gallery]>Edit</a> | 
		                <a href=$aksi?module=galerifoto&act=hapus&id=$r[id_gallery]>Hapus</a></td>
		        </tr>";
      $no++;
    }
    echo "</table>";

    $jmldata = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM gallery"));

    $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
    $linkHalaman = $p->navHalaman($_GET['halaman'], $jmlhalaman);

    echo "<div id=paging>Hal: $linkHalaman</div><br>";

    break;

  case "tambahgalerifoto":
    echo "<h2>Tambah Galeri Foto</h2>
          <form method=POST action='$aksi?module=galerifoto&act=input' enctype='multipart/form-data'>
          <table>
          <tr><td width=70>Judul Foto</td>     <td> : <input type=text name='jdl_gallery' size=60></td></tr>
          <tr><td>Album</td>  <td> : 
          <select name='album'>
            <option value=0 selected>- Pilih Album -</option>";
    $tampil = mysqli_query($conn, "SELECT * FROM album ORDER BY jdl_album");
    while ($r = mysqli_fetch_array($tampil)) {
      echo "<option value=$r[id_album]>$r[jdl_album]</option>";
    }
    echo "</select></td></tr>
          <tr><td>Keterangan</td>  <td> <textarea name='keterangan' style='width: 250px; height: 50px;'></textarea></td></tr>
          <tr><td>Gambar</td>      <td> : <input type=file name='fupload' size=40> 
                                          <br>Tipe gambar harus JPG/JPEG</td></tr>
          </td></tr>
          <tr><td colspan=2><input type=submit value=Simpan>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
    break;

  case "editgalerifoto":
    $edit = mysqli_query($conn, "SELECT * FROM gallery WHERE id_gallery='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);

    echo "<h2>Edit Galeri Foto</h2>
          <form method=POST enctype='multipart/form-data' action=$aksi?module=galerifoto&act=update>
          <input type=hidden name=id value=$r[id_gallery]>
          <table>
          <tr><td width=70>Judul Foto</td>     <td> : <input type=text name='jdl_gallery' size=60 value='$r[judul]'></td></tr>
          <tr><td>Album</td>  <td> : <select name='album'>";

    $tampil = mysqli_query($conn, "SELECT * FROM album ORDER BY jdl_album");
    if ($r['id_album'] == 0) {
      echo "<option value=0 selected>- Pilih Album -</option>";
    }

    while ($w = mysqli_fetch_array($tampil)) {
      if ($r['id_album'] == $w['id_album']) {
        echo "<option value=$w[id_album] selected>$w[jdl_album]</option>";
      } else {
        echo "<option value=$w[id_album]>$w[jdl_album]</option>";
      }
    }
    echo "</select></td></tr>
          <tr><td>Keterangan</td>   <td> <textarea name='keterangan' style='width: 250px; height: 50px;'>$r[keterangan]</textarea></td></tr>
          <tr><td>Gambar</td>       <td> :  ";
    if ($r['gbr_gallery'] != '') {
      echo "<img src='../img_galeri/kecil_$r[gambar]'>";
    }
    echo "</td></tr>
          <tr><td>Ganti Gbr</td>    <td> : <input type=file name='fupload' size=30> *)</td></tr>
          <tr><td colspan=2>*) Apabila gambar tidak diubah, dikosongkan saja.</td></tr>
          <tr><td colspan=2><input type=submit value=Update>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
         </table></form>";
    break;
}
