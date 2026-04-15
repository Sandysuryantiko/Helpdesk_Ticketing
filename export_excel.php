<?php
include 'core/config.php';
proteksi_halaman();

// Cek apakah export untuk riwayat (tiket selesai) atau semua tiket
$is_riwayat = isset($_GET['riwayat']) && $_GET['riwayat'] == '1';

// Header untuk memaksa browser mendownload file Excel
$filename = $is_riwayat
    ? "Riwayat_Tiket_Selesai_" . date('d-m-Y') . ".xls"
    : "Laporan_Helpdesk_" . date('d-m-Y') . ".xls";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$filename");

if ($is_riwayat) {
    // ─── Export Riwayat (dengan filter) ──────────────────────────────────────
    $filter_jenis   = isset($_GET['jenis'])   ? input($_GET['jenis'])   : '';
    $filter_teknisi = isset($_GET['teknisi']) ? input($_GET['teknisi']) : '';
    $filter_dari    = isset($_GET['dari'])    ? input($_GET['dari'])    : '';
    $filter_sampai  = isset($_GET['sampai'])  ? input($_GET['sampai'])  : '';
    $filter_divisi  = isset($_GET['divisi'])  ? input($_GET['divisi'])  : '';

    $where_parts = ["t.status = 'selesai'"];
    if (!empty($filter_jenis))   $where_parts[] = "t.jenis = '$filter_jenis'";
    if (!empty($filter_teknisi)) $where_parts[] = "t.teknisi_nama LIKE '%$filter_teknisi%'";
    if (!empty($filter_dari))    $where_parts[] = "DATE(t.tgl_selesai) >= '$filter_dari'";
    if (!empty($filter_sampai))  $where_parts[] = "DATE(t.tgl_selesai) <= '$filter_sampai'";
    if (!empty($filter_divisi))  $where_parts[] = "u.divisi = '$filter_divisi'";
    $where_sql = implode(' AND ', $where_parts);

    $res = mysqli_query($conn, "SELECT t.*, u.username as pelapor, u.divisi 
                                FROM tickets t 
                                JOIN users u ON t.user_id = u.id 
                                WHERE $where_sql 
                                ORDER BY t.tgl_selesai DESC");
?>
    <table border="1">
        <thead>
            <tr>
                <th colspan="9" style="background-color: #059669; color: white; font-size: 14px; padding: 8px;">
                    Laporan Riwayat Tiket Selesai — Diekspor: <?= date('d/m/Y H:i') ?>
                </th>
            </tr>
            <?php if (!empty($filter_dari) || !empty($filter_sampai) || !empty($filter_jenis) || !empty($filter_divisi) || !empty($filter_teknisi)): ?>
                <tr>
                    <td colspan="9" style="background-color: #d1fae5; color: #065f46; font-size: 10px; padding: 4px 8px;">
                        Filter aktif:
                        <?= !empty($filter_jenis)   ? "Kategori: $filter_jenis | " : '' ?>
                        <?= !empty($filter_divisi)  ? "Divisi: $filter_divisi | " : '' ?>
                        <?= !empty($filter_teknisi) ? "Teknisi: $filter_teknisi | " : '' ?>
                        <?= !empty($filter_dari)    ? "Dari: $filter_dari | " : '' ?>
                        <?= !empty($filter_sampai)  ? "Sampai: $filter_sampai" : '' ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <th style="background-color: #059669; color: white;">No</th>
                <th style="background-color: #059669; color: white;">Tgl Lapor</th>
                <th style="background-color: #059669; color: white;">Tgl Selesai</th>
                <th style="background-color: #059669; color: white;">Pelapor</th>
                <th style="background-color: #059669; color: white;">Divisi</th>
                <th style="background-color: #059669; color: white;">No PC</th>
                <th style="background-color: #059669; color: white;">Keluhan</th>
                <th style="background-color: #059669; color: white;">Kategori</th>
                <th style="background-color: #059669; color: white;">Teknisi</th>
                <th style="background-color: #059669; color: white;">Keterangan IT</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <td><?= $row['tgl_selesai'] ?? '-'; ?></td>
                    <td><?= $row['pelapor']; ?></td>
                    <td><?= $row['divisi']; ?></td>
                    <td><?= $row['no_pc']; ?></td>
                    <td><?= $row['keluhan']; ?></td>
                    <td><?= strtoupper($row['jenis']); ?></td>
                    <td><?= $row['teknisi_nama']; ?></td>
                    <td><?= $row['keterangan_it']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<?php } else {
    // ─── Export Semua Tiket (default dari dashboard) ──────────────────────────
    $res = mysqli_query($conn, "SELECT t.*, u.username as pelapor 
                                FROM tickets t 
                                JOIN users u ON t.user_id = u.id 
                                ORDER BY t.created_at DESC");
?>
    <table border="1">
        <thead>
            <tr>
                <th colspan="7" style="background-color: #4F46E5; color: white; font-size: 14px; padding: 8px;">
                    Laporan Helpdesk IT — Diekspor: <?= date('d/m/Y H:i') ?>
                </th>
            </tr>
            <tr>
                <th style="background-color: #4F46E5; color: white;">No</th>
                <th style="background-color: #4F46E5; color: white;">Tanggal</th>
                <th style="background-color: #4F46E5; color: white;">Pelapor</th>
                <th style="background-color: #4F46E5; color: white;">No PC</th>
                <th style="background-color: #4F46E5; color: white;">Keluhan</th>
                <th style="background-color: #4F46E5; color: white;">Status</th>
                <th style="background-color: #4F46E5; color: white;">Teknisi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <td><?= $row['pelapor']; ?></td>
                    <td><?= $row['no_pc']; ?></td>
                    <td><?= $row['keluhan']; ?></td>
                    <td><?= strtoupper($row['status']); ?></td>
                    <td><?= $row['teknisi_nama']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php } ?>