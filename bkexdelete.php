<?php
// Include the database connection file
include('dbconfig.php');
session_start();

// Get id parameter value from URL
$id = $_GET['id'];

// fetch row (if needed)
$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM bykeexpencesl WHERE id = $id"));

// Delete row from the database table
$result = mysqli_query($con, "DELETE FROM bykeexpencesl WHERE id = $id");

if ($result) {
	$details = isset($row['brn']) ? ('brn:' . $row['brn'] . ' description:' . (isset($row['description']) ? $row['description'] : '') . ' amount:' . (isset($row['amount']) ? $row['amount'] : '')) : ('id:' . $id);
	system_log($con, 'Deleted byke expense', 'bykeexpencesl', $details, $id);
}

// Redirect to the main display page
header("Location:bykeexpencess.php");
