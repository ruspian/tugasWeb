<script language="javascript">
  function validasi(form) {
    if (form.nama_komentar.value == "") {
      alert("Anda belum mengisikan Nama.");
      form.nama_komentar.focus();
      return (false);
    }

    if (form.isi_komentar.value == "") {
      alert("Anda belum mengisikan komentar.");
      form.isi_komentar.focus();
      return (false);
    }
    return (true);
  }
</script>

<script language="JavaScript">
  function bukajendela(url) {
    window.open(url, "window_baru", "width=1000,height=700,left=120,top=10,resizable=1,scrollbars=1");
  }
</script>

<?php
// Skrip javascript diatas untuk melakukan validasi data untuk pengisi komentar agar tidak mengosongkan nama dan isi komentar.


// Halaman utama (Home)
if ($_GET['module'] == 'home') {
    // Query untuk mendapatkan berita terbaru dengan jumlah komentar
    $terkini = mysqli_query($conn, "SELECT COUNT(komentar.id_komentar) as jml, judul, judul_seo, jam, 
                                    berita.id_berita, hari, tanggal, gambar, isi_berita    
                                    FROM berita 
                                    LEFT JOIN komentar 
                                    ON berita.id_berita = komentar.id_berita AND aktif='Y' 
                                    GROUP BY berita.id_berita 
                                    ORDER BY berita.id_berita DESC 
                                    LIMIT 6");

    if (!$terkini) {
        die("Query Error: " . mysqli_error($conn)); // Debugging error query
    }

    while ($t = mysqli_fetch_array($terkini)) {
    $tgl = tgl_indo($t['tanggal']);
    $isi_berita = strip_tags($t["isi_berita"]); // Hapus tag HTML
    $isi = substr($isi_berita, 0, 220);
    $isi = substr($isi, 0, strrpos($isi, " ")); // Potong sampai spasi terakhir

    echo "<div class='card mb-3' style='max-width: 660px;'>
            <div class='row g-0'>
                <div class='col-md-4'>";
                
    // Tampilkan gambar jika ada
    if (!empty($t['gambar'])) {
        echo "<img src='foto_berita/small_{$t['gambar']}' class='img-fluid mt-4 ms-4 rounded-start' alt='Gambar Berita'>";
    } else {
        echo "<img src='https://via.placeholder.com/150' class='img-fluid rounded-start' alt='Gambar Default'>";
    }

    echo "    </div>
                <div class='col-md-8'>
                    <div class='card-body'>
                        <h5 class='card-title'>
                            <a href='berita-{$t['id_berita']}-{$t['judul_seo']}.html' class='text-decoration-none'>
                                " . htmlspecialchars($t['judul']) . "
                            </a>
                        </h5>
                        <p class='card-text'>" . htmlspecialchars($isi) . "...</p>
                        <p class='card-text'>
                            <small class='text-body-secondary'>
                                <p><i class='fa-solid fa-clock'></i> {$t['hari']}, $tgl - {$t['jam']} WIB</p> 
                                
                            </small>
                        </p>
                        <a href='berita-{$t['id_berita']}-{$t['judul_seo']}.html' class='btn btn-success btn-sm text-white'>Selengkapnya <span class='badge bg-secondary'>{$t['jml']} Komentar</span></a>
                        
                    </div>
                </div>
            </div>
        </div>";
  }


  // Tampilkan 7 judul berita sebelumnya 
  echo "<p class='fs-5'><i class='fa-solid fa-newspaper'></i> <b>Berita Lainnya</b></p>";
  $sebelum = mysqli_query($conn, "SELECT * FROM berita 
                        ORDER BY id_berita DESC LIMIT 6,7");
  while ($s = mysqli_fetch_array($sebelum)) {
    echo "<li><a href=berita-$s[id_berita]-$s[judul_seo].html>$s[judul]</a></li>";
  }
  echo "</ul><hr color=#e0cb91 noshade=noshade /><br />";

  echo "<img src=$f[folder]/images/galeri_foto.jpg>";

  // Tentukan kolom
  $col = 3;

  $g = mysqli_query($conn, "SELECT * FROM gallery ORDER BY id_gallery DESC LIMIT 6");

  echo "<table><tr>";
  $cnt = 0;
  while ($w = mysqli_fetch_array($g)) {
    if ($cnt >= $col) {
      echo "</tr><tr>";
      $cnt = 0;
    }
    $cnt++;
    echo "<td align=center valign=top><br>
    <a href=album-$w[id_album].html>
    <img class=img2 src=img_galeri/kecil_$w[gbr_gallery] border=0 width=120 height=90><br />
    $w[jdl_gallery]</a><br /></td>";
  }
  echo "</tr></table>";
}

elseif ($_GET["module"] == "detailberita") {
    // Amankan ID dari GET
    $id = intval($_GET["id"]);

    // Ambil detail berita
    $query = "SELECT berita.*, users.nama_lengkap, kategori.nama_kategori, kategori.kategori_seo 
              FROM berita 
              JOIN users ON users.username = berita.username 
              JOIN kategori ON kategori.id_kategori = berita.id_kategori 
              WHERE id_berita = $id";

    $detail = mysqli_query($conn, $query);

    // Jika query gagal
    if (!$detail) {
        die("Query Error: " . mysqli_error($conn));
    }

    $d = mysqli_fetch_assoc($detail);

    // Jika berita ditemukan
    if ($d) {
        $tgl = tgl_indo($d["tanggal"]);
        $baca = $d["dibaca"] + 1;

        echo "<span class=date>{$d['hari']}, $tgl - {$d['jam']} WIB</span><br />";
        echo "<span class=judul>{$d['judul']}</span><br />";
        echo "<span class=posting>Diposting oleh: <b>{$d['nama_lengkap']}</b><br /> 
              Kategori: <a href=kategori-{$d['id_kategori']}-{$d['kategori_seo']}.html>
              <b>{$d['nama_kategori']}</b></a> - Dibaca: <b>$baca</b> kali</span><br /><br />";

        // **Tampilkan isi berita**
        echo "<p>{$d['isi_berita']}</p>";

        // Update jumlah pembaca
        $updateQuery = "UPDATE berita SET dibaca = dibaca + 1 WHERE id_berita = $id";
        if (!mysqli_query($conn, $updateQuery)) {
            die("Query Update Error: " . mysqli_error($conn));
        }
    } else {
        echo "Berita tidak ditemukan.";
        exit;
    }

    // **Tampilkan berita terkait**
    echo "<img src=$f[folder]/images/berita_terkait.jpg><br /><ul>";

    // Perbaikan pencarian berita terkait
    $tags = explode(",", $d["tag"]);
    $tagConditions = [];

    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $tagConditions[] = "tag LIKE '%" . mysqli_real_escape_string($conn, $tag) . "%'";
        }
    }

    $whereTags = implode(" OR ", $tagConditions);
    if (!empty($whereTags)) {
        $cari = "SELECT * FROM berita WHERE id_berita != $id AND ($whereTags) 
                 ORDER BY id_berita DESC LIMIT 5";
        
        $hasil = mysqli_query($conn, $cari);

        if ($hasil) {
            while ($h = mysqli_fetch_assoc($hasil)) {
                echo "<li><a href=berita-{$h['id_berita']}-{$h['judul_seo']}.html>{$h['judul']}</a></li>";
            }
        }
    }

    echo "</ul>";

    // **Tampilkan jumlah komentar**
    $komentarQuery = "SELECT COUNT(id_komentar) AS jml FROM komentar WHERE id_berita = $id AND aktif='Y'";
    $komentar = mysqli_query($conn, $komentarQuery);
    $k = mysqli_fetch_assoc($komentar);

    echo "<span class=judul><b>{$k['jml']}</b> Komentar :</span><br /><hr color=#e0cb91 noshade=noshade />";

    // **Tampilkan komentar berita**
    $sql = "SELECT * FROM komentar WHERE id_berita = $id AND aktif='Y'";
    $komentarHasil = mysqli_query($conn, $sql);

    if ($komentarHasil && mysqli_num_rows($komentarHasil) > 0) {
        while ($s = mysqli_fetch_assoc($komentarHasil)) {
            $tanggal = tgl_indo($s["tgl"]);
            echo "<span class=komentar>" . ($s["url"] ? "<a href=http://{$s['url']} target=_blank>{$s['nama_komentar']}</a>" : "{$s['nama_komentar']}") . "</span><br />";
            echo "<span class=date>$tanggal - {$s['jam_komentar']} WIB</span><br /><br />";

            // Perbaiki sensor isi komentar
            $isian = nl2br($s["isi_komentar"]);
            $isikan = sensor($isian, $conn);

            // Pastikan fungsi autolink tersedia sebelum dipanggil
            if (file_exists('config/fungsi_autolink.php')) {
                include_once 'config/fungsi_autolink.php';
                if (function_exists('autolink')) {
                    echo autolink($isikan);
                } else {
                    echo $isikan;
                }
            } else {
                echo $isikan;
            }

            echo "<hr color=#e0cb91 noshade=noshade />";
        }
    }

    // **Form tambah komentar**
    echo "<b>Isi Komentar :</b>
        <table width=100% style='border: 1pt dashed #0000CC; padding: 10px;'>
        <form action=simpankomentar.php method=POST onSubmit=\"return validasi(this)\">
        <input type=hidden name=id_berita value=$id>
        <input type=hidden name=id value=$id>
        <tr><td>Nama</td><td> : <input type=text name=nama_komentar size=40></td></tr>
        <tr><td>Website</td><td> : <input type=text name=url size=40></td></tr>
        <tr><td valign=top>Komentar</td><td> <textarea name=isi_komentar style='width: 300px; height: 100px;'></textarea></td></tr>
        <tr><td>&nbsp;</td><td><img src=captcha.php></td></tr>
        <tr><td>&nbsp;</td><td>(Masukkan 6 kode di atas)<br /><input type=text name=kode size=6><br /></td></tr>
        <tr><td>&nbsp;</td><td><input type=submit name=submit value=Kirim></td></tr>
        </form></table><br />";
}


// Modul berita per kategori
elseif ($_GET["module"] == "detailkategori") {
  // Tampilkan nama kategori
  $sq = mysqli_query($conn, "SELECT nama_kategori from kategori where id_kategori=$_GET[id]");
  $n = mysqli_fetch_array($sq);
  echo "<span class=judul_head>&#187; Kategori : <b>$n[nama_kategori]</b></span><br /><br />";

  $p      = new Paging3;
  $batas  = 5;
  $posisi = $p->cariPosisi($batas);

  // Tampilkan daftar berita sesuai dengan kategori yang dipilih
  $sql   = "SELECT * FROM berita WHERE id_kategori=$_GET[id] 
            ORDER BY id_berita DESC LIMIT $posisi,$batas";

  $hasil = mysqli_query($conn, $sql);
  $jumlah = mysqli_num_rows($hasil);
  // Apabila ditemukan berita dalam kategori
  if ($jumlah > 0) {
    while ($r = mysqli_fetch_assoc($hasil)) {
    $tgl = tgl_indo($r["tanggal"]);

    echo "<span class='date'><img src='templates/standar/images/clock.gif'> {$r["hari"]}, $tgl - {$r["jam"]} WIB</span><br />";
    echo "<span class='judul'><a href='berita-{$r["id_berita"]}-{$r["judul_seo"]}.html'>{$r["judul"]}</a></span><br />";

    // Tampilkan gambar jika ada
    if (!empty($r["gambar"])) {
        echo "<span class='image'><img src='foto_berita/small_{$r["gambar"]}' width='110' border='0'></span>";
    }

    // Ambil isi berita hanya 220 karakter
    $isi_berita = nl2br(htmlspecialchars($r["isi_berita"])); // Hindari error HTML
    $isi = substr($isi_berita, 0, 220);
    $isi = substr($isi, 0, strrpos($isi, " ")); // Potong sampai spasi terakhir

    echo "$isi ... <a href='berita-{$r["id_berita"]}-{$r["judul_seo"]}.html'>Selengkapnya</a>
          <br /><hr color='#e0cb91' noshade='noshade' />";
  }


    // Pastikan $_GET["id"] ada dan merupakan angka
    $id_kategori = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

    // Ambil jumlah data dengan kategori tertentu
    $sql = "SELECT * FROM berita WHERE id_kategori = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_kategori);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $jmldata = mysqli_num_rows($result);

    // Tentukan jumlah halaman
    $jmlhalaman = $p->jumlahHalaman($jmldata, $batas);

    // Pastikan $_GET["halkategori"] ada dan merupakan angka
    $halkategori = isset($_GET["halkategori"]) ? intval($_GET["halkategori"]) : 1;

    $linkHalaman = $p->navHalaman($halkategori, $jmlhalaman);

    echo "Hal: $linkHalaman";

  } else {
    echo "Belum ada berita pada kategori ini.";
  }
}


// Modul detail agenda
elseif ($_GET["module"] == "detailagenda") {
    // Validasi dan casting ID ke integer untuk keamanan
    $id_agenda = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

    // Cek apakah ID valid
    if ($id_agenda <= 0) {
        echo "Agenda tidak ditemukan.";
        exit;
    }

    // Query dengan prepared statement 
    $stmt = $conn->prepare("SELECT * FROM agenda WHERE id_agenda = ?");
    $stmt->bind_param("i", $id_agenda);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Agenda tidak ditemukan.";
    } else {
        $d = $result->fetch_assoc();
        $tgl_posting  = tgl_indo($d["tgl_posting"]);
        $tgl_mulai    = tgl_indo($d["tgl_mulai"]);
        $tgl_selesai  = tgl_indo($d["tgl_selesai"]);
        $isi_agenda   = nl2br($d["isi_agenda"]);

        echo "<span class='judul'>{$d['tema']}</span><br />";
        echo "<span class='date'>Diposting tanggal: $tgl_posting</span><br /><br />";
        echo "<b>Topik</b>  : $isi_agenda <br />";
        echo "<b>Tanggal</b> : $tgl_mulai s/d $tgl_selesai <br /><br />";
        echo "<b>Tempat</b> : {$d['tempat']} <br /><br />";
        echo "<b>Pengirim (Contact Person)</b> : {$d['pengirim']} <br />";
    }

    // Tutup statement
    $stmt->close();
}

// Modul hasil pencarian berita 
elseif ($_GET["module"] == "hasilcari") {
    echo "<span class=judul_head>&#187; <b>Hasil Pencarian</b></span><br />";
    
    // Menghilangkan spasi di kiri dan kanan
    $kata = trim($_POST["kata"]);

    // Jika kosong, beri peringatan
    if (empty($kata)) {
        echo "Masukkan kata kunci pencarian!";
        exit;
    }

    // Pisahkan kata pencarian
    $pisah_kata = explode(" ", $kata);
    $jml_kata = count($pisah_kata);

    // Buat query pencarian dinamis
    $cari = "SELECT * FROM berita WHERE ";
    $whereClauses = [];
    foreach ($pisah_kata as $word) {
        $whereClauses[] = "isi_berita LIKE '%" . mysqli_real_escape_string($conn, $word) . "%'";
    }
    $cari .= implode(" OR ", $whereClauses);
    $cari .= " ORDER BY id_berita DESC LIMIT 7";

    // Eksekusi query
    $hasil = mysqli_query($conn, $cari);
    if (!$hasil) {
        die("Query Error: " . mysqli_error($conn));
    }

    $ketemu = mysqli_num_rows($hasil);

    if ($ketemu > 0) {
        echo "<p>Ditemukan <b>$ketemu</b> berita dengan kata <font color=red><b>$kata</b></font> :</p>";
        while ($t = mysqli_fetch_assoc($hasil)) {
            echo "<span class=judul><a href=berita-{$t['id_berita']}-{$t['judul_seo']}.html>{$t['judul']}</a></span><br />";

            // Menampilkan ringkasan isi berita
            $isi_berita = nl2br($t["isi_berita"]);
            $isi = substr($isi_berita, 0, 150);
            $isi = substr($isi, 0, strrpos($isi, " ")); // Hindari pemotongan kata

            echo "$isi ... <a href=berita-{$t['id_berita']}-{$t['judul_seo']}.html>Selengkapnya</a>
                  <br /><hr color=#e0cb91 noshade=noshade />";
        }
    } else {
        echo "Tidak ditemukan berita dengan kata <b>$kata</b>";
    }
}

// Modul indeks berita
elseif ($_GET["module"] == "indeksberita") {
  echo "<span class=judul_head>&#187; <b>Hasil Indeks Berita</b></span><br />";

  $format_mysql = $_POST["tahun"]  -  $_POST["bulan"]  -  $_POST["tanggal"];
  $format_indo = tgl_indo($_POST["tahun"]  -  $_POST["bulan"]  -  $_POST["tanggal"]);

  // Hanya mengindeks berita, apabila diperlukan bisa ditambahkan utk menngindeks agenda, dll
  $cari   = mysqli_query($conn, "SELECT * FROM berita WHERE tanggal=$format_mysql");
  $jumlah = mysqli_num_rows($cari);
  // Apabila berita ditemukan sesuai dengan tanggal yang diinginkan 
  if ($jumlah > 0) {
    echo "<br />Ditemukan <b>$jumlah</b> berita pada tanggal <b>$format_indo</b> : <ul>";
    while ($r = mysqli_fetch_array($cari)) {
      echo "<p><li><a href=berita-$r[id_berita]-$r[judul_seo].html>$r[judul]</a></li></p>";
    }
    echo "</ul>";
  } else {
    echo "<br />Tidak ada berita pada tanggal <b>$format_indo</b>";
  }
}


// Modul hasil poling
elseif ($_GET["module"] == "hasilpoling") {
  if (isset($_COOKIE["poling"])) {
    echo "Sorry, anda sudah pernah melakukan voting terhadap poling ini.";
  } else {
    // membuat cookie dengan nama poling
    // cookie akan secara otomatis terhapus dalam waktu 24 jam
    setcookie("poling", "sudah poling", time() + 3600 * 24);

    echo "<span class=posting>&#187; <b>Hasil Poling</b></span><br /><br />";

    $u = mysqli_query($conn, "UPDATE poling SET rating=rating+1 WHERE id_poling=$_POST[pilihan]");

    echo "<p align=center>Terimakasih atas partisipasi Anda mengikuti poling kami<br /><br />
        Hasil poling saat ini: </p><br />";

    echo "<table width=100% style=border: 1pt dashed #0000CC;padding: 10px;>";

    $jml = mysqli_query($conn, "SELECT SUM(rating) as jml_vote FROM poling WHERE aktif=Y");
    $j = mysqli_fetch_array($jml);

    $jml_vote = $j["jml_vote"];

    $sql = mysqli_query($conn, "SELECT * FROM poling WHERE aktif=Y");

    while ($s = mysqli_fetch_array($sql)) {

      $prosentase = sprintf("%2.1f", (($s["rating"] / $jml_vote) * 100));
      $gbr_vote   = $prosentase * 3;

      echo "<tr><td width=120>$s[pilihan] ($s[rating]) </td><td> 
          <img src=$f[folder]/images/blue.png width=$gbr_vote height=18 border=0> $prosentase % 
          </td></tr>";
    }
    echo "</table>
        <p>Jumlah Voting: <b>$jml_vote</b></p>";
  }
}


// Modul hasil poling
elseif ($_GET["module"] == "lihatpoling") {
    echo "<span class='posting'>&#187; <b>Hasil Poling</b></span><br /><br />";

    echo "<p align='center'>Terimakasih atas partisipasi Anda mengikuti poling kami<br /><br />
          Hasil poling saat ini: </p><br />";

    echo "<table width='100%' style='border: 1pt dashed #0000CC; padding: 10px;'>";

    // Ambil jumlah total vote
    $jml = mysqli_query($conn, "SELECT SUM(rating) AS jml_vote FROM poling WHERE aktif='Y'");
    $j = mysqli_fetch_assoc($jml);
    $jml_vote = $j["jml_vote"] ?? 0;

    // Ambil data polling
    $sql = mysqli_query($conn, "SELECT * FROM poling WHERE aktif='Y'");

    while ($s = mysqli_fetch_assoc($sql)) {
        // Cegah pembagian dengan nol
        $prosentase = ($jml_vote > 0) ? sprintf("%2.1f", (($s["rating"] / $jml_vote) * 100)) : 0;
        $gbr_vote   = $prosentase * 3;

        // Pastikan variabel gambar ada
        $gambar = "templates/building/images/blue.png"; // Sesuaikan dengan lokasi file gambar

        echo "<tr>
                <td width='120'>" . htmlspecialchars($s['pilihan']) . " (" . $s['rating'] . ") </td>
                <td> 
                    <img src='$gambar' width='$gbr_vote' height='18' border='0'> $prosentase % 
                </td>
              </tr>";
    }

    echo "</table>
          <p>Jumlah Voting: <b>$jml_vote</b></p>";
}



// Menu utama di header

// Modul profil
elseif ($_GET["module"] == "profilkami") {
  echo "<span class=judul_head>&#187; <b>Profil</b></span><br /><br />";

  $profil = mysqli_query($conn, "SELECT * FROM modul WHERE id_modul=37");
  $r      = mysqli_fetch_array($profil);

  echo "<tr><td class=isi>";
  if (!$r["gambar"]) {
    echo "<span class=image><img src=foto_banner/$r[gambar]></span>";
  }
  $isi_profil = nl2br($r["static_content"]);
  echo "$isi_profil";
}


// Modul semua berita
elseif ($_GET["module"] == "semuaberita") {
    echo "<span class=judul_head>&#187; <b>Berita</b></span><br /><br />";

    // Konfigurasi Pagination
    $p = new Paging2;
    $batas = 8;
    $posisi = $p->cariPosisi($batas);

    // **Query utama untuk menampilkan berita**
    $sql = mysqli_query($conn, "SELECT berita.id_berita, berita.judul, berita.judul_seo, berita.jam, 
                                       berita.hari, berita.tanggal, berita.gambar, berita.isi_berita,
                                       COUNT(komentar.id_komentar) AS jml 
                                FROM berita 
                                LEFT JOIN komentar ON berita.id_berita = komentar.id_berita AND komentar.aktif = 'Y' 
                                GROUP BY berita.id_berita
                                ORDER BY berita.tanggal DESC 
                                LIMIT $posisi, $batas");

    if (!$sql) {
        die("Query Error: " . mysqli_error($conn));
    }

    while ($r = mysqli_fetch_assoc($sql)) {
        $tgl = tgl_indo($r["tanggal"]);
        echo "<span class=date>{$r['hari']}, $tgl - {$r['jam']} WIB</span><br />";
        echo "<span class=judul><a href=berita-{$r['id_berita']}-{$r['judul_seo']}.html>{$r['judul']}</a></span><br />";

        // **Menampilkan ringkasan isi berita**
        $isi_berita = nl2br($r["isi_berita"]); // Konversi newline ke <br>
        $isi = substr($isi_berita, 0, 150);
        $isi = substr($isi, 0, strrpos($isi, " ")); // Potong per kata agar tidak terputus

        echo "$isi ... <a href=berita-{$r['id_berita']}-{$r['judul_seo']}.html>Selengkapnya</a> 
              (<b>{$r['jml']} komentar</b>)<br /><hr color=#e0cb91 noshade=noshade />";
    }

    // **Hitung jumlah total data untuk pagination**
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM berita");
    $data = mysqli_fetch_assoc($result);
    $jmldata = $data['total'];

    $jmlhalaman = $p->jumlahHalaman($jmldata, $batas);
    $linkHalaman = $p->navHalaman($_GET["halberita"], $jmlhalaman);

    echo "Hal: $linkHalaman <br /><br />";
}


// Modul semua agenda
elseif ($_GET["module"] == "semuaagenda") {
  echo "<span class=judul_head>&#187; <b>Agenda</b></span><br /><br />";
  $p      = new Paging4;
  $batas  = 6;
  $posisi = $p->cariPosisi($batas);
  // Tampilkan semua agenda
  $sql = mysqli_query($conn, "SELECT * FROM agenda  
                      ORDER BY id_agenda DESC LIMIT $posisi,$batas");
  while ($d = mysqli_fetch_array($sql)) {
    $tgl_posting = tgl_indo($d["tgl_posting"]);
    $tgl_mulai   = tgl_indo($d["tgl_mulai"]);
    $tgl_selesai = tgl_indo($d["tgl_selesai"]);
    $isi_agenda  = nl2br($d["isi_agenda"]);

    echo "<span class=date>$tgl_posting</span><br />";
    echo "<span class=judul>$d[tema]</span><br />";
    echo "<b>Topik</b>  : $isi_agenda ";
    echo "<b>Tanggal</b> : $tgl_mulai s/d $tgl_selesai <br />";
    echo "<b>Tempat</b> : $d[tempat] <br />";
    echo "<b>Pengirim (Contact Person)</b> : $d[pengirim] 
          <br /><hr color=#e0cb91 noshade=noshade />";
  }

  $jmldata     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM agenda"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman($_GET["halagenda"], $jmlhalaman);

  echo "Hal: $linkHalaman <br /><br />";
}


// Modul semua download
elseif ($_GET["module"] == "semuadownload") {
  echo "<span class=judul_head>&#187; <b>Download</b></span><br />";
  $p      = new Paging5;
  $batas  = 20;
  $posisi = $p->cariPosisi($batas);
  // Tampilkan semua download
  $sql = mysqli_query($conn, "SELECT * FROM download  
                      ORDER BY id_download DESC LIMIT $posisi,$batas");

  echo "<ul>";
  while ($d = mysqli_fetch_array($sql)) {
    echo "<li><a href=downlot.php?file=$d[nama_file]>$d[judul]</a> ($d[hits])</li>";
  }
  echo "</ul><hr color=#e0cb91 noshade=noshade />";

  $jmldata     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM download"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman($_GET["haldownload"], $jmlhalaman);

  echo "Hal: $linkHalaman <br /><br />";
}


// Modul semua album
elseif ($_GET["module"] == "semuaalbum") {
  echo "<span class=judul_head>&#187; <b>Album</b></span><br />";
  // Tentukan kolom
  $col = 3;

  $a = mysqli_query($conn, "SELECT jdl_album, album.id_album, gbr_album, album_seo,  
                  COUNT(gallery.id_gallery) as jumlah 
                  FROM album LEFT JOIN gallery 
                  ON album.id_album=gallery.id_album 
                  GROUP BY jdl_album");
  echo "<table><tr>";
  $cnt = 0;
  while ($w = mysqli_fetch_array($a)) {
    if ($cnt >= $col) {
      echo "</tr><tr>";
      $cnt = 0;
    }
    $cnt++;


    echo "<td align=center valign=top><br />
    <a href=album-$w[id_album].html>
    <img class=img2 src=files/img_album/kecil_$w[gbr_album] border=0 width=120 height=90><br />
    $w[jdl_album]</a><br />($w[jumlah] Foto)<br /></td>";
  }
  echo "</tr></table>";
}



// Modul galeri foto berdasarkan album
elseif ($_GET["module"] == "detailalbum") {
  echo "<span class=judul_head>&#187; <b>Galeri Foto</b></span><br />";
  $p      = new Paging6;
  $batas  = 9;
  $posisi = $p->cariPosisi($batas);

  // Tentukan kolom
  $col = 3;

  $g = mysqli_query($conn, "SELECT * FROM gallery WHERE id_album=$_GET[id] ORDER BY id_gallery DESC LIMIT $posisi,$batas");

  echo "<table><tr>";
  $cnt = 0;
  while ($w = mysqli_fetch_array($g)) {
    if ($cnt >= $col) {
      echo "</tr><tr>";
      $cnt = 0;
    }
    $cnt++;
    echo "<td align=center valign=top><br />
          <a href= onclick=\"bukajendela(galeri-$w[id_gallery]-$w[gallery_seo].html)\">
          <b>$w[jdl_gallery]</b><br>
          <img class=img src=img_galeri/kecil_$w[gbr_gallery] border=0 width=100 height=75></a><br>
          $w[keterangan]<br></td>";
  }
  echo "</tr></table><br />";

  $jmldata     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM gallery WHERE id_album=$_GET[id]"));
  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman($_GET["halgaleri"], $jmlhalaman);

  echo "Hal: $linkHalaman <br /><br />";
}


// Modul hubungi kami
elseif ($_GET["module"] == "hubungikami") {
  echo "<span class=judul_head>&#187; <b>Hubungi Kami</b></span><br /><br />";
  echo "<b>Hubungi kami secara online dengan mengisi form dibawah ini:</b>
        <table width=100% style=border: 1pt dashed #0000CC;padding: 10px;>
        <form action=hubungi-aksi.html method=POST>
        <tr><td>Nama</td><td> : <input type=text name=nama size=40></td></tr>
        <tr><td>Email</td><td> : <input type=text name=email size=40></td></tr>
        <tr><td>Subjek</td><td> : <input type=text name=subjek size=55></td></tr>
        <tr><td valign=top>Pesan</td><td> <textarea name=pesan  style=width: 315px; height: 100px;></textarea></td></tr>
        </td><td colspan=2><input type=submit name=submit value=Kirim></td></tr>
        </form></table><br />";
}


// Modul hubungi aksi
elseif ($_GET["module"] == "hubungiaksi") {
  mysqli_query($conn, "INSERT INTO hubungi(nama,
                                   email,
                                   subjek,
                                   pesan,
                                   tanggal) 
                        VALUES($_POST[nama],
                               $_POST[email],
                               $_POST[subjek],
                               $_POST[pesan],
                               $tgl_sekarang)");
  echo "<span class=posting>&#187; <b>Hubungi Kami</b></span><br /><br />";
  echo "<p align=center><b>Terimakasih telah menghubungi kami. <br /> Kami akan segera meresponnya.</b></p>";
}
?>
<style>
  .img {
    border: 2px solid #72a143;
    padding: 2px;
    background: #ffeda5;
  }

  .img2 {
    border: 2px solid #F0892C;
    padding: 2px;
    background: #ffeda5;
  }
</style>