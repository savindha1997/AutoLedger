<?php
    include('dbconfig.php');

    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

    $message = '';
    $message_type = '';
    $brn = '';
    $bprice = 0;
    $bval = 0;

    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        if ($deleteId > 0) {
            $saleRes = mysqli_query($con, "SELECT id, brn FROM bykesale WHERE id = '$deleteId' LIMIT 1");
            if ($saleRes && ($saleRow = mysqli_fetch_assoc($saleRes))) {
                $saleBrn = mysqli_real_escape_string($con, $saleRow['brn']);
                $delSale = mysqli_query($con, "DELETE FROM bykesale WHERE id = '$deleteId'");
                $updByke = mysqli_query($con, "UPDATE bykes SET bstatus='In-store' WHERE brn='$saleBrn'");

                if ($delSale && $updByke) {
                    system_log($con, 'Deleted byke sale', 'bykesale', 'id:' . $deleteId . ' brn:' . $saleBrn . ' set_bstatus:In-store', $deleteId);
                    $message = 'Sale record deleted and byke status changed to In-store';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete sale record';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Sale record not found';
                $message_type = 'danger';
            }
        }
    }

        if(isset($_POST['search'])){
            $brn_input = isset($_POST['brn']) ? trim($_POST['brn']) : '';
            if ($brn_input !== ''){
                $brn = $brn_input;
                $brn_esc = mysqli_real_escape_string($con, $brn_input);
                $query5 = "SELECT * FROM bykes WHERE brn = '$brn_esc'";
                $query5_run = mysqli_query($con,$query5);
                if ($row = mysqli_fetch_assoc($query5_run)) {
                    $bprice = isset($row['bprice']) ? $row['bprice'] : 0;
                }
            }
        }



    if(isset($_POST['submit'])){

        $brn = isset($_POST['brn']) ? trim($_POST['brn']) : '';
        $sdate = isset($_POST['sdate']) ? trim($_POST['sdate']) : '';
        $sprice = isset($_POST['sprice']) ? trim($_POST['sprice']) : '';
        $bvalue = isset($_POST['bvalue']) ? trim($_POST['bvalue']) : '';
        $s_method = isset($_POST['s_method']) ? trim($_POST['s_method']) : '';
        $leasing_company = isset($_POST['leasing_company']) ? trim($_POST['leasing_company']) : '';
        $down_payment = isset($_POST['down_payment']) ? trim($_POST['down_payment']) : '';
        $finance_charges = isset($_POST['finance_charges']) ? trim($_POST['finance_charges']) : '';
        $finance_amount = isset($_POST['finance_amount']) ? trim($_POST['finance_amount']) : '';
        $buyer_name = isset($_POST['buyer_name']) ? trim($_POST['buyer_name']) : '';
        $buyer_address = isset($_POST['buyer_address']) ? trim($_POST['buyer_address']) : '';
        $buyer_nic = isset($_POST['buyer_nic']) ? trim($_POST['buyer_nic']) : '';
        $buyer_phone = isset($_POST['buyer_phone']) ? trim($_POST['buyer_phone']) : '';
        $bstatus = isset($_POST['bstatus']) ? trim($_POST['bstatus']) : '';

            if ($brn === '' || $sdate === '' || $sprice === '' || $buyer_name === '' || $buyer_address === '' || $buyer_nic === '' || $buyer_phone === '' || $s_method === '') {
            $message = 'BRN, Date, Sold Price, selling method and buyer details are required';
            $message_type = 'danger';
        } else {
            // ensure we have up-to-date byke info for the provided BRN
            $brn_esc_sub = mysqli_real_escape_string($con, $brn);
            $bykeRow = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM bykes WHERE brn='$brn_esc_sub' LIMIT 1"));
            $bprice = isset($bykeRow['bprice']) ? $bykeRow['bprice'] : 0;
            // compute total expenses for this byke
            $expRow = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(amount) AS amount_sum FROM bykeexpencesl WHERE brn='$brn_esc_sub'"));
            $sumExp = isset($expRow['amount_sum']) ? $expRow['amount_sum'] : 0;
            $bval = floatval($bprice) + floatval($sumExp);
            // override posted bvalue with server-calculated one
            $bvalue = $bval;

            $brn = $brn_esc_sub;
            $sdate = mysqli_real_escape_string($con, $sdate);
            $sprice = mysqli_real_escape_string($con, $sprice);
            $s_method = mysqli_real_escape_string($con, $s_method);
            $bvalue = mysqli_real_escape_string($con, $bvalue);
            $buyer_name = mysqli_real_escape_string($con, $buyer_name);
            $buyer_address = mysqli_real_escape_string($con, $buyer_address);
            $buyer_nic = mysqli_real_escape_string($con, $buyer_nic);
            $buyer_phone = mysqli_real_escape_string($con, $buyer_phone);
            $bstatus = mysqli_real_escape_string($con, $bstatus);

            // Build insert dynamically to include lease-related columns only if they exist
            $cols = [
                'brn','sdate','sprice','s_method','bvalue','buyer_name','buyer_address','buyer_nic','buyer_phone'
            ];
            $vals = [
                "'$brn'","'$sdate'","'$sprice'","'$s_method'","'$bvalue'","'$buyer_name'","'$buyer_address'","'$buyer_nic'","'$buyer_phone'"
            ];
            // helper to check column existence
            function _col_exists($con, $table, $col){
                $res = @mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$col'");
                return ($res && mysqli_num_rows($res) > 0);
            }
            if ($s_method === 'Lease') {
                // include lease fields only if columns exist
                if (_col_exists($con, 'bykesale', 'leasing_company')) { $cols[] = 'leasing_company'; $vals[] = "'".mysqli_real_escape_string($con,$leasing_company)."'"; }
                if (_col_exists($con, 'bykesale', 'down_payment')) { $cols[] = 'down_payment'; $vals[] = "'".mysqli_real_escape_string($con,$down_payment)."'"; }
                if (_col_exists($con, 'bykesale', 'finance_charges')) { $cols[] = 'finance_charges'; $vals[] = "'".mysqli_real_escape_string($con,$finance_charges)."'"; }
                if (_col_exists($con, 'bykesale', 'finance_amount')) { $cols[] = 'finance_amount'; $vals[] = "'".mysqli_real_escape_string($con,$finance_amount)."'"; }
            }
            $colstr = implode(',', $cols);
            $valstr = implode(',', $vals);

            // verify table and required columns exist before attempting insert
            $tblCheck = mysqli_query($con, "SHOW TABLES LIKE 'bykesale'");
            if (!$tblCheck || mysqli_num_rows($tblCheck) == 0) {
                $message = "Database table 'bykesale' not found.";
                $message_type = 'danger';
            } else {
                $colsRes = mysqli_query($con, "SHOW COLUMNS FROM bykesale");
                $existingCols = [];
                if ($colsRes) {
                    while ($c = mysqli_fetch_assoc($colsRes)) $existingCols[] = $c['Field'];
                }
                $missing = array_diff($cols, $existingCols);
                if (!empty($missing)) {
                    $message = 'Missing columns in bykesale: ' . implode(', ', $missing) . '. Existing columns: ' . implode(', ', $existingCols);
                    $message_type = 'danger';
                } else {
                    $insert_sql = "INSERT INTO bykesale ($colstr) VALUES ($valstr)";
                    $update_sql = "UPDATE bykes SET bstatus = '$bstatus' WHERE brn = '$brn'";

                    $query = mysqli_query($con, $insert_sql);
                    $query6 = mysqli_query($con, $update_sql);

                    if($query && $query6){
                        system_log($con, 'Added byke sale', 'bykesale', 'brn:' . $brn . ' date:' . $sdate . ' method:' . $s_method . ' sold_price:' . $sprice . ' byke_value:' . $bvalue);
                        $message = 'Vehicle sold successfully';
                        $message_type = 'success';
                    }
                    else{
                        $err = mysqli_error($con);
                        $errno = mysqli_errno($con);
                        $failedParts = [];
                        if (!$query) $failedParts[] = 'INSERT';
                        if (!$query6) $failedParts[] = 'UPDATE';
                        $message = 'Failed to record sale. Failed: ' . implode(',', $failedParts) . 
                            '. MySQL error (' . $errno . '): ' . $err . '. Please verify table schema.';
                        $message_type = 'danger';
                    }
                }
            }
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM bykesale ORDER BY id asc");
    $query2 = mysqli_query($con, "SELECT * FROM bykes WHERE bstatus='In-store'");
?>





<?php include "header.php"; ?>
<title>Selling Vehicle</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9 pl-4 pr-4">
                

            <p class="mt-4 topic-1">Selling Vehicle</p>
            <p class="mb-4 sub-topic-1">Here is your all vehicle sales information</p>

                <div class="table-panel mb-4">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Sold Date</th>
                            <th>VRN</th>
                            <th>Sold Price</th>
                            <th>Method</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                        <tr>
                            <td><?php echo htmlspecialchars($res['id']);?></td>
                            <td><?php echo htmlspecialchars($res['sdate']);?></td>
                            <td><?php echo htmlspecialchars($res['brn']);?></td>
                            <td><?php echo htmlspecialchars($res['sprice']);?></td>
                            <td><?php echo isset($res['s_method']) ? htmlspecialchars($res['s_method']) : '';?></td>
                            <td>
                                <a href="#" class="text-danger delete-sale-link" data-delete-url="salebyke.php?delete_id=<?php echo urlencode($res['id']); ?>">Delete</a>
                            </td>
                        </tr>

                        <?php } ?>                                
                    
                    </tbody>
                </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class="topic-1">Sell Vehicle</p>
                <p class="mb-4 sub-topic-1">Fill all required details</p>

                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                <div class="brn-sub">

                    <form method="POST">
                        <div class="row d-flex align-items-end">

                            <div class="col-lg-8">
                                <label class="add-byke-label">Select Vehicle</label>
                                <select class="form-select form-select-sm mb-3 add-byke-input" name="brn">
                                    <option value="" disabled <?php echo ($brn==='' ? 'selected' : ''); ?>>Select VRN</option>
                                    <?php while ($res = mysqli_fetch_assoc($query2)) { ?>
                                        <option value="<?php echo htmlspecialchars($res['brn']);?>" <?php if ($res['brn']==$brn) echo 'selected'; ?>><?php echo htmlspecialchars($res['brn']);?></option>
                                    <?php } ?>    
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <button class="btn dashboard-filter-btn btn-sm mb-3" type="submit" name="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>




                    <form method="POST">


                        <label class="add-byke-label">Vehicle Registerd Number</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input"  type="text" name="brn" value="<?php echo htmlspecialchars($brn);?>" readonly> 
                             
                        <!-- Investor removed from sale form -->

                        <?php
                            if ($brn !== ''){
                                $brn_esc2 = mysqli_real_escape_string($con, $brn);
                                $result = mysqli_query($con, "SELECT SUM(amount) AS amount_sum FROM bykeexpencesl WHERE brn='$brn_esc2'"); 
                                $row = mysqli_fetch_assoc($result); 
                                $sum = isset($row['amount_sum']) ? $row['amount_sum'] : 0;
                            } else {
                                $sum = 0;
                            }

                            $bval = floatval($bprice) + floatval($sum);
                        ?>

                        <label class="add-byke-label">Vehicle Value</label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="bvalue" value="<?php echo htmlspecialchars($bval);?>" readonly>

                        <label class="add-byke-label">Selling Method <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm mb-3 add-byke-input" name="s_method" required>
                            <option value="" selected disabled>-- Select Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="Lease">Lease</option>
                        </select>

                        <label class="add-byke-label mt-3">Date <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="sdate" required>


                        <label class="add-byke-label">Sold Price <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="sprice" placeholder="e.g. 1250000" required>


                            <div id="leaseFields" style="display:none;">
                            <label class="add-byke-label">Leasing Company</label>
                            <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="leasing_company" placeholder="e.g. LOLC Finance">

                            <label class="add-byke-label">Down Payment</label>
                            <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="down_payment" placeholder="e.g. 300000">

                            <label class="add-byke-label">Finance Charges</label>
                            <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="finance_charges" placeholder="e.g. 45000">

                            <label class="add-byke-label">Finance Amount</label>
                            <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="finance_amount" placeholder="e.g. 905000">
                        
                        
                        
                        </div>

                        <label class="add-byke-label">Buyer's Name <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="buyer_name" placeholder="Buyer full name" required>

                        <label class="add-byke-label">Buyer's Address <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm mb-3 add-byke-input" name="buyer_address" rows="2" placeholder="Buyer address" required></textarea>

                        <label class="add-byke-label">Buyer's NIC <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="buyer_nic" placeholder="e.g. 901234567V" required>

                        <label class="add-byke-label">Buyer Phone <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="buyer_phone" placeholder="e.g. 0771234567" required>


                        
                        <input class="form-control form-control-sm mb-3" type="hidden" name="bstatus" value="Sold">

                        <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Sale</button>

                    </form>

                    </div>

            </div>
        
        </div>
    
    </div>

</section>

<div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSaleModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this sale record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteSaleBtn" class="btn btn-danger">Delete</a>
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
    var leaseFields = document.getElementById('leaseFields');
    var deleteLinks = document.querySelectorAll('.delete-sale-link');
    var confirmDeleteBtn = document.getElementById('confirmDeleteSaleBtn');

    deleteLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var deleteUrl = link.getAttribute('data-delete-url');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.setAttribute('href', deleteUrl);
            }
            if (typeof $ !== 'undefined' && $('#deleteSaleModal').length) {
                $('#deleteSaleModal').modal('show');
            } else if (window.confirm('Are you sure you want to delete this sale record?')) {
                window.location.href = deleteUrl;
            }
        });
    });

    function toggleLease(){
        if (!method) return;
        if (method.value === 'Lease') leaseFields.style.display = '';
        else leaseFields.style.display = 'none';
    }
    if (method) {
        method.addEventListener('change', toggleLease);
        toggleLease();
    }
});
</script>



