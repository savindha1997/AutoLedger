<?php
    include('dbconfig.php');

    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        header("Location: addByke.php?msg=" . urlencode('Invalid byke record') . "&msg_type=danger");
        exit();
    }

    if (isset($_GET['delete_byke']) && $_GET['delete_byke'] === '1') {
        $bykeRes = mysqli_query($con, "SELECT id, brn FROM bykes WHERE id = '$id' LIMIT 1");
        $bykeData = $bykeRes ? mysqli_fetch_assoc($bykeRes) : null;

        if (!$bykeData) {
            header("Location: addByke.php?msg=" . urlencode('Byke not found') . "&msg_type=danger");
            exit();
        }

        $deleteBrn = mysqli_real_escape_string($con, $bykeData['brn']);
        mysqli_begin_transaction($con);

        $ok = true;
        $ok = $ok && mysqli_query($con, "DELETE FROM bykeexpencesl WHERE brn = '$deleteBrn'");
        $ok = $ok && mysqli_query($con, "DELETE FROM bykesale WHERE brn = '$deleteBrn'");
        $ok = $ok && mysqli_query($con, "DELETE FROM criditgiving WHERE ctowhom = '$deleteBrn'");
        $ok = $ok && mysqli_query($con, "DELETE FROM creditreturns WHERE cbfrom = '$deleteBrn'");
        $ok = $ok && mysqli_query($con, "DELETE FROM bykes WHERE id = '$id'");

        if ($ok) {
            mysqli_commit($con);
            system_log($con, 'Deleted byke and related data', 'bykes', 'id:' . $id . ' brn:' . $bykeData['brn'], $id);
            header("Location: addByke.php?msg=" . urlencode('Byke and related records deleted') . "&msg_type=success");
            exit();
        }

        mysqli_rollback($con);
        header("Location: update.php?id=$id&msg=" . urlencode('Delete failed, please try again') . "&msg_type=danger");
        exit();
    }

    $query1 = mysqli_query($con, "SELECT * FROM bykes WHERE id = $id");
    $resultData = mysqli_fetch_assoc($query1);
    
    $brn = $resultData['brn'];
    $bprice = $resultData['bprice'];
    // investor removed from bykes
    $bdate = $resultData['bdate'];
    $bstatus = $resultData['bstatus'];
    // Optional fields
    $year_make_model = isset($resultData['year_make_model']) ? $resultData['year_make_model'] : '';
    $chassis_number = isset($resultData['chassis_number']) ? $resultData['chassis_number'] : '';
    $engine_number = isset($resultData['engine_number']) ? $resultData['engine_number'] : '';
    $bought_from = isset($resultData['bought_from']) ? $resultData['bought_from'] : '';
    $address = isset($resultData['address']) ? $resultData['address'] : '';
    $nic = isset($resultData['nic']) ? $resultData['nic'] : '';
    $phone_number = isset($resultData['phone_number']) ? $resultData['phone_number'] : '';


    // investors list not needed for byke update


    $query3 = mysqli_query($con, "SELECT * FROM bykesale WHERE brn = '$brn'");
    $resultData3 = mysqli_fetch_assoc($query3);

    $sprice = $resultData3['sprice'];
    $sdate = $resultData3['sdate'];
    $buyer_name = isset($resultData3['buyer_name']) ? $resultData3['buyer_name'] : '';
    $buyer_address = isset($resultData3['buyer_address']) ? $resultData3['buyer_address'] : '';
    $buyer_nic = isset($resultData3['buyer_nic']) ? $resultData3['buyer_nic'] : '';
    $buyer_phone = isset($resultData3['buyer_phone']) ? $resultData3['buyer_phone'] : '';
    $s_method = isset($resultData3['s_method']) ? $resultData3['s_method'] : '';
    $message = '';
    $message_type = '';

    if (isset($_GET['msg'])) {
        $message = $_GET['msg'];
        $message_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
    }


    if(isset($_POST['submit'])){

        // investor removed from bykes
        $bprice = mysqli_real_escape_string($con, $_POST['bprice']);
        $bdate = mysqli_real_escape_string($con, $_POST['bdate']);
        $bstatus = mysqli_real_escape_string($con, $_POST['bstatus']);
        $sdate = mysqli_real_escape_string($con, $_POST['sdate']);
        $sprice = mysqli_real_escape_string($con, $_POST['sprice']);
        $s_method = isset($_POST['s_method']) ? mysqli_real_escape_string($con, $_POST['s_method']) : '';
        $leasing_company = isset($_POST['leasing_company']) ? mysqli_real_escape_string($con, $_POST['leasing_company']) : '';
        $down_payment = isset($_POST['down_payment']) ? mysqli_real_escape_string($con, $_POST['down_payment']) : '';
        $finance_charges = isset($_POST['finance_charges']) ? mysqli_real_escape_string($con, $_POST['finance_charges']) : '';
        $finance_amount = isset($_POST['finance_amount']) ? mysqli_real_escape_string($con, $_POST['finance_amount']) : '';
        $buyer_name = isset($_POST['buyer_name']) ? mysqli_real_escape_string($con, $_POST['buyer_name']) : '';
        $buyer_address = isset($_POST['buyer_address']) ? mysqli_real_escape_string($con, $_POST['buyer_address']) : '';
        $buyer_nic = isset($_POST['buyer_nic']) ? mysqli_real_escape_string($con, $_POST['buyer_nic']) : '';
        $buyer_phone = isset($_POST['buyer_phone']) ? mysqli_real_escape_string($con, $_POST['buyer_phone']) : '';
        // Optional fields
        $year_make_model = isset($_POST['year_make_model']) ? mysqli_real_escape_string($con, $_POST['year_make_model']) : '';
        $chassis_number = isset($_POST['chassis_number']) ? mysqli_real_escape_string($con, $_POST['chassis_number']) : '';
        $engine_number = isset($_POST['engine_number']) ? mysqli_real_escape_string($con, $_POST['engine_number']) : '';
        $bought_from = isset($_POST['bought_from']) ? mysqli_real_escape_string($con, $_POST['bought_from']) : '';
        $address = isset($_POST['address']) ? mysqli_real_escape_string($con, $_POST['address']) : '';
        $nic = isset($_POST['nic']) ? mysqli_real_escape_string($con, $_POST['nic']) : '';
        $phone_number = isset($_POST['phone_number']) ? mysqli_real_escape_string($con, $_POST['phone_number']) : '';

        $query = mysqli_query($con, "UPDATE bykes SET bprice = '$bprice', bdate = '$bdate', bstatus = '$bstatus', year_make_model = '$year_make_model', chassis_number = '$chassis_number', engine_number = '$engine_number', bought_from = '$bought_from', address = '$address', nic = '$nic', phone_number = '$phone_number' WHERE id = '$id'");
        // build dynamic update for bykesale to include lease fields if columns exist
        $setParts = [];
        $setParts[] = "sdate = '$sdate'";
        $setParts[] = "sprice = '$sprice'";
        $setParts[] = "s_method = '$s_method'";
        $setParts[] = "buyer_name = '$buyer_name'";
        $setParts[] = "buyer_address = '$buyer_address'";
        $setParts[] = "buyer_nic = '$buyer_nic'";
        $setParts[] = "buyer_phone = '$buyer_phone'";
        function _col_exists_up($con,$table,$col){ $r=@mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$col'"); return ($r && mysqli_num_rows($r)>0); }
        if ($s_method === 'Lease'){
            if (_col_exists_up($con,'bykesale','leasing_company')) $setParts[] = "leasing_company = '$leasing_company'";
            if (_col_exists_up($con,'bykesale','down_payment')) $setParts[] = "down_payment = '$down_payment'";
            if (_col_exists_up($con,'bykesale','finance_charges')) $setParts[] = "finance_charges = '$finance_charges'";
            if (_col_exists_up($con,'bykesale','finance_amount')) $setParts[] = "finance_amount = '$finance_amount'";
        }
        $setStr = implode(', ', $setParts);
        $query1 = mysqli_query($con, "UPDATE bykesale SET $setStr WHERE brn = '$brn'");

        if($query){
            system_log($con, 'Updated byke', 'bykes', 'id:' . $id . ' brn:' . $brn . ' bought_price:' . $bprice . ' status:' . $bstatus, $id);
            $message = 'Data updated successfully';
            $message_type = 'success';
        }
        else{
            $message = 'Data updating failed';
            $message_type = 'danger';
        }
    }


?>





<?php include "header.php"; ?>
<title>Update Information</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12 right-boder-1 pl-4 pr-4">
                

                <div class="add-byke-panel mt-4 mb-4">

                <p class="topic-1">Update Information of: <?php echo $brn; ?></p>

                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } ?>


                <form class="mt-3" method="POST">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="card vehicle-info-card h-100">
                                <div class="card-body">
                                    <p class="section-title mb-3">Vehicle Information</p>

                                    <label class="add-byke-label">Status</label>
                                    <select class="form-select form-select-sm mb-3 add-byke-input" type="text" name="bstatus">
                                        <option value="<?php echo $bstatus; ?>"><?php echo $bstatus; ?></option>
                                        <option value="In-store">In-Store</option>
                                        <option value="Sold">Sold</option>
                                    </select>

                                    <label class="add-byke-label">Year / Make / Model</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="year_make_model" value="<?php echo htmlspecialchars($year_make_model); ?>" placeholder="e.g. 2018 Honda CB">

                                    <label class="add-byke-label">Chassis Number</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="chassis_number" value="<?php echo htmlspecialchars($chassis_number); ?>" placeholder="e.g. MH4ABCD1234567890">

                                    <label class="add-byke-label">Engine Number</label>
                                    <input class="form-control form-control-sm mb-0 add-byke-input" type="text" name="engine_number" value="<?php echo htmlspecialchars($engine_number); ?>" placeholder="e.g. ENG12345678">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="card vehicle-info-card h-100">
                                <div class="card-body">
                                    <p class="section-title mb-3">Seller's Information</p>

                                    <label class="add-byke-label">Bought Date</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="bdate" value="<?php echo $bdate; ?>">

                                    <label class="add-byke-label">Bought Price</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="bprice" value="<?php echo $bprice; ?>" placeholder="e.g. 850000">

                                    <label class="add-byke-label">Bought From</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="bought_from" value="<?php echo htmlspecialchars($bought_from); ?>" placeholder="Seller name">

                                    <label class="add-byke-label">Address</label>
                                    <textarea class="form-control form-control-sm mb-3 add-byke-input" name="address" rows="2" placeholder="Seller address"><?php echo htmlspecialchars($address); ?></textarea>

                                    <label class="add-byke-label">NIC</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="nic" value="<?php echo htmlspecialchars($nic); ?>" placeholder="e.g. 123456789V">

                                    <label class="add-byke-label">Phone Number</label>
                                    <input class="form-control form-control-sm mb-0 add-byke-input" type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" placeholder="e.g. 0771234567">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="card vehicle-info-card h-100">
                                <div class="card-body">
                                    <p class="section-title mb-3">Buyer's Information</p>

                                    <label class="add-byke-label">Sold Date</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="sdate" value="<?php echo $sdate; ?>">

                                    <label class="add-byke-label">Sold Price</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="sprice" value="<?php echo $sprice; ?>" placeholder="e.g. 1250000">

                                    <label class="add-byke-label">Selling Method</label>
                                    <select class="form-select form-select-sm mb-3 add-byke-input" name="s_method">
                                        <option value="" <?php if($s_method==="") echo 'selected'; ?>>-- Select Method --</option>
                                        <option value="Cash" <?php if($s_method==="Cash") echo 'selected'; ?>>Cash</option>
                                        <option value="Lease" <?php if($s_method==="Lease") echo 'selected'; ?>>Lease</option>
                                    </select>

                                    <label class="add-byke-label">Buyer Name</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="buyer_name" value="<?php echo htmlspecialchars($buyer_name); ?>" placeholder="Buyer full name">

                                    <label class="add-byke-label">Buyer Address</label>
                                    <textarea class="form-control form-control-sm mb-3 add-byke-input" name="buyer_address" rows="2" placeholder="Buyer address"><?php echo htmlspecialchars($buyer_address); ?></textarea>

                                    <label class="add-byke-label">Buyer NIC</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="buyer_nic" value="<?php echo htmlspecialchars($buyer_nic); ?>" placeholder="e.g. 901234567V">

                                    <label class="add-byke-label">Buyer Phone</label>
                                    <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="buyer_phone" value="<?php echo htmlspecialchars($buyer_phone); ?>" placeholder="e.g. 0771234567">

                                    <div id="leaseFieldsUpdate" style="display:<?php echo ($s_method==='Lease') ? 'block' : 'none'; ?>;">
                                        <label class="add-byke-label">Leasing Company</label>
                                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="leasing_company" value="<?php echo isset($resultData3['leasing_company'])?htmlspecialchars($resultData3['leasing_company']):''; ?>" placeholder="e.g. LOLC Finance">

                                        <label class="add-byke-label">Down Payment</label>
                                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="down_payment" value="<?php echo isset($resultData3['down_payment'])?htmlspecialchars($resultData3['down_payment']):''; ?>" placeholder="e.g. 300000">

                                        <label class="add-byke-label">Finance Charges</label>
                                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="finance_charges" value="<?php echo isset($resultData3['finance_charges'])?htmlspecialchars($resultData3['finance_charges']):''; ?>" placeholder="e.g. 45000">

                                        <label class="add-byke-label">Finance Amount</label>
                                        <input class="form-control form-control-sm mb-0 add-byke-input" type="text" name="finance_amount" value="<?php echo isset($resultData3['finance_amount'])?htmlspecialchars($resultData3['finance_amount']):''; ?>" placeholder="e.g. 905000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 px-0">
                        <button type="submit" class="btn dashboard-filter-btn" name="submit">Update Vehicle</button>
                        <button type="button" class="btn btn-danger ml-2" id="openDeleteBykeModal">Delete Vehicle</button>
                    </div>
                </form>

                </div>
            

                
            </div>

        
        </div>
    
    </div>

</section>

<div class="modal fade" id="deleteBykeModal" tabindex="-1" role="dialog" aria-labelledby="deleteBykeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBykeModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                This will delete byke <strong><?php echo htmlspecialchars($brn); ?></strong> and all related records. This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="update.php?id=<?php echo urlencode($id); ?>&delete_byke=1" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php 
}else{
header("Location: login.php");
exit();
}
include "footer.php"; 
?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var method = document.querySelector('select[name="s_method"]');
    var leaseFields = document.getElementById('leaseFieldsUpdate');
    var openDeleteBtn = document.getElementById('openDeleteBykeModal');

    function toggleLease(){
        if (!method) return;
        if (method.value === 'Lease') leaseFields.style.display = 'block';
        else leaseFields.style.display = 'none';
    }
    if (method) {
        method.addEventListener('change', toggleLease);
        toggleLease();
    }

    if (openDeleteBtn && typeof $ !== 'undefined' && $('#deleteBykeModal').length) {
        openDeleteBtn.addEventListener('click', function(){
            $('#deleteBykeModal').modal('show');
        });
    }
});
</script>




