<?php
$aksi = "modul/mod_poling/aksi_poling.php";
switch ($aksi) {
  // Tampil Modul
  default:
    echo "<h2>Poling</h2>
          <input type=button value='Tambah Poling' onclick=\"window.location.href='?module=poling&act=tambahpoling';\">
          <table>
          <tr><th>no</th><th>pilihan</th><th>rating</th><th>aktif</th><th>aksi</th></tr>";

    $no = 1;
    $tampil = mysqli_query($conn, "SELECT * FROM poling ORDER BY id_poling DESC");
    while ($r = mysqli_fetch_array($tampil)) {
      echo "<tr>
            <td>$no</td>
            <td>$r[pilihan]</td>
            <td align=center>$r[rating]</td>
            <td align=center>$r[aktif]</td>
            <td><a href=?module=poling&act=editpoling&id=$r[id_poling]>Edit</a> | 
	              <a href=$aksi?module=poling&act=hapus&id=$r[id_poling]>Hapus</a>
            </td></tr>";
      $no++;
    }
    echo "</table>";
    break;

  case "tambahpoling":
    echo "<h2>Tambah Poling</h2>
          <form method=POST action='$aksi?module=poling&act=input'>
          <table>
          <tr><td>Pilihan</td> <td> : <input type=text name='pilihan'></td></tr>
          <tr><td>Aktif</td>   <td> : <input type=radio name='aktif' value='Y' checked>Y 
                                         <input type=radio name='aktif' value='N'>N  </td></tr>
          <tr><td colspan=2><input type=submit value=Simpan>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
    break;

  case "editpoling":
    $edit = mysqli_query($conn, "SELECT * FROM poling WHERE id_poling='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);

    echo "<h2>Edit Poling</h2>
          <form method=POST action=$aksi?module=poling&act=update>
          <input type=hidden name=id value='$r[id_poling]'>
          <table>
          <tr><td>Pilihan</td> <td> : <input type=text name='pilihan' value='$r[pilihan]'></td></tr>";
    if ($r['aktif'] == 'Y') {
      echo "<tr><td>Aktif</td> <td> : <input type=radio name='aktif' value='Y' checked>Y  
                                      <input type=radio name='aktif' value='N'> N</td></tr>";
    } else {
      echo "<tr><td>Aktif</td> <td> : <input type=radio name='aktif' value='Y'>Y  
                                      <input type=radio name='aktif' value='N' checked>N</td></tr>";
    }

    echo "<tr><td colspan=2><input type=submit value=Update>
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
    break;
}
