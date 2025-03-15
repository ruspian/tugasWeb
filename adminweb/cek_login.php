<?php
include "../config/koneksi.php";
function anti_injection($conn, $data)
{
  $filter = mysqli_real_escape_string($conn, stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
  return $filter;
}

$username = anti_injection($conn, $_POST['username']);
$pass = anti_injection($conn, $_POST['password']);

// pastikan username dan password adalah berupa huruf atau angka.
if (!ctype_alnum($username) or !ctype_alnum($pass)) {
  echo "Sekarang loginnya tidak bisa di injeksi lho.";
} else {
  $login = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$pass' AND blokir='N'");
  $ketemu = mysqli_num_rows($login);
  $r = mysqli_fetch_array($login);

  // Apabila username dan password ditemukan
  if ($ketemu > 0) {
    session_start();

    $_SESSION['namauser']   = $r['username'];
    $_SESSION['namalengkap'] = $r['nama_lengkap'];
    $_SESSION['passuser']   = $r['password'];
    $_SESSION['leveluser']  = $r['level'];


    $sid = session_id();
    mysqli_query($conn, "UPDATE users SET id_session='$sid' WHERE username='$username'");
    header('location:media.php?module=home');
  } else {
    echo "<link href=../config/adminstyle.css rel=stylesheet type=text/css>";
    echo "<center>LOGIN GAGAL! <br> 
        Username atau Password Anda tidak benar.<br>
        Atau account Anda sedang diblokir.<br>";
    echo "<a href=index.php><b>ULANGI LAGI</b></a></center>";
  }
}
