<?php
    include('dbconfig.php');

    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    $id = $_GET['id'];

    $query1 = mysqli_query($con, "SELECT * FROM criditgiving WHERE id = $id");

    $resultData = mysqli_fetch_assoc($query1);

    $brn = $resultData['brn'];
    $ctowhom = $resultData['ctowhom'];
    $cfrom = $resultData['cfrom'];
    $cgdate = $resultData['cgdate'];
    $camount = $resultData['camount'];

   
    $query2 = mysqli_query($con, "SELECT * FROM creditreturns WHERE cbfrom = '$ctowhom'");

    $result = mysqli_query($con, "SELECT SUM(amount) AS crm_sum FROM creditreturns WHERE cbfrom='$ctowhom'"); 
    $row = mysqli_fetch_assoc($result); 
    $sumcrm = $row['crm_sum'];


    $dueamount = $camount - $sumcrm ;

?>





<?php include "header.php"; ?>
<title>Cheque Asset Information</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">


        <div class="row">

            <div class="col-lg-9  pl-4 pr-4">

                <p class="mt-4 topic-1 mb-3">Related Vehicle: <?php echo $ctowhom; ?></p>

                <div class="row">
                    <div class="col-lg-3">
                        <p class="sub-text-byke">Cheque Asset #ID</p>
                        <p class="main-hed-byke"><?php echo $id; ?></p>
                    </div>



                    <div class="col-lg-3">
                        <p class="sub-text-byke">Cheque on Lease Date</p>
                        <p class="main-hed-byke"><?php echo $cgdate; ?></p>
                    </div>

                    <div class="col-lg-3">
                        <p class="sub-text-byke">Cheque Amount</p>
                        <p class="main-hed-byke">Rs.<?php echo $camount; ?></p>
                    </div>
                </div>
                

                <div class="mt-3">

                    <p class="sub-text-byke mb-3">Cheque Received</p>

                    <div class="table-panel">
                    <table class="table"  style="width:100%">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Date</th>
                                <th>Amount</th>
                                
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($res = mysqli_fetch_assoc($query2)) { ?>

                                <tr>
                                <td><?php echo $res['id'];?></td>
                                    <td><?php echo $res['cbdate'];?></td>
                                    <td><?php echo $res['amount'];?></td>

                                </tr>

                            <?php } ?>                                  
                        
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>


            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class="topic-1">Balance</p>

                    <div class="mt-3">
                        <p class="sub-text-byke">Due Amount</p>
                        <p class="main-hed-byke">Rs.<?php echo $dueamount; ?></p>
                    </div>

                    

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




