<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {


    $result11 = mysqli_query($con, "SELECT SUM(icaptail) AS invs_sum FROM investors "); 
    $row10 = mysqli_fetch_assoc($result11); 
    $suminvs = $row10['invs_sum'];

    $result10 = mysqli_query($con, "SELECT SUM(sprice) AS bykes_sum FROM bykesale "); 
    $row9 = mysqli_fetch_assoc($result10); 
    $sumbykes = $row9['bykes_sum'];

    $result7 = mysqli_query($con, "SELECT SUM(amount) AS cdr_sum FROM creditreturns "); 
    $row6 = mysqli_fetch_assoc($result7); 
    $sumcdr = $row6['cdr_sum'];

    $result6 = mysqli_query($con, "SELECT SUM(amount) AS ind_sum FROM investad "); 
    $row5 = mysqli_fetch_assoc($result6); 
    $sumind = $row5['ind_sum'];

    $result4 = mysqli_query($con, "SELECT SUM(amount) AS oth_sum FROM otherincome "); 
    $row3 = mysqli_fetch_assoc($result4); 
    $sumoth = $row3['oth_sum'];

    $result1 = mysqli_query($con, "SELECT SUM(amount) AS bex_sum FROM bykeexpencesl "); 
    $row = mysqli_fetch_assoc($result1); 
    $sumbykeexpencess = $row['bex_sum'];

    $result2 = mysqli_query($con, "SELECT SUM(camount) AS crg_sum FROM criditgiving "); 
    $row1 = mysqli_fetch_assoc($result2); 
    $sumcrg = $row1['crg_sum'];

    $result3 = mysqli_query($con, "SELECT SUM(withdamount) AS inw_sum FROM investorwithdraw "); 
    $row2 = mysqli_fetch_assoc($result3); 
    $suminw = $row2['inw_sum'];

    $result8 = mysqli_query($con, "SELECT SUM(amount) AS ote_sum FROM otherexpencess "); 
    $row7 = mysqli_fetch_assoc($result8); 
    $sumote = $row7['ote_sum'];

    $result9 = mysqli_query($con, "SELECT SUM(bprice) AS bykeb_sum FROM bykes "); 
    $row8 = mysqli_fetch_assoc($result9); 
    $sumbykeb = $row8['bykeb_sum'];

    $result12 = mysqli_query($con, "SELECT SUM(amount) AS inig_sum FROM invintgiv "); 
    $row11 = mysqli_fetch_assoc($result12); 
    $suminig = $row11['inig_sum'];



    $totcashin = $suminvs + $sumbykes + $sumcdr + $sumind + $sumoth;
    $totcashout = $sumbykeexpencess + $sumcrg + $suminw + $sumote + $sumbykeb + $suminig;
    $cashinhand = $totcashin - $totcashout;


    $message = '';
    $message_type = '';
    if (isset($_GET['msg'])) {
        $message = $_GET['msg'];
        $message_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
    }
    if(isset($_POST['submit'])){

        $brn = isset($_POST['brn']) ? trim($_POST['brn']) : '';
        $bcost = isset($_POST['bcost']) ? trim($_POST['bcost']) : '';
        $bcost_raw = str_replace(',', '', $bcost);
        $bdate = isset($_POST['bdate']) ? trim($_POST['bdate']) : '';
        // Status is fixed for new bykes added from this form
        $bstatus = 'In-store';

        // Optional fields
        $year_make_model = isset($_POST['year_make_model']) ? trim($_POST['year_make_model']) : '';
        $chassis_number = isset($_POST['chassis_number']) ? trim($_POST['chassis_number']) : '';
        $engine_number = isset($_POST['engine_number']) ? trim($_POST['engine_number']) : '';
        $bought_from = isset($_POST['bought_from']) ? trim($_POST['bought_from']) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $nic = isset($_POST['nic']) ? trim($_POST['nic']) : '';
        $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';

        if ($brn === '' || $bcost === '' || $bdate === '') {
            $message = 'Please fill all required fields';
            $message_type = 'danger';
        } else {
            $brn = mysqli_real_escape_string($con, $brn);
            $bcost = mysqli_real_escape_string($con, $bcost_raw);
            $bdate = mysqli_real_escape_string($con, $bdate);
            $bstatus = mysqli_real_escape_string($con, $bstatus);

            $year_make_model = mysqli_real_escape_string($con, $year_make_model);
            $chassis_number = mysqli_real_escape_string($con, $chassis_number);
            $engine_number = mysqli_real_escape_string($con, $engine_number);
            $bought_from = mysqli_real_escape_string($con, $bought_from);
            $address = mysqli_real_escape_string($con, $address);
            $nic = mysqli_real_escape_string($con, $nic);
            $phone_number = mysqli_real_escape_string($con, $phone_number);

            $check = mysqli_query($con, "SELECT id FROM bykes WHERE brn='$brn' LIMIT 1");
            if ($check && mysqli_num_rows($check) > 0) {
                $message = 'The vehicle is already in the system';
                $message_type = 'danger';
            } elseif (!is_numeric($bcost) || (float)$bcost <= 0) {
                $message = 'Please enter a valid bought price';
                $message_type = 'danger';
            } elseif ((float)$cashinhand < (float)$bcost) {
                $message = 'Cash is not enought. Please add some funds';
                $message_type = 'danger';
            } else {
                $query = mysqli_query($con, "INSERT INTO bykes (brn, bprice, bdate, bstatus, year_make_model, chassis_number, engine_number, bought_from, address, nic, phone_number) VALUES ('$brn','$bcost','$bdate','$bstatus','$year_make_model','$chassis_number','$engine_number','$bought_from','$address','$nic','$phone_number')");
                if($query){
                    system_log($con, 'Added byke', 'bykes', 'brn:' . $brn . ' bought_price:' . $bcost . ' status:' . $bstatus);
                    $message = 'Vehicle added successfully';
                    $message_type = 'success';
                }
                else{
                    $message = 'Failed to add vehicle';
                    $message_type = 'danger';
                }
            }
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM bykes ORDER BY id asc");
    $expenseMap = [];
    $queryExp = mysqli_query($con, "SELECT brn, SUM(amount) AS total_exp FROM bykeexpencesl GROUP BY brn");
    if ($queryExp) {
        while ($expRow = mysqli_fetch_assoc($queryExp)) {
            $expenseMap[$expRow['brn']] = (float)($expRow['total_exp'] ?? 0);
        }
    }

?>



<?php include "header.php"; ?>
<title>All Vehicles</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9  pl-4 pr-4">
                

            <p class="mt-4 topic-1">All Vehicles</p>
            <p class="mb-4 sub-topic-1">Here are all bought Vehicles in-store and sold</p>

                <div class="table-panel mb-4">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>VRN</th>
                                <th>Vehicle Value</th>
                                <th>Bought Date</th>
                                <!-- Investor column removed -->
                                <th>Status</th>
                                <th>Actions</th>
                                
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                            <tr>
                                <td><?php echo $res['id'];?></td>
                                <td><?php echo $res['brn'];?></td>
                            
                                <?php
                                $bcst = (float)$res['bprice'];
                                $expTotal = isset($expenseMap[$res['brn']]) ? (float)$expenseMap[$res['brn']] : 0;
                                $bval = $bcst + $expTotal;
                                ?>
                                <td><?php echo $bval;?></td>



                                <td><?php echo $res['bdate'];?></td>
                                <!-- investor removed -->
                                <td><?php echo $res['bstatus'];?></td>
                                <?php echo "<td><a href=\"view.php?id=$res[id]\">View</a> | <a href=\"update.php?id=$res[id]\">Edit</a> </td>" ?>

                                
                            </tr>

                            <?php } ?>                                
                        
                        </tbody>
                    </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class="topic-1">Add Vehicle</p>
                <p class="mb-4 sub-topic-1">Fill All The Required Fields</p>
                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                    <form method="POST">

                        <p class="section-title mb-2">Basic Information</p>

                        <label class="add-byke-label">Vehicle Registration Number <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="brn" placeholder="e.g. CAB-1234" required>

                        <label class="add-byke-label">Bought Price <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="bcost" placeholder="e.g. 850000" required>

                        <label class="add-byke-label">Bought Date <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="bdate" required>

                        <!-- Investor selection removed from bykes -->

                        <input type="hidden" name="bstatus" value="In-store">
                        
                        <p class="section-title mt-3 mb-2 mt-4">Additional Information</p>

                        <label class="add-byke-label">Year / Make / Model (optional)</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="year_make_model" placeholder="e.g. 2018 Honda CB" />

                        <label class="add-byke-label">Chassis Number (optional)</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="chassis_number" placeholder="e.g. MH4ABCD1234567890" />

                        <label class="add-byke-label">Engine Number (optional)</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="engine_number" placeholder="e.g. ENG12345678" />

                        <label class="add-byke-label">Bought From (optional)</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="bought_from" placeholder="Seller Name" />

                        <label class="add-byke-label">Address (optional)</label>
                        <textarea class="form-control form-control-sm mb-3 add-byke-input" name="address" rows="2" placeholder="Seller address"></textarea>

                        <label class="add-byke-label">NIC (optional)</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="nic" placeholder="e.g. 123456789V" />

                        <label class="add-byke-label">Phone Number (optional)</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="phone_number" placeholder="e.g. 0771234567" />


                        <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Vehicle</button>

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


