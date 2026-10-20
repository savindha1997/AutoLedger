<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    $result1 = mysqli_query($con, "SELECT SUM(amount) AS bex_sum FROM bykeexpencesl "); 
    $row = mysqli_fetch_assoc($result1); 
    $sumbykeexpencess = $row['bex_sum'];

    $result2 = mysqli_query($con, "SELECT SUM(camount) AS crg_sum FROM criditgiving "); 
    $row1 = mysqli_fetch_assoc($result2); 
    $sumcrg = $row1['crg_sum'];

    $result3 = mysqli_query($con, "SELECT SUM(withdamount) AS inw_sum FROM investorwithdraw "); 
    $row2 = mysqli_fetch_assoc($result3); 
    $suminw = $row2['inw_sum'];

    $result4 = mysqli_query($con, "SELECT SUM(amount) AS oth_sum FROM otherincome "); 
    $row3 = mysqli_fetch_assoc($result4); 
    $sumoth = $row3['oth_sum'];

    $result6 = mysqli_query($con, "SELECT SUM(amount) AS ind_sum FROM investad "); 
    $row5 = mysqli_fetch_assoc($result6); 
    $sumind = $row5['ind_sum'];

    $result7 = mysqli_query($con, "SELECT SUM(amount) AS cdr_sum FROM creditreturns "); 
    $row6 = mysqli_fetch_assoc($result7); 
    $sumcdr = $row6['cdr_sum'];

    $result8 = mysqli_query($con, "SELECT SUM(amount) AS ote_sum FROM otherexpencess "); 
    $row7 = mysqli_fetch_assoc($result8); 
    $sumote = $row7['ote_sum'];

    $result9 = mysqli_query($con, "SELECT SUM(bprice) AS bykeb_sum FROM bykes "); 
    $row8 = mysqli_fetch_assoc($result9); 
    $sumbykeb = $row8['bykeb_sum'];

    $result10 = mysqli_query($con, "SELECT SUM(sprice) AS bykes_sum FROM bykesale "); 
    $row9 = mysqli_fetch_assoc($result10); 
    $sumbykes = $row9['bykes_sum'];

    $result11 = mysqli_query($con, "SELECT SUM(icaptail) AS invs_sum FROM investors "); 
    $row10 = mysqli_fetch_assoc($result11); 
    $suminvs = $row10['invs_sum'];

    $result12 = mysqli_query($con, "SELECT SUM(amount) AS inig_sum FROM invintgiv "); 
    $row11 = mysqli_fetch_assoc($result12); 
    $suminig = $row11['inig_sum'];

    

    $query13 = "SELECT * FROM bykes WHERE bstatus='In-store'";
    $result13 = mysqli_query($con, $query13);
    $bykecount = mysqli_num_rows($result13);

    $query14 = "SELECT * FROM bykes WHERE bstatus='Sold'";
    $result14 = mysqli_query($con, $query14);
    $bykecountsold = mysqli_num_rows($result14);

    $query15 = "SELECT * FROM bykes";
    $result15 = mysqli_query($con, $query15);
    $bykecountall = mysqli_num_rows($result15);


    $result16 = mysqli_query($con, "SELECT SUM(sprice) AS totbs_sum FROM bykesale "); 
    $row16 = mysqli_fetch_assoc($result16); 
    $sumtotbs = $row16['totbs_sum'];

    $result17 = mysqli_query($con, "SELECT SUM(bvalue) AS totbv_sum FROM bykesale "); 
    $row17 = mysqli_fetch_assoc($result17); 
    $sumtotbv = $row17['totbv_sum'];

    $result18 = mysqli_query($con, "SELECT SUM(amount) AS totpg_sum FROM invintgiv "); 
    $row18 = mysqli_fetch_assoc($result18); 
    $sumtotpg = $row18['totpg_sum'];

    $result19 = mysqli_query($con, "SELECT SUM(bprice) AS bykebin_sum FROM bykes WHERE bstatus='Sold'"); 
    $row19 = mysqli_fetch_assoc($result19); 
    $sumbykebin = $row19['bykebin_sum'];

    $result20 = mysqli_query($con, "SELECT SUM(bprice) AS bykebina_sum FROM bykes WHERE bstatus='In-store'"); 
    $row20 = mysqli_fetch_assoc($result20); 
    $sumbykebina = $row20['bykebina_sum'];


    
    $totcashin = $suminvs + $sumbykes + $sumcdr + $sumind + $sumoth;

    $totcashout = $sumbykeexpencess + $sumcrg + $suminw + $sumote + $sumbykeb + $suminig;

    $cashinhand = $totcashin - $totcashout;

    $outforprofit = $sumbykeb + $sumbykeexpencess + $suminig;

    $profitfornow = ($sumbykes + $sumoth)  - $outforprofit;

    $totvalueinst = ($sumbykeb + $sumbykeexpencess) - $sumtotbv;
    
    $totcrdbal = $sumcrg - $sumcdr;

    $totinvamount = ($suminvs+$sumind) - $suminw;

    $totbsvlue = $cashinhand + $totvalueinst;

    $totproinhand = (($sumtotbs-$sumtotbv) + $sumoth) - ($sumtotpg+$sumote);


    $avalbalan_wop = (($suminvs+$sumind)-$suminw);

    $avalbalan = (($suminvs+$sumind)-$suminw)+$totproinhand;

    $cashinhand_wop = $totcashin - $totcashout - $totproinhand;

?>





<?php include "header.php"; ?>
<title>Dashboard</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12 right-boder-1 pl-4 pr-4">

            <div class="row">
                <div class="col-lg-10">
                <p class="mt-4 topic-1">Dashboard</p>
                <p class="mb-4 sub-topic-1">Here is the analitical summary of your business</p>

                </div>

                <div class="col-lg-2">
                <a class="mt-4 btn dashboard-filter-btn float-right" href="filterrecords.php" role="button"><i class="ri-filter-line"></i> Filter</a>
                </div>
            </div>

                

                <div class="row">

                
                    <div class="col-lg-4 pb-3">
                        <div class="card dashboard-summary-card">
                            <div class="card-body">
                            <div class="dashboard-summary-top">
                                <div class="dashboard-summary-icon">
                                    <i class="ri-store-2-line"></i>
                                </div>
                                <p class="main-hed-byke dashboard-summary-value"><?php echo $bykecount; ?></p>
                            </div>
                            <div class="dashboard-summary-meta">
                                <p class="sub-text-byke">In-Store Vehicle Count</p>
                                <span class="dashboard-summary-period">All Time</span>
                            </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="col-lg-4 pb-3">
                        <div class="card dashboard-summary-card">
                            <div class="card-body">
                            <div class="dashboard-summary-top">
                                <div class="dashboard-summary-icon">
                                    <i class="ri-shopping-bag-3-line"></i>
                                </div>
                                <p class="main-hed-byke dashboard-summary-value"><?php echo $bykecountsold; ?></p>
                            </div>
                            <div class="dashboard-summary-meta">
                                <p class="sub-text-byke">Sold Vehicle Count</p>
                                <span class="dashboard-summary-period">All Time</span>
                            </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-lg-4 pb-3">
                        <div class="card dashboard-summary-card">
                            <div class="card-body">
                            <div class="dashboard-summary-top">
                                <div class="dashboard-summary-icon">
                                    <i class="ri-stack-line"></i>
                                </div>
                                <p class="main-hed-byke dashboard-summary-value"><?php echo $bykecountall; ?></p>
                            </div>
                            <div class="dashboard-summary-meta">
                                <p class="sub-text-byke">All Vehicle Count</p>
                                <span class="dashboard-summary-period">All Time</span>
                            </div>
                            </div>
                        </div>
                        
                    </div>

                    


                </div>



                <div class="dashboard-finance-panel mt-3">
                <div class="row">

                    <div class="col-12">
                        <p class="mb-3 finance-title">Finance Information</p>
                    </div>

                    <div class="col-12 pt-1 mb-3">
                        <div class="card dashboard-profit-highlight">
                            <div class="card-body">
                                <div class="dashboard-profit-row">
                                    <div class="dashboard-profit-icon">
                                        <i class="ri-pie-chart-line"></i>
                                    </div>
                                    <div class="dashboard-profit-content">
                                        <p class="sub-text-byke mb-0">Total Business Profit</p>
                                        <p class="main-hed-byke dashboard-profit-value">Rs.<?php echo $totproinhand; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="pb-3">
                            <div class="card finance-stat-card">
                                <div class="card-body">
                                    <div class="finance-stat-row">
                                        <div class="finance-stat-icon"><i class="ri-wallet-3-line"></i></div>
                                        <div class="finance-stat-content">
                                            <p class="finance-stat-title">Total Cash In-Hand With Profit</p>
                                            <p class="finance-stat-value">Rs.<?php echo $cashinhand; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-3">
                            <div class="card finance-stat-card">
                                <div class="card-body">
                                    <div class="finance-stat-row">
                                        <div class="finance-stat-icon"><i class="ri-safe-line"></i></div>
                                        <div class="finance-stat-content">
                                            <p class="finance-stat-title">Total Cash In-Hand Without Profit</p>
                                            <p class="finance-stat-value">Rs.<?php echo $cashinhand_wop; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="pb-3">
                            <div class="card finance-stat-card">
                                <div class="card-body">
                                    <div class="finance-stat-row">
                                        <div class="finance-stat-icon"><i class="ri-store-2-line"></i></div>
                                        <div class="finance-stat-content">
                                            <p class="finance-stat-title">Total Value of In-Store Vehicles</p>
                                            <p class="finance-stat-value">Rs.<?php echo $totvalueinst; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-3">
                            <div class="card finance-stat-card">
                                <div class="card-body">
                                    <div class="finance-stat-row">
                                        <div class="finance-stat-icon"><i class="ri-bank-card-line"></i></div>
                                        <div class="finance-stat-content">
                                            <p class="finance-stat-title">Receivable Amount</p>
                                            <p class="finance-stat-value">Rs.<?php echo $totcrdbal; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="pb-3">
                            <div class="card finance-stat-card">
                                <div class="card-body">
                                    <div class="finance-stat-row">
                                        <div class="finance-stat-icon"><i class="ri-line-chart-line"></i></div>
                                        <div class="finance-stat-content">
                                            <p class="finance-stat-title">Total Business Assets</p>
                                            <p class="finance-stat-value">Rs.<?php echo $avalbalan; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pb-3">
                            <div class="card finance-stat-card">
                                <div class="card-body">
                                    <div class="finance-stat-row">
                                        <div class="finance-stat-icon"><i class="ri-funds-line"></i></div>
                                        <div class="finance-stat-content">
                                            <p class="finance-stat-title">Total Business Assets (without profit)</p>
                                            <p class="finance-stat-value">Rs.<?php echo $avalbalan_wop; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

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



