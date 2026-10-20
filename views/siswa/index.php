<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

$message = "";

if (isset($_POST['submit'])) {
    $nis         = mysqli_real_escape_string($conn, $_POST['nis']);
    $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $ket         = mysqli_real_escape_string($conn, $_POST['ket']);

    $check_nis = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    if (mysqli_num_rows($check_nis) == 0) {
        $kelas_default = "XII RPL 1";
        mysqli_query($conn, "INSERT INTO siswa (nis, kelas) VALUES ('$nis', '$kelas_default')");
    }

    if (tambahAspirasi($conn, $nis, $id_kategori, $lokasi, $ket)) {
        $message = "<div style='background: #d1fae5; color: #065f46; padding: 14px 18px; border-radius: 12px; margin-bottom: 25px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);'><i class='fa-solid fa-circle-check' style='font-size: 20px;'></i> Laporan berhasil terkirim dan tersimpan di sistem!</div>";
    } else {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 25px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);'><i class='fa-solid fa-circle-xmark' style='font-size: 20px;'></i> Gagal mengirim laporan. Silakan coba kembali.</div>";
    }
}

// Mengambil Statistik Keseluruhan untuk Banner Siswa
$g_stats = getAdminStats($conn);

$search_nis = isset($_GET['search_nis']) ? mysqli_real_escape_string($conn, $_GET['search_nis']) : '';

$page_title = "Portal Siswa 3D - Pengaduan Sarana Sekolah";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container" style="max-width: 1200px; background: transparent; border: none; box-shadow: none; padding: 20px auto;">
    
    <!-- Hero Banner 3D -->
    <div class="card-3d" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white; margin-bottom: 30px; position: relative; overflow: hidden; border: none;">
        <div style="position: relative; z-index: 2;">
            <span style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; border: 1px solid rgba(255,255,255,0.2); display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-cube" style="color: #67e8f9;"></i> Portal Aspirasi Siswa UKK
            </span>
            <h1 style="font-size: 28px; margin: 15px 0 8px 0; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Layanan Pengaduan Sarana Sekolah</h1>
            <p style="margin: 0 0 20px 0; opacity: 0.88; font-size: 14px; max-width: 680px; line-height: 1.6;">Laporkan kerusakkan fasilitas kelas, sarana sanitasi, atau alat laboratorium secara cepat dan lacak penanganannya hingga tuntas.</p>
        </div>
    </div>

    <!-- Statistik Umum Global Siswa (3D Grid) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 30px;">
        <div class="stat-card-3d" style="--accent-color: #6366f1;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Total Pengaduan</div>
                    <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 4px;"><?= $g_stats['total'] ?></div>
                </div>
                <div style="width: 48px; height: 48px; background: #e0e7ff; color: #4338ca; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.15);">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
            </div>
        </div>

        <div class="stat-card-3d" style="--accent-color: #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Menunggu Respon</div>
                    <div style="font-size: 26px; font-weight: 800; color: #f59e0b; margin-top: 4px;"><?= $g_stats['menunggu'] ?></div>
                </div>
                <div style="width: 48px; height: 48px; background: #fef3c7; color: #d97706; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.15);">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="stat-card-3d" style="--accent-color: #3b82f6;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Sedang Diperbaiki</div>
                    <div style="font-size: 26px; font-weight: 800; color: #3b82f6; margin-top: 4px;"><?= $g_stats['proses'] ?></div>
                </div>
                <div style="width: 48px; height: 48px; background: #dbeafe; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.15);">
                    <i class="fa-solid fa-wrench"></i>
                </div>
            </div>
        </div>

        <div class="stat-card-3d" style="--accent-color: #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Selesai Diperbaiki</div>
                    <div style="font-size: 26px; font-weight: 800; color: #10b981; margin-top: 4px;"><?= $g_stats['selesai'] ?></div>
                </div>
                <div style="width: 48px; height: 48px; background: #d1fae5; color: #059669; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.15);">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <?= $message ?>

    <!-- Grid Utama: Form Pengaduan & Lacak Personal -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
        
        <!-- Form Pengaduan 3D -->
        <div class="card-3d">
            <h3 style="margin-top: 0; font-size: 18px; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-pen-to-square" style="color: #4f46e5;"></i> Form Kirim Pengaduan Sarana
            </h3>
            
            <form action="" method="POST">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">NIS SISWA:</label>
                        <input type="number" name="nis" class="input-3d" required placeholder="Masukkan NIS Anda">
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">KATEGORI SARANA:</label>
                        <select name="id_kategori" class="input-3d" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $kat_query = mysqli_query($conn, "SELECT * FROM kategori");
                            while ($k = mysqli_fetch_assoc($kat_query)) {
                                echo "<option value='{$k['id_kategori']}'>{$k['ket_kategori']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">LOKASI SPESIFIK:</label>
                    <input type="text" name="lokasi" class="input-3d" required placeholder="Contoh: Lab Komputer RPL 2 / Toilet Siswa Lt. 2">
                </div>

                <div style="margin-bottom: 22px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">RINCIAN KET_KERUSAKAN:</label>
                    <textarea name="ket" rows="3" class="input-3d" required placeholder="Jelaskan detail kerusakan sarana secara lengkap..."></textarea>
                </div>

                <button type="submit" name="submit" class="btn-3d" style="width: 100%;"><i class="fa-solid fa-paper-plane"></i> Kirim Laporan Pengaduan</button>
            </form>
        </div>

        <!-- Lacak Histori Laporan & Statistik Personal 3D -->
        <div class="card-3d">
            <h3 style="margin-top: 0; font-size: 18px; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #4f46e5;"></i> Lacak Status Laporan Saya
            </h3>

            <form action="" method="GET" style="display: flex; gap: 10px; margin-bottom: 25px;">
                <input type="number" name="search_nis" class="input-3d" value="<?= htmlspecialchars($search_nis) ?>" placeholder="Masukkan NIS Anda untuk melihat histori..." required style="max-width: 450px;">
                <button type="submit" class="btn-3d" style="white-space: nowrap;"><i class="fa-solid fa-magnifying-glass"></i> Lacak</button>
            </form>

            <?php if (!empty($search_nis)): 
                $m_stats = getStudentStats($conn, $search_nis);
            ?>
                <!-- Statistik Personal Siswa (3D Ringkas) -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px;">
                    <div style="background: #f8fafc; padding: 15px; border-radius: 14px; text-align: center; border: 1px solid #e2e8f0; box-shadow: inset 0 -3px 0 #6366f1;">
                        <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Laporan Anda</div>
                        <div style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 4px;"><?= $m_stats['total'] ?></div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 14px; text-align: center; border: 1px solid #e2e8f0; box-shadow: inset 0 -3px 0 #f59e0b;">
                        <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Dalam Pengerjaan</div>
                        <div style="font-size: 22px; font-weight: 800; color: #f59e0b; margin-top: 4px;"><?= $m_stats['proses'] ?></div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 14px; text-align: center; border: 1px solid #e2e8f0; box-shadow: inset 0 -3px 0 #10b981;">
                        <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">Selesai Diperbaiki</div>
                        <div style="font-size: 22px; font-weight: 800; color: #10b981; margin-top: 4px;"><?= $m_stats['selesai'] ?></div>
                    </div>
                </div>

                <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <table style="margin: 0;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Lokasi Sarana</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Tanggapan Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $data = getFilteredAspirasi($conn, '', '', $search_nis, '');
                            $no = 1;
                            if (mysqli_num_rows($data) > 0):
                                while ($row = mysqli_fetch_assoc($data)):
                                    $s_val = !empty($row['status']) ? $row['status'] : 'Menunggu';
                            ?>
                                <tr>
                                    <td><b><?= $no++ ?></b></td>
                                    <td style="white-space: nowrap;"><i class="fa-regular fa-calendar" style="color: #64748b;"></i> <?= $row['tgl_pelaporan'] ?></td>
                                    <td><span style="font-weight: 700; color: #4f46e5;"><?= $row['ket_kategori'] ?></span></td>
                                    <td><i class="fa-solid fa-map-pin" style="color: #ef4444;"></i> <b><?= htmlspecialchars($row['lokasi']) ?></b></td>
                                    <td><?= htmlspecialchars($row['ket']) ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?= getStatusBadge($s_val) ?>; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            <?= $s_val ?>
                                        </span>
                                    </td>
                                    <td style="background: #f8fafc; font-weight: 500;">
                                        <?= !empty($row['feedback']) ? htmlspecialchars($row['feedback']) : '<i style="color: #94a3b8; font-weight: normal;">Belum ada umpan balik</i>' ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <tr><td colspan="7" style="text-align: center; color: #64748b; padding: 25px;"><i class="fa-solid fa-circle-exclamation"></i> Tidak ada data riwayat untuk NIS tersebut.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>