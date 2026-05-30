<?php
include 'core/config.php';
proteksi_halaman();

$user_id_session  = $_SESSION['user_id'];
$username_session = $_SESSION['username'];

// ─────────────────────────────────────────────────────────────────────────────
// 1. SIMPAN TIKET BARU (USER)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['simpan_tiket'])) {
    $pc      = input($_POST['no_pc']);
    $keluhan = input($_POST['keluhan']);
    $jenis   = input($_POST['jenis']);
    $urgensi = input($_POST['urgensi']);

    if (!empty($_FILES['bukti']['name'])) {
        $nama_file = "REQ_" . time() . "_" . basename($_FILES['bukti']['name']);
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], "uploads/" . $nama_file)) {
            $q = "INSERT INTO tickets (user_id, no_pc, keluhan, jenis, urgensi, status, bukti_keluhan) 
                  VALUES ('$user_id_session', '$pc', '$keluhan', '$jenis', '$urgensi', 'Antrian', '$nama_file')";

            if (mysqli_query($conn, $q)) {

                // === START PUSHER NOTIFICATION (FIXED MD5) ===
                $app_id  = '2139929';
                $key     = '1143d77caa902710fd33';
                $secret  = '17b8639226d874a57485';
                $cluster = 'ap1';

                $data_notif = [
                    'nama'   => $username_session,
                    'judul'  => $keluhan,
                    'device' => $pc
                ];

                // Bungkus payload ke dalam format yang diminta Pusher
                $payload = json_encode([
                    'name'     => 'new-ticket',
                    'channels' => ['helpdesk_chanel'],
                    'data'     => json_encode($data_notif)
                ]);

                $auth_timestamp = time();
                $auth_version   = '1.0';
                // Hitung MD5 dari $payload yang akan dikirim
                $body_md5       = md5($payload);

                $query_string   = "auth_key=$key&auth_timestamp=$auth_timestamp&auth_version=$auth_version&body_md5=$body_md5";
                $path           = "/apps/$app_id/events";
                $auth_signature = hash_hmac('sha256', "POST\n$path\n$query_string", $secret);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://api-$cluster.pusher.com$path?$query_string&auth_signature=$auth_signature");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // Kirim payload yang sama dengan yang di-MD5
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpCode !== 200) {
                    $err = curl_error($ch);
                    file_put_contents('debug_pusher.txt', "Waktu: " . date('Y-m-d H:i:s') . "\nError: $err\nHTTP Code: $httpCode\nResponse: $response\n\n", FILE_APPEND);
                } else {
                    // Jika sukses, hapus log error agar tidak membingungkan
                    if (file_exists('debug_pusher.txt')) unlink('debug_pusher.txt');
                }
                curl_close($ch);
                // === END PUSHER NOTIFICATION ===

                header("Location: index.php?msg=success");
            } else {
                header("Location: create_ticket.php?msg=error_db");
            }
        } else {
            header("Location: create_ticket.php?msg=error_upload");
        }
    } else {
        header("Location: create_ticket.php?msg=missing_file");
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 2. AMBIL TIKET (ADMIN/TEKNISI)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_GET['ambil_id'])) {
    $id    = input($_GET['ambil_id']);
    $check = mysqli_query($conn, "SELECT status FROM tickets WHERE id='$id' AND status='Antrian'");

    if (mysqli_num_rows($check) > 0) {
        $q = "UPDATE tickets SET status='proses', teknisi_nama='$username_session' WHERE id='$id'";
        mysqli_query($conn, $q);
        header("Location: index.php?msg=taken");
    } else {
        header("Location: index.php?msg=already_taken");
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 3. UPDATE STATUS TIKET (ADMIN)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['update_admin'])) {
    $id         = input($_POST['id']);
    $status     = input($_POST['status']);
    $keterangan = input($_POST['keterangan_it']);
    $extra      = "";

    if ($status == 'selesai') {
        if (!empty($_FILES['bukti_selesai']['name'])) {
            $nama_done = "DONE_" . time() . "_" . basename($_FILES['bukti_selesai']['name']);
            move_uploaded_file($_FILES['bukti_selesai']['tmp_name'], "uploads/" . $nama_done);
            $extra .= ", bukti_selesai = '$nama_done'";
        }
        $extra .= ", tgl_selesai = NOW()";
    }

    $q = "UPDATE tickets SET 
            status        = '$status', 
            keterangan_it = '$keterangan',
            teknisi_nama  = '$username_session'
            $extra 
          WHERE id = '$id'";

    if (mysqli_query($conn, $q)) {
        header("Location: index.php?msg=updated");
    } else {
        header("Location: index.php?msg=error");
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 4. HAPUS TIKET (USER — hanya status Antrian)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_GET['delete_id'])) {
    $id         = input($_GET['delete_id']);
    $query_file = mysqli_query($conn, "SELECT bukti_keluhan FROM tickets WHERE id='$id' AND user_id='$user_id_session'");
    $data_file  = mysqli_fetch_assoc($query_file);

    if ($data_file) {
        $delete = mysqli_query($conn, "DELETE FROM tickets WHERE id='$id' AND status='Antrian'");
        if ($delete) {
            if (file_exists("uploads/" . $data_file['bukti_keluhan'])) {
                unlink("uploads/" . $data_file['bukti_keluhan']);
            }
            header("Location: index.php?msg=deleted");
        } else {
            header("Location: index.php?msg=error_delete");
        }
    } else {
        header("Location: index.php?msg=error");
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 5. TAMBAH PENGGUNA (ADMIN)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['add_user'])) {
    $username = input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = input($_POST['role']);
    $divisi   = input($_POST['divisi']);

    $query = "INSERT INTO users (username, password, role, divisi) VALUES ('$username', '$password', '$role', '$divisi')";

    if (mysqli_query($conn, $query)) {
        header("Location: users.php?msg=user_added");
    } else {
        header("Location: users.php?msg=error");
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 6. UPDATE PENGGUNA (ADMIN)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['update_user'])) {
    $id      = input($_POST['id']);
    $role    = input($_POST['role']);
    $divisi  = input($_POST['divisi']);
    $pass_q  = "";

    if (!empty($_POST['password'])) {
        $hashed  = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pass_q  = ", password = '$hashed'";
    }

    $query = "UPDATE users SET role='$role', divisi='$divisi' $pass_q WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: users.php?msg=user_updated");
    } else {
        header("Location: users.php?msg=error");
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 7. HAPUS PENGGUNA (ADMIN)
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_GET['delete_user'])) {
    $id = input($_GET['delete_user']);

    // Cegah admin hapus akun sendiri
    if ($id == $_SESSION['user_id']) {
        header("Location: users.php?msg=self_delete_error");
        exit;
    }

    if (mysqli_query($conn, "DELETE FROM users WHERE id='$id'")) {
        header("Location: users.php?msg=user_deleted");
    } else {
        header("Location: users.php?msg=error");
    }
    exit;
}
