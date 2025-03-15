<?php
$sql = mysqli_query($conn, "select judul from berita where id_berita='$_GET[id]'");
$j   = mysqli_fetch_array($sql);

if (isset($_GET['id'])) {
	echo "$j[judul]";
} else {
	echo "lokomedia adalah penerbit buku-buku komputer khususnya di bidang pemrograman web dan internet.";
}
