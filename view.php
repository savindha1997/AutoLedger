<?php
    include('dbconfig.php');

    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    $id = 0;
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = intval($_GET['id']);
    } elseif (isset($_GET['brn'])) {
        $searchBrn = trim($_GET['brn']);
        if ($searchBrn !== '') {
            $searchBrnEsc = mysqli_real_escape_string($con, $searchBrn);
            $bykeFromBrn = mysqli_query($con, "SELECT id FROM bykes WHERE brn = '$searchBrnEsc' LIMIT 1");
            $bykeFromBrnData = $bykeFromBrn ? mysqli_fetch_assoc($bykeFromBrn) : null;
            if ($bykeFromBrnData && isset($bykeFromBrnData['id'])) {
                $id = intval($bykeFromBrnData['id']);
            } else {
                header("Location: addByke.php?msg=" . urlencode('No byke found for BRN: ' . $searchBrn) . "&msg_type=danger");
                exit();
            }
        }
    }

    if ($id <= 0) {
        header("Location: addByke.php?msg=" . urlencode('Invalid byke record') . "&msg_type=danger");
        exit();
    }

    $query1 = mysqli_query($con, "SELECT * FROM bykes WHERE id = $id");

    $resultData = mysqli_fetch_assoc($query1);

    if (!$resultData) {
        header("Location: addByke.php?msg=" . urlencode('Byke not found') . "&msg_type=danger");
        exit();
    }

    $brn = $resultData['brn'];
    $id = $resultData['id'];
    $bprice = $resultData['bprice'];
    $bdate = $resultData['bdate'];
    // investor removed from byke details
    $bstatus = $resultData['bstatus'];
    $sprice = $resultData['sprice'];
    // Optional fields
    $year_make_model = isset($resultData['year_make_model']) ? $resultData['year_make_model'] : '';
    $chassis_number = isset($resultData['chassis_number']) ? $resultData['chassis_number'] : '';
    $engine_number = isset($resultData['engine_number']) ? $resultData['engine_number'] : '';
    $bought_from = isset($resultData['bought_from']) ? $resultData['bought_from'] : '';
    $address = isset($resultData['address']) ? $resultData['address'] : '';
    $nic = isset($resultData['nic']) ? $resultData['nic'] : '';
    $phone_number = isset($resultData['phone_number']) ? $resultData['phone_number'] : '';

    // restore byke value: bought price + related byke expenses
    $resultExp = mysqli_query($con, "SELECT SUM(amount) AS bex_sum FROM bykeexpencesl WHERE brn='$brn'");
    $rowExp = mysqli_fetch_assoc($resultExp);
    $sumbykeexp = isset($rowExp['bex_sum']) ? $rowExp['bex_sum'] : 0;
    $bykevalue = (float)$bprice + (float)$sumbykeexp;
    $queryExpList = mysqli_query($con, "SELECT * FROM bykeexpencesl WHERE brn='$brn' ORDER BY id ASC");


    $query3 = mysqli_query($con, "SELECT * FROM bykesale WHERE brn='$brn' ");
    $resultData1 = mysqli_fetch_assoc($query3);
    $soldprice = $resultData1['sprice'];
    $solddate = $resultData1['sdate'];
    $buyer_name = isset($resultData1['buyer_name']) ? $resultData1['buyer_name'] : '';
    $buyer_address = isset($resultData1['buyer_address']) ? $resultData1['buyer_address'] : '';
    $buyer_nic = isset($resultData1['buyer_nic']) ? $resultData1['buyer_nic'] : '';
    $buyer_phone = isset($resultData1['buyer_phone']) ? $resultData1['buyer_phone'] : '';
    $s_method = isset($resultData1['s_method']) ? $resultData1['s_method'] : '';
    $leasing_company = isset($resultData1['leasing_company']) ? $resultData1['leasing_company'] : '';
    $down_payment = isset($resultData1['down_payment']) ? $resultData1['down_payment'] : '';
    $finance_charges = isset($resultData1['finance_charges']) ? $resultData1['finance_charges'] : '';
    $finance_amount = isset($resultData1['finance_amount']) ? $resultData1['finance_amount'] : '';

    $bykeprofit = $soldprice - $bykevalue;


?>





<?php include "header.php"; ?>
<title>Vehicle Information</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">


        <div class="row">

            <div class="col-lg-9  pl-4 pr-4">

                <div class="row align-items-start mt-4 mb-3">
                    <div class="col-lg-6 col-md-12">
                        <p class="topic-1 mb-1"><?php echo htmlspecialchars($brn); ?></p>
                        <p class="sub-topic-1 mb-0"><?php echo $year_make_model !== '' ? htmlspecialchars($year_make_model) : 'N/A'; ?></p>
                    </div>
                    <div class="col-lg-6 col-md-12 mt-3 mt-lg-0">
                        <div class="vehicle-view-actions">
                            <a href="invoice_pdf.php?id=<?php echo urlencode($id); ?>" class="btn dashboard-filter-btn vehicle-view-action-btn"><i class="ri-download-2-line mr-1"></i>Download Seller Invoice</a>
                            <a href="buyer_invoice_pdf.php?id=<?php echo urlencode($id); ?>" class="btn dashboard-filter-btn vehicle-view-action-btn"><i class="ri-download-2-line mr-1"></i>Download Buyer Invoice</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="card vehicle-info-card h-100">
                            <div class="card-body">
                                <p class="section-title mb-3">Vehicle Information</p>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Byke Info #ID</p>
                                    <p class="main-hed-byke"><?php echo htmlspecialchars($id); ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Status</p>
                                    <p class="main-hed-byke"><?php echo htmlspecialchars($bstatus); ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Chassis Number</p>
                                    <p class="main-hed-byke"><?php echo $chassis_number !== '' ? htmlspecialchars($chassis_number) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Engine Number</p>
                                    <p class="main-hed-byke"><?php echo $engine_number !== '' ? htmlspecialchars($engine_number) : 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="card vehicle-info-card h-100">
                            <div class="card-body">
                                <p class="section-title mb-3">Seller Information</p>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Bought Date</p>
                                    <p class="main-hed-byke"><?php echo $bdate !== '' ? htmlspecialchars($bdate) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Seller Name</p>
                                    <p class="main-hed-byke"><?php echo $bought_from !== '' ? htmlspecialchars($bought_from) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Seller's Address</p>
                                    <p class="main-hed-byke"><?php echo $address !== '' ? nl2br(htmlspecialchars($address)) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Seller's NIC</p>
                                    <p class="main-hed-byke"><?php echo $nic !== '' ? htmlspecialchars($nic) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Seller's Phone Number</p>
                                    <p class="main-hed-byke"><?php echo $phone_number !== '' ? htmlspecialchars($phone_number) : 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="card vehicle-info-card h-100">
                            <div class="card-body">
                                <p class="section-title mb-3">Buyer Information</p>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Sold Date</p>
                                    <p class="main-hed-byke"><?php echo $solddate !== '' ? htmlspecialchars($solddate) : 'N/A'; ?></p>
                                </div>


                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Buyer's Name</p>
                                    <p class="main-hed-byke"><?php echo $buyer_name !== '' ? htmlspecialchars($buyer_name) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Buyer's Address</p>
                                    <p class="main-hed-byke"><?php echo $buyer_address !== '' ? nl2br(htmlspecialchars($buyer_address)) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Buyer's NIC</p>
                                    <p class="main-hed-byke"><?php echo $buyer_nic !== '' ? htmlspecialchars($buyer_nic) : 'N/A'; ?></p>
                                </div>

                                <div class="vehicle-info-item">
                                    <p class="sub-text-byke">Buyer's Phone</p>
                                    <p class="main-hed-byke"><?php echo $buyer_phone !== '' ? htmlspecialchars($buyer_phone) : 'N/A'; ?></p>
                                </div>

                                
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="mt-2">
                    <p class="sub-text-byke mb-3">Related Expenses</p>
                    <div class="table-panel">
                    <table id="example2" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Date</th>
                                <th>BRN</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($exp = mysqli_fetch_assoc($queryExpList)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exp['id']); ?></td>
                                    <td><?php echo htmlspecialchars($exp['date']); ?></td>
                                    <td><?php echo htmlspecialchars($exp['brn']); ?></td>
                                    <td><?php echo htmlspecialchars($exp['description']); ?></td>
                                    <td><?php echo htmlspecialchars($exp['amount']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>


            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class="topic-1">Finance Information</p>

                    <div class="mt-3">
                        <p class="sub-text-byke">Bought Price</p>
                        <p class="main-hed-byke">Rs.<?php echo $bprice; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Total Expences Amount</p>
                        <p class="main-hed-byke">Rs.<?php echo $sumbykeexp; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Total Value</p>
                        <p class="main-hed-byke">Rs.<?php echo $bykevalue; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Sold Price</p>
                        <p class="main-hed-byke">Rs.<?php echo $soldprice; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Profit</p>
                        <p class="main-hed-byke">Rs.<?php echo $bykeprofit; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Method</p>
                                    <p class="main-hed-byke"><?php echo $s_method !== '' ? htmlspecialchars($s_method) : 'N/A'; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Leasing Company</p>
                        <p class="main-hed-byke"><?php echo $leasing_company !== '' ? htmlspecialchars($leasing_company) : 'N/A'; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Down Payment</p>
                        <p class="main-hed-byke">Rs.<?php echo $down_payment !== '' ? htmlspecialchars($down_payment) : 'N/A'; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Finance Charges</p>
                        <p class="main-hed-byke">Rs.<?php echo $finance_charges !== '' ? htmlspecialchars($finance_charges) : 'N/A'; ?></p>
                    </div>

                    <div class="mt-3">
                        <p class="sub-text-byke">Finance Amount</p>
                        <p class="main-hed-byke">Rs.<?php echo $finance_amount !== '' ? htmlspecialchars($finance_amount) : 'N/A'; ?></p>
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



