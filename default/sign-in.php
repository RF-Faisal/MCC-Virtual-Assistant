<?php
    $wrong='d-none';
    session_start();
    session_destroy();
    
    $lifetime=86400;
    session_start();
    setcookie(session_name(),session_id(),time()+$lifetime);

    include 'db_conn.php';
    
    if(isset($_POST['submit']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $sql = "select * from PROFILE where USERNAME='$username' and PASSWORD='$password'";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $userr = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

        if($userr == NULL)
        {
            $wrong='d-flex';
        }
        else
        {   
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $userr['NAME'];
            $_SESSION['email'] = $userr['EMAIL'];
            $_SESSION['date_of_birth'] = $userr['DATE_OF_BIRTH'];
            $_SESSION['contact_no'] = $userr['CONTACT_NO'];
            $_SESSION['gender'] = $userr['GENDER'];
            $_SESSION['tshirt_size'] = $userr['TSHIRT_SIZE'];
            $_SESSION['house'] = $userr['HOUSE'];
            $_SESSION['street'] = $userr['STREET'];
            $_SESSION['city'] = $userr['CITY'];
            $_SESSION['address'] = $userr['ADDRESS'];

            $sql = "select * from MEMBER where USERNAME='$username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $member = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
    
            $sql = "select * from ALUMNI where USERNAME='$username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $alumni = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
    
            $sql = "select * from ADMIN_POSITION where USERNAME='$username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $admin = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

            if($member != NULL)
            {
                $_SESSION['type'] = 'Member';
                $_SESSION['role'] = 'Member';
                if($admin != NULL) $_SESSION['role'] = $admin['POSITION'];
            
                $_SESSION['rating'] = $member['RATING'];
                $_SESSION['reward_point'] = $member['REWARD_POINT'];
                $_SESSION['rank'] = $member['RANK'];
                $_SESSION['student_id'] = $member['STUDENT_ID'];
                $_SESSION['department'] = $member['DEPT'];
                $team_name = $member['TEAM_NAME'];
                $_SESSION['team_name'] = $team_name;

                $sql = "select * from PROFILE, MEMBER where PROFILE.USERNAME=MEMBER.USERNAME and team_name='$team_name' ORDER BY RANK";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                oci_fetch_all($stid, $team_members, null, null, OCI_FETCHSTATEMENT_BY_ROW);

                $_SESSION['team_member_1_name'] = $team_members[0]['NAME'];
                $_SESSION['team_member_2_name'] = $team_members[1]['NAME'];
                $_SESSION['team_member_3_name'] = $team_members[2]['NAME'];

                $_SESSION['team_member_1_username'] = $team_members[0]['USERNAME'];
                $_SESSION['team_member_2_username'] = $team_members[1]['USERNAME'];
                $_SESSION['team_member_3_username'] = $team_members[2]['USERNAME'];

                $_SESSION['team_member_1_rating'] = $team_members[0]['RATING'];
                $_SESSION['team_member_2_rating'] = $team_members[1]['RATING'];
                $_SESSION['team_member_3_rating'] = $team_members[2]['RATING'];

                $_SESSION['team_member_1_rank'] = $team_members[0]['RANK'];
                $_SESSION['team_member_2_rank'] = $team_members[1]['RANK'];
                $_SESSION['team_member_3_rank'] = $team_members[2]['RANK'];
            }
            elseif($alumni != NULL)
            {
                $_SESSION['type'] = 'Alumni';
                $_SESSION['role'] = 'Alumni';
                if($admin != NULL) $_SESSION['role'] = $admin['POSITION'];

                $sql = "select * from ALUMNI_POSITION where USERNAME='$username' ORDER BY END_DATE DESC, START_DATE DESC";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                $no_of_pos = oci_fetch_all($stid, $alumni_pos, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                $_SESSION['no_of_pos'] = $no_of_pos;
                for ($id = 0; $id < $no_of_pos; $id++)
                {
                    $_SESSION['position'][$id] = $alumni_pos[$id]['POSITION'];
                    $_SESSION['committee'][$id] = $alumni_pos[$id]['COMMITTEE'];
                    $_SESSION['start_date'][$id] = $alumni_pos[$id]['START_DATE'];
                    $_SESSION['end_date'][$id] = $alumni_pos[$id]['END_DATE'];                   
                }

                $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'Mon yyyy'";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);

                $sql = "select * from PROFESSION where USERNAME='$username' ORDER BY END_DATE DESC, START_DATE DESC";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                $no_of_exp=oci_fetch_all($stid, $alumni_exp, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                $_SESSION['no_of_exp'] = $no_of_exp;
                for ($id = 0; $id < $no_of_exp; $id++)
                {
                    $_SESSION['designation'][$id] = $alumni_exp[$id]['DESIGNATION'];
                    $_SESSION['organization'][$id] = $alumni_exp[$id]['ORGANIZATION'];
                    $_SESSION['exp_start_date'][$id] = $alumni_exp[$id]['START_DATE'];
                    $_SESSION['exp_end_date'][$id] = $alumni_exp[$id]['END_DATE'];
                    if ($_SESSION['exp_end_date'][$id] == NULL) $_SESSION['exp_end_date'][$id] = 'Present';                
                }

                $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
            }
            elseif($admin != NULL) 
            {
                $_SESSION['type'] = 'External';
                $_SESSION['role'] = $admin['POSITION'];                
            }
            
            header("Location: user-profile.php?un={$_SESSION['username']}");
        }        
    }
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

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Welcome!</h5>
                                    <p class="text-muted">Sign in to continue</p>
                                </div>
                                <div class="p-2 mt-4">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username">
                                        </div>

                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="auth-pass-reset-basic.html" class="text-muted">Forgot Password?</a>
                                            </div>
                                            <label class="form-label" for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5" placeholder="Enter Password" name="password" id="password-input">
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                            </div>
                                        </div>
                                        <div class="mt-4 <?php echo $wrong;?> justify-content-center">
                                                Invalid Username or Password
                                        </div>
                                        <div class="mt-4 d-flex justify-content-center">
                                            <button class="btn btn-success w-45" type="submit" name="submit">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-2 text-center">
                            <p class="mb-0">Don't have an account? <a href="auth-signup-basic.html" class="fw-semibold text-primary text-decoration-underline"> Sign Up! </a> </p>
                        </div>

                    </div>
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