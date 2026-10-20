<?php
// Hidden super admin page: reachable only by typing the URL; not linked anywhere.

const SA_ENTER_PASSWORD = 'entersuperadmin@3856';
const SA_CLEAR_PASSWORD = 'cleardbsuperadmin@1780';
const SA_PROTECTED_TABLES = ['business_info', 'admin_users', 'mobile_users'];
const SA_SESSION_TTL = 900;

header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");

require_once __DIR__ . '/dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('SA_SESSID');
    session_start();
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function sa_csrf() {
    if (empty($_SESSION['sa_csrf'])) {
        $_SESSION['sa_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['sa_csrf'];
}

function sa_check_csrf() {
    $t = $_POST['csrf'] ?? '';
    if (!is_string($t) || empty($_SESSION['sa_csrf']) || !hash_equals($_SESSION['sa_csrf'], $t)) {
        http_response_code(403);
        exit('Invalid request.');
    }
}

// Throttle for a given password scope; returns seconds left if locked.
function sa_locked($scope) {
    $until = (int)($_SESSION["sa_lock_$scope"] ?? 0);
    return $until > time() ? $until - time() : 0;
}

function sa_fail($scope) {
    $_SESSION["sa_fail_$scope"] = (int)($_SESSION["sa_fail_$scope"] ?? 0) + 1;
    if ($_SESSION["sa_fail_$scope"] >= 5) {
        $_SESSION["sa_lock_$scope"] = time() + 900;
        $_SESSION["sa_fail_$scope"] = 0;
    }
    usleep(500000);
}

function sa_ok($scope) {
    $_SESSION["sa_fail_$scope"] = 0;
}

function sa_ident($name) { return '`' . str_replace('`', '``', $name) . '`'; }

function sa_tables($con) {
    $out = [];
    $r = mysqli_query($con, 'SHOW FULL TABLES');
    while ($row = mysqli_fetch_row($r)) {
        $out[] = ['name' => $row[0], 'type' => $row[1]];
    }
    return $out;
}

function sa_columns($con, $table) {
    $cols = [];
    $r = mysqli_query($con, 'SHOW COLUMNS FROM ' . sa_ident($table));
    while ($row = mysqli_fetch_assoc($r)) { $cols[] = $row; }
    return $cols;
}

function sa_pk($cols) {
    foreach ($cols as $c) { if ($c['Key'] === 'PRI') return $c['Field']; }
    return null;
}

function sa_is_secret($col) { return (bool)preg_match('/pass|pin|secret/i', $col); }

function sa_dump($con) {
    $nl = "\n";
    echo "-- AutoLedger dump " . date('Y-m-d H:i:s') . $nl;
    echo "SET NAMES utf8mb4;" . $nl . "SET FOREIGN_KEY_CHECKS=0;" . $nl . "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';" . $nl . $nl;
    foreach (sa_tables($con) as $t) {
        $name = $t['name'];
        $id = sa_ident($name);
        if ($t['type'] === 'VIEW') {
            $r = mysqli_fetch_row(mysqli_query($con, "SHOW CREATE VIEW $id"));
            echo "DROP VIEW IF EXISTS $id;" . $nl . $r[1] . ";" . $nl . $nl;
            continue;
        }
        $r = mysqli_fetch_row(mysqli_query($con, "SHOW CREATE TABLE $id"));
        echo "DROP TABLE IF EXISTS $id;" . $nl . $r[1] . ";" . $nl . $nl;
        $res = mysqli_query($con, "SELECT * FROM $id", MYSQLI_USE_RESULT);
        $fields = mysqli_fetch_fields($res);
        $names = implode(',', array_map(fn($f) => sa_ident($f->name), $fields));
        while ($row = mysqli_fetch_row($res)) {
            $vals = [];
            foreach ($row as $i => $v) {
                if ($v === null) { $vals[] = 'NULL'; }
                elseif ($fields[$i]->flags & MYSQLI_BINARY_FLAG && $fields[$i]->type >= 249 && $fields[$i]->type <= 252) { $vals[] = '0x' . bin2hex($v); }
                elseif (is_numeric($v) && !preg_match('/^0\d/', $v) && preg_match('/^-?\d+(\.\d+)?$/', $v)) { $vals[] = $v; }
                else { $vals[] = "'" . mysqli_real_escape_string($con, $v) . "'"; }
            }
            echo "INSERT INTO $id ($names) VALUES (" . implode(',', $vals) . ");" . $nl;
        }
        mysqli_free_result($res);
        echo $nl;
    }
    echo "SET FOREIGN_KEY_CHECKS=1;" . $nl;
}

$msg = '';
$err = '';

// Session expiry
if (!empty($_SESSION['sa_auth']) && (time() - (int)($_SESSION['sa_last'] ?? 0)) > SA_SESSION_TTL) {
    unset($_SESSION['sa_auth']);
    $err = 'Session expired.';
}

$action = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['action'] ?? '') : '';

// Login
if ($action === 'login') {
    sa_check_csrf();
    if ($w = sa_locked('enter')) {
        $err = "Too many attempts. Try again in $w seconds.";
    } elseif (hash_equals(SA_ENTER_PASSWORD, (string)($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['sa_auth'] = true;
        $_SESSION['sa_last'] = time();
        unset($_SESSION['sa_csrf']);
        sa_ok('enter');
        system_log($con, 'Super admin login', 'superadmin', 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        header('Location: superadminmakechanges.php');
        exit;
    } else {
        sa_fail('enter');
        system_log($con, 'Super admin login failed', 'superadmin', 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        $err = 'Incorrect password.';
    }
}

if (empty($_SESSION['sa_auth'])) {
    $csrf = sa_csrf();
    ?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="robots" content="noindex,nofollow"><title>Restricted</title>
<style>body{font-family:sans-serif;background:#f1f3f6;display:flex;justify-content:center;align-items:center;height:100vh;margin:0}
form{background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 10px #0002;width:300px}
input,button{width:100%;padding:10px;margin-top:10px;box-sizing:border-box}button{background:#222;color:#fff;border:0;cursor:pointer}.e{color:#c00;margin-top:10px}</style></head><body>
<form method="post" autocomplete="off"><h3>Restricted Area</h3>
<input type="hidden" name="action" value="login"><input type="hidden" name="csrf" value="<?= e($csrf) ?>">
<input type="password" name="password" placeholder="Password" required autofocus autocomplete="off">
<button type="submit">Enter</button>
<?php if ($err): ?><div class="e"><?= e($err) ?></div><?php endif; ?></form></body></html><?php
    exit;
}

$_SESSION['sa_last'] = time();
$csrf = sa_csrf();

$allTables = array_column(sa_tables($con), 'name');
$editable = ['business_info', 'admin_users', 'mobile_users'];
$editable = array_values(array_intersect($editable, $allTables));

if ($action !== '') {
    sa_check_csrf();

    if ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        header('Location: superadminmakechanges.php');
        exit;
    }

    if ($action === 'download') {
        system_log($con, 'Super admin DB download', 'superadmin', 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="atl_backup_' . date('Ymd_His') . '.sql"');
        sa_dump($con);
        exit;
    }

    $table = (string)($_POST['table'] ?? '');
    $needsTable = in_array($action, ['update', 'insert', 'delete'], true);
    if ($needsTable && !in_array($table, $editable, true)) {
        http_response_code(400);
        exit('Invalid table.');
    }

    if ($needsTable) {
        $cols = sa_columns($con, $table);
        $pk = sa_pk($cols);
        $colMap = [];
        foreach ($cols as $c) { $colMap[$c['Field']] = $c; }
        $hashPw = ($table === 'admin_users');

        if ($action === 'delete') {
            if (!$pk) { $err = 'Table has no primary key.'; }
            else {
                if ($table === 'admin_users') {
                    $cnt = (int)mysqli_fetch_row(mysqli_query($con, 'SELECT COUNT(*) FROM admin_users'))[0];
                    if ($cnt <= 1) { $err = 'Cannot delete the last admin user.'; }
                }
                if (!$err) {
                    $stmt = mysqli_prepare($con, 'DELETE FROM ' . sa_ident($table) . ' WHERE ' . sa_ident($pk) . ' = ? LIMIT 1');
                    $id = (string)($_POST['pk'] ?? '');
                    mysqli_stmt_bind_param($stmt, 's', $id);
                    mysqli_stmt_execute($stmt);
                    $msg = 'Deleted ' . mysqli_stmt_affected_rows($stmt) . ' row(s).';
                    system_log($con, 'Super admin delete', 'superadmin', "table:$table id:$id");
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            $data = [];
            foreach ($colMap as $f => $c) {
                if (stripos($c['Extra'], 'auto_increment') !== false || stripos($c['Extra'], 'GENERATED') !== false) continue;
                if (!array_key_exists($f, $_POST['f'] ?? []) || !is_string($_POST['f'][$f])) continue;
                $v = $_POST['f'][$f];
                if ($action === 'update' && sa_is_secret($f) && $v === '') continue; // keep existing
                if ($hashPw && sa_is_secret($f) && $v !== '') $v = password_hash($v, PASSWORD_DEFAULT);
                if ($v === '' && $c['Null'] === 'YES') { $data[$f] = null; } else { $data[$f] = $v; }
            }
            if (!$data) { $err = 'No data.'; }
            elseif ($action === 'insert') {
                $fl = implode(',', array_map('sa_ident', array_keys($data)));
                $ph = implode(',', array_fill(0, count($data), '?'));
                $stmt = mysqli_prepare($con, 'INSERT INTO ' . sa_ident($table) . " ($fl) VALUES ($ph)");
                $vals = array_values($data);
                mysqli_stmt_bind_param($stmt, str_repeat('s', count($vals)), ...$vals);
                try {
                    mysqli_stmt_execute($stmt);
                    $msg = 'Row added.';
                    system_log($con, 'Super admin insert', 'superadmin', "table:$table");
                } catch (mysqli_sql_exception $ex) { $err = 'Insert failed: ' . $ex->getMessage(); }
                mysqli_stmt_close($stmt);
            } else {
                if (!$pk) { $err = 'Table has no primary key.'; }
                else {
                    $set = implode(',', array_map(fn($k) => sa_ident($k) . '=?', array_keys($data)));
                    $stmt = mysqli_prepare($con, 'UPDATE ' . sa_ident($table) . " SET $set WHERE " . sa_ident($pk) . ' = ?');
                    $vals = array_values($data);
                    $vals[] = (string)($_POST['pk'] ?? '');
                    mysqli_stmt_bind_param($stmt, str_repeat('s', count($vals)), ...$vals);
                    try {
                        mysqli_stmt_execute($stmt);
                        $msg = 'Row updated.';
                        system_log($con, 'Super admin update', 'superadmin', "table:$table id:" . end($vals));
                    } catch (mysqli_sql_exception $ex) { $err = 'Update failed: ' . $ex->getMessage(); }
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    if ($action === 'cleardb') {
        if ($w = sa_locked('clear')) {
            $err = "Too many attempts. Try again in $w seconds.";
        } elseif (!hash_equals(SA_CLEAR_PASSWORD, (string)($_POST['password'] ?? ''))) {
            sa_fail('clear');
            $err = 'Incorrect clear DB password.';
        } elseif (($_POST['confirm'] ?? '') !== 'CLEAR') {
            $err = 'Type CLEAR to confirm.';
        } else {
            sa_ok('clear');
            mysqli_query($con, 'SET FOREIGN_KEY_CHECKS=0');
            $n = 0;
            foreach (sa_tables($con) as $t) {
                if ($t['type'] === 'VIEW' || in_array($t['name'], SA_PROTECTED_TABLES, true)) continue;
                mysqli_query($con, 'TRUNCATE TABLE ' . sa_ident($t['name']));
                $n++;
            }
            mysqli_query($con, 'SET FOREIGN_KEY_CHECKS=1');
            $msg = "Cleared $n table(s).";
            system_log($con, 'Super admin clear DB', 'superadmin', "tables:$n ip:" . ($_SERVER['REMOTE_ADDR'] ?? ''));
        }
    }
}

?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="robots" content="noindex,nofollow"><title>Super Admin</title>
<style>
body{font-family:sans-serif;background:#f1f3f6;margin:0;padding:20px;color:#222}
.card{background:#fff;padding:16px;border-radius:8px;box-shadow:0 1px 6px #0002;margin-bottom:20px;overflow-x:auto}
table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:4px;font-size:13px}
input[type=text],input[type=password]{padding:5px;width:100%;box-sizing:border-box}
button{padding:6px 12px;border:0;border-radius:4px;cursor:pointer;background:#222;color:#fff}
.danger{background:#c0392b}.ok{background:#e7f6e7;padding:8px;margin-bottom:10px}.er{background:#fdeaea;padding:8px;margin-bottom:10px}
.top{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.top form{margin:0}
</style></head><body>
<div class="top"><h2 style="margin:0;flex:1">Super Admin</h2>
<form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="download"><button>Download DB (.sql)</button></form>
<form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="logout"><button>Logout</button></form></div>
<br>
<?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="er"><?= e($err) ?></div><?php endif; ?>

<?php foreach ($editable as $table):
    $cols = sa_columns($con, $table);
    $pk = sa_pk($cols);
    $showAdd = $table !== 'business_info';
    $rows = mysqli_query($con, 'SELECT * FROM ' . sa_ident($table));
    $fieldsEditable = array_filter($cols, fn($c) => stripos($c['Extra'], 'auto_increment') === false && stripos($c['Extra'], 'GENERATED') === false);
?>
<div class="card"><h3><?= e($table) ?></h3>
<table><tr><?php foreach ($cols as $c): ?><th><?= e($c['Field']) ?></th><?php endforeach; ?><th>Actions</th></tr>
<?php while ($row = mysqli_fetch_assoc($rows)): $formId = 'f' . md5($table . ($pk ? $row[$pk] : serialize($row))); ?>
<tr>
<?php foreach ($cols as $c): $f = $c['Field']; $isAuto = !isset($fieldsEditable[array_search($c, $cols, true)]); ?>
<td><?php if ($isAuto): ?><?= e($row[$f]) ?><?php elseif ($table === 'admin_users' && sa_is_secret($f)): ?>
<input type="password" form="<?= $formId ?>" name="f[<?= e($f) ?>]" placeholder="(unchanged)" autocomplete="new-password">
<?php elseif (sa_is_secret($f)): ?><input type="text" form="<?= $formId ?>" name="f[<?= e($f) ?>]" value="<?= e($row[$f]) ?>">
<?php else: ?><input type="text" form="<?= $formId ?>" name="f[<?= e($f) ?>]" value="<?= e($row[$f]) ?>"><?php endif; ?></td>
<?php endforeach; ?>
<td style="white-space:nowrap">
<form id="<?= $formId ?>" method="post" style="display:inline">
<input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="table" value="<?= e($table) ?>">
<input type="hidden" name="pk" value="<?= e($pk ? $row[$pk] : '') ?>">
<button name="action" value="update">Save</button>
<?php if ($showAdd): ?><button name="action" value="delete" class="danger" onclick="return confirm('Delete this row?')">Delete</button><?php endif; ?>
</form></td></tr>
<?php endwhile; ?></table>

<?php if ($showAdd): ?>
<h4>Add new</h4>
<form method="post" autocomplete="off"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="table" value="<?= e($table) ?>"><input type="hidden" name="action" value="insert">
<table><tr><?php foreach ($fieldsEditable as $c): ?><th><?= e($c['Field']) ?></th><?php endforeach; ?></tr>
<tr><?php foreach ($fieldsEditable as $c): ?><td><input type="<?= ($table === 'admin_users' && sa_is_secret($c['Field'])) ? 'password' : 'text' ?>" name="f[<?= e($c['Field']) ?>]" autocomplete="off"></td><?php endforeach; ?></tr></table>
<br><button>Add</button></form>
<?php endif; ?>
</div>
<?php endforeach; ?>

<div class="card"><h3>Clear DB</h3>
<p>Truncates every table except <?= e(implode(', ', SA_PROTECTED_TABLES)) ?>. This cannot be undone.</p>
<form method="post" autocomplete="off" style="max-width:320px"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="cleardb">
<input type="password" name="password" placeholder="Clear DB password" required autocomplete="off"><br><br>
<input type="text" name="confirm" placeholder="Type CLEAR to confirm" required autocomplete="off"><br><br>
<button class="danger">Clear DB</button></form></div>
</body></html>
