<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'config/koneksi.php';

$username = $_SESSION['username'] ?? 'neca';

// HAPUS folder
if (isset($_GET['hapus'])) {
    $hapus_id = (int) $_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM folders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $hapus_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// TAMBAH folder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulan'])) {
    $bulan = $_POST['bulan'];
    $tahun = (int) $_POST['tahun'];
    $warna = (rand(0, 1) === 0) ? 'blue' : 'pink';
    $stmt = $koneksi->prepare("INSERT INTO folders (user_id, bulan, tahun, warna) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $_SESSION['user_id'], $bulan, $tahun, $warna);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

$folders = [];
$stmt = $koneksi->prepare("SELECT id, bulan, tahun, warna FROM folders WHERE user_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $folders[] = $row; }
$stmt->close();

$bulan_list = ['January','February','March','April','May','June','July','August','September','October','November','December'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard - necaly</title>
<link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="topbar"></div>
    <div class="headband"></div>
    <a href="logout.php" class="logout-dot" title="Logout"></a>

    <div class="profile"></div>
    <svg class="cat" width="170" height="149" viewBox="0 0 170 149" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M23.7844 0C34.9446 5.41269 53.1906 12.7726 71.7903 21.5312C76.4256 20.7728 81.2118 20.376 86.1067 20.376C92.7624 20.376 99.2167 21.1108 105.365 22.4893C124.433 14.4649 142.71 8.18785 154.033 4.77539C184.282 4.77539 162.879 32.9344 155.975 60.3271C159.587 67.8409 161.578 76.0666 161.578 84.6885C161.578 91.0145 160.505 97.1267 158.507 102.901C164.441 101.505 169.133 101.184 169.419 102.237C169.741 103.426 164.336 105.912 157.345 107.791C157.047 107.871 156.749 107.946 156.455 108.021C156.175 108.635 155.884 109.244 155.582 109.849C162.574 109.536 168.207 110.224 168.269 111.424C168.331 112.653 162.521 113.951 155.29 114.323C154.529 114.362 153.782 114.386 153.055 114.402C152.699 114.983 152.333 115.559 151.957 116.13C152.694 116.318 153.45 116.521 154.218 116.742C161.173 118.746 166.533 121.329 166.19 122.512C165.846 123.694 159.929 123.028 152.973 121.023C151.684 120.652 150.451 120.261 149.298 119.862C135.821 137.408 112.553 149 86.1067 149C59.5345 149 36.1706 137.297 22.7229 119.611C21.6218 120.199 20.4353 120.794 19.1878 121.376C12.6308 124.434 6.88963 126.011 6.36451 124.896C5.83999 123.782 10.7302 120.398 17.2874 117.34C18.2836 116.875 19.2622 116.447 20.2053 116.055C19.904 115.596 19.608 115.134 19.3196 114.669C18.2175 114.837 17.0538 114.99 15.8489 115.116C8.64817 115.87 2.70587 115.489 2.57545 114.266C2.44614 113.042 8.1795 111.438 15.3811 110.684C15.921 110.627 16.454 110.578 16.9778 110.534C16.763 110.118 16.5536 109.699 16.3489 109.277C15.217 109.211 14.0256 109.115 12.7981 108.983C5.59927 108.212 -0.12889 106.594 0.00220606 105.37C0.134578 104.146 6.07792 103.78 13.2776 104.552C13.6396 104.591 13.9978 104.633 14.3518 104.676C11.9399 98.3844 10.6351 91.6663 10.635 84.6885C10.635 75.7681 12.7661 67.2713 16.6184 59.5488C9.89919 30.5576 -7.95821 0 23.7844 0Z" fill="#FFC898"/>
        <g transform="translate(28.54,66.22)"><path d="M40.9321 20.8352C41.0887 32.4916 31.8547 27.2889 20.5521 27.4394C9.24961 27.5899 0.158627 33.0366 0.00203737 21.3801C-0.154553 9.7237 8.88102 0.152294 20.1835 0.00180297C31.4861 -0.148689 40.7755 9.17872 40.9321 20.8352Z" fill="white"/></g>
        <g transform="translate(100.82,66.86)"><path d="M0.00195826 20.8352C-0.154632 32.4916 9.07941 27.2889 20.3819 27.4394C31.6845 27.5899 40.7755 33.0366 40.932 21.3801C41.0886 9.7237 32.0531 0.152294 20.7505 0.00180297C9.448 -0.148689 0.158548 9.17872 0.00195826 20.8352Z" fill="white"/></g>
        <g transform="translate(41.34,80.87)"><path d="M9.91364 0C15.3888 0 19.8273 4.13371 19.8273 9.23291C19.8273 10.9808 19.3058 12.6152 18.3998 14.0085L1.22964 13.6902C0.446001 12.3687 0 10.8493 0 9.23291C0 4.13371 4.43849 0 9.91364 0Z" fill="#AA8046"/></g>
        <g transform="translate(111.37,80.98)"><path d="M9.91363 2.85409e-05C4.43848 2.85409e-05 -1.14441e-05 4.22769 -1.14441e-05 9.44277C-1.14441e-05 11.2303 0.521469 12.9019 1.42743 14.327L5.58477 14.0218L9.91363 13.6757L14.2192 14.0218L18.0568 14.3402C18.5248 13.6889 19.8273 11.0959 19.8273 9.44277C19.8273 4.22769 15.3888 2.85409e-05 9.91363 2.85409e-05Z" fill="#AA8046"/></g>
        <g transform="translate(81.63,103.79)"><path d="M10.2334 3.18376C10.2334 4.9421 7.99487 4.77564 5.11672 6.36752C2.55836 4.77564 0 4.9421 0 3.18376C0 1.42542 2.29083 0 5.11672 0C7.9426 0 10.2334 1.42542 10.2334 3.18376Z" fill="#C76E33"/></g>
    </svg>

    <div class="page-title"><?= htmlspecialchars($username) ?>'s page!</div>

    <main class="bubble-area">
        <?php foreach ($folders as $f): ?>
            <div class="bubble-wrap">
                <a class="bubble <?= ($f['warna'] === 'pink') ? 'bubble-pink' : 'bubble-blue' ?>" href="note.php?folder_id=<?= $f['id'] ?>">
                    <span><?= htmlspecialchars($f['bulan']) ?> <?= substr($f['tahun'], -2) ?></span>
                    <svg class="bubble-tail" width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0H50V50L25.5 33.3333L0 50V0Z" fill="<?= ($f['warna']==='pink') ? '#FBDEE3' : '#E3EFF6' ?>"/>
                    </svg>
                </a>
                <a class="bubble-del" href="#" onclick="confirmHapus(<?= $f['id'] ?>); return false;" title="Hapus">&times;</a>
            </div>
        <?php endforeach; ?>
    </main>

    <!-- tombol menu (pen) di pojok kanan bawah -->
    <div class="fab" id="fab">
        <div class="fab-actions">
            <button class="fab-btn fab-add" onclick="openAdd()" title="Tambah folder">+</button>
            <button class="fab-btn fab-del" onclick="toggleDeleteMode()" title="Hapus folder">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7A6972" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </button>
        </div>
        <button class="fab-main" onclick="toggleFab()" title="Menu">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7A6972" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
            </svg>
        </button>
    </div>

    <!-- popup tambah folder -->
    <div class="modal" id="modal">
        <form class="modal-box" method="POST">
            <h3>Tambah Folder</h3>
            <label>Bulan</label>
            <select name="bulan" required>
                <?php foreach ($bulan_list as $b): ?>
                    <option value="<?= $b ?>"><?= $b ?></option>
                <?php endforeach; ?>
            </select>
            <label>Tahun</label>
            <input type="number" name="tahun" value="2026" min="2020" max="2100" required>
            <div class="modal-actions">
                <button type="button" onclick="document.getElementById('modal').style.display='none'">Batal</button>
                <button type="submit">Simpan</button>
            </div>
        </form>
    </div>

    <!-- popup konfirmasi hapus (template sama kayak tambah folder) -->
    <div class="modal" id="modal-hapus">
        <div class="modal-box">
            <h3>Hapus Folder</h3>
            <p class="modal-text">Yakin mau hapus folder ini?<br>Catatan di dalamnya juga ikut hilang ya.</p>
            <div class="modal-actions">
                <button type="button" onclick="closeHapus()">Batal</button>
                <a id="btn-hapus-ok" href="#" class="btn-hapus">Hapus</a>
            </div>
        </div>
    </div>

<script>
    function toggleFab() { document.getElementById('fab').classList.toggle('open'); }
    function openAdd() {
        document.getElementById('modal').style.display = 'flex';
        document.getElementById('fab').classList.remove('open');
    }
    function toggleDeleteMode() {
        document.body.classList.toggle('delete-mode');
        document.getElementById('fab').classList.remove('open');
    }
    function confirmHapus(id) {
        document.getElementById('btn-hapus-ok').href = 'dashboard.php?hapus=' + id;
        document.getElementById('modal-hapus').style.display = 'flex';
    }
    function closeHapus() {
        document.getElementById('modal-hapus').style.display = 'none';
    }
</script>
</body>
</html>