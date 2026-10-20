<?php 
include('dbconfig.php');
session_start();

if (isset($_POST['uname']) && isset($_POST['password'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$uname = validate($_POST['uname']);
	$pass = trim($_POST['password']);

	if (!isset($_SESSION['login_attempts'])) {
		$_SESSION['login_attempts'] = 0;
	}
	if (!isset($_SESSION['login_lock_until'])) {
		$_SESSION['login_lock_until'] = 0;
	}

	if ((int)$_SESSION['login_lock_until'] > time()) {
		header("Location: login.php?error=Too many attempts. Please try again later.");
		exit();
	}

	if (empty($uname)) {
		system_log($con, 'Login failed', 'auth', 'reason:username missing');
		header("Location: login.php?error=User Name is required");
	    exit();
	}else if(empty($pass)){
		system_log($con, 'Login failed', 'auth', 'reason:password missing username:' . $uname);
        header("Location: login.php?error=Password is required");
	    exit();
	}else{
		$stmt = mysqli_prepare($con, "SELECT id, user_name, password FROM admin_users WHERE user_name = ? LIMIT 1");
		if (!$stmt) {
			header("Location: login.php?error=Login service unavailable");
			exit();
		}

		mysqli_stmt_bind_param($stmt, 's', $uname);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : null;
		mysqli_stmt_close($stmt);

		$validPassword = false;
		if ($row) {
			$stored = (string)$row['password'];
			$validPassword = password_verify($pass, $stored) || hash_equals($stored, $pass);
		}

		if ($row && $validPassword) {
			session_regenerate_id(true);
	            $_SESSION['user_name'] = $row['user_name'];
	            $_SESSION['id'] = $row['id'];
			$_SESSION['login_attempts'] = 0;
			$_SESSION['login_lock_until'] = 0;
			system_log($con, 'User login', 'auth', 'username:' . $row['user_name'], $row['id']);
	            header("Location: index.php");
		    exit();
		}

		$_SESSION['login_attempts'] = (int)$_SESSION['login_attempts'] + 1;
		if ((int)$_SESSION['login_attempts'] >= 5) {
			$_SESSION['login_lock_until'] = time() + 300;
			$_SESSION['login_attempts'] = 0;
		}

		system_log($con, 'Login failed', 'auth', 'reason:invalid credentials username:' . $uname);
		header("Location: login.php?error=Incorect User name or password");
		exit();
	}
	
}else{
	header("Location: login.php");
	exit();
}

?>