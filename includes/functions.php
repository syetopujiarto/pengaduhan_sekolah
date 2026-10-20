<?php
require_once __DIR__ . '/../config/database.php';

// Tambah Pengaduan Siswa
function tambahAspirasi($conn, $nis, $id_kategori, $lokasi, $ket) {
    $tgl = date('Y-m-d');
    $query = "INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, tgl_pelaporan) 
              VALUES ('$nis', '$id_kategori', '$lokasi', '$ket', '$tgl')";
    return mysqli_query($conn, $query);
}

// Update Status & Feedback oleh Admin
function updateAspirasi($conn, $id_aspirasi, $status, $feedback) {
    $query_check = mysqli_query($conn, "SELECT * FROM aspirasi WHERE id_aspirasi = '$id_aspirasi'");
    if (mysqli_num_rows($query_check) > 0) {
        $query = "UPDATE aspirasi SET status = '$status', feedback = '$feedback' WHERE id_aspirasi = '$id_aspirasi'";
    } else {
        $query = "INSERT INTO aspirasi (id_aspirasi, status, feedback) VALUES ('$id_aspirasi', '$status', '$feedback')";
    }
    return mysqli_query($conn, $query);
}

// Filter Data Aspirasi
function getFilteredAspirasi($conn, $tgl = '', $bulan = '', $nis = '', $kategori = '') {
    $sql = "SELECT i.*, k.ket_kategori, s.kelas, a.status, a.feedback 
            FROM input_aspirasi i
            JOIN kategori k ON i.id_kategori = k.id_kategori
            LEFT JOIN siswa s ON i.nis = s.nis
            LEFT JOIN aspirasi a ON i.id_aspirasi = a.id_aspirasi
            WHERE 1=1";

    if (!empty($tgl)) {
        $sql .= " AND i.tgl_pelaporan = '$tgl'";
    }
    if (!empty($bulan)) {
        $sql .= " AND DATE_FORMAT(i.tgl_pelaporan, '%Y-%m') = '$bulan'";
    }
    if (!empty($nis)) {
        $sql .= " AND i.nis = '$nis'";
    }
    if (!empty($kategori)) {
        $sql .= " AND i.id_kategori = '$kategori'";
    }

    $sql .= " ORDER BY i.id_aspirasi DESC";
    return mysqli_query($conn, $sql);
}

// Lencana Warna Status
function getStatusBadge($status) {
    if ($status == 'Proses') {
        return '#f59e0b';
    } elseif ($status == 'Selesai') {
        return '#10b981';
    } else {
        return '#6b7280';
    }
}

// Statistik Keseluruhan (Untuk Admin)
function getAdminStats($conn) {
    $stats = [];
    
    $q1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM input_aspirasi");
    $stats['total'] = mysqli_fetch_assoc($q1)['total'];
    
    $q2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM aspirasi WHERE status = 'Menunggu' OR status IS NULL");
    $stats['menunggu'] = mysqli_fetch_assoc($q2)['total'];
    
    $q3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM aspirasi WHERE status = 'Proses'");
    $stats['proses'] = mysqli_fetch_assoc($q3)['total'];
    
    $q4 = mysqli_query($conn, "SELECT COUNT(*) as total FROM aspirasi WHERE status = 'Selesai'");
    $stats['selesai'] = mysqli_fetch_assoc($q4)['total'];
    
    $q5 = mysqli_query($conn, "SELECT COUNT(DISTINCT nis) as total FROM input_aspirasi");
    $stats['total_siswa'] = mysqli_fetch_assoc($q5)['total'];

    return $stats;
}

// Statistik Personal Siswa berdasarkan NIS
function getStudentStats($conn, $nis) {
    $stats = [];
    $nis = mysqli_real_escape_string($conn, $nis);
    
    $q1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM input_aspirasi WHERE nis = '$nis'");
    $stats['total'] = mysqli_fetch_assoc($q1)['total'];
    
    $q2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM input_aspirasi i JOIN aspirasi a ON i.id_aspirasi = a.id_aspirasi WHERE i.nis = '$nis' AND a.status = 'Selesai'");
    $stats['selesai'] = mysqli_fetch_assoc($q2)['total'];
    
    $stats['proses'] = $stats['total'] - $stats['selesai'];

    return $stats;
}
?>