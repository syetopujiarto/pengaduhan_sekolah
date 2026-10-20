<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: /pengaduan_sekolah/login.php");
    exit;
}

$message = "";

if (isset($_POST['update_status'])) {
    $id_aspirasi = mysqli_real_escape_string($conn, $_POST['id_aspirasi']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);
    $feedback    = mysqli_real_escape_string($conn, $_POST['feedback']);

    if (updateAspirasi($conn, $id_aspirasi, $status, $feedback)) {
        $message = "<div style='background: #d1fae5; color: #065f46; padding: 14px 18px; border-radius: 12px; margin-bottom: 25px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);'><i class='fa-solid fa-circle-check' style='font-size: 20px;'></i> Status & Tanggapan berhasil diperbarui!</div>";
    } else {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 25px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);'><i class='fa-solid fa-circle-xmark' style='font-size: 20px;'></i> Gagal menyimpan perubahan data.</div>";
    }
}

$stats = getAdminStats($conn);

$f_tgl      = isset($_GET['tgl']) ? $_GET['tgl'] : '';
$f_bulan    = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$f_nis      = isset($_GET['nis']) ? $_GET['nis'] : '';
$f_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$f_status   = isset($_GET['status_tab']) ? $_GET['status_tab'] : '';

// Modifikasi query filter dengan tab status
$sql_custom = "SELECT i.*, k.ket_kategori, s.kelas, a.status, a.feedback 
        FROM input_aspirasi i
        JOIN kategori k ON i.id_kategori = k.id_kategori
        LEFT JOIN siswa s ON i.nis = s.nis
        LEFT JOIN aspirasi a ON i.id_aspirasi = a.id_aspirasi
        WHERE 1=1";

if (!empty($f_tgl)) { $sql_custom .= " AND i.tgl_pelaporan = '$f_tgl'"; }
if (!empty($f_bulan)) { $sql_custom .= " AND DATE_FORMAT(i.tgl_pelaporan, '%Y-%m') = '$bulan'"; }
if (!empty($f_nis)) { $sql_custom .= " AND i.nis = '$f_nis'"; }
if (!empty($f_kategori)) { $sql_custom .= " AND i.id_kategori = '$f_kategori'"; }
if (!empty($f_status)) { 
    if($f_status == 'Menunggu') {
        $sql_custom .= " AND (a.status = 'Menunggu' OR a.status IS NULL)";
    } else {
        $sql_custom .= " AND a.status = '$f_status'";
    }
}
$sql_custom .= " ORDER BY i.id_aspirasi DESC";
$aspirasi_list = mysqli_query($conn, $sql_custom);

$page_title = "Panel Admin - Pengaduan Sarana Sekolah";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="max-width: 1250px;">
    <?= $message ?>

    <!-- Header Judul & Tombol Cetak Laporan Resmi UKK -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; font-size: 24px; color: #0f172a; font-weight: 800;"><i class="fa-solid fa-gauge-high" style="color: #4f46e5; margin-right: 10px;"></i> Dashboard Admin Sarpras</h3>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Kelola tanggapan perbaikan fasilitas sekolah secara real-time dan transparan.</p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn-3d" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);"><i class="fa-solid fa-print"></i> Cetak Laporan Resmi</button>
        </div>
    </div>

    <!-- Kop Surat Khusus Mode Print (Hanya Muncul Saat Dicetak) -->
    <div class="print-only" style="display: none; text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 15px;">
        <h2 style="margin: 0; font-size: 22px; text-transform: uppercase;">Laporan Resmi Pengaduan Sarana & Prasarana</h2>
        <h3 style="margin: 5px 0; font-size: 16px; font-weight: normal;">Sekolah Menengah Kejuruan (SMK) Tahun 2025/2026</h3>
        <p style="margin: 0; font-size: 12px; font-style: italic;">Dicetak pada tanggal: <?= date('d-m-Y') ?> oleh Administrator Sistem</p>
    </div>

    <!-- Kartu Statistik 3D Modern -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 25px;">
        <div class="stat-card-3d" style="--accent-color: #6366f1;">
            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-inbox" style="margin-right: 6px; color: #6366f1;"></i> Total Masuk</div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 6px;"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-card-3d" style="--accent-color: #f59e0b;">
            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-hourglass-start" style="margin-right: 6px; color: #f59e0b;"></i> Menunggu Verifikasi</div>
            <div style="font-size: 26px; font-weight: 800; color: #f59e0b; margin-top: 6px;"><?= $stats['menunggu'] ?></div>
        </div>
        <div class="stat-card-3d" style="--accent-color: #3b82f6;">
            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-screwdriver-wrench" style="margin-right: 6px; color: #3b82f6;"></i> Dalam Penanganan</div>
            <div style="font-size: 26px; font-weight: 800; color: #3b82f6; margin-top: 6px;"><?= $stats['proses'] ?></div>
        </div>
        <div class="stat-card-3d" style="--accent-color: #10b981;">
            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-circle-check" style="margin-right: 6px; color: #10b981;"></i> Selesai Diperbaiki</div>
            <div style="font-size: 26px; font-weight: 800; color: #10b981; margin-top: 6px;"><?= $stats['selesai'] ?></div>
        </div>
        <div class="stat-card-3d" style="--accent-color: #8b5cf6;">
            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-users" style="margin-right: 6px; color: #8b5cf6;"></i> Siswa Pelapor</div>
            <div style="font-size: 26px; font-weight: 800; color: #8b5cf6; margin-top: 6px;"><?= $stats['total_siswa'] ?></div>
        </div>
    </div>

    <!-- Tab Navigasi Cepat Berdasarkan Status -->
    <div class="no-print" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="admin.php" class="btn-3d" style="background: <?= empty($f_status) ? '#4f46e5' : '#64748b' ?>; padding: 8px 16px; font-size: 12px;">Semua Laporan</a>
        <a href="admin.php?status_tab=Menunggu" class="btn-3d" style="background: <?= $f_status == 'Menunggu' ? '#f59e0b' : '#64748b' ?>; padding: 8px 16px; font-size: 12px;">Menunggu</a>
        <a href="admin.php?status_tab=Proses" class="btn-3d" style="background: <?= $f_status == 'Proses' ? '#3b82f6' : '#64748b' ?>; padding: 8px 16px; font-size: 12px;">Proses</a>
        <a href="admin.php?status_tab=Selesai" class="btn-3d" style="background: <?= $f_status == 'Selesai' ? '#10b981' : '#64748b' ?>; padding: 8px 16px; font-size: 12px;">Selesai</a>
    </div>

    <!-- Filter Data Lanjutan -->
    <div class="filter-box no-print" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 14px; margin-bottom: 25px;">
        <h4 style="margin: 0 0 14px 0; font-size: 14px; color: #1e293b;"><i class="fa-solid fa-filter" style="color: #4f46e5; margin-right: 6px;"></i> Filter & Cari Berdasarkan Kriteria</h4>
        <form method="GET" action="" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <input type="hidden" name="status_tab" value="<?= htmlspecialchars($f_status) ?>">
            <div style="flex: 1; min-width: 140px;">
                <label style="font-size: 11px; color: #64748b;">Tanggal Lapor:</label>
                <input type="date" name="tgl" class="input-3d" value="<?= htmlspecialchars($f_tgl) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label style="font-size: 11px; color: #64748b;">Periode Bulan:</label>
                <input type="month" name="bulan" class="input-3d" value="<?= htmlspecialchars($f_bulan) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label style="font-size: 11px; color: #64748b;">NIS Siswa:</label>
                <input type="number" name="nis" class="input-3d" value="<?= htmlspecialchars($f_nis) ?>" placeholder="Contoh: 1023">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label style="font-size: 11px; color: #64748b;">Kategori Sarana:</label>
                <select name="kategori" class="input-3d">
                    <option value="">-- Semua Kategori --</option>
                    <?php
                    $kat_query = mysqli_query($conn, "SELECT * FROM kategori");
                    while ($k = mysqli_fetch_assoc($kat_query)) {
                        $selected = ($f_kategori == $k['id_kategori']) ? 'selected' : '';
                        echo "<option value='{$k['id_kategori']}' $selected>{$k['ket_kategori']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-3d" style="padding: 12px 18px;"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                <a href="/pengaduan_sekolah/admin.php" style="text-decoration: none;"><button type="button" style="background:#64748b; padding: 12px 18px;" class="btn-3d"><i class="fa-solid fa-rotate-right"></i> Reset</button></a>
            </div>
        </form>
    </div>

    <!-- Tabel Kelola Pengaduan -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 14px; width: 130px; text-align: left;">ID & Waktu</th>
                    <th style="padding: 14px; width: 120px; text-align: left;">Pelapor</th>
                    <th style="padding: 14px; text-align: left;">Rincian & Lokasi Kerusakan</th>
                    <th style="padding: 14px; width: 110px; text-align: center;">Status</th>
                    <th class="no-print" style="padding: 14px; width: 280px; text-align: left;">Aksi Respon Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($aspirasi_list) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($aspirasi_list)): 
                        $feedback_val = isset($row['feedback']) && $row['feedback'] !== null ? htmlspecialchars($row['feedback']) : '';
                        $status_val   = isset($row['status']) && !empty($row['status']) ? $row['status'] : 'Menunggu';
                    ?>
                        <tr>
                            <td style="vertical-align: top; padding: 16px;">
                                <span style="font-weight: 800; color: #4f46e5; font-size: 14px;">#<?= $row['id_aspirasi'] ?></span><br>
                                <small style="color: #64748b; font-size: 11px; white-space: nowrap;"><i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> <?= $row['tgl_pelaporan'] ?></small>
                            </td>
                            <td style="vertical-align: top; padding: 16px; white-space: nowrap;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 13px;"><i class="fa-solid fa-user-graduate" style="color: #64748b; margin-right: 6px;"></i> <?= htmlspecialchars($row['nis']) ?></div>
                                <span style="color: #475569; font-size: 11px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; font-weight: 600;"><?= !empty($row['kelas']) ? htmlspecialchars($row['kelas']) : 'Siswa' ?></span>
                            </td>
                            <td style="vertical-align: top; padding: 16px;">
                                <div style="margin-bottom: 6px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                    <span style="background: #e0e7ff; color: #3730a3; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px;"><i class="fa-solid fa-layer-group" style="margin-right: 4px;"></i> <?= htmlspecialchars($row['ket_kategori']) ?></span>
                                    <span style="color: #dc2626; font-weight: 700; font-size: 12px;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['lokasi']) ?></span>
                                </div>
                                <div style="color: #334155; font-size: 13px; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9; line-height: 1.5;">
                                    <?= htmlspecialchars($row['ket']) ?>
                                </div>
                                <!-- Tanggapan khusus saat dicetak -->
                                <div class="print-only" style="display: none; margin-top: 8px; font-size: 12px; color: #000;">
                                    <b>Tanggapan Petugas:</b> <?= !empty($feedback_val) ? $feedback_val : 'Belum ada tanggapan' ?>
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 16px; text-align: center;">
                                <span class="badge" style="background-color: <?= getStatusBadge($status_val) ?>; padding: 6px 12px;">
                                    <?= $status_val ?>
                                </span>
                            </td>
                            <td class="no-print" style="vertical-align: top; padding: 16px; background: #fafafa;">
                                <form method="POST" action="">
                                    <input type="hidden" name="id_aspirasi" value="<?= $row['id_aspirasi'] ?>">
                                    
                                    <label style="font-size: 10px; color: #64748b; font-weight: 700; margin-bottom: 3px; display: block;">STATUS:</label>
                                    <select name="status" class="input-3d" style="margin-bottom: 8px; padding: 8px 10px; font-size: 12px;" required>
                                        <option value="Menunggu" <?= $status_val == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                        <option value="Proses" <?= $status_val == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                        <option value="Selesai" <?= $status_val == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    </select>

                                    <label style="font-size: 10px; color: #64748b; font-weight: 700; margin-bottom: 3px; display: block;">TANGGAPAN ADMIN:</label>
                                    <textarea name="feedback" class="input-3d" placeholder="Ketik tanggapan perbaikan..." style="margin-bottom: 10px; height: 70px; padding: 8px; font-size: 12px;" required><?= $feedback_val ?></textarea>
                                    
                                    <button type="submit" name="update_status" class="btn-3d" style="padding: 9px; font-size: 12px; width: 100%; border-radius: 8px;"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 40px;">
                            <i class="fa-solid fa-box-open" style="font-size: 36px; margin-bottom: 10px; color: #cbd5e1; display: block;"></i>
                            <b>Tidak ada data pengaduan ditemukan.</b>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Kolom Tanda Tangan Resmi Khusus Mode Print (UKK) -->
    <div class="print-only" style="display: none; margin-top: 50px; justify-content: space-between; page-break-inside: avoid;">
        <div style="text-align: center; width: 250px;">
            <p style="margin-bottom: 60px;">Mengetahui,<br>Kepala Sekolah SMK</p>
            <b>( _________________________ )</b><br>
            <small>NIP. ....................................</small>
        </div>
        <div style="text-align: center; width: 250px;">
            <p style="margin-bottom: 60px;">Petugas Sarpras / Admin,</p>
            <b>( _________________________ )</b><br>
            <small>NIP. ....................................</small>
        </div>
    </div>
</div>

<style>
/* Styling khusus ketika tombol cetak/print ditekan */
@media print {
    .print-only { display: flex !important; }
    body { background: white !important; font-size: 12px; }
    .app-header, .filter-box, button, .btn-3d, form, .no-print, hr { display: none !important; }
    .container { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
    table { border: 1px solid #000 !important; width: 100% !important; }
    th { background-color: #eee !important; color: #000 !important; border: 1px solid #000 !important; }
    td { border: 1px solid #000 !important; color: #000 !important; }
}
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>