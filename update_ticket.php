<?php
include 'core/config.php';
proteksi_halaman();

if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update_status'])) {
    $id      = input($_POST['ticket_id']);
    $status  = input($_POST['status']);
    $teknisi = input($_POST['teknisi']);

    $query = "UPDATE tickets SET status='$status', teknisi_nama='$teknisi' WHERE id='$id'";

    if (!empty($_FILES['bukti_selesai']['name'])) {
        $nama_file = "done_" . time() . "_" . basename($_FILES['bukti_selesai']['name']);
        move_uploaded_file($_FILES['bukti_selesai']['tmp_name'], "uploads/" . $nama_file);
        $query = "UPDATE tickets SET status='$status', teknisi_nama='$teknisi', bukti_selesai='$nama_file' WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: index.php?msg=updated");
    } else {
        header("Location: index.php?msg=error");
    }
    exit;
}
