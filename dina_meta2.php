<?php
$sql = mysqli_query($conn, "select tag from berita where id_berita='$_GET[id]'");
$j   = mysqli_fetch_array($sql);

if (isset($_GET['id'])) {
	echo "$j[tag]";
} else {
	echo "lokomedia, bukulokomedia, , toko online, buku komputer, trik, tutorial, konsultasi, distro kaos, php";
}
