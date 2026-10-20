<?php
    include('dbconfig.php');

    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    $id = $_GET['id'];

    $query1 = mysqli_query($con, "SELECT * FROM investors WHERE id = $id");

    $resultData = mysqli_fetch_assoc($query1);

    $iname = $resultData['iname'];
    $stcaptail = $resultData['icaptail'];

    $query2 = mysqli_query($con, "SELECT * FROM invintgiv WHERE investor='$iname'");


    $result1 = mysqli_query($con, "SELECT SUM(amount) AS ind_sum FROM investad WHERE investor='$iname'"); 
    $row = mysqli_fetch_assoc($result1); 
    $sumind = (float)($row['ind_sum'] ?? 0);



    $result6 = mysqli_query($con, "SELECT SUM(withdamount) AS inw_sum FROM investorwithdraw WHERE investor='$iname'"); 
    $row = mysqli_fetch_assoc($result6); 
    $suminw = (float)($row['inw_sum'] ?? 0);



    // Removed byke-related sums from investor view per request
    $bykecount = 0;

    $cashin = $stcaptail + $sumind;
    $cashout = $suminw;
    $avacash = $cashin - $cashout;
    $invcashinhand = ($stcaptail+$sumind) - $suminw;
    $totassets = ($stcaptail+$sumind)-$suminw;

?>



<?php include "header.php"; ?>
<title>Investor Information</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">


        <div class="row">

            <div class="col-lg-9  pl-4 pr-4">

                <p class="mt-4 topic-1 mb-3">Investor: <?php echo $iname; ?></p>

                <div class="row">
                    <div class="col-lg-3">
                        <p class="sub-text-byke">Invesror #ID</p>
                        <p class="main-hed-byke"><?php echo $id; ?></p>
                    </div>

                    

                    <div class="col-lg-3">
                        <p class="sub-text-byke">Available Capital</p>
                        <p class="main-hed-byke"><?php echo $totassets; ?></p>
                    </div>

                    <div class="col-lg-3">
                        <p class="sub-text-byke">Available Cash In-Hand </p>
                        <p class="main-hed-byke"><?php echo $invcashinhand; ?></p>
                    </div>

                </div>
                

                <div class="mt-3">

                    <p class="sub-text-byke mb-3">Profit Sharing</p>

                    <div class="table-panel">
                    <table id="example" class="table table-striped" style="width:100%">
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
                                    <td><?php echo $res['date'];?></td>
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


                <p class=" topic-1">Finance Information</p>

                    <div class="mt-3">
                        <p class="sub-text-byke">Starting Captail</p>
                        <p class="main-hed-byke">Rs.<?php echo $stcaptail; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Additional Diposits</p>
                        <p class="main-hed-byke">Rs.<?php echo $sumind; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Totle Withdrawals</p>
                        <p class="main-hed-byke">Rs.<?php echo $suminw; ?></p>
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