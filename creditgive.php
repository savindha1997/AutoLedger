<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    $message = '';
    $message_type = '';

    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        if ($deleteId > 0) {
            $rowRes = mysqli_query($con, "SELECT * FROM criditgiving WHERE id = '$deleteId' LIMIT 1");
            $rowData = $rowRes ? mysqli_fetch_assoc($rowRes) : null;
            $del = mysqli_query($con, "DELETE FROM criditgiving WHERE id = '$deleteId'");
            if ($del) {
                $logDetails = 'id:' . $deleteId;
                if ($rowData) {
                    $logDetails = 'id:' . $deleteId . ' date:' . $rowData['cgdate'] . ' credit_holder:' . $rowData['ctowhom'] . ' amount:' . $rowData['camount'];
                }
                system_log($con, 'Deleted credit give', 'criditgiving', $logDetails, $deleteId);
                $message = 'Credit record deleted successfully';
                $message_type = 'success';
            } else {
                $message = 'Failed to delete credit record';
                $message_type = 'danger';
            }
        }
    }

    if(isset($_POST['submit'])){

        $cgdate = isset($_POST['cgdate']) ? trim($_POST['cgdate']) : '';
        $ctowhom = isset($_POST['ctowhom']) ? trim($_POST['ctowhom']) : '';
        $camount = isset($_POST['camount']) ? trim($_POST['camount']) : '';
        
        if ($cgdate === '' || $ctowhom === '' || $camount === '') {
            $message = 'All fields are required';
            $message_type = 'danger';
        } else {
            $check = mysqli_query($con, "SELECT id FROM criditgiving WHERE ctowhom = '".mysqli_real_escape_string($con, $ctowhom)."' LIMIT 1");
            if ($check && mysqli_num_rows($check) > 0) {
                $message = 'crediet holder already in the system. please use different name';
                $message_type = 'danger';
            } else {
                $query = mysqli_query($con, "INSERT INTO criditgiving (cgdate, ctowhom, camount) VALUES ('".mysqli_real_escape_string($con, $cgdate)."','".mysqli_real_escape_string($con, $ctowhom)."','".mysqli_real_escape_string($con, $camount)."')");

                if($query){
                    system_log($con, 'Added credit give', 'criditgiving', 'date:' . $cgdate . ' credit_holder:' . $ctowhom . ' amount:' . $camount);
                    $message = 'Credit record added successfully';
                    $message_type = 'success';
                }
                else{
                    $message = 'Failed to add credit record';
                    $message_type = 'danger';
                }
            }
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM criditgiving ORDER BY id asc");
    $query2 = mysqli_query($con, "SELECT DISTINCT brn FROM bykesale WHERE s_method='Lease' ORDER BY brn ASC");
?>





<?php include "header.php"; ?>
<title>Cheque on Lease</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9  pl-4 pr-4">
                

            <p class="mt-4 topic-1">Cheque on Lease</p>
            <p class="mb-4 sub-topic-1">Here is your all cheque on lease records</p>

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
                            <td><?php echo $res['cgdate'];?></td>
                            <td><?php echo $res['ctowhom'];?></td>
                            <td><?php echo $res['camount'];?></td>
                            <td>
                                <a href="#" class="text-danger delete-creditgive-link" data-delete-url="creditgive.php?delete_id=<?php echo urlencode($res['id']); ?>">Delete</a>
                            </td>
                            

                        </tr>

                        <?php } ?>                                
                    
                    </tbody>
                </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class=" topic-1">Add Cheque on Lease</p>
                <p class="mb-4 sub-topic-1">Fill all required details</p>
                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                    <form method="POST">


                        <label class="add-byke-label">Date <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="cgdate" required>

                        <label class="add-byke-label">Related Vehicle <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm mb-3 add-byke-input" name="ctowhom" required>
                            <option value="" selected disabled>Select VRN</option>
                            <?php while ($res = mysqli_fetch_assoc($query2)) { ?>
                                <option value="<?php echo htmlspecialchars($res['brn']); ?>"><?php echo htmlspecialchars($res['brn']); ?></option>
                            <?php } ?>
                        </select>

                        <label class="add-byke-label">Amount <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="camount" placeholder="e.g. 25000" required>

                        <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Cheque on Lease</button>

                    </form>

                    </div>

            </div>
        
        </div>
    
    </div>

</section>

<div class="modal fade" id="deleteCreditGiveModal" tabindex="-1" role="dialog" aria-labelledby="deleteCreditGiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCreditGiveModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this credit record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteCreditGiveBtn" class="btn btn-danger">Delete</a>
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
    var deleteLinks = document.querySelectorAll('.delete-creditgive-link');
    var confirmBtn = document.getElementById('confirmDeleteCreditGiveBtn');

    deleteLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var deleteUrl = link.getAttribute('data-delete-url');
            if (confirmBtn) confirmBtn.setAttribute('href', deleteUrl);

            if (typeof $ !== 'undefined' && $('#deleteCreditGiveModal').length) {
                $('#deleteCreditGiveModal').modal('show');
            } else if (window.confirm('Are you sure you want to delete this credit record?')) {
                window.location.href = deleteUrl;
            }
        });
    });
});
</script>



