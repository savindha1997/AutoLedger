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
    
    if(isset($_POST['search'])){

        $brn = $_POST['brn'];

        // no investor needed for expense entry
        $query5 = "SELECT * FROM bykes WHERE brn = '$brn'";
        $query5_run = mysqli_query($con,$query5);
    }
        

    
    if(isset($_POST['submit'])){

        $date = isset($_POST['date']) ? trim($_POST['date']) : '';
        $brn = isset($_POST['brn']) ? trim($_POST['brn']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
        // investor removed from byke expenses

        if ($brn === '' || $date === '' || $description === '' || $amount === '') {
            $message = 'Byke Registered Number, Date, Description, and Amount are required';
            $message_type = 'danger';
        } else {
            $date = mysqli_real_escape_string($con, $date);
            $brn = mysqli_real_escape_string($con, $brn);
            $description = mysqli_real_escape_string($con, $description);
            $amount = mysqli_real_escape_string($con, $amount);

            if (!is_numeric($amount) || $amount <= 0) {
                $message = 'Please enter a valid amount';
                $message_type = 'danger';
            } elseif ($cashinhand < $amount) {
                $message = 'Cash is not enought. Please add some funds';
                $message_type = 'danger';
            } else {
                $query = mysqli_query($con, "INSERT INTO bykeexpencesl (date, brn, description, amount) VALUES ('$date','$brn','$description','$amount')");

                if($query){
                    system_log($con, 'Added byke expense', 'bykeexpencesl', 'date:' . $date . ' brn:' . $brn . ' amount:' . $amount . ' description:' . $description);
                    $message = 'Expense added successfully';
                    $message_type = 'success';
                }
                else{
                    $message = 'Failed to add expense';
                    $message_type = 'danger';
                }
            }
        }
    }

    $query1 = mysqli_query($con, "SELECT * FROM bykeexpencesl ORDER BY id asc");
    $query2 = mysqli_query($con, "SELECT * FROM bykes WHERE bstatus='In-store' ORDER BY brn asc");
?>





<?php include "header.php"; ?>
<title>Vehicle Expenses</title>
</head>

<body class="bg-color-pr">

<?php include "topbar.php"; ?>
<?php include "sidebar.php"; ?>


<section class="page-wrap">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-9 pl-4 pr-4">
                

            <p class="mt-4 topic-1">Expencess Related to Vehicles</p>
            <p class="mb-4 sub-topic-1">Here is your all expencess related to vehicles</p>

                <div class="table-panel mb-4">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>VRN</th>
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
                                <td><?php echo $res['brn'];?></td>
                                <td><?php echo $res['description'];?></td>
                                <td><?php echo $res['amount'];?></td>
                                <td>
                                    <a href="#" class="text-danger delete-expense-link" data-delete-url="bkexdelete.php?id=<?php echo urlencode($res['id']); ?>">Delete</a>
                                </td>




                            </tr>

                        <?php } ?>                                  
                    
                    </tbody>
                </table>
                </div>
            </div>



            <div class="col-lg-3 pr-4">

                <div class="add-byke-panel mt-4 mb-4">


                <p class=" topic-1">Add Expencess</p>
                <p class="mb-4 sub-topic-1">Fill all required details and add record</p>
                <?php if (!empty($message)) { ?>
                    <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                <div class="brn-sub">

                    <form method="POST">
                        <div class="row d-flex align-items-end">

                            <div class="col-lg-8">
                                          <label class="add-byke-label">VRN <span class="text-danger">*</span></label>
                                          <select class="form-select form-select-sm mb-3 add-byke-input" type="text" name="brn" required>
                                    
                                              <option value="" selected disabled>Select VRN</option>
                                    <?php while ($res = mysqli_fetch_assoc($query2)) { ?>

                                        <option value="<?php echo $res['brn'];?>"><?php echo $res['brn'];?></option>

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

                

                <label class="add-byke-label">Vehicle Registration Number <span class="text-danger">*</span></label>
                <input class="form-control form-control-sm mb-3 add-byke-input"  type="text" name="brn" value="<?php echo $brn;?>" placeholder="Select a vehicle above" readonly required> 

                <hr>

                <label class="add-byke-label mt-3">Date <span class="text-danger">*</span></label>
                <input class="form-control form-control-sm mb-3 add-byke-input" type="date" name="date" required>

                <label class="add-byke-label">Description <span class="text-danger">*</span></label>
                <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="description" placeholder="Expense description" required>

                <label class="add-byke-label">Amount Rs. <span class="text-danger">*</span></label>
                <input class="form-control form-control-sm mb-3 add-byke-input" type="text" name="amount" placeholder="e.g. 5000" required>


                <button class="btn dashboard-filter-btn btn-block" type="submit" name="submit">Add Expense</button>

                </form>

                </div>

            </div>
        
        </div>
    
    </div>

</section>

<div class="modal fade" id="deleteExpenseModal" tabindex="-1" role="dialog" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteExpenseModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this expense record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteExpenseBtn" class="btn btn-danger">Delete</a>
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
    var deleteLinks = document.querySelectorAll('.delete-expense-link');
    var confirmBtn = document.getElementById('confirmDeleteExpenseBtn');

    deleteLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var deleteUrl = link.getAttribute('data-delete-url');
            if (confirmBtn) confirmBtn.setAttribute('href', deleteUrl);

            if (typeof $ !== 'undefined' && $('#deleteExpenseModal').length) {
                $('#deleteExpenseModal').modal('show');
            } else if (window.confirm('Are you sure you want to delete this expense record?')) {
                window.location.href = deleteUrl;
            }
        });
    });
});
</script>





