<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    $message = '';
    $message_type = '';

    if (isset($_GET['msg'])) {
        $message = $_GET['msg'];
        $message_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
    }

    if(isset($_POST['submit'])){

        $idate = $_POST['idate'];
        $iname = $_POST['iname'];
        $icaptail = $_POST['icaptail'];
        

        $query = mysqli_query($con, "INSERT INTO investors (idate, iname, icaptail) VALUES ('$idate','$iname','$icaptail')");

        if($query){
            system_log($con, 'Added investor', 'investors', 'date:' . $idate . ' name:' . $iname . ' capital:' . $icaptail);
            $message = 'Data added successfully';
            $message_type = 'success';
        }
        else{
            $message = 'Data add failed';
            $message_type = 'danger';
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM investors ORDER BY id asc");

    
?>





<?php include "header.php"; ?>
<title>Investors</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9 pl-4 pr-4">
                

            <p class="mt-4 topic-1">All Investors</p>
            <p class="mb-4 sub-topic-1">Here is investors information</p>

                <div class="table-panel mb-4">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Starting Captail</th>
                            <th>View</th>
                            
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                        <tr>
                            <td><?php echo $res['id'];?></td>
                            <td><?php echo $res['idate'];?></td>
                            <td><?php echo $res['iname'];?></td>
                            <td><?php echo $res['icaptail'];?></td>
                            <?php echo "<td><a href=\"viewinvestors.php?id=$res[id]\">View</a> </td>" ?>
                            
                        </tr>

                        <?php } ?>                                
                    
                    </tbody>
                </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class="topic-1">Add Investor</p>
                <p class="mb-4 sub-topic-1">Fill details and add record</p>

                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } ?>

                    <form method="POST">


                        <label class="add-byke-label">Date <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="idate" required>

                        <label class="add-byke-label">Name <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="iname" placeholder="Investor Name" required>

                        <label class="add-byke-label">Capital <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="icaptail" placeholder="e.g. 500000" required>


                        <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Investor</button>

                    </form>

                    </div>

            </div>
        
        </div>
    
    </div>

</section>

<?php 
}else{
header("Location: login.php");
exit();
}
include "footer.php"; 
?>



