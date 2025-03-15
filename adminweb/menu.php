<?php
include "../config/koneksi.php";

if ($_SESSION['leveluser'] == 'admin') {
  $sql = mysqli_query($conn, "select * from modul where aktif='Y' order by urutan");
} else {
  $sql = mysqli_query($conn, "select * from modul where status='user' and aktif='Y' order by urutan");
}
while ($m = mysqli_fetch_array($sql)) {
  echo "<li><a href='$m[link]'>&#187; $m[nama_modul]</a></li>";
}
