<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    $message = '';
    $message_type = '';

    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        if ($deleteId > 0) {
            $rowRes = mysqli_query($con, "SELECT * FROM creditreturns WHERE id = '$deleteId' LIMIT 1");
            $rowData = $rowRes ? mysqli_fetch_assoc($rowRes) : null;
            $del = mysqli_query($con, "DELETE FROM creditreturns WHERE id = '$deleteId'");
            if ($del) {
                $logDetails = 'id:' . $deleteId;
                if ($rowData) {
                    $logDetails = 'id:' . $deleteId . ' date:' . $rowData['cbdate'] . ' credit_holder:' . $rowData['cbfrom'] . ' amount:' . $rowData['amount'];
                }
                system_log($con, 'Deleted credit return', 'creditreturns', $logDetails, $deleteId);
                $message = 'Credit return record deleted successfully';
                $message_type = 'success';
            } else {
                $message = 'Failed to delete credit return record';
                $message_type = 'danger';
            }
        }
    }

    if(isset($_POST['submit'])){

        $cbdate = isset($_POST['cbdate']) ? trim($_POST['cbdate']) : '';
        $cbfrom = isset($_POST['cbfrom']) ? trim($_POST['cbfrom']) : '';
        $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';

        if ($cbdate === '' || $cbfrom === '' || $amount === '') {
            $message = 'All fields are required';
            $message_type = 'danger';
        } elseif (!is_numeric($amount) || $amount <= 0) {
            $message = 'Please enter a valid amount';
            $message_type = 'danger';
        } else {
            $cbdate = mysqli_real_escape_string($con, $cbdate);
            $cbfrom = mysqli_real_escape_string($con, $cbfrom);
            $amount = mysqli_real_escape_string($con, $amount);

            // keep compatibility if legacy cbto column still exists
            $cbtoCol = @mysqli_query($con, "SHOW COLUMNS FROM creditreturns LIKE 'cbto'");
            if ($cbtoCol && mysqli_num_rows($cbtoCol) > 0) {
                $query = mysqli_query($con, "INSERT INTO creditreturns (cbdate, cbfrom, amount, cbto) VALUES ('$cbdate','$cbfrom','$amount','')");
            } else {
                $query = mysqli_query($con, "INSERT INTO creditreturns (cbdate, cbfrom, amount) VALUES ('$cbdate','$cbfrom','$amount')");
            }

            if($query){
                system_log($con, 'Added credit return', 'creditreturns', 'date:' . $cbdate . ' credit_holder:' . $cbfrom . ' amount:' . $amount);
                $message = 'Credit return added successfully';
                $message_type = 'success';
            }
            else{
                $message = 'Failed to add credit return';
                $message_type = 'danger';
            }
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM creditreturns ORDER BY id asc");
    $query2 = mysqli_query($con, "SELECT * FROM criditgiving");
?>





<?php include "header.php"; ?>
<title>Cheque Received</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9 pl-4 pr-4">
                

            <p class="mt-4 topic-1">Cheque Received</p>
            <p class="mb-4 sub-topic-1">Here is your all cheque received details</p>

                <div class="table-panel mb-4">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Related Vehicle</th>
                            <th>Amount</th>
                            <th>Action</th>
                            
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                        <tr>
                            <td><?php echo $res['id'];?></td>
                            <td><?php echo $res['cbdate'];?></td>
                            <td><?php echo $res['cbfrom'];?></td>
                            <td><?php echo $res['amount'];?></td>
                            <td>
                                <a href="#" class="text-danger delete-creditreturn-link" data-delete-url="creditreturns.php?delete_id=<?php echo urlencode($res['id']); ?>">Delete</a>
                            </td>
                            

                        </tr>

                        <?php } ?>                                
                    
                    </tbody>
                </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class=" topic-1">Cheque Received</p>
                <p class="mb-4 sub-topic-1">Fill all required details</p>

                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                    <form method="POST">

                        <label class="add-byke-label">Date <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="cbdate" required>

                        <label class="add-byke-label">Select VRN <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm mb-3 add-byke-input" type="text" name="cbfrom" required>
                            <option value="" selected disabled>Select VRN</option>
                             
                            <?php while ($res = mysqli_fetch_assoc($query2)) { ?>

                                <option value="<?php echo $res['ctowhom'];?>"><?php echo $res['ctowhom'];?></option>

                            <?php } ?>    
                        </select>

                        <label class="add-byke-label">Amount <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="amount" placeholder="e.g. 15000" required>


                        <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Credit Return</button>

                    </form>

                    </div>

            </div>
        
        </div>
    
    </div>

</section>

<div class="modal fade" id="deleteCreditReturnModal" tabindex="-1" role="dialog" aria-labelledby="deleteCreditReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCreditReturnModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this credit return record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteCreditReturnBtn" class="btn btn-danger">Delete</a>
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
    var deleteLinks = document.querySelectorAll('.delete-creditreturn-link');
    var confirmBtn = document.getElementById('confirmDeleteCreditReturnBtn');

    deleteLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var deleteUrl = link.getAttribute('data-delete-url');
            if (confirmBtn) confirmBtn.setAttribute('href', deleteUrl);

            if (typeof $ !== 'undefined' && $('#deleteCreditReturnModal').length) {
                $('#deleteCreditReturnModal').modal('show');
            } else if (window.confirm('Are you sure you want to delete this credit return record?')) {
                window.location.href = deleteUrl;
            }
        });
    });
});
</script>



