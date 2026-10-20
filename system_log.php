<?php

if (!function_exists('system_log')) {
    function system_log($con, $action, $entity, $details = '', $recordId = '')
    {
        if (!$con) {
            return false;
        }

        static $tableExists = null;
        static $columns = null;

        if ($tableExists === null) {
            $tableCheck = @mysqli_query($con, "SHOW TABLES LIKE 'system_logs'");
            $tableExists = ($tableCheck && mysqli_num_rows($tableCheck) > 0);
        }

        if (!$tableExists) {
            return false;
        }

        if ($columns === null) {
            $columns = [];
            $colResult = @mysqli_query($con, "SHOW COLUMNS FROM system_logs");
            if ($colResult) {
                while ($col = mysqli_fetch_assoc($colResult)) {
                    $columns[] = $col['Field'];
                }
            }
        }

        if (empty($columns)) {
            return false;
        }

        $values = [];

        $assign = function ($candidateNames, $value) use (&$values, $columns, $con) {
            foreach ($candidateNames as $name) {
                if (in_array($name, $columns, true) && !array_key_exists($name, $values)) {
                    $values[$name] = "'" . mysqli_real_escape_string($con, (string)$value) . "'";
                    return true;
                }
            }
            return false;
        };

        $actionText = trim((string)$action);
        if ($details !== '') {
            $actionText .= ' ' . trim((string)$details);
        }

        $assign(['action', 'action_type', 'activity', 'log_action', 'event'], $actionText);
        $assign(['entity', 'module', 'table_name', 'category', 'section'], $entity);
        $assign(['details', 'description', 'message', 'note'], $details);

        if ($recordId !== '') {
            $assign(['record_id', 'entity_id', 'ref_id', 'reference_id'], $recordId);
        }

        if (isset($_SESSION['id'])) {
            $assign(['user_id', 'admin_id'], $_SESSION['id']);
        }

        if (isset($_SESSION['user_name'])) {
            $assign(['user_name', 'username', 'user', 'actor'], $_SESSION['user_name']);
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $assign(['ip_address', 'ip'], $_SERVER['REMOTE_ADDR']);
        }

        foreach (['created_at', 'logged_at', 'log_time', 'action_time', 'timestamp'] as $timeColumn) {
            if (in_array($timeColumn, $columns, true) && !array_key_exists($timeColumn, $values)) {
                $values[$timeColumn] = "'" . mysqli_real_escape_string($con, date('Y-m-d H:i:s')) . "'";
                break;
            }
        }

        if (empty($values)) {
            return false;
        }

        $insertColumns = implode(', ', array_keys($values));
        $insertValues = implode(', ', array_values($values));

        return @mysqli_query($con, "INSERT INTO system_logs ($insertColumns) VALUES ($insertValues)");
    }
}

?>