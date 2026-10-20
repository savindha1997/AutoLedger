<?php
    include('dbconfig.php');
    session_start();
    if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    $message = '';
    $message_type = '';

    if (isset($_GET['msg'])) {
        $message = $_GET['msg'];
        $message_type = isset($_GET['msg_type']) ? $_GET['msg_type'] : 'success';
    }

    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        if ($deleteId > 0) {
            $rowRes = mysqli_query($con, "SELECT * FROM otherincome WHERE id = '$deleteId' LIMIT 1");
            $rowData = $rowRes ? mysqli_fetch_assoc($rowRes) : null;
            $del = mysqli_query($con, "DELETE FROM otherincome WHERE id = '$deleteId'");
            if ($del) {
                $logDetails = 'id:' . $deleteId;
                if ($rowData) {
                    $logDetails = 'id:' . $deleteId . ' date:' . $rowData['date'] . ' amount:' . $rowData['amount'] . ' description:' . $rowData['description'];
                }
                system_log($con, 'Deleted other income', 'otherincome', $logDetails, $deleteId);
                header("Location: otherincome.php?msg=" . urlencode('Record deleted') . "&msg_type=success");
                exit();
            } else {
                $message = 'Record delete failed';
                $message_type = 'danger';
            }
        }
    }

    if(isset($_POST['submit'])){

        $date = $_POST['date'];
        $description = $_POST['description'];
        $amount = $_POST['amount'];
        

        $query = mysqli_query($con, "INSERT INTO otherincome (date, description, amount) VALUES ('$date','$description','$amount')");

        if($query){
            system_log($con, 'Added other income', 'otherincome', 'date:' . $date . ' amount:' . $amount . ' description:' . $description);
            $message = 'Data added successfully';
            $message_type = 'success';
        }
        else{
            $message = 'Data add failed';
            $message_type = 'danger';
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM otherincome ORDER BY id asc");

?>





<?php include "header.php"; ?>
<title>Other Incomes</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9  pl-4 pr-4">
                

            <p class="mt-4 topic-1">Other Incomes</p>
            <p class="mb-4 sub-topic-1">Here is your all other income information</p>

                <div class="table-panel mb-4">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($res = mysqli_fetch_assoc($query1)) { ?>

                        <tr>
                            <td><?php echo $res['id'];?></td>
                            <td><?php echo $res['date'];?></td>
                            <td><?php echo $res['description'];?></td>
                            <td><?php echo $res['amount'];?></td>
                            <td>
                                <a href="#" class="text-danger delete-otherincome-link" data-delete-url="otherincome.php?delete_id=<?php echo urlencode($res['id']); ?>">Delete</a>
                            </td>
                            

                        </tr>

                        <?php } ?>                                
                    
                    </tbody>
                </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class=" topic-1">Add Income</p>
                <p class="mb-4 sub-topic-1">Fill details and add record</p>

                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } ?>

                    <form method="POST">


                        <label class="add-byke-label">Date <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="date" required>

                        <label class="add-byke-label">Description <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="description" placeholder="Income description" required>
                        

                        <label class="add-byke-label">Amount <span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="amount" placeholder="e.g. 7500" required>


                        <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Income</button>

                    </form>

                    </div>

            </div>
        
        </div>
    
    </div>

</section>

<div class="modal fade" id="deleteOtherIncomeModal" tabindex="-1" role="dialog" aria-labelledby="deleteOtherIncomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOtherIncomeModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this income record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteOtherIncomeBtn" class="btn btn-danger">Delete</a>
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
    var deleteLinks = document.querySelectorAll('.delete-otherincome-link');
    var confirmBtn = document.getElementById('confirmDeleteOtherIncomeBtn');

    deleteLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var deleteUrl = link.getAttribute('data-delete-url');
            if (confirmBtn) confirmBtn.setAttribute('href', deleteUrl);

            if (typeof $ !== 'undefined' && $('#deleteOtherIncomeModal').length) {
                $('#deleteOtherIncomeModal').modal('show');
            } else if (window.confirm('Are you sure you want to delete this income record?')) {
                window.location.href = deleteUrl;
            }
        });
    });
});
</script>




