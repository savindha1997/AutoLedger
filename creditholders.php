<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    $message = '';
    $message_type = '';
    
    if(isset($_POST['submit'])){

        $cgdate = $_POST['cgdate'];
        $ctowhom = $_POST['ctowhom'];
        $cfrom = $_POST['cfrom'];
        $camount = $_POST['camount'];
        

        $query = mysqli_query($con, "INSERT INTO criditgiving (cgdate, ctowhom, cfrom, camount) VALUES ('$cgdate','$ctowhom','$cfrom','$camount')");

        if($query){
            $message = 'Data added successfully';
            $message_type = 'success';
        }
        else{
            $message = 'Data add failed';
            $message_type = 'danger';
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM criditgiving ORDER BY id asc");
    $query2 = mysqli_query($con, "SELECT * FROM investors");
?>





<?php include "header.php"; ?>
<title>Cheque Assets</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12 right-boder-1 pl-4 pr-4">
                

            <p class="mt-4 topic-1">Cheque Assets</p>
            <p class="mb-4 sub-topic-1">Here is your all cheque assets information</p>

                <div class="table-panel mb-4">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Related Vehicle</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                        <tr>
                            <td><?php echo $res['id'];?></td>
                            <td><?php echo $res['cgdate'];?></td>
                            <td><?php echo $res['ctowhom'];?></td>


                            <?php
                                $chol = $res['ctowhom'];
                                
                                $result = mysqli_query($con, "SELECT SUM(camount) AS amount_sum FROM criditgiving WHERE ctowhom='$chol'"); 
                                $row = mysqli_fetch_assoc($result); 
                                $sum = $row['amount_sum'];

                                $result1 = mysqli_query($con, "SELECT SUM(amount) AS due_sum FROM creditreturns WHERE cbfrom='$chol'"); 
                                $row1 = mysqli_fetch_assoc($result1); 
                                $sum1 = $row1['due_sum'];
                                
                                $avsum = $sum - $sum1;
                                
                            ?>

                            <td><?php echo $avsum;?></td>

                            <?php echo "<td><a href=\"viewcredietholder.php?id=$res[id]\">View</a> </td>" ?>
                            

                        </tr>

                        <?php } ?>                                
                    
                    </tbody>
                </table>
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




