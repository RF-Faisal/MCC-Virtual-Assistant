<?php
    session_start();

    if($_SESSION['username'] == NULL) header("Location: sign-in.php");
    include 'db_conn.php';

    $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    if(isset($_GET['id']))
    {
        $reward_id = $_GET['id'];
        $sql = "select * from REWARD_STOCK where REWARD_ID='$reward_id'";
        $stid = oci_parse($conn, $sql);
        $exc = oci_execute($stid);
        $reward = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
 
        if($reward == NULL) header("Location: reward-store.php");
        else
        {
            if(isset($_POST['order']) && $_POST['quantity']>0)
            {
                $quantity = $_POST['quantity'];
                $reward_name = $reward['REWARD_NAME'];
                $reward_price = $reward['PRICE'];
                $username = $_SESSION['username'];
        
                $sql = "DECLARE
                CURSOR CRS IS SELECT * FROM REWARD WHERE ORDER_STATUS='Available' AND REWARD_NAME='$reward_name' FOR UPDATE OF USERNAME, ORDER_STATUS;
                REC CRS%ROWTYPE;
                BEGIN
                OPEN CRS;
                    FOR I IN 1..$quantity
                    LOOP
                        FETCH CRS INTO REC;
                        EXIT WHEN CRS%NOTFOUND;
                        UPDATE REWARD
                        SET USERNAME = '$username',
                        ORDER_STATUS = 'Processing'
                        WHERE CURRENT OF CRS;
                        UPDATE MEMBER
                        SET REWARD_POINT = REWARD_POINT - $reward_price
                        WHERE USERNAME = '$username';
                    END LOOP;
                CLOSE CRS;
                END;";

                $stid = oci_parse($conn, $sql);
                oci_execute($stid);

                $_SESSION['reward_point'] = $_SESSION['reward_point'] - $quantity*$reward_price;
                header("Location: reward-store.php");
            }            
        }
    }
    else header("Location: sign-in.php");
?>
<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-layout-mode="dark">

<head>
    <meta charset="utf-8">
    <title>MCC Virtual Assistant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="MCC Virtual Assistant" name="description">
    <meta content="MIST Computer Club" name="TEAM NEXT PERMUTATION">

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- nouisliderribute css -->
    <link rel="stylesheet" href="assets/libs/nouislider/nouislider.min.css">
    <!-- gridjs css -->
    <link rel="stylesheet" href="assets/libs/gridjs/theme/mermaid.min.css">
    <!-- Sweet Alert css-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css">
    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="assets/css/app.main.css" rel="stylesheet" type="text/css">
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css">

    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.7.96/css/materialdesignicons.css" rel="stylesheet">
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="index.html" class="logo logo-dark">
                                <span class="logo-lg">
                                    <img src="assets/images/logo-dark.png" alt="" height="40">
                                </span>
                            </a>
        
                            <a href="index.html" class="logo logo-light">
                                <span class="logo-lg">
                                    <img src="assets/images/logo-light.png" alt="" height="40">
                                </span>
                            </a>
                        </div>
        
                        <!-- App Search-->
                        <form class="app-search d-none d-md-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Search" autocomplete="off"
                                    id="search-options" value="">
                                <span class="mdi mdi-magnify search-widget-icon"></span>
                                <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                                    id="search-close-options"></span>
                            </div>
                            <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                                <div data-simplebar style="max-height: 500px;">
                                    <!-- item-->
                                    <div class="dropdown-header">
                                        <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                                    </div>
        
                                    <div class="dropdown-item bg-transparent text-wrap">
                                        <a href="index.html" class="btn btn-soft-secondary btn-sm btn-rounded">how to setup <i
                                                class="mdi mdi-magnify ms-1"></i></a>
                                        <a href="index.html" class="btn btn-soft-secondary btn-sm btn-rounded">buttons <i
                                                class="mdi mdi-magnify ms-1"></i></a>
                                    </div>
                                    <!-- item-->
                                    <div class="dropdown-header mt-2">
                                        <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                                    </div>
        
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                        <span>Analytics Dashboard</span>
                                    </a>
        
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                        <span>Help Center</span>
                                    </a>
        
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                        <span>My account settings</span>
                                    </a>
        
                                    <!-- item-->
                                    <div class="dropdown-header mt-2">
                                        <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                                    </div>
        
                                    <div class="notification-list">
                                        <!-- item -->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-2.jpg"
                                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                    <h6 class="m-0">Angela Bernier</h6>
                                                    <span class="fs-11 mb-0 text-muted">Manager</span>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- item -->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-3.jpg"
                                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                    <h6 class="m-0">David Grasso</h6>
                                                    <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- item -->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                            <div class="d-flex">
                                                <img src="assets/images/users/avatar-5.jpg"
                                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                    <h6 class="m-0">Mike Bunch</h6>
                                                    <span class="fs-11 mb-0 text-muted">React Developer</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
        
                                <div class="text-center pt-3 pb-1">
                                    <a href="pages-search-results.html" class="btn btn-primary btn-sm">View All Results <i
                                            class="ri-arrow-right-line ms-1"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
        
                    <div class="d-flex align-items-center">
                        <div class="dropdown d-md-none topbar-head-dropdown header-item">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..."
                                                aria-label="Recipient's username">
                                            <button class="btn btn-primary" type="submit"><i
                                                    class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
        
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>
        
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="assets/images/users/<?php echo $_SESSION['username'];?>.jpg" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text"><?php echo $_SESSION['name'];?>
                                        </span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="user-profile.php?un=<?php echo $_SESSION['username'];?>"><i class="mdi mdi-account-circle-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">My Profile</span></a>
                                <?php if ($_SESSION['type'] == 'Member') {?>
                                <a class="dropdown-item" href="reward-store.php"><i class="mdi mdi-alpha-p-circle-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Points: <b><?php echo $_SESSION['reward_point'];?></b></span></a>
                                <?php }?>
                                <?php if ($_SESSION['role'] == 'Moderator' || $_SESSION['role'] == 'President' || $_SESSION['role'] == 'Secretary') {?>
                                <a class="dropdown-item" href="table-member.php"><i class="mdi mdi-clipboard-list-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">All Members</span></a>
                                <a class="dropdown-item" href="table-alumni.php"><i class="mdi mdi-clipboard-file-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">All Alumnis</span></a>
                                <a class="dropdown-item" href="table-alumni.php"><i class="mdi mdi-code-brackets text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Online Judges</span></a>
                                <?php }?>
                                <a class="dropdown-item" href="edit-profile.php"><i
                                        class="mdi mdi-account-edit-outline text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle">Edit Profile</span></a>
                                <a class="dropdown-item" href="sign-out.php"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-logout">Sign Out</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav d-flex justify-content-center" id="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="user-profile.php?un=<?php echo $_SESSION['username'];?>" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-account-circle-line"></i> <span data-key="t-dashboards">My Profile</span>
                            </a>
                        </li>
                        <!-- end My Profile -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="courses.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-terminal-box-line"></i><span data-key="t-dashboards">Courses</span>
                            </a>
                        </li>
                        <!-- end Courses -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="reward-store.php" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-store-3-line"></i><span data-key="t-dashboards">Reward Store</span>
                            </a>
                        </li>
                        <!-- end Reward Store -->
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row gx-lg-5">
                                        <div class="col-xl-4 col-md-8 mx-auto">
                                            <div class="product-img-slider sticky-side-div">
                                                <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                                    <div class="swiper-wrapper">
                                                        <div class="swiper-slide">
                                                            <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                        </div>
                                                        <div class="swiper-slide">
                                                            <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                        </div>
                                                        <div class="swiper-slide">
                                                            <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                        </div>
                                                        <div class="swiper-slide">
                                                            <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                        </div>
                                                    </div>
                                                    <div class="swiper-button-next"></div>
                                                    <div class="swiper-button-prev"></div>
                                                </div>
                                                <!-- end swiper thumbnail slide -->
                                                <div class="swiper product-nav-slider mt-2">
                                                    <div class="swiper-wrapper">
                                                        <div class="swiper-slide">
                                                            <div class="nav-slide-item">
                                                                <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                            </div>
                                                        </div>
                                                        <div class="swiper-slide">
                                                            <div class="nav-slide-item">
                                                                <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                            </div>
                                                        </div>
                                                        <div class="swiper-slide">
                                                            <div class="nav-slide-item">
                                                                <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                            </div>
                                                        </div>
                                                        <div class="swiper-slide">
                                                            <div class="nav-slide-item">
                                                                <img src="assets/images/rewards/reward (1).png" alt="" class="img-fluid d-block" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end swiper nav slide -->
                                            </div>
                                        </div>
                                        <!-- end col -->

                                        <div class="col-xl-8">
                                            <div class="mt-xl-0 mt-5">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <h4 class="mt-1"><?php echo $reward['REWARD_NAME'] ?></h4>
                                                    </div>
                                                    <?php if ($_SESSION['type'] == 'Moderator' || $_SESSION['type'] == 'President' || $_SESSION['type'] == 'Secretary') {?>
                                                    <div class="flex-shrink-0">
                                                        <div>
                                                            <a href="apps-ecommerce-add-product.html" class="btn btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="ri-pencil-fill align-bottom"></i></a>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-lg-4 col-sm-6">
                                                        <div class="p-2 border border-dashed rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm me-2">
                                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                                        <i class="ri-exchange-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-muted mb-1">Required Points</p>
                                                                    <h5 class="mb-0"><?php echo $reward['PRICE'] ?></h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- end col -->
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-lg-4 col-sm-6">
                                                        <div class="p-2 border border-dashed rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm me-2">
                                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                                        <i class="ri-apps-2-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-muted mb-1">Category</p>
                                                                    <h5 class="mb-0"><?php echo $reward['CATEGORY'] ?></h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- end col -->
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-lg-4 col-sm-6">
                                                        <div class="p-2 border border-dashed rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm me-2">
                                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                                        <i class="ri-stack-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-muted mb-1">Available Stock</p>
                                                                    <h5 class="mb-0"><?php echo $reward['STOCK'] ?></h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- end col -->
                                                </div>
                                                <div>
                                                    <form action="" method="POST">
                                                        <div class="row mt-2">
                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="p-2 border border-dashed rounded">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-sm me-2">
                                                                            <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                                                <i class="ri-numbers-fill"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <p class="text-muted mb-1">Quantity</p>
                                                                            <select type="radio" class="form-control" name="quantity" id="quantityInput">
                                                                                <option value="0" selected>0</option>
                                                                                <?php for ($id = 1; $id <= min($reward['STOCK'], $_SESSION['reward_point']/$reward['PRICE']); $id++) {?>
                                                                                <option value="<?php echo $id;?>" ><?php echo $id;?></option>
                                                                                <?php }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-4 col-sm-6">
                                                                <button class="btn btn-dark w-100 fs-20 fw-medium" id="sa-ordernow"><i class="mdi mdi-hand-coin"></i> Order Now!</button>
                                                                <button type="submit" name="order" class="btn btn-dark d-none" id="sa-ordernowconfirmed">Yes, Update!</button>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                    </form>
                                                </div>
                                            
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <!-- footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">MIST Computer Club &copy; Crafted with great care by TEAM NEXT PERMUTATION
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- swiper js -->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
    <!-- profile init js -->
    <script src="assets/js/pages/profile.init.js"></script>
    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <!-- Sweet alert init js-->
    <script src="assets/js/pages/sweetalerts.init.js"></script>
    <!-- nouisliderribute js -->
    <script src="assets/libs/nouislider/nouislider.min.js"></script>
    <script src="assets/libs/wnumb/wNumb.min.js"></script>
    <!-- gridjs js -->
    <script src="assets/libs/gridjs/gridjs.umd.js"></script>
    <script src="https://unpkg.com/gridjs/plugins/selection/dist/selection.umd.js"></script>
    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
    <!-- ecommerce product details init -->
    <script src="assets/js/pages/ecommerce-product-details.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script type="text/javascript">
        document.getElementById("sa-ordernow") && document.getElementById("sa-ordernow").addEventListener("click", function(event){
        event.returnValue = false;
        Swal.fire({
            title: "Are You Sure?",
            text: "Your order is going to be confirmed.",
            icon: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success w-xs me-2 mt-2",
            cancelButtonClass: "btn btn-danger w-xs mt-2",
            confirmButtonText: "Yes, Order Now!",
            buttonsStyling: !1,
            showCloseButton: !0
            }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                title: "Confirmed!",
                text: "Your order has been confirmed.",
                icon: "success",
                timer: 2500,
                timerProgressBar: !0,
                showCloseButton: !0,
                didOpen: function() {
                    Swal.showLoading(), t = setInterval(function() {
                        var t = Swal.getHtmlContainer();
                        !t || (t = t.querySelector("b")) && (t.textContent = Swal.getTimerLeft())
                    }, 100)
                },
                onClose: function() {
                    clearInterval(t)
                }
            })
            setTimeout( function () { 
                document.getElementById("sa-ordernowconfirmed").click();
            }, 2500);
                                
            }
        })
    });
    </script>
</body>

</html>