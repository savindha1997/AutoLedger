<section>

    <?php
        $topbarUser = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
        $topbarBusinessName = 'Demo Auto Sale';
        if (isset($con)) {
            $businessInfoResult = mysqli_query($con, "SELECT b_name FROM business_info ORDER BY id ASC LIMIT 1");
            if ($businessInfoResult && mysqli_num_rows($businessInfoResult) > 0) {
                $businessInfoRow = mysqli_fetch_assoc($businessInfoResult);
                if (!empty($businessInfoRow['b_name'])) {
                    $topbarBusinessName = $businessInfoRow['b_name'];
                }
            }
        }
    ?>

    <nav class="navbar navbar-expand-lg fixed-top top-bar">
    
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="appSidebar" aria-expanded="false">
            <i class="ri-menu-line" aria-hidden="true"></i>
        </button>
        <img src="AutoLedger.png" alt="Kalhara Auto House Logo" class="login-brand-logo-t-b"> <a class="navbar-brand name-id" href="#"><?php echo htmlspecialchars($topbarBusinessName); ?></a>
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        
        </ul>
        <div class="d-flex align-items-center ml-auto" style="gap:10px;">
            <form class="form-inline" method="GET" action="view.php" style="margin:0;">
                <input class="form-control form-control-sm" type="text" name="brn" placeholder="Search VRN" style="width:180px;" required>
            </form>

            <span style="font-size:14px; color:#4a4a4a; margin-right:2px;">Hi, <?php echo htmlspecialchars($topbarUser); ?></span>

            <div class="dropdown">
                <a class="side-bar-link" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Profile" style="padding-left:0 !important;">
                    <span style="width:34px;height:34px;border-radius:50%;background-color:#E8F1F0;border:1px solid #d0e3e0;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="ri-user-line" style="color:#237F6F; font-size:18px; line-height:1;"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown" style="border:none; box-shadow:0 12px 24px rgba(0,0,0,0.12); border-radius:10px; min-width:190px;">
                    <a class="dropdown-item" href="#" id="helpDrawerTrigger" style="font-size:14px; display:flex; align-items:center; gap:8px; color:#4a4a4a;">
                        <i class="ri-question-line" style="font-size:16px;"></i>
                        <span>Help</span>
                    </a>
                    <a class="dropdown-item" href="logout.php" style="font-size:14px; display:flex; align-items:center; gap:8px; color:#dc3545;">
                        <i class="ri-logout-box-r-line" style="font-size:16px;"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
    </nav>

    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var menuToggle = document.getElementById('mobileMenuToggle');
        var menuOverlay = document.getElementById('mobileMenuOverlay');
        var sidebar = document.getElementById('appSidebar');

        if (!menuToggle || !menuOverlay || !sidebar) return;

        function closeMenu() {
            sidebar.classList.remove('is-open');
            menuOverlay.classList.remove('is-open');
            document.body.classList.remove('mobile-menu-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Open navigation menu');
        }

        menuToggle.addEventListener('click', function () {
            var isOpen = sidebar.classList.toggle('is-open');
            menuOverlay.classList.toggle('is-open', isOpen);
            document.body.classList.toggle('mobile-menu-open', isOpen);
            menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            menuToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
        });

        menuOverlay.addEventListener('click', closeMenu);
        sidebar.querySelectorAll('.side-bar-link, .side-bar-link-sub').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeMenu();
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 992) closeMenu();
        });
    });
    </script>

    <div class="help-drawer-overlay" id="helpDrawerOverlay"></div>
    <aside class="help-drawer" id="helpDrawer" aria-hidden="true">
        <div class="help-drawer-header">
            <p class="help-drawer-title mb-0">Help</p>
            <div class="help-drawer-header-actions">
                <button type="button" class="help-lang-toggle" id="helpLangToggle" aria-label="Toggle help language">සිං</button>
                <button type="button" class="help-drawer-close" id="helpDrawerClose" aria-label="Close help drawer">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        <div class="help-drawer-body">
            <div class="help-doc-layout is-visible" data-lang="en" id="helpDocEn">
                <nav class="help-topic-nav" aria-label="Help topics">
                    <p class="help-topic-nav-title">Topics</p>
                    <a href="#help-topic-overview" class="help-topic-link">System Overview</a>
                    <a href="#help-topic-start" class="help-topic-link">How to Start</a>
                    <a href="#help-topic-actions" class="help-topic-link">How to Perform Actions</a>
                    <a href="#help-topic-dashboard" class="help-topic-link">Dashboard</a>
                    <a href="#help-topic-vehicles" class="help-topic-link">Add Vehicle</a>
                    <a href="#help-topic-sale" class="help-topic-link">Sell Vehicle</a>
                    <a href="#help-topic-view-update" class="help-topic-link">View and Update</a>
                    <a href="#help-topic-expenses" class="help-topic-link">Vehicle Expenses</a>
                    <a href="#help-topic-cheque" class="help-topic-link">Cheque Management</a>
                    <a href="#help-topic-investors" class="help-topic-link">Investor Management</a>
                    <a href="#help-topic-other" class="help-topic-link">Other Income and Expenses</a>
                    <a href="#help-topic-filter" class="help-topic-link">Filter Records</a>
                    <a href="#help-topic-alerts" class="help-topic-link">Alerts and Toasters</a>
                </nav>

                <div class="help-topic-content">
                    <section id="help-topic-overview" class="help-topic-section">
                        <h4>System Overview</h4>
                        <p>This system tracks vehicle buying, selling, expenses, credit flows, investor funds, and business-level profitability in one place.</p>
                        <p>Most pages are split into two areas: data table on the left and an action form on the right. When you save, update, or delete data, a top-right toast message confirms the result.</p>
                    </section>

                    <section id="help-topic-start" class="help-topic-section">
                        <h4>How to Start the System</h4>
                        <p>Recommended startup flow for a new business record set:</p>
                        <ol>
                            <li>Add Investor: create initial capital sources in All Investors.</li>
                            <li>Add Investor Deposit (optional): add additional investor deposits in New Investments.</li>
                            <li>Add Vehicle: register purchased vehicle details and bought price.</li>
                            <li>Add Vehicle Expenses: record repair/parts/transport costs against the selected vehicle.</li>
                            <li>Sell Vehicle: enter sold details, buyer details, and selling method.</li>
                            <li>Review Profit and Insights: open Dashboard and Filter pages to review totals and trends.</li>
                        </ol>
                    </section>

                    <section id="help-topic-actions" class="help-topic-section">
                        <h4>How to Perform Actions in Detail</h4>
                        <p>Use these action patterns everywhere in the system:</p>
                        <ol>
                            <li>Create record: fill required fields marked with * and click the primary green action button.</li>
                            <li>Search-linked forms: first choose VRN and click Search, then submit the full form.</li>
                            <li>Edit record: open View or Edit from table actions, update values, then save.</li>
                            <li>Delete record: click Delete, confirm in modal, and wait for success toast.</li>
                            <li>Review results: use tables for history and the dashboard cards for high-level KPIs.</li>
                            <li>Check alerts: all success/error responses appear as top-right toast messages.</li>
                        </ol>
                        <p>Tip: complete one vehicle cycle (buy -> expenses -> sale) before checking profit so calculations include full data.</p>
                    </section>

                    <section id="help-topic-dashboard" class="help-topic-section">
                        <h4>Dashboard</h4>
                        <p>The Dashboard summarizes counts, cash, balances, assets, and profit using totals from all key modules.</p>
                        <p>Top cards show:</p>
                        <ul>
                            <li>In-Store Vehicle Count</li>
                            <li>Sold Vehicle Count</li>
                            <li>All Vehicle Count</li>
                        </ul>
                        <p class="help-formula-title">Total Business Profit Calculation</p>
                        <div class="help-formula-box">
                            <p class="mb-1"><strong>Total Business Profit</strong> = ((Total Sold Price - Total Sold Vehicle Value) + Other Income) - (Investor Profit Giving + Other Expenses)</p>
                            <p class="mb-1">In code:</p>
                            <p class="mb-0">totproinhand = ((sumtotbs - sumtotbv) + sumoth) - (sumtotpg + sumote)</p>
                        </div>

                        <p class="help-formula-title mt-3">Other Dashboard Financial Indicators</p>
                        <div class="help-formula-box">
                            <p class="mb-1"><strong>Total Cash In-Hand With Profit</strong> = total cash in - total cash out</p>
                            <p class="mb-1">cashinhand = totcashin - totcashout</p>

                            <p class="mb-1"><strong>Total Cash In-Hand Without Profit</strong> = cash in hand with profit - total business profit</p>
                            <p class="mb-1">cashinhand_wop = cashinhand - totproinhand</p>

                            <p class="mb-1"><strong>Total Value of In-Store Vehicles</strong> = (total bought value + total vehicle expenses) - sold vehicle value portion</p>
                            <p class="mb-1">totvalueinst = (sumbykeb + sumbykeexpencess) - sumtotbv</p>

                            <p class="mb-1"><strong>Receivable Amount</strong> = total cheque/credit given - total cheque/credit received</p>
                            <p class="mb-1">totcrdbal = sumcrg - sumcdr</p>

                            <p class="mb-1"><strong>Total Business Assets</strong> = (Starting Investor Capital + Additional Deposits - Investor Withdrawals) + Total Business Profit</p>
                            <p class="mb-1">avalbalan = ((suminvs + sumind) - suminw) + totproinhand</p>

                            <p class="mb-1"><strong>Total Business Assets (without profit)</strong> = Starting Investor Capital + Additional Deposits - Investor Withdrawals</p>
                            <p class="mb-0">avalbalan_wop = ((suminvs + sumind) - suminw)</p>
                        </div>
                    </section>

                    <section id="help-topic-vehicles" class="help-topic-section">
                        <h4>Add Vehicle</h4>
                        <p>Use the Add Vehicle form to register a new vehicle purchase.</p>
                        <ol>
                            <li>Enter required fields: Vehicle Registration Number, Bought Price, Bought Date.</li>
                            <li>Add optional seller and vehicle details for complete records.</li>
                            <li>Click Add Vehicle.</li>
                        </ol>
                        <p>Validation rules:</p>
                        <ul>
                            <li>Duplicate registration numbers are blocked.</li>
                            <li>Bought price must be numeric and greater than zero.</li>
                            <li>If cash in hand is lower than bought price, save is blocked.</li>
                            <li>Newly added vehicles default to In-store status.</li>
                        </ul>
                    </section>

                    <section id="help-topic-sale" class="help-topic-section">
                        <h4>Sell Vehicle</h4>
                        <ol>
                            <li>Select a vehicle and click Search.</li>
                            <li>Confirm auto-calculated Vehicle Value.</li>
                            <li>Select selling method: Cash or Lease.</li>
                            <li>For Lease sales, fill Leasing Company, Down Payment, Finance Charges, and Finance Amount.</li>
                            <li>Enter buyer details, sold date, and sold price, then submit.</li>
                        </ol>
                        <p>After saving sale data, the vehicle is marked as Sold and appears in sold-related reports.</p>
                    </section>

                    <section id="help-topic-view-update" class="help-topic-section">
                        <h4>View and Update</h4>
                        <p>View page displays vehicle profile with three information cards and finance summary:</p>
                        <ul>
                            <li>Vehicle Information</li>
                            <li>Seller Information</li>
                            <li>Buyer Information</li>
                        </ul>
                        <p>Update page allows editing these fields and deleting a vehicle with all related records in one action.</p>
                        <p>Delete operation removes related data from vehicle expenses, sales, credit giving, and credit returns for that vehicle.</p>
                    </section>

                    <section id="help-topic-expenses" class="help-topic-section">
                        <h4>Vehicle Expenses</h4>
                        <p>Use this page to assign expenses directly to a selected vehicle.</p>
                        <ol>
                            <li>Select VRN and click Search.</li>
                            <li>Enter date, description, and amount.</li>
                            <li>Submit to save expense.</li>
                        </ol>
                        <p>Vehicle Value shown across the system is based on Bought Price + related vehicle expenses.</p>
                    </section>

                    <section id="help-topic-cheque" class="help-topic-section">
                        <h4>Cheque Management</h4>
                        <p>Cheque flow uses three pages:</p>
                        <ul>
                            <li>Cheque Giving: records issued amounts (usually lease-linked VRN entries).</li>
                            <li>Cheque Received: records returned/received amounts.</li>
                            <li>Cheque Holders: shows running balance by holder.</li>
                        </ul>
                        <p class="help-formula-title">Receivable Balance</p>
                        <div class="help-formula-box">
                            <p class="mb-0">Receivable Balance = Total Cheque Giving - Total Cheque Received</p>
                        </div>
                    </section>

                    <section id="help-topic-investors" class="help-topic-section">
                        <h4>Investor Management</h4>
                        <p>Investor module has four key pages:</p>
                        <ul>
                            <li>All Investors: register base investor capital.</li>
                            <li>New Investments: add additional deposits.</li>
                            <li>Investor Withdrawals: track withdrawn amounts.</li>
                            <li>Profit Giving: track profit distributions to investors.</li>
                        </ul>
                        <p>Investor detail view summarizes investor-level capital, cash impact, and profit-sharing history.</p>
                    </section>

                    <section id="help-topic-other" class="help-topic-section">
                        <h4>Other Income and Expenses</h4>
                        <p>These pages capture non-vehicle financial records that still affect total business cash and profit.</p>
                        <ul>
                            <li>Other Income increases cash and profit.</li>
                            <li>Other Expenses reduce cash and profit.</li>
                        </ul>
                    </section>

                    <section id="help-topic-filter" class="help-topic-section">
                        <h4>Filter Records</h4>
                        <ol>
                            <li>Select From and To dates.</li>
                            <li>Submit to load sold records in that range.</li>
                            <li>Review sold count and period profit summary.</li>
                        </ol>
                        <p class="help-formula-title">Filtered Profit</p>
                        <div class="help-formula-box">
                            <p class="mb-0">Filtered Profit = Sum of Sold Price - Sum of Sold Vehicle Value</p>
                        </div>
                    </section>

                    <section id="help-topic-alerts" class="help-topic-section">
                        <h4>Alerts and Toasters</h4>
                        <p>All form success/error messages are converted to top-right toast messages automatically.</p>
                        <ul>
                            <li>Green: success</li>
                            <li>Red: error</li>
                            <li>Yellow: warning</li>
                            <li>Blue/teal: info</li>
                        </ul>
                        <p>Toasts dismiss automatically or can be closed manually.</p>
                    </section>
                </div>
            </div>

            <div class="help-doc-layout" data-lang="si" id="helpDocSi">
                <nav class="help-topic-nav" aria-label="Help topics Sinhala">
                    <p class="help-topic-nav-title">මාතෘකා</p>
                    <a href="#help-si-topic-overview" class="help-topic-link">පද්ධති සාරාංශය</a>
                    <a href="#help-si-topic-start" class="help-topic-link">පද්ධතිය ආරම්භ කිරීම</a>
                    <a href="#help-si-topic-actions" class="help-topic-link">ක්‍රියා කරන ආකාරය</a>
                    <a href="#help-si-topic-dashboard" class="help-topic-link">ඩෑෂ්බෝඩ්</a>
                    <a href="#help-si-topic-vehicles" class="help-topic-link">වාහනය එක් කිරීම</a>
                    <a href="#help-si-topic-sale" class="help-topic-link">වාහනය විකිණීම</a>
                    <a href="#help-si-topic-view-update" class="help-topic-link">බැලීම සහ යාවත්කාලීන කිරීම</a>
                    <a href="#help-si-topic-expenses" class="help-topic-link">වාහන වියදම්</a>
                    <a href="#help-si-topic-cheque" class="help-topic-link">චෙක්පත් කළමනාකරණය</a>
                    <a href="#help-si-topic-investors" class="help-topic-link">ආයෝජක කළමනාකරණය</a>
                    <a href="#help-si-topic-other" class="help-topic-link">වෙනත් ආදායම් සහ වියදම්</a>
                    <a href="#help-si-topic-filter" class="help-topic-link">දිනය අනුව පෙරහන්</a>
                    <a href="#help-si-topic-alerts" class="help-topic-link">දැනුම්දීම් සහ ටෝස්ට්</a>
                </nav>

                <div class="help-topic-content">
                    <section id="help-si-topic-overview" class="help-topic-section">
                        <h4>පද්ධති සාරාංශය</h4>
                        <p>මෙම පද්ධතියෙන් වාහන මිලදී ගැනීම්, විකිණීම්, වියදම්, චෙක්පත්/ලැබිය යුතු මුදල්, ආයෝජක මුදල් සහ ව්‍යාපාර ලාභය එකම ස්ථානයකින් කළමනාකරණය කරයි.</p>
                        <p>බොහෝ පිටු වගු කොටසක් (වම) සහ ක්‍රියාකාරී පෝරම කොටසක් (දකුණ) ලෙස බෙදී ඇත. සුරකින්න/යාවත්කාලීන/මකන්න කළ විට දකුණු ඉහළට ටෝස්ට් පණිවිඩයක් පෙන්වයි.</p>
                    </section>

                    <section id="help-si-topic-start" class="help-topic-section">
                        <h4>පද්ධතිය ආරම්භ කරන නිවැරදි ක්‍රමය</h4>
                        <p>නව ව්‍යාපාර දත්ත ආරම්භ කිරීමට පහත පියවර අනුගමනය කරන්න:</p>
                        <ol>
                            <li>ආයෝජකයෙකු එක් කරන්න: ආරම්භක මුල්ධනය සටහන් කරන්න.</li>
                            <li>අමතර තැන්පතු එක් කරන්න (අවශ්‍ය නම්): New Investments පිටුවෙන් එකතු කරන්න.</li>
                            <li>වාහනයක් එක් කරන්න: මිලදී ගත් දිනය/මිල සහ වාහන විස්තර ඇතුළත් කරන්න.</li>
                            <li>වාහන වියදම් එක් කරන්න: එම වාහනයට අදාල අලුත්වැඩියා/ප්‍රවාහන/අමතර වියදම් සටහන් කරන්න.</li>
                            <li>වාහනය විකිණීම සටහන් කරන්න: Buyer විස්තර, විකුණුම් ක්‍රමය සහ මිල දමන්න.</li>
                            <li>ලාභය බලන්න: Dashboard සහ Filter Records තුළින් සාරාංශ පරීක්ෂා කරන්න.</li>
                        </ol>
                    </section>

                    <section id="help-si-topic-actions" class="help-topic-section">
                        <h4>පද්ධතියේ ක්‍රියා කරන ආකාරය (විස්තරාත්මක)</h4>
                        <ol>
                            <li>නව දත්ත එක් කිරීම: * සලකුණ ඇති අත්‍යවශ්‍ය ක්ෂේත්‍ර පුරවා හරිත බොත්තම ක්ලික් කරන්න.</li>
                            <li>Search-සම්බන්ධ පෝරම: පළමුව VRN තෝරන්න, Search කරන්න, පසුව පෝරමය Submit කරන්න.</li>
                            <li>සංස්කරණය: වගුවෙන් View/Edit තෝරා අගයන් වෙනස් කර Save කරන්න.</li>
                            <li>මකාදැමීම: Delete ක්ලික් කර Modal තුළ තහවුරු කර ක්‍රියාව සම්පූර්ණ කරන්න.</li>
                            <li>ප්‍රතිඵල පරීක්ෂාව: වගු ඉතිහාසයටත් Dashboard කාඩ්පත් KPI වලටත් භාවිතා කරන්න.</li>
                            <li>දැනුම්දීම්: සාර්ථක/දෝෂ පණිවිඩ සියල්ල Top-right Toast ලෙස පෙන්වයි.</li>
                        </ol>
                        <p>සලහක්: ලාභය නිවැරදිව බලන්න Buy -> Expense -> Sale යන සම්පූර්ණ චක්‍රය අවසන් කර පසුව Dashboard බලන්න.</p>
                    </section>

                    <section id="help-si-topic-dashboard" class="help-topic-section">
                        <h4>ඩෑෂ්බෝඩ්</h4>
                        <p>ඩෑෂ්බෝඩ් එකෙන් ගණන්, මුදල්, ලැබිය යුතු මුදල්, ව්‍යාපාර වත්කම් සහ ලාභය එකවර සාරාංශ කර පෙන්වයි.</p>
                        <p>ඉහළ කාඩ්පත් තුන පෙන්වන්නේ:</p>
                        <ul>
                            <li>In-Store Vehicle Count</li>
                            <li>Sold Vehicle Count</li>
                            <li>All Vehicle Count</li>
                        </ul>
                        <p class="help-formula-title">Total Business Profit ගණනය</p>
                        <div class="help-formula-box">
                            <p class="mb-1"><strong>Total Business Profit</strong> = ((Total Sold Price - Total Sold Vehicle Value) + Other Income) - (Investor Profit Giving + Other Expenses)</p>
                            <p class="mb-1">කේත අගය:</p>
                            <p class="mb-0">totproinhand = ((sumtotbs - sumtotbv) + sumoth) - (sumtotpg + sumote)</p>
                        </div>

                        <p class="help-formula-title mt-3">අනෙකුත් Dashboard දර්ශක</p>
                        <div class="help-formula-box">
                            <p class="mb-1"><strong>Total Cash In-Hand With Profit</strong> = total cash in - total cash out</p>
                            <p class="mb-1">cashinhand = totcashin - totcashout</p>

                            <p class="mb-1"><strong>Total Cash In-Hand Without Profit</strong> = cash in hand with profit - total business profit</p>
                            <p class="mb-1">cashinhand_wop = cashinhand - totproinhand</p>

                            <p class="mb-1"><strong>Total Value of In-Store Vehicles</strong> = (total bought value + total vehicle expenses) - sold vehicle value portion</p>
                            <p class="mb-1">totvalueinst = (sumbykeb + sumbykeexpencess) - sumtotbv</p>

                            <p class="mb-1"><strong>Receivable Amount</strong> = total cheque/credit given - total cheque/credit received</p>
                            <p class="mb-1">totcrdbal = sumcrg - sumcdr</p>

                            <p class="mb-1"><strong>Total Business Assets</strong> = (Starting Investor Capital + Additional Deposits - Investor Withdrawals) + Total Business Profit</p>
                            <p class="mb-1">avalbalan = ((suminvs + sumind) - suminw) + totproinhand</p>

                            <p class="mb-1"><strong>Total Business Assets (without profit)</strong> = Starting Investor Capital + Additional Deposits - Investor Withdrawals</p>
                            <p class="mb-0">avalbalan_wop = ((suminvs + sumind) - suminw)</p>
                        </div>
                    </section>

                    <section id="help-si-topic-vehicles" class="help-topic-section">
                        <h4>වාහනය එක් කිරීම</h4>
                        <p>Add Vehicle පිටුවෙන් නව මිලදී ගත් වාහන ලියාපදිංචි කරයි.</p>
                        <ol>
                            <li>අත්‍යවශ්‍ය ක්ෂේත්‍ර: Vehicle Registration Number, Bought Price, Bought Date.</li>
                            <li>විකල්ප විස්තර: Year/Make/Model, Seller info, Chassis/Engine.</li>
                            <li>Add Vehicle බොත්තම ක්ලික් කරන්න.</li>
                        </ol>
                        <p>Validation නීති:</p>
                        <ul>
                            <li>එකම Registration Number එක දෙවරක් ඇතුළත් කළ නොහැක.</li>
                            <li>Bought Price අගය අංකීය හා 0 ට වැඩි විය යුතුය.</li>
                            <li>Cash in hand ප්‍රමාණය ප්‍රමාණවත් නොවුණොත් save අවහිර වේ.</li>
                            <li>නව වාහනයේ තත්ත්වය ස්වයංක්‍රීයව In-store ලෙස සුරකියි.</li>
                        </ul>
                    </section>

                    <section id="help-si-topic-sale" class="help-topic-section">
                        <h4>වාහනය විකිණීම</h4>
                        <ol>
                            <li>VRN තෝරා Search කරන්න.</li>
                            <li>ස්වයංක්‍රීය Vehicle Value පරීක්ෂා කරන්න.</li>
                            <li>Selling Method: Cash හෝ Lease තෝරන්න.</li>
                            <li>Lease නම් Leasing Company, Down Payment, Finance Charges, Finance Amount පුරවන්න.</li>
                            <li>Buyer විස්තර + Sold Date + Sold Price දමා Submit කරන්න.</li>
                        </ol>
                        <p>විකුණූ පසු වාහනය Sold ලෙස සලකුණු වී reports වලට ඇතුළත් වේ.</p>
                    </section>

                    <section id="help-si-topic-view-update" class="help-topic-section">
                        <h4>බැලීම සහ යාවත්කාලීන කිරීම</h4>
                        <p>View පිටුවෙන් වාහන තොරතුරු කාඩ් 3 ක් ලෙස පෙන්වයි:</p>
                        <ul>
                            <li>Vehicle Information</li>
                            <li>Seller Information</li>
                            <li>Buyer Information</li>
                        </ul>
                        <p>Update පිටුවෙන් එම දත්ත සංස්කරණය කළ හැකි අතර වාහනය සමඟ සම්බන්ධ දත්ත එක්වර මකාදැමිය හැක.</p>
                    </section>

                    <section id="help-si-topic-expenses" class="help-topic-section">
                        <h4>වාහන වියදම්</h4>
                        <ol>
                            <li>VRN තෝරන්න සහ Search කරන්න.</li>
                            <li>Date, Description, Amount ඇතුළත් කරන්න.</li>
                            <li>Submit කර වියදම සුරකින්න.</li>
                        </ol>
                        <p>පද්ධතියේ Vehicle Value = Bought Price + එම වාහනයට අදාල වියදම්.</p>
                    </section>

                    <section id="help-si-topic-cheque" class="help-topic-section">
                        <h4>චෙක්පත් කළමනාකරණය</h4>
                        <p>මෙම ප්‍රවාහයේ පිටු 3 ක් ඇත:</p>
                        <ul>
                            <li>Cheque Giving: නිකුත් කරන මුදල් සටහන් කරයි.</li>
                            <li>Cheque Received: ආපසු ලැබෙන මුදල් සටහන් කරයි.</li>
                            <li>Cheque Holders: ඒ ඒ කෙනාගේ balance පෙන්වයි.</li>
                        </ul>
                        <p class="help-formula-title">Receivable Balance</p>
                        <div class="help-formula-box">
                            <p class="mb-0">Receivable Balance = Total Cheque Giving - Total Cheque Received</p>
                        </div>
                    </section>

                    <section id="help-si-topic-investors" class="help-topic-section">
                        <h4>ආයෝජක කළමනාකරණය</h4>
                        <ul>
                            <li>All Investors: ආරම්භක මුල්ධනය.</li>
                            <li>New Investments: අමතර තැන්පතු.</li>
                            <li>Investor Withdrawals: ආපසු ගත් මුදල්.</li>
                            <li>Profit Giving: ආයෝජක ලාභ බෙදාහැරීම.</li>
                        </ul>
                        <p>Investor detail පිටුවෙන් ඒ ඒ ආයෝජකයාගේ මුදල් ප්‍රවාහය හා profit records පෙන්වයි.</p>
                    </section>

                    <section id="help-si-topic-other" class="help-topic-section">
                        <h4>වෙනත් ආදායම් සහ වියදම්</h4>
                        <p>වාහන ගනුදෙනු නොවන නමුත් ව්‍යාපාර cash සහ profit වලට බලපාන සියලු entries මෙහි සටහන් කරයි.</p>
                        <ul>
                            <li>Other Income: cash/profit වැඩි කරයි.</li>
                            <li>Other Expenses: cash/profit අඩු කරයි.</li>
                        </ul>
                    </section>

                    <section id="help-si-topic-filter" class="help-topic-section">
                        <h4>දිනය අනුව පෙරහන්</h4>
                        <ol>
                            <li>From සහ To දිනය තෝරන්න.</li>
                            <li>Submit කර එම පරාසයේ sold records ලබාගන්න.</li>
                            <li>Sold count සහ period profit සාරාංශ පරීක්ෂා කරන්න.</li>
                        </ol>
                        <p class="help-formula-title">Filtered Profit</p>
                        <div class="help-formula-box">
                            <p class="mb-0">Filtered Profit = Sum of Sold Price - Sum of Sold Vehicle Value</p>
                        </div>
                    </section>

                    <section id="help-si-topic-alerts" class="help-topic-section">
                        <h4>දැනුම්දීම් සහ ටෝස්ට්</h4>
                        <p>පෝරම සාර්ථක/දෝෂ පණිවිඩ සියල්ලම ස්වයංක්‍රීයව top-right toast ලෙස පෙන්වයි.</p>
                        <ul>
                            <li>කොළ: success</li>
                            <li>රතු: error</li>
                            <li>කහ: warning</li>
                            <li>නීල-හරිත: info</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </aside>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var trigger = document.getElementById('helpDrawerTrigger');
        var drawer = document.getElementById('helpDrawer');
        var overlay = document.getElementById('helpDrawerOverlay');
        var closeBtn = document.getElementById('helpDrawerClose');
        var langToggle = document.getElementById('helpLangToggle');

        function getActiveLayout(){
            return drawer ? drawer.querySelector('.help-doc-layout.is-visible') : null;
        }

        function getTopicContext(){
            var activeLayout = getActiveLayout();
            if (!activeLayout) {
                return { topicContent: null, topicLinks: [], topicSections: [] };
            }

            return {
                topicContent: activeLayout.querySelector('.help-topic-content'),
                topicLinks: activeLayout.querySelectorAll('.help-topic-link'),
                topicSections: activeLayout.querySelectorAll('.help-topic-section')
            };
        }

        function setActiveTopic(topicId){
            var ctx = getTopicContext();
            var topicLinks = ctx.topicLinks;
            topicLinks.forEach(function(link){
                var href = link.getAttribute('href') || '';
                link.classList.toggle('is-active', href === '#' + topicId);
            });
        }

        function syncActiveTopicByScroll(){
            var ctx = getTopicContext();
            var topicContent = ctx.topicContent;
            var topicSections = ctx.topicSections;
            if (!topicContent || !topicSections.length) return;
            var scrollPos = topicContent.scrollTop + 28;
            var currentId = topicSections[0].id;

            topicSections.forEach(function(section){
                if (section.offsetTop <= scrollPos) {
                    currentId = section.id;
                }
            });

            setActiveTopic(currentId);
        }

        function openDrawer(){
            if (!drawer || !overlay) return;
            drawer.classList.add('is-open');
            overlay.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            document.body.classList.add('help-drawer-open');
            syncActiveTopicByScroll();
        }

        function closeDrawer(){
            if (!drawer || !overlay) return;
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('help-drawer-open');
        }

        if (trigger) {
            trigger.addEventListener('click', function(e){
                e.preventDefault();
                if (typeof $ !== 'undefined' && $('.dropdown-menu').length) {
                    $('.dropdown-menu').removeClass('show');
                }
                openDrawer();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeDrawer);
        }

        if (overlay) {
            overlay.addEventListener('click', closeDrawer);
        }

        if (drawer) {
            drawer.querySelectorAll('.help-topic-content').forEach(function(contentEl){
                contentEl.addEventListener('scroll', syncActiveTopicByScroll);
            });

            drawer.addEventListener('click', function(e){
                var link = e.target.closest('.help-topic-link');
                if (!link) return;

                var href = link.getAttribute('href') || '';
                if (!href || href.charAt(0) !== '#') return;
                var activeLayout = getActiveLayout();
                var target = activeLayout ? activeLayout.querySelector(href) : null;
                var ctx = getTopicContext();
                var topicContent = ctx.topicContent;
                if (!target || !topicContent) return;

                e.preventDefault();
                setActiveTopic(target.id);
                topicContent.scrollTo({
                    top: Math.max(0, target.offsetTop - 8),
                    behavior: 'smooth'
                });
            });
        }

        if (langToggle && drawer) {
            langToggle.addEventListener('click', function(){
                var enLayout = document.getElementById('helpDocEn');
                var siLayout = document.getElementById('helpDocSi');
                if (!enLayout || !siLayout) return;

                var isEnglishVisible = enLayout.classList.contains('is-visible');
                if (isEnglishVisible) {
                    enLayout.classList.remove('is-visible');
                    siLayout.classList.add('is-visible');
                    langToggle.textContent = 'EN';
                } else {
                    siLayout.classList.remove('is-visible');
                    enLayout.classList.add('is-visible');
                    langToggle.textContent = 'සිං';
                }

                syncActiveTopicByScroll();
            });
        }

        syncActiveTopicByScroll();

        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') {
                closeDrawer();
            }
        });
    });
    </script>

</section>