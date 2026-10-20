<?php
$currentPage = basename($_SERVER['PHP_SELF']);

function sideActive($pages, $currentPage)
{
    return in_array($currentPage, $pages, true) ? 'active' : '';
}
?>

<div class="side-bar">
    <div class="side-bar-items">       
        <ul>
        
        <span class="hide-menu-cust">Management</span>
          <li class="side-bar-item <?php echo sideActive(['index.php'], $currentPage); ?>">
              <a class="side-bar-link <?php echo sideActive(['index.php'], $currentPage); ?>" href="index.php">
                  <span><i class="ri-dashboard-3-line" ></i></span>
                  <span class="hide-menu">Dashboard</span>
              </a>
          </li>  

          <li class="side-bar-item <?php echo sideActive(['addByke.php', 'update.php', 'view.php'], $currentPage); ?>">
              <a class="side-bar-link <?php echo sideActive(['addByke.php', 'update.php', 'view.php'], $currentPage); ?>" href="addByke.php" >
                  <span><i class="ri-car-line"></i></span>
                  <span class="hide-menu">Vehicles</span>
              </a>
          </li>  

          <li class="side-bar-item <?php echo sideActive(['investors.php', 'viewinvestors.php'], $currentPage); ?>">
              <a class="side-bar-link <?php echo sideActive(['investors.php', 'viewinvestors.php'], $currentPage); ?>" href="investors.php" >
                  <span><i class="ri-shield-user-line"></i></span>
                  <span class="hide-menu">Investors</span>
              </a>
          </li>  
          
          <li class="side-bar-item <?php echo sideActive(['creditholders.php', 'viewcredietholder.php'], $currentPage); ?>">
              <a class="side-bar-link <?php echo sideActive(['creditholders.php', 'viewcredietholder.php'], $currentPage); ?>" href="creditholders.php" >
                  <span><i class="ri-bank-card-line"></i></span>
                  <span class="hide-menu">Cheques</span>
              </a>
          </li>
        </ul>

        <ul>

          <span class="hide-menu-cust mt-3">Cash-in</span>

          <li class="side-bar-item <?php echo sideActive(['salebyke.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['salebyke.php'], $currentPage); ?>" href="salebyke.php">
                    <span><i class="ri-shopping-cart-line"></i></span>    
                    <span class="hide-menu">Selling Vehicles</span>
                </a>
            </li>


           
              <li class="side-bar-item <?php echo sideActive(['investad.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['investad.php'], $currentPage); ?>" href="investad.php">
                    <span><i class="ri-mail-add-line"></i></span>        
                    <span class="hide-menu">Investor Deposits</span>
                  </a>
              </li>

              <li class="side-bar-item <?php echo sideActive(['creditreturns.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['creditreturns.php'], $currentPage); ?>" href="creditreturns.php">
                    <span><i class="ri-sticky-note-add-line"></i></span>      
                    <span class="hide-menu">Cheque Received</span>
                  </a>
              </li>

              <li class="side-bar-item <?php echo sideActive(['otherincome.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['otherincome.php'], $currentPage); ?>" href="otherincome.php">
                        <span><i class="ri-inbox-archive-line"></i></span>      
                      <span class="hide-menu">Other Incomes</span>
                  </a>
              </li>
        </ul>
        <ul>

            <span class="hide-menu-cust mt-3">Cash-out</span>
 

              <li class="side-bar-item <?php echo sideActive(['bykeexpencess.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['bykeexpencess.php'], $currentPage); ?>" href="bykeexpencess.php">
                  <span><i class="ri-wallet-line"></i></span>    
                  <span class="hide-menu">Vehicle Expenses</span>
                  </a>
              </li>

              <li class="side-bar-item <?php echo sideActive(['investorwithd.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['investorwithd.php'], $currentPage); ?>" href="investorwithd.php">
                  <span><i class="ri-chat-upload-line"></i></span>    
                  <span class="hide-menu">Investor Withdrawals</span>
                  </a>
              </li>


              <li class="side-bar-item <?php echo sideActive(['invinterestgiving.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['invinterestgiving.php'], $currentPage); ?>" href="invinterestgiving.php">
                  <span><i class="ri-shake-hands-line"></i></span>    
                  <span class="hide-menu">Profit Sharing</span>
                  </a>
              </li>

              <li class="side-bar-item <?php echo sideActive(['creditgive.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['creditgive.php'], $currentPage); ?>" href="creditgive.php">
                  <span><i class="ri-folder-shared-line"></i></span>    
                  <span class="hide-menu">Cheque on Lease</span>
                  </a>
              </li>

              <li class="side-bar-item <?php echo sideActive(['otherexpencess.php'], $currentPage); ?>">
                  <a class="side-bar-link <?php echo sideActive(['otherexpencess.php'], $currentPage); ?>" href="otherexpencess.php">
                  <span><i class="ri-inbox-unarchive-line"></i></span>    
                  <span class="hide-menu">Other Expencess</span>
                  </a>
              </li>

              <li class="side-bar-item <?php echo sideActive(['system_logs.php'], $currentPage); ?> mt-3">
                  <a class="side-bar-link <?php echo sideActive(['system_logs.php'], $currentPage); ?>" href="system_logs.php">
                  <span><i class="ri-file-list-3-line"></i></span>
                  <span class="hide-menu">System Logs</span>
                  </a>
              </li>
            

          

          <!-- Logs menu removed per revert request -->
          

        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var current = window.location.pathname.split('/').pop();
    if (!current) current = 'index.php';

    var allLinks = document.querySelectorAll('.side-bar .side-bar-link, .side-bar .side-bar-link-sub');
    allLinks.forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href === '#') return;
        var file = href.split('?')[0].split('/').pop();
        if (file === current) {
            link.classList.add('active');
            var parentLi = link.closest('li');
            if (parentLi) parentLi.classList.add('active');
        }
    });
});
</script>

