<?php
session_start();
include "../../../config/koneksi.php";
include "../../../config/fungsi_seo.php";

$module = $_GET['module'] ?? '';
$act = $_GET['act'] ?? '';

// --- HAPUS TAG ---
if ($module === 'tag' && $act === 'hapus') {
    $id_tag = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Debugging: Cek query DELETE
    error_log("QUERY HAPUS: DELETE FROM tag WHERE id_tag = '$id_tag'");
    
    $query = mysqli_query($conn, "DELETE FROM tag WHERE id_tag = '$id_tag'");
    
    if ($query) {
        header('Location: ../../media.php?module=' . $module);
        exit();
    } else {
        error_log("Gagal menghapus tag: " . mysqli_error($conn));  // Log error SQL
        echo "<script>alert('Gagal menghapus tag: " . mysqli_error($conn) . "');</script>";
        header('Location: ../../media.php?module=' . $module);
        exit();
    }
}

// --- INPUT TAG ---
if ($module === 'tag' && $act === 'input') {
    if (isset($_POST['nama_tag'])) {
        $nama_tag = mysqli_real_escape_string($conn, $_POST['nama_tag']);
        
        // Debugging: Cek nama tag yang dikirim
        error_log("Nama tag yang diterima: $nama_tag");
        
        $query = mysqli_query($conn, "INSERT INTO tag (nama_tag) VALUES ('$nama_tag')");
        
        // Debugging: Cek query INSERT
        error_log("QUERY INSERT: INSERT INTO tag (nama_tag) VALUES ('$nama_tag')");
        
        if ($query) {
            // Jika berhasil, redirect ke halaman yang sesuai
            header('Location: ../../media.php?module=' . $module);
            exit();
        } else {
            error_log("Gagal menambahkan tag: " . mysqli_error($conn));  // Log error SQL
            echo "<script>alert('Gagal menambahkan tag');</script>";
            header('Location: ../../media.php?module=' . $module . '&act=tambahtag');
            exit();
        }
    } else {
        echo "<script>alert('Data nama tag tidak ada');</script>";
    }
}

// --- UPDATE TAG ---
elseif ($module === 'tag' && $act === 'update') {
    $id_tag = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_tag = mysqli_real_escape_string($conn, $_POST['nama_tag']);
    
    // Debugging: Cek data yang akan diperbarui
    error_log("UPDATE Tag: id_tag = $id_tag, nama_tag = $nama_tag");
    
    $query = mysqli_query($conn, "UPDATE tag SET nama_tag = '$nama_tag' WHERE id_tag = '$id_tag'");
    
    if ($query) {
        header('Location: ../../media.php?module=' . $module);
        exit();
    } else {
        error_log("Gagal memperbarui tag: " . mysqli_error($conn));  // Log error SQL
        echo "<script>alert('Gagal memperbarui tag: " . mysqli_error($conn) . "');</script>";
        header('Location: ../../media.php?module=' . $module . '&act=edittag&id=' . $id_tag);
        exit();
    }
}
?>
