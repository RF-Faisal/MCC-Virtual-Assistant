<?php
    session_start();
    session_destroy();
?>

</html>
<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-layout-mode="dark">

<head>
    <meta charset="utf-8">
    <title>MCC Virtual Assistant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="MCC Virtual Assistant" name="description">
    <meta content="MIST Computer Club" name="TEAM NEXT PERMUTATION">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.png">

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
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-3 mb-3 text-white-50">
                            <div>
                                <a href="indexx.html" class="d-inline-block auth-logo">
                                    <img src="assets/images/logo-light.png" alt="" height="80">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4 text-center">
                                <lord-icon src="https://cdn.lordicon.com/hzomhqxz.json" trigger="loop" colors="primary:#405189,secondary:#08a88a" style="width:180px;height:180px"></lord-icon>
                                <div class="mt-4 pt-2">
                                    <h5>You are signed out!</h5>
                                    <p class="text-muted">Thank you for using <span class="fw-semibold">MCC Virtual Assistant</span></p>
                                    <div class="mt-4">
                                        <a href="sign-in.php" class="btn btn-success w-100">Sign In</a>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

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
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="assets/js/pages/password-addon.init.js"></script>
</body>

</html>