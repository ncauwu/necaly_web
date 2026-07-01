<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
require 'config/koneksi.php';

$username = $_SESSION['username'] ?? 'neca';
$folder_id = isset($_GET['folder_id']) ? (int) $_GET['folder_id'] : 0;

$stmt = $koneksi->prepare("SELECT bulan, tahun FROM folders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $folder_id, $_SESSION['user_id']);
$stmt->execute();
$folder = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$folder) { header("Location: dashboard.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $week = (int) ($_POST['week'] ?? 1);
    if ($week < 1 || $week > 4) $week = 1;
    $schedule = $_POST['schedule'] ?? '';
    $todo     = $_POST['todo'] ?? '';
    $notes    = $_POST['notes'] ?? '';
    $mood     = $_POST['mood'] ?? '';

    $stmt = $koneksi->prepare("INSERT INTO catatan (folder_id, week, schedule, todo, notes, mood) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE schedule = VALUES(schedule), todo = VALUES(todo), notes = VALUES(notes), mood = VALUES(mood)");
    $stmt->bind_param("iissss", $folder_id, $week, $schedule, $todo, $notes, $mood);
    $stmt->execute();
    $stmt->close();

    if (isset($_POST['keluar'])) { header("Location: dashboard.php"); exit; }
    $next = (isset($_POST['week_baru']) && $_POST['week_baru'] !== '') ? (int) $_POST['week_baru'] : $week;
    if ($next < 1 || $next > 4) $next = 1;
    header("Location: note.php?folder_id=$folder_id&week=$next");
    exit;
}

$week = isset($_GET['week']) ? (int) $_GET['week'] : 1;
if ($week < 1 || $week > 4) $week = 1;

$data = ['schedule' => '', 'todo' => '', 'notes' => '', 'mood' => ''];
$stmt = $koneksi->prepare("SELECT schedule, todo, notes, mood FROM catatan WHERE folder_id = ? AND week = ?");
$stmt->bind_param("ii", $folder_id, $week);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($r) { $data = $r; }

$judul = htmlspecialchars($folder['bulan']) . ' ' . substr($folder['tahun'], -2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= $judul ?> - necaly</title>
<link rel="stylesheet" href="note.css">
</head>
<body>
<form method="POST" id="form-note">
    <input type="hidden" name="folder_id" value="<?= $folder_id ?>">
    <input type="hidden" name="week" value="<?= $week ?>">
    <input type="hidden" name="schedule" id="in-schedule">
    <input type="hidden" name="todo" id="in-todo">

    <!-- background + header (sama kayak dashboard) -->
    <div class="topbar"></div>
    <div class="headband"></div>
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

    <!-- container folder -->
    <div class="folder">
        <div class="tab">
            <span class="tab-title"><?= $judul ?></span>
            <span class="exit-btn" onclick="bukaKeluar()" title="Keluar">+</span>
        </div>
        <div class="board">
        <div class="card card-mood">
            <div class="mood-days" id="mood-days"></div>
            <input type="hidden" name="mood" id="in-mood" value='<?= htmlspecialchars($data['mood']) ?>'>
        </div>

            <div class="card card-todo">
                <div class="card-title">To-do List</div>
                <div class="edit" id="todo-list"></div>
            </div>

            <div class="card card-schedule">
                <div class="card-title">Schedule</div>
                <ul class="edit schedule-list" id="schedule-list" contenteditable="true"><?= $data['schedule'] !== '' ? $data['schedule'] : '<li></li>' ?></ul>
            </div>

            <div class="stars-area">
                <div class="stars-cluster">
                    <button type="submit" name="week_baru" value="1" class="week-btn w1 <?= $week==1?'active':'' ?>">
                        <svg width="126" height="126" viewBox="0 0 126 126" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M71.2445 1.73232C73.4136 -1.40762 78.329 -0.0159105 78.5301 3.79509L80.6117 43.2543C80.7018 44.9636 81.8695 46.426 83.5164 46.8923L122.814 58.0187C126.587 59.087 126.729 64.3826 123.019 65.652L85.3829 78.5282C83.6912 79.1069 82.5891 80.7381 82.6833 82.5235L84.735 121.416C84.9382 125.269 80.1076 127.148 77.6538 124.171L51.7706 92.7673C50.711 91.4817 48.9654 90.9875 47.3891 91.5268L8.88463 104.7C5.23436 105.949 2.10595 101.816 4.29874 98.642L26.4351 66.5978C27.4513 65.1267 27.3679 63.1599 26.2307 61.7802L0.931401 31.085C-1.56273 28.0589 1.33466 23.6239 5.1078 24.6922L44.405 35.8185C46.0518 36.2848 47.8129 35.6516 48.7857 34.2433L71.2445 1.73232Z" fill="#E8BCE3"/></svg>
                        <span class="wlabel" style="left:37px; top: 37px; transform:rotate(24deg);">W1</span>
                    </button>
                    <button type="submit" name="week_baru" value="2" class="week-btn w2 <?= $week==2?'active':'' ?>">
                        <svg width="132" height="131" viewBox="0 0 132 131" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M125.747 19.7262C129.544 18.3344 132.772 22.8128 130.251 25.9752L103.326 59.752C102.209 61.1544 102.161 63.1295 103.209 64.5843L125.922 96.0935C128.104 99.1198 125.321 103.221 121.703 102.312L80.6258 91.9905C79.1048 91.6083 77.5008 92.1502 76.5232 93.3766L48.5119 128.517C46.0572 131.596 41.0985 129.648 41.3955 125.722L44.3761 86.3057C44.5229 84.3643 43.2505 82.5992 41.3623 82.1247L3.02543 72.4918C-0.793844 71.5321 -1.07385 66.212 2.62359 64.8567L44.8168 49.3909C46.2893 48.8512 47.3105 47.5007 47.4288 45.9369L50.6224 3.70368C50.9037 -0.0163272 55.6744 -1.36004 57.8559 1.6663L80.5688 33.1754C81.6175 34.6303 83.5064 35.2093 85.1903 34.5921L125.747 19.7262Z" fill="#BCC1E8"/></svg>
                        <span class="wlabel" style="left: 36px; top: 55px; transform:rotate(-15deg);">W2</span>
                    </button>
                    <button type="submit" name="week_baru" value="3" class="week-btn w3 <?= $week==3?'active':'' ?>">
                        <svg width="138" height="125" viewBox="0 0 138 125" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M60.5128 3.01855C61.4336 -0.616405 66.4003 -1.10025 68.0053 2.28864L85.4212 39.0604C86.1434 40.5852 87.7448 41.493 89.4241 41.3294L132.84 37.0999C136.794 36.7147 138.831 41.7047 135.737 44.1963L103.531 70.1342C102.091 71.2941 101.633 73.2902 102.425 74.9616L119.33 110.654C120.974 114.126 117.255 117.694 113.854 115.907L75.5361 95.7738C74.1249 95.0323 72.4081 95.1996 71.1666 96.1995L37.4549 123.35C34.4629 125.76 30.125 122.976 31.0684 119.252L40.7666 80.9682C41.2207 79.1755 40.3867 77.3052 38.7496 76.445L2.14306 57.2107C-1.37342 55.3631 -0.337859 50.0738 3.61577 49.6886L47.0317 45.4591C48.711 45.2955 50.1071 44.0958 50.5214 42.4602L60.5128 3.01855Z" fill="#B9DBA7"/></svg>
                        <span class="wlabel" style="left: 40px; top: 48px; transform:rotate(2deg);">W3</span>
                    </button>
                    <button type="submit" name="week_baru" value="4" class="week-btn w4 <?= $week==4?'active':'' ?>">
                        <svg width="122" height="119" viewBox="0 0 122 119" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M33.0775 4.59697C32.5218 0.82998 37.0462 -1.50928 39.7987 1.12189L66.6238 26.7651C67.8588 27.9457 69.7072 28.2116 71.2249 27.4269L105.432 9.7408C108.921 7.93671 112.649 11.7176 110.796 15.1811L93.1807 48.1028C92.3335 49.6862 92.6454 51.6403 93.9436 52.8813L120.3 78.0761C123.087 80.7405 120.862 85.4175 117.036 84.9363L78.9485 80.1451C77.3049 79.9383 75.7039 80.7661 74.9224 82.2267L56.812 116.074C54.9929 119.474 49.8905 118.585 49.3279 114.771L44.0075 78.6999C43.7454 76.9232 42.3314 75.539 40.5495 75.3148L3.50349 70.6547C-0.393913 70.1645 -1.32371 64.9369 2.1656 63.1328L36.3725 45.4467C37.8901 44.6621 38.7418 43.0001 38.4925 41.3099L33.0775 4.59697Z" fill="#EBBBB4"/></svg>
                        <span class="wlabel" style="left: 30px; top: 46px; transform:rotate(-11deg);">W4</span>
                    </button>
                </div>
            </div>

            <div class="card card-notes">
                <div class="card-title">Notes</div>
                <textarea class="edit" name="notes" placeholder="What's on your mind?"><?= htmlspecialchars($data['notes']) ?></textarea>
            </div>
        </div>
    </div>
</form>

<div class="modal" id="modal-keluar">
    <div class="modal-box">
        <h3>Keluar Folder</h3>
        <p class="modal-text">Mau simpan perubahan dulu<br>sebelum keluar?</p>
        <div class="modal-actions">
            <a href="dashboard.php" class="btn-link">Jangan simpan</a>
            <button type="submit" 
                    name="keluar" 
                    value="1" 
                    form="form-note" 
                    class="btn-fill">Simpan
            </button>
        </div>
        <button type="button" class="btn-batal" onclick="tutupKeluar()">Batal</button>
    </div>
</div>

<script>
var todoData = <?= ($data['todo'] !== '' && $data['todo'] !== null) ? $data['todo'] : '[]' ?>;
var todoList = document.getElementById('todo-list');

function addTodoRow(text, done){
    var row = document.createElement('div');
    row.className = 'todo-row';
    var cb = document.createElement('input');
    cb.type = 'checkbox';
    cb.checked = !!done;
    var span = document.createElement('div');
    span.className = 'todo-text' + (done ? ' done' : '');
    span.contentEditable = 'true';
    span.innerText = text || '';
    cb.addEventListener('change', function(){ span.classList.toggle('done', cb.checked); });
    span.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && !e.shiftKey){
            e.preventDefault();
            var nr = addTodoRow('', false);
            nr.querySelector('.todo-text').focus();
        }
    });
    row.appendChild(cb);
    row.appendChild(span);
    todoList.appendChild(row);
    return row;
}
if (Array.isArray(todoData) && todoData.length){
    todoData.forEach(function(it){ addTodoRow(it.text, it.done); });
} else {
    addTodoRow('', false);
}

document.getElementById('form-note').addEventListener('submit', function(){
    document.getElementById('in-schedule').value = document.getElementById('schedule-list').innerHTML;
    var items = [];
    todoList.querySelectorAll('.todo-row').forEach(function(r){
        var t = r.querySelector('.todo-text').innerText.trim();
        var d = r.querySelector('input').checked;
        if (t !== '' || d) items.push({ text: t, done: d });
    });
    document.getElementById('in-todo').value = JSON.stringify(items);
});

// mood per hari (cycle: hari → 😊 → 😐 → 🙁 → hari)
var moodDays  = ['M', 'Tue', 'W', 'Th', 'Fr', 'Sat', 'Sun'];
var moodFaces = ['', '😊', '😐', '🙁'];   // index 0 = tulisan hari
var inMood = document.getElementById('in-mood');
var moodWrap = document.getElementById('mood-days');
var moodState = {};
try { moodState = JSON.parse(inMood.value || '{}'); } catch (e) { moodState = {}; }

function drawDay(btn, hari) {
    var s = moodState[hari] || 0;
    if (s === 0) { btn.textContent = hari; btn.classList.remove('has-face'); }
    else { btn.textContent = moodFaces[s]; btn.classList.add('has-face'); }
}
moodDays.forEach(function (hari) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'mood-day';
    drawDay(btn, hari);
    btn.addEventListener('click', function () {
        moodState[hari] = ((moodState[hari] || 0) + 1) % 4;
        drawDay(btn, hari);
        inMood.value = JSON.stringify(moodState);
    });
    moodWrap.appendChild(btn);
});

function bukaKeluar(){ document.getElementById('modal-keluar').style.display = 'flex'; }
function tutupKeluar(){ document.getElementById('modal-keluar').style.display = 'none'; }
</script>
</body>
</html>