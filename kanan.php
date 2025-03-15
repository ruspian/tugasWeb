<script language="JavaScript" type="text/javascript">
  function addSmiley(textToAdd) {
    document.formshout.pesan.value += textToAdd;
    document.formshout.pesan.focus();
  }
</script>


<?php
// RSS
echo "<p align=center><a href=rss.xml target=_blank><img src=$f[folder]/images/rssku.jpg border=0 /></a><br />
      <a href=rss.xml target=_blank>Langganan RSS</a></p>
      <hr color=#e0cb91 noshade=noshade /><br />";

// Form indeks berita
echo "<img src=$f[folder]/images/indeksberita.jpg /><br /><br />
      <form method=POST action='indeks-berita.html'>";
combotgl(1, 31, 'tanggal', $tgl_skrg);
echo " / ";
combobln(1, 12, 'bulan', $bln_sekarang);
echo " / ";
combothn(2000, $thn_sekarang, 'tahun', $thn_sekarang);
echo "<br /><input type=submit value=Go />
      </form>
      <hr color=#e0cb91 noshade=noshade /><br />";

// Kalender
echo "<img src='$f[folder]/images/kalender.jpg' /><p align=center>";

$tgl_skrg = date("d");
$bln_skrg = date("n");
$thn_skrg = date("Y");

echo buatkalender($tgl_skrg, $bln_skrg, $thn_skrg);

echo "</p><hr color=#e0cb91 noshade=noshade /><br />";


// Statistik user
echo "<img src='$f[folder]/images/statistik.jpg' /><br />";

$ip      = $_SERVER['REMOTE_ADDR']; // Mendapatkan IP komputer user
$tanggal = date("Ymd"); // Mendapatkan tanggal sekarang
$waktu   = time(); // 

// Mencek berdasarkan IPnya, apakah user sudah pernah mengakses hari ini 
$s = mysqli_query($conn, "SELECT * FROM statistik WHERE ip='$ip' AND tanggal='$tanggal'");
// Kalau belum ada, simpan data user tersebut ke database
if (mysqli_num_rows($s) == 0) {
  mysqli_query($conn, "INSERT INTO statistik(ip, tanggal, hits, online) VALUES('$ip','$tanggal','1','$waktu')");
} else {
  mysqli_query($conn, "UPDATE statistik SET hits=hits+1, online='$waktu' WHERE ip='$ip' AND tanggal='$tanggal'");
}

$pengunjung_query = mysqli_query($conn, "SELECT COUNT(DISTINCT ip) AS total FROM statistik WHERE tanggal='$tanggal'");
$pengunjung_data = mysqli_fetch_assoc($pengunjung_query);
$pengunjung = $pengunjung_data['total'];

$totalpengunjung_query = mysqli_query($conn, "SELECT COUNT(hits) AS total FROM statistik");
$totalpengunjung_data = mysqli_fetch_assoc($totalpengunjung_query);
$totalpengunjung = $totalpengunjung_data['total'];

$hits_query = mysqli_query($conn, "SELECT SUM(hits) AS total FROM statistik WHERE tanggal='$tanggal'");
$hits_data = mysqli_fetch_assoc($hits_query);
$hits = $hits_data['total'];

$totalhits_query = mysqli_query($conn, "SELECT SUM(hits) AS total FROM statistik");
$totalhits_data = mysqli_fetch_assoc($totalhits_query);
$totalhits = $totalhits_data['total'];

$tothitsgbr_query = mysqli_query($conn, "SELECT SUM(hits) AS total FROM statistik");
$tothitsgbr_data = mysqli_fetch_assoc($tothitsgbr_query);
$tothitsgbr = $tothitsgbr_data['total'];

$bataswaktu = time() - 300;

$pengunjungonline_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM statistik WHERE online > '$bataswaktu'");
$pengunjungonline_data = mysqli_fetch_assoc($pengunjungonline_query);
$pengunjungonline = $pengunjungonline_data['total'];


$path = "counter/";
$ext = ".png";

$tothitsgbr = sprintf("%06d", $tothitsgbr);
for ($i = 0; $i <= 9; $i++) {
  $tothitsgbr = str_replace($i, "<img src='$path$i$ext' alt='$i'>", $tothitsgbr);
}

echo "<p align=center>$tothitsgbr </p>
      <img src=counter/hariini.png> Pengunjung hari ini : $pengunjung <br>
      <img src=counter/total.png> Total pengunjung    : $totalpengunjung <br><br>
      <img src=counter/hariini.png> Hits hari ini    : $hits <br>
      <img src=counter/total.png> Total Hits       : $totalhits <br><br>
      <img src=counter/online.png> Pengunjung Online: $pengunjungonline
      <hr color=#e0cb91 noshade=noshade /><br />";


// Polling
echo "<img src='$f[folder]/images/polling.jpg' /><br /><br />";
echo "<b>Pilih Browser Favorit Anda?</b> <br /><br />";

echo "<form method=POST action='hasil-poling.html'>";

$poling = mysqli_query($conn, "SELECT * FROM poling WHERE aktif='Y'");
while ($p = mysqli_fetch_assoc($poling)) {
  echo "<input type=radio name=pilihan value='$p[id_poling]' />$p[pilihan]<br />";
}
echo "<p align=center><input type=submit value=Vote /></p>
      </form>
      <p align=center><a href=lihat-poling.html>Lihat Hasil Poling</a></p>
      <hr color=#e0cb91 noshade=noshade /><br />";


// Shoutbox
echo "<img src='$f[folder]/images/shoutbox.jpg' /><br /><br />";
echo "<iframe src='shoutbox.php' width=160 height=250 border=1 solid></iframe><br /><br />";
echo "  <table class=shout width=100%>
        <form name=formshout action=simpanshoutbox.php method=POST>
        <tr><td>Nama</td><td> : <input class=shout type=text name=nama size=21></td></tr>
        <tr><td>Website</td><td> : <input class=shout type=text name=website size=21></td></tr>
        <tr><td valign=top>Pesan</td><td> <textarea class=shout name='pesan' style='width: 115px; height: 35px;'></textarea></td></tr>";
?>
<tr>
  <td colspan=2>
    <a onClick="addSmiley(':-)')"><img src='smiley/1.gif'></a>
    <a onClick="addSmiley(':-(')"><img src='smiley/2.gif'></a>
    <a onClick="addSmiley(';-)')"><img src='smiley/3.gif'></a>
    <a onClick="addSmiley(';-D')"><img src='smiley/4.gif'></a>
    <a onClick="addSmiley(';;-)')"><img src='smiley/5.gif'></a>
    <a onClick="addSmiley('<:D>')"><img src='smiley/6.gif'></a>
  </td>
</tr>
<?php
echo "<tr><td colspan=2><input class=shout type=submit name=submit value=Kirim><input class=shout type=reset name=reset value=Reset></td></tr>
        </form></table>";

echo "<hr color=#e0cb91 noshade=noshade /><br />";

// Banner
$banner = mysqli_query($conn, "SELECT * FROM banner 
                    ORDER BY id_banner DESC LIMIT 4");
while ($b = mysqli_fetch_array($banner)) {
  echo "<p align=center><a href=$b[url] target='_blank' title='$b[judul]'><img src='foto_banner/$b[gambar]' border=0></a></p>";
}
?>
<style>
  .tr_judul {
    font-weight: bold;
    text-align: center;
    background: #d0d0d0;
  }

  .tr_terang {
    text-align: center;
    background: #f0f0f0;
  }

  .tabel_data {
    background: #d0d0d0;
    color: #000000;
  }
</style>