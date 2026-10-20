<?php
include('dbconfig.php');
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$columns = [];
$logs = false;
$tableExists = false;

$tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'system_logs'");
if ($tableCheck && mysqli_num_rows($tableCheck) > 0) {
    $tableExists = true;
    $columnResult = mysqli_query($con, "SHOW COLUMNS FROM system_logs");

    while ($column = mysqli_fetch_assoc($columnResult)) {
        $columns[] = $column['Field'];
    }

    $orderColumn = null;
    foreach (['id', 'created_at', 'logged_at', 'log_time', 'action_time', 'timestamp'] as $candidate) {
        if (in_array($candidate, $columns, true)) {
            $orderColumn = $candidate;
            break;
        }
    }

    $orderColumn = $orderColumn ?: $columns[0];
    $logs = mysqli_query($con, "SELECT * FROM system_logs ORDER BY `$orderColumn` DESC");
}
?>

<?php include "header.php"; ?>
<title>System Logs</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>

<section class="page-wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 pl-4 pr-4">
                <p class="mt-4 topic-1">System Logs</p>
                <p class="mb-4 sub-topic-1">Here are all recorded system activities</p>

                <div class="table-panel mb-4">
                    <?php if (!$tableExists) { ?>
                        <div class="alert alert-warning mb-0" role="alert">The system logs table is not available.</div>
                    <?php } elseif (!$logs) { ?>
                        <div class="alert alert-danger mb-0" role="alert">Unable to load system logs.</div>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table id="example" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <?php foreach ($columns as $column) { ?>
                                            <th><?php echo htmlspecialchars($column, ENT_QUOTES, 'UTF-8'); ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($log = mysqli_fetch_assoc($logs)) { ?>
                                        <tr>
                                            <?php foreach ($columns as $column) { ?>
                                                <td><?php echo htmlspecialchars((string)($log[$column] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>