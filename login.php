<?php include "header.php"; ?>

<title>Login</title>
</head>
    <body class="bg-color-pr">

  <div class="container-fluid px-0">
    <div class="row no-gutters min-vh-100">
        <div class="col-lg-4 login-showcase-panel d-flex flex-column">
            <div class="login-showcase-top">
                <img src="AutoLedger.png" alt="Kalhara Auto House Logo" class="login-brand-logo">
            </div>

            <div class="login-showcase-middle">
                <p class="login-showcase-title">Buy & Sell</p>
                <p class="login-showcase-title">Track & Record</p>
                <p class="login-showcase-title pr-name-col">Maximize Profit.</p>

                <p class="login-showcase-title-sub">AutoLedger is a financial management app for vehicle 
                    buyers and sellers. It tracks all your cash transactions, 
                    payments, and balances automatically in real time and maximize 
                    every profit. - translate this to sinhala </p>
            </div>

            <div class="login-showcase-bottom">
                <p class="login-version-text mb-0">Version: 0.1</p>
            </div>
        </div>

        <div class="col-lg-8 p-4 p-lg-5 form-wrap-cst d-flex align-items-center justify-content-center login-form-shell">
            <div class="add-byke-panel-log w-100 login-form-panel">
               

                <div class="">
                    <h3 class="login-text">Welcome Back!</h3>
                    <p class="mt-2 sub-topic-1-2">Login to continue <span class="shop-name-login">Demo Auto Sale</span> Finance Management</p>
                    <hr>
                </div>

                <div>
                    <form action="auth.php" method="post">
                        <div class="form-group">
                            <label class="add-byke-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control add-byke-input" name="uname" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label class="add-byke-label">Password <span class="text-danger">*</span></label>
                            <div class="password-toggle-wrap">
                                <input type="password" class="form-control add-byke-input login-password-input" id="password" name="password" placeholder="Enter password" required>
                                <button type="button" class="login-password-toggle" id="togglePassword" aria-label="Show password"><i class="ri-eye-line"></i></button>
                            </div>
                        </div>

                        <button type="submit" class="btn dashboard-filter-btn btn-block login-btn"><i class="ri-lock-2-line"></i> &nbsp; Login</button>

                        <p class="mt-4 sub-topic-1-2">Having Trouble? <span class="shop-name-login">Call Us: 0767612349</span></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>
    




<?php include "footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var toggleBtn = document.getElementById('togglePassword');
    var password = document.getElementById('password');
    if (toggleBtn && password) {
        toggleBtn.addEventListener('click', function(){
            if (password.type === 'password') {
                password.type = 'text';
                toggleBtn.querySelector('i').className = 'ri-eye-off-line';
            } else {
                password.type = 'password';
                toggleBtn.querySelector('i').className = 'ri-eye-line';
            }
        });
    }

    var form = document.querySelector('form[action="auth.php"]');
    if (form) {
        form.addEventListener('submit', function(e){
            var uname = form.querySelector('[name="uname"]').value.trim();
            var pwd = form.querySelector('[name="password"]').value.trim();
            if (!uname || !pwd) {
                e.preventDefault();
                var msg = '';
                if (!uname) msg += 'Username is required.';
                if (!pwd) msg += (msg ? ' ' : '') + 'Password is required.';
                showLoginToast('danger', msg);
            }
        });
    }

    function ensureToastContainer(){
        var container = document.querySelector('.app-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'app-toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    function showLoginToast(type, msg){
        if (!msg) return;
        var container = ensureToastContainer();
        var toast = document.createElement('div');
        toast.className = 'app-toast app-toast-' + (type || 'info');
        toast.innerHTML = '<p class="app-toast-message"></p><button type="button" class="app-toast-close" aria-label="Close">&times;</button>';
        toast.querySelector('.app-toast-message').textContent = msg;

        var closeBtn = toast.querySelector('.app-toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(){
                toast.remove();
            });
        }

        container.appendChild(toast);
        setTimeout(function(){
            if (toast.parentNode) {
                toast.remove();
            }
        }, 4500);
    }

    var serverError = <?php echo isset($_GET['error']) ? json_encode($_GET['error']) : 'null'; ?>;
    if (serverError) {
        showLoginToast('danger', serverError);
    }

});
</script>
