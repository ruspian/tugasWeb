<?php
// Cek apakah parameter 'module' dan 'act' ada di URL
$module = isset($_GET['module']) ? $_GET['module'] : '';
$act = isset($_GET['act']) ? $_GET['act'] : '';

// Tentukan file aksi yang akan diproses
$aksi = "modul/mod_tag/aksi_tag.php";

// Cek koneksi database terlebih dahulu
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($module == 'tag') {
    switch ($act) {
        // Tampil Tag - Form tambah tag
        case 'tambahtag':
            echo "<div class='container mt-4'>
                    <h2>Tambah Tag</h2>
                    <form method='POST' action='{$aksi}?module=tag&act=input'>
                        <div class='mb-3'>
                            <label class='form-label'>Nama Tag</label>
                            <input type='text' name='nama_tag' class='form-control' required>
                        </div>
                        <button type='submit' class='btn btn-success'>Simpan</button>
                        <button type='button' class='btn btn-secondary' onclick='self.history.back()'>Batal</button>
                    </form>
                </div>";
            break;

        // Edit Tag
        case 'edittag':
            // Pastikan parameter 'id' ada dan aman
            if (isset($_GET['id'])) {
                $id_tag = mysqli_real_escape_string($conn, $_GET['id']);
                // Menggunakan prepared statement untuk menghindari SQL Injection
                $query = "SELECT * FROM tag WHERE id_tag = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $id_tag);  // 'i' untuk integer
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $r = mysqli_fetch_array($result);
                    echo "<div class='container mt-4'>
                            <h2>Edit Tag</h2>
                            <form method='POST' action='{$aksi}?module=tag&act=update'>
                                <input type='hidden' name='id' value='{$r['id_tag']}'>
                                <div class='mb-3'>
                                    <label class='form-label'>Nama Tag</label>
                                    <input type='text' name='nama_tag' class='form-control' value='{$r['nama_tag']}' required>
                                </div>
                                <button type='submit' class='btn btn-primary'>Update</button>
                                <button type='button' class='btn btn-secondary' onclick='self.history.back()'>Batal</button>
                            </form>
                        </div>";
                } else {
                    echo "<script>alert('Tag tidak ditemukan');</script>";
                    header('Location: ../../media.php?module=tag');
                    exit();
                }
            } else {
                echo "<script>alert('ID Tag tidak valid');</script>";
                header('Location: ../../media.php?module=tag');
                exit();
            }
            break;

        // Default untuk menampilkan daftar tag
        default:
            echo "<div class='container mt-4'>
                    <h2 class='mb-3'>Tag</h2>
                    <a href='?module=tag&act=tambahtag' class='btn btn-primary mb-3'>Tambah Tag</a>
                    <table class='table table-bordered table-striped'>
                        <thead class='table-dark'>
                            <tr>
                                <th>No</th>
                                <th>Nama Tag</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>";
            
            $tampil = mysqli_query($conn, "SELECT * FROM tag ORDER BY id_tag DESC");
            if ($tampil) {
                $no = 1;
                while ($r = mysqli_fetch_array($tampil)) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$r['nama_tag']}</td>
                            <td>
                                <a href='?module=tag&act=edittag&id={$r['id_tag']}' class='btn btn-warning btn-sm'>Edit</a> 
                                <a href='{$aksi}?module=tag&act=hapus&id={$r['id_tag']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus tag ini?\")'>Hapus</a>
                            </td>
                        </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='3'>Gagal memuat data tag.</td></tr>";
            }
            echo "</tbody></table></div>";
            break;
    }
}
?>
