<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    if(isset($_POST['submit'])){

        $fromdate = $_POST['fromdate'];
        $todate = $_POST['todate'];

        $query1 = mysqli_query($con, "SELECT * FROM bykesale WHERE sdate BETWEEN '$fromdate' AND '$todate' ORDER BY id asc");

        $result10 = mysqli_query($con, "SELECT SUM(sprice) AS bykes_sum FROM bykesale WHERE sdate BETWEEN '$fromdate' AND '$todate' "); 
        $row9 = mysqli_fetch_assoc($result10); 
        $sumbykes = $row9['bykes_sum'];

        $query14 = "SELECT * FROM bykesale WHERE sdate BETWEEN '$fromdate' AND '$todate'";
        $result14 = mysqli_query($con, $query14);
        $bykecountsold = mysqli_num_rows($result14);

        $result17 = mysqli_query($con, "SELECT SUM(bvalue) AS totbv_sum FROM bykesale WHERE sdate BETWEEN '$fromdate' AND '$todate'"); 
        $row17 = mysqli_fetch_assoc($result17); 
        $sumtotbv = $row17['totbv_sum'];

        $filteredprofit = $sumbykes - $sumtotbv;

    }

    

?>





<?php include "header.php"; ?>
<title>Filtering Records</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12 right-boder-1 pl-4 pr-4">

                <div class="row">
                    <div class="col-lg-6">
                    <p class="mt-4 topic-1">Filtering Information</p>
                    <p class="mb-4 sub-topic-1">Here is a summary of selected date range</p>

                    </div>

                    <div class="col-lg-6">
                        <form method="POST">
                            <div class="filter-inline-row mt-4">
                                <div class="filter-inline-group">
                                    <label class="filter-input-label">From</label>
                                    <input class="form-control form-control-sm filter-inline-input" type="date" name="fromdate" >
                                </div>

                                <div class="filter-inline-group">
                                    <label class="filter-input-label">To</label>
                                    <input class="form-control form-control-sm filter-inline-input" type="date" name="todate">
                                </div>

                                <div class="filter-inline-group filter-inline-submit">
                                    <button class="btn dashboard-filter-btn" type="submit" name="submit">Submit</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="dashboard-finance-panel mt-3">

                <p class="section-title mb-3">Data Displaying From : <?php echo $fromdate;?> To : <?php echo $todate;?></p>



                <div class="row mt-3">
                    
                    <div class="col-lg-3 pb-3">
                        <div class="card finance-stat-card">
                            <div class="card-body">
                                <div class="finance-stat-row">
                                    <div class="finance-stat-icon"><i class="ri-shopping-bag-3-line"></i></div>
                                    <div class="finance-stat-content">
                                        <p class="finance-stat-title">Total Sold Vehicle Count</p>
                                        <p class="finance-stat-value">#<?php echo $bykecountsold; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-lg-3 pb-3">
                        <div class="card finance-stat-card">
                            <div class="card-body">
                                <div class="finance-stat-row">
                                    <div class="finance-stat-icon"><i class="ri-money-dollar-circle-line"></i></div>
                                    <div class="finance-stat-content">
                                        <p class="finance-stat-title">Total Profit</p>
                                        <p class="finance-stat-value">Rs.<?php echo $filteredprofit; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>


                </div>

                
                <div class="table-panel mt-4 mb-4">

                <p class="mb-3">Sold Byke Information</p>

                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>VRN</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th>Profit</th>
                                
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                            <tr>
                                <td><?php echo $res['id'];?></td>
                                <td><?php echo $res['brn'];?></td>
                                <td><?php echo $res['sdate'];?></td>
                                <td><?php echo $res['sprice'];?></td>
                                <td><?php echo $res['sprice'] - $res['bvalue'];?></td>
                                
                            </tr>

                            <?php } ?>                                
                        
                        </tbody>
                    </table>
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




