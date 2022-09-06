<?php
    session_start();

    if($_SESSION['username'] == NULL) header("Location: sign-in.php");
    include 'db_conn.php';

    $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    if(isset($_GET['un']))
    {
        $view_username = $_GET['un'];
        $sql = "select * from PROFILE where USERNAME='$view_username'";
        $stid = oci_parse($conn, $sql);
        $exc = oci_execute($stid);
        $view_user = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
 
        if($view_user == NULL) header("Location: user-profile.php?un={$_SESSION['username']}");
        else
        {
            $sql = "select * from MEMBER where USERNAME='$view_username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $member = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
    
            $sql = "select * from ALUMNI where USERNAME='$view_username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $alumni = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
    
            $sql = "select * from ADMIN_POSITION where USERNAME='$view_username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $admin = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

            if($member != NULL)
            {
                $view_user['type'] = 'Member';
                $view_user['role'] = 'Member';
                if($admin != NULL) $view_user['role'] = $admin['POSITION'];
            
                $view_user['rating'] = $member['RATING'];
                $view_user['reward_point'] = $member['REWARD_POINT'];
                $view_user['rank'] = $member['RANK'];
                $view_user['student_id'] = $member['STUDENT_ID'];
                $view_user['department'] = $member['DEPT'];

                $team_name = $member['TEAM_NAME'];
                $view_user['team_name'] = $team_name;

                if($view_user['team_name'] != NULL)
                {
                    $sql = "select * from PROFILE, MEMBER where PROFILE.USERNAME=MEMBER.USERNAME and team_name='$team_name' ORDER BY RANK";
                    $stid = oci_parse($conn, $sql);
                    oci_execute($stid);
                    oci_fetch_all($stid, $team_members, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    
                    $view_user['team_member_1_name'] = $team_members[0]['NAME'];
                    $view_user['team_member_2_name'] = $team_members[1]['NAME'];
                    $view_user['team_member_3_name'] = $team_members[2]['NAME'];
    
                    $view_user['team_member_1_username'] = $team_members[0]['USERNAME'];
                    $view_user['team_member_2_username'] = $team_members[1]['USERNAME'];
                    $view_user['team_member_3_username'] = $team_members[2]['USERNAME'];
    
                    $view_user['team_member_1_rating'] = $team_members[0]['RATING'];
                    $view_user['team_member_2_rating'] = $team_members[1]['RATING'];
                    $view_user['team_member_3_rating'] = $team_members[2]['RATING'];
    
                    $view_user['team_member_1_rank'] = $team_members[0]['RANK'];
                    $view_user['team_member_2_rank'] = $team_members[1]['RANK'];
                    $view_user['team_member_3_rank'] = $team_members[2]['RANK'];
                }
            }
            elseif($alumni != NULL)
            {
                $view_user['type'] = 'Alumni';
                $view_user['role'] = 'Alumni';
                if($admin != NULL) $view_user['role'] = $admin['POSITION'];
                $view_user['student_id'] = $alumni['STUDENT_ID'];
                $view_user['department'] = $alumni['DEPT'];
                $view_user['batch'] = $alumni['BATCH'];
                $view_user['grad_year'] = $alumni['GRADUATION_YEAR'];

                $sql = "select * from ALUMNI_POSITION where USERNAME='$view_username' ORDER BY END_DATE DESC, START_DATE DESC";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                $no_of_pos=oci_fetch_all($stid, $alumni_pos, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                $view_user['no_of_pos'] = $no_of_pos;
                for ($id = 0; $id < $no_of_pos; $id++)
                {
                    $view_user['position'][$id] = $alumni_pos[$id]['POSITION'];
                    $view_user['committee'][$id] = $alumni_pos[$id]['COMMITTEE'];
                    $view_user['start_date'][$id] = $alumni_pos[$id]['START_DATE'];
                    $view_user['end_date'][$id] = $alumni_pos[$id]['END_DATE'];                   
                }

                $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'Mon yyyy'";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);

                $sql = "select * from PROFESSION where USERNAME='$view_username' ORDER BY END_DATE DESC, START_DATE DESC";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                $no_of_exp=oci_fetch_all($stid, $alumni_exp, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                $view_user['no_of_exp'] = $no_of_exp;
                for ($id = 0; $id < $no_of_exp; $id++)
                {
                    $view_user['designation'][$id] = $alumni_exp[$id]['DESIGNATION'];
                    $view_user['organization'][$id] = $alumni_exp[$id]['ORGANIZATION'];
                    $view_user['exp_start_date'][$id] = $alumni_exp[$id]['START_DATE'];
                    $view_user['exp_end_date'][$id] = $alumni_exp[$id]['END_DATE'];
                    if ($view_user['exp_end_date'][$id] == NULL) $view_user['exp_end_date'][$id] = 'Present';                
                }

                $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
            }
            elseif($admin != NULL) 
            {
                $view_user['type'] = 'External';
                $view_user['role'] = $admin['POSITION'];

                $sql = "select * from ADMIN where USERNAME='$view_username'";
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                $admin = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

                $view_user['institute'] = $admin['INSTITUTE'];              
            }
        }    
    }
    else header("Location: sign-in.php");

    if(isset($_POST['update']))
    {
        $objExe = true;
        $objMember = true;
        $objAlumni = true;
        $objExteral = true;

        $name = $_POST['name'];
        $email = $_POST['email'];
        $date_of_birth = $_POST['date_of_birth'];
        $contact_no = $_POST['contact_no'];
        $gender = $_POST['gender']; 
        $tshirt_size = $_POST['tshirt_size'];
        $house = $_POST['house'];        
        $street = $_POST['street'];
        $city = $_POST['city'];            
        $username = $view_user['USERNAME'];

        $sql = "UPDATE PROFILE set  
            NAME = '$name',
            EMAIL = '$email',
            DATE_OF_BIRTH = TO_DATE('$date_of_birth','YYYY-MM-DD'),
            CONTACT_NO = '$contact_no',
            GENDER = '$gender', 
            TSHIRT_SIZE = '$tshirt_size',
            HOUSE = '$house',
            STREET = '$street',
            CITY = '$city'
            WHERE USERNAME = '$username'";

        $stid = oci_parse($conn, $sql);
        $objExe = oci_execute($stid);

        if($view_user['type'] == 'Member')
        {
            $student_id = $_POST['student_id'];
            $department = $_POST['department'];

            $sql = "UPDATE MEMBER set
                STUDENT_ID = '$student_id',
                DEPT = '$department'
                WHERE USERNAME = '$username'";
        
            $stid = oci_parse($conn, $sql);
            $objMember = oci_execute($stid);
        }
        elseif($view_user['type'] == 'Alumni')
        {
            $student_id = $_POST['student_id'];
            $department = $_POST['department'];
            $batch = $_POST['batch'];
            $grad_year = $_POST['grad_year'];

            $sql = "UPDATE ALUMNI set
                STUDENT_ID = '$student_id',
                DEPT = '$department',
                BATCH = '$batch',
                GRADUATION_YEAR = '$grad_year'
                WHERE USERNAME = '$username'";

            $stid = oci_parse($conn, $sql);
            $objAlumni = oci_execute($stid);
        }
        
        if($view_user['type'] == 'External')
        {
            $institute = $_POST['institute'];

            $sql = "UPDATE ADMIN set
                INSTITUTE = '$institute'
                WHERE USERNAME = '$username'";

            $stid = oci_parse($conn, $sql);
            $objExteral = oci_execute($stid);
        }

        if($objExe && $objMember && $objAlumni && $objExteral)
        {
            oci_commit($conn);
            header("Location: user-profile.php?un={$view_user['USERNAME']}");
        }

        else
        {
            oci_rollback($conn); //rollback transaction
            $e = oci_error($stid);  
            echo "Error Save [".$e['message']."]";  
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
                <div class="container-sm">
                    <div class="pb-lg-4">
                        <div class="row g-4">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    <img src="assets/images/users/<?php echo $view_user['USERNAME'];?>.jpg" alt="user-img" class="img-thumbnail rounded-circle" />
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-auto">
                                <div class="p-2">
                                    <h3 class="mb-2"><a href="user-profile.php?un=<?php echo $view_user['USERNAME'];?>"><?php echo $view_user['NAME'];?></a></h3>
                                    <h5><?php echo $view_user['role'];?></h5>
                                    <h4><i class="ri-account-circle-line me-1 fs-16 align-middle"></i><a href="user-profile.php?un=<?php echo $view_user['USERNAME'];?>"><?php echo $view_user['USERNAME'];?></a></h4>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-12 col-lg">
                                <div class="row text text-center mt-3 d-flex justify-content-end">
                                <?php if ($view_user['type'] == 'Member') {?>
                                    <div class="col-lg-auto">
                                        <div class="p-2">
                                            <h4 class="mb-1"><?php echo $view_user['rating'];?></h4>
                                            <p class="fs-14 mb-0">MCC Rating</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="p-2">
                                            <h4 class="mb-1"><?php echo $view_user['rank'];?></h4>
                                            <p class="fs-14 mb-0">Combined Position</p>
                                        </div>
                                    </div>
                                    <?php if ($view_user['USERNAME'] == $_SESSION['username']) {?>
                                    <div class="col-lg-auto">
                                        <div class="p-2">
                                            <h4 class="mb-1"><?php echo $view_user['reward_point'];?></h4>
                                            <p class="fs-14 mb-0">Reward Points</p>
                                        </div>
                                    </div>
                                    <?php }?>
                                <?php }?>
                                <?php if ($view_user['type'] == 'Alumni') {?>
                                    <?php
                                    for ($id = 0; $id < $view_user['no_of_pos']; $id++)
                                    {
                                    ?>
                                        <div class="col-lg-4">
                                            <div class="p-2">
                                                <p class="fs-14 mb-0"><?php echo $view_user['start_date'][$id];?> to <?php echo $view_user['end_date'][$id];?></p>
                                                <p class="fs-14 mb-0"><?php echo $view_user['position'][$id];?></p>
                                                <p class="fs-14 mb-0"><?php echo $view_user['committee'][$id];?></p>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                <?php }?>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <div class="d-flex">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#personal-info" role="tab">
                                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Personal Info</span>
                                            </a>
                                        </li>
                                        <?php if ($view_user['type'] == 'Member') {?>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#team-info" role="tab">
                                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Team Info</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#contest-history" role="tab">
                                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Contests</span>
                                            </a>
                                        </li>
                                        <?php }?>
                                        <?php if ($view_user['type'] == 'Alumni') {?>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#experience" role="tab">
                                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Experience</span>
                                            </a>
                                        </li>
                                        <?php }?>
                                        <?php if($_SESSION['role'] == 'Moderator' || $_SESSION['role'] == 'President' || $_SESSION['role'] == 'Secretary') {?>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#edit-details" role="tab">
                                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Edit Details</span>
                                            </a>
                                        </li>
                                        <?php }?>
                                    </ul>
                                    <?php if ($view_user['USERNAME'] == $_SESSION['username']) {?>
                                    <div class="flex-shrink-0">
                                        <a href="edit-profile.php" class="btn btn-success"><i class="mdi mdi-account-edit-outline align-bottom"></i> Edit Profile</a>
                                    </div>
                                    <?php }?>
                                </div>

                                <!-- Tab panes -->
                                <div class="tab-content pt-4 text-muted">
                                    <div class="tab-pane active" id="personal-info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-xxl-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Username</th>
                                                                        <td class="text-muted"><?php echo $view_user['USERNAME'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Full Name</th>
                                                                        <td class="text-muted"><?php echo $view_user['NAME'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Email</th>
                                                                        <td class="text-muted"><?php echo $view_user['EMAIL'];?></td>
                                                                    </tr>
                                                                    <?php if ($view_user['type'] != 'External') {?>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Student ID</th>
                                                                        <td class="text-muted"><?php echo $view_user['student_id'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Department</th>
                                                                        <td class="text-muted"><?php echo $view_user['department'];?></td>
                                                                    </tr>
                                                                    <?php }?>
                                                                    <?php if ($view_user['type'] == 'Alumni') {?>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Batch</th>
                                                                        <td class="text-muted"><?php echo $view_user['batch'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Graduation Year</th>
                                                                        <td class="text-muted"><?php echo $view_user['grad_year'];?></td>
                                                                    </tr>
                                                                    <?php }?>
                                                                    <?php if ($view_user['USERNAME'] == $_SESSION['username']) {?>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Date of Birth</th>
                                                                        <td class="text-muted"><?php echo $view_user['DATE_OF_BIRTH'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Contact No</th>
                                                                        <td class="text-muted"><?php echo $view_user['CONTACT_NO'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Gender</th>
                                                                        <td class="text-muted"><?php echo $view_user['GENDER'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Tshirt Size</th>
                                                                        <td class="text-muted"><?php echo $view_user['TSHIRT_SIZE'];?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Address</th>
                                                                        <td class="text-muted"><?php echo $view_user['ADDRESS'];?></td>
                                                                    </tr>
                                                                    <?php }?>
                                                                    <?php if ($view_user['type'] == 'External') {?>
                                                                    <tr>
                                                                        <th class="ps-0" scope="row">Institute</th>
                                                                        <td class="text-muted"><?php echo $view_user['institute'];?></td>
                                                                    </tr>
                                                                    <?php }?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- end card body -->
                                                </div><!-- end card -->
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </div>
                                    <!--end tab-pane-->

                                    <div class="tab-pane fade" id="team-info" role="tabpanel">
                                        <div class="card">
                                            <div class="card-body">
                                            <?php if($view_user['team_name'] != NULL) {?>
                                                <h5 class="card-title mt-2 mb-3"><?php echo $view_user['team_name'];?></h5>
                                                <div class="row">
                                                    <div class="col-xxl-3 col-sm-4">
                                                        <div class="card profile-project-card shadow-none profile-project-warning">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="?un=<?php echo $view_user['team_member_1_username'];?>" class="text-dark"><?php echo $view_user['team_member_1_name'];?></a></h5>
                                                                        <p class="text-muted text-truncate mb-0">MCC Rating: <span class="fw-semibold text-dark"><?php echo $view_user['team_member_1_rating'];?></span></p>
                                                                        <p class="text-muted text-truncate mb-0">Combined Position: <span class="fw-semibold text-dark"><?php echo $view_user['team_member_1_rank'];?></span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="avatar-sm">
                                                                            <img class="rounded-circle header-profile-user" src="assets/images/users/<?php echo $view_user['team_member_1_username'];?>.jpg">
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-xxl-3 col-sm-4">
                                                        <div class="card profile-project-card shadow-none profile-project-success">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="?un=<?php echo $view_user['team_member_2_username'];?>" class="text-dark"><?php echo $view_user['team_member_2_name'];?></a></h5>
                                                                        <p class="text-muted text-truncate mb-0">MCC Rating: <span class="fw-semibold text-dark"><?php echo $view_user['team_member_2_rating'];?></span></p>
                                                                        <p class="text-muted text-truncate mb-0">Combined Position: <span class="fw-semibold text-dark"><?php echo $view_user['team_member_2_rank'];?></span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="avatar-sm">
                                                                            <img class="rounded-circle header-profile-user" src="assets/images/users/<?php echo $view_user['team_member_2_username'];?>.jpg">
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-xxl-3 col-sm-4">
                                                        <div class="card profile-project-card shadow-none profile-project-info">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="?un=<?php echo $view_user['team_member_3_username'];?>" class="text-dark"><?php echo $view_user['team_member_3_name'];?></a></h5>
                                                                        <p class="text-muted text-truncate mb-0">MCC Rating: <span class="fw-semibold text-dark"><?php echo $view_user['team_member_3_rating'];?></span></p>
                                                                        <p class="text-muted text-truncate mb-0">Combined Position: <span class="fw-semibold text-dark"><?php echo $view_user['team_member_3_rank'];?></span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="avatar-sm">
                                                                            <img class="rounded-circle header-profile-user" src="assets/images/users/<?php echo $view_user['team_member_3_username'];?>.jpg">
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                                <?php if ($view_user['USERNAME'] == $_SESSION['username']) {?>
                                                <div class="d-flex justify-content-end">
                                                    <div class="p-1">
                                                        <a href="edit-profile.html" class="btn btn-success"><i class="mdi mdi-account-group-outline align-bottom"></i> Rename Team</a>
                                                    </div>
                                                    <div class="p-1">
                                                            <button type="button" class="btn btn-danger" id="custom-sa-warning"><i class="mdi mdi-trash-can-outline align-bottom"></i> Delete Team</button>
                                                    </div>
                                                </div>
                                                <?php }?>
                                            <?php } else {?>
                                                <h5 class="card-title mt-2 mb-3">Not attached to any team right now!</h5>
                                                <?php if ($view_user['USERNAME'] == $_SESSION['username']) {?>
                                                <div class="d-flex justify-content-end">
                                                    <div class="p-1">
                                                        <a href="edit-profile.html" class="btn btn-success"><i class="mdi mdi-account-group-outline align-bottom"></i> Form A Team</a>
                                                    </div>
                                                </div>
                                                <?php }?>
                                            <?php }?>
                                            </div>
                                            <!--end card-body-->
                                        </div>
                                        <!--end card-->
                                    </div>
                                    <!--end tab-pane-->

                                    <div class="tab-pane fade" id="contest-history" role="tabpanel">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">Contests</h5>
                                                <div class="acitivity-timeline">
                                                    <div class="acitivity-item d-flex">
                                                        <div class="flex-shrink-0 avatar-sm acitivity-avatar">
                                                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                                <i class="ri-user-star-line fs-17"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="mb-1">Talent Hunt Programming Contest 2022</h5>
                                                            <h5 class="text-muted">5th</h5>
                                                            <h6 class="mb-4 text-muted">22 Jun 2022</h6>
                                                        </div>
                                                    </div>
                                                    <div class="acitivity-item d-flex">
                                                        <div class="flex-shrink-0 avatar-sm acitivity-avatar">
                                                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                                <i class="ri-team-line fs-17"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="mb-1">AUST IUPC Selection Contest 3</h5>
                                                            <h5 class="text-muted">1st</h5>
                                                            <h6 class="mb-4 text-muted">13 Jun 2022</h6>
                                                        </div>
                                                    </div>
                                                    <div class="acitivity-item d-flex">
                                                        <div class="flex-shrink-0 avatar-sm acitivity-avatar">
                                                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                                <i class="ri-team-line fs-17"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="mb-1">AUST IUPC Selection Contest 2</h5>
                                                            <h5 class="text-muted">3rd</h5>
                                                            <h6 class="mb-4 text-muted">04 Jun 2022</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end card-body-->
                                        </div>
                                        <!--end card-->
                                    </div>
                                    <!--end tab-pane-->

                                    <div class="tab-pane fade" id="experience" role="tabpanel">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="acitivity-timeline mt-3">
                                                <?php
                                                for ($id = 0; $id < $view_user['no_of_exp']; $id++)
                                                {
                                                ?>
                                                    <div class="acitivity-item d-flex">
                                                        <div class="flex-shrink-0 avatar-sm acitivity-avatar">
                                                            <div class="flex-shrink-0">
                                                                <img src="assets/images/organizations/<?php echo $view_user['organization'][$id];?>.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar" />
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="mb-1"><?php echo $view_user['organization'][$id];?></h5>
                                                            <h5 class="text-muted"><?php echo $view_user['designation'][$id];?></h5>
                                                            <h6 class="mb-4 text-muted"><?php echo $view_user['exp_start_date'][$id];?> - <?php echo $view_user['exp_end_date'][$id];?></h6>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end card-body-->
                                        </div>
                                        <!--end card-->
                                    </div>
                                    <!--end tab-pane-->

                                    <div class="tab-pane fade" id="projects" role="tabpanel">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-xxl-3 col-sm-6">
                                                        <div class="card profile-project-card shadow-none profile-project-warning">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="#" class="text-dark">Chat App Update</a></h5>
                                                                        <p class="text-muted text-truncate mb-0">Last Update : <span class="fw-semibold text-dark">2 year Ago</span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="badge badge-soft-warning fs-10">Inprogress</div>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex mt-4">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div>
                                                                                <h5 class="fs-12 text-muted mb-0">Members :</h5>
                                                                            </div>
                                                                            <div class="avatar-group">
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-1.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-3.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                                                                            J
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-xxl-3 col-sm-6">
                                                        <div class="card profile-project-card shadow-none profile-project-success">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="#" class="text-dark">ABC Project Customization</a></h5>
                                                                        <p class="text-muted text-truncate mb-0">Last Update : <span class="fw-semibold text-dark">2 month Ago</span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="badge badge-soft-primary fs-10"> Progress</div>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex mt-4">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div>
                                                                                <h5 class="fs-12 text-muted mb-0">Members :</h5>
                                                                            </div>
                                                                            <div class="avatar-group">
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-8.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-7.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-6.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <div class="avatar-title rounded-circle bg-primary">
                                                                                            2+
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-xxl-3 col-sm-6">
                                                        <div class="card profile-project-card shadow-none profile-project-info">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="#" class="text-dark">Client - Frank Hook</a></h5>
                                                                        <p class="text-muted text-truncate mb-0">Last Update : <span class="fw-semibold text-dark">1 hr Ago</span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="badge badge-soft-info fs-10">New</div>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex mt-4">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div>
                                                                                <h5 class="fs-12 text-muted mb-0"> Members :</h5>
                                                                            </div>
                                                                            <div class="avatar-group">
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-4.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                                                                            M
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-3.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-xxl-3 col-sm-6">
                                                        <div class="card profile-project-card shadow-none profile-project-primary">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate"><a href="#" class="text-dark">Velzon Project</a></h5>
                                                                        <p class="text-muted text-truncate mb-0">Last Update : <span class="fw-semibold text-dark">11 hr Ago</span></p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="badge badge-soft-success fs-10">Completed</div>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex mt-4">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div>
                                                                                <h5 class="fs-12 text-muted mb-0">Members :</h5>
                                                                            </div>
                                                                            <div class="avatar-group">
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-7.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="avatar-group-item">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="assets/images/users/avatar-5.jpg" alt="" class="rounded-circle img-fluid" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end card-body-->
                                        </div>
                                        <!--end card-->
                                    </div>
                                    <!--end tab-pane-->

                                    <div class="tab-pane fade" id="edit-details" role="tabpanel">
                                        <div class="row">
                                            <div class="col-xxl-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <form action="" method="POST">
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="nameInput" class="form-label">Name</label>
                                                                        <input type="text" class="form-control" name="name" id="nameInput" placeholder="Enter your name" value="<?php echo $view_user['NAME'];?>">
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="emailInput" class="form-label">Email</label>
                                                                        <input type="email" class="form-control" name="email" id="emailInput" placeholder="Enter your email" value="<?php echo $view_user['EMAIL'];?>">
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="dateInput" class="form-label">Date of Birth</label>
                                                                        <input type="date" class="form-control" name="date_of_birth" id="dateInput" placeholder="Enter your dob" value="<?php echo date('Y-m-d', strtotime($view_user['DATE_OF_BIRTH'])); ?>">
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="phonenumberInput" class="form-label">Contact No.</label>
                                                                        <input type="text" class="form-control" name="contact_no" id="phonenumberInput" placeholder="Enter your contact no."  value="<?php echo $view_user['CONTACT_NO'];?>">
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="genderInput" class="form-label">Gender</label>
                                                                        <select type="radio" class="form-control" name="gender" id="genderInput">
                                                                            <option value="Female" <?php if ($view_user['GENDER'] == 'Female') echo 'selected';?>>Female</option>
                                                                            <option value="Male" <?php if ($view_user['GENDER'] == 'Male') echo 'selected';?>>Male</option>
                                                                            <option value="Other" <?php if ($view_user['GENDER'] == 'Other') echo 'selected';?>>Other</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="tshirtInput" class="form-label">T-shirt Size</label>
                                                                        <select type="radio" class="form-control" name="tshirt_size" id="tshirtInput">
                                                                            <option value="M" <?php if ($view_user['TSHIRT_SIZE'] == 'M') echo 'selected';?>>M</option>
                                                                            <option value="L" <?php if ($view_user['TSHIRT_SIZE'] == 'L') echo 'selected';?>>L</option>
                                                                            <option value="XL" <?php if ($view_user['TSHIRT_SIZE'] == 'XL') echo 'selected';?>>XL</option>
                                                                            <option value="XXL" <?php if ($view_user['TSHIRT_SIZE'] == 'XXL') echo 'selected';?>>XXL</option>
                                                                            <option value="XXXL" <?php if ($view_user['TSHIRT_SIZE'] == 'XXXL') echo 'selected';?>>XXXL</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="houseInput" class="form-label">House</label>
                                                                        <input type="text" class="form-control" name="house" minlength="1" maxlength="6" id="houseInput" placeholder="Enter house no." value=<?php echo $view_user['HOUSE'];?>>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="streetInput" class="form-label">Street</label>
                                                                        <input type="text" class="form-control" name="street" id="streetInput" placeholder="Street" value=<?php echo $view_user['STREET'];?>>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="cityInput" class="form-label">City</label>
                                                                        <input type="text" class="form-control" name="city" id="cityInput" placeholder="City" value=<?php echo $view_user['CITY'];?>>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->

                                                                <?php if($view_user['type'] == 'Member' || $view_user['type'] == 'Alumni') {?>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="idInput" class="form-label">Student ID</label>
                                                                        <input type="text" class="form-control" name="id" id="idInput" placeholder="City" value=<?php echo $view_user['student_id'];?>>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="departmentInput" class="form-label">Department</label>
                                                                        <select type="radio" class="form-control" name="department" id="departmentInput">
                                                                            <option value="AE" <?php if ($view_user['department'] == 'AE') echo 'selected';?>>AE</option>
                                                                            <option value="BME" <?php if ($view_user['department'] == 'BME') echo 'selected';?>>BME</option>
                                                                            <option value="CE" <?php if ($view_user['department'] == 'CE') echo 'selected';?>>CE</option>
                                                                            <option value="CSE" <?php if ($view_user['department'] == 'CSE') echo 'selected';?>>CSE</option>
                                                                            <option value="EECE" <?php if ($view_user['department'] == 'EECE') echo 'selected';?>>EECE</option>
                                                                            <option value="EWCE" <?php if ($view_user['department'] == 'EWCE') echo 'selected';?>>EWCE</option>
                                                                            <option value="IPE" <?php if ($view_user['department'] == 'IPE') echo 'selected';?>>IPE</option>
                                                                            <option value="ME" <?php if ($view_user['department'] == 'ME') echo 'selected';?>>ME</option>
                                                                            <option value="NAME" <?php if ($view_user['department'] == 'NAME') echo 'selected';?>>NAME</option>
                                                                            <option value="NSE" <?php if ($view_user['department'] == 'NSE') echo 'selected';?>>NSE</option>
                                                                            <option value="PME" <?php if ($view_user['department'] == 'PME') echo 'selected';?>>PME</option>
                                                                            <option value="Arch." <?php if ($view_user['department'] == 'Arch.') echo 'selected';?>>Arch.</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <?php }?>

                                                                <?php if($view_user['type'] == 'Alumni') {?>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="batchInput" class="form-label">Batch</label>
                                                                        <input type="text" class="form-control" name="batch" id="batchInput" placeholder="Batch" value=<?php echo $view_user['batch'];?>>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label for="gradYearInput" class="form-label">Graduation Year</label>
                                                                        <input type="date" class="form-control" name="grad_year" id="gradYearInput" placeholder="Grad_year" value="<?php echo date('Y-m-d', strtotime($view_user['grad_year'])); ?>">
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <?php }?>

                                                                <?php if($view_user['type'] == 'External') {?>
                                                                <div class="col-lg-4">
                                                                <div class="mb-3">
                                                                        <label for="instituteInput" class="form-label">Institute</label>
                                                                        <input type="text" class="form-control" name="institute" id="instituteInput" placeholder="Institute" value="<?php echo $view_user['institute']; ?>">
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                                <?php }?>

                                                                <div class="col-lg-12">
                                                                    <div class="hstack gap-2 justify-content-center">
                                                                        <button class="btn btn-dark" id="sa-updateinfo"><i class="mdi mdi-update align-bottom"></i> Update Info</button>
                                                                        <button type="submit" name="update" class="btn btn-dark d-none" id="sa-updateinfoconfirmed">Yes, Update!</button>
                                                                    </div>
                                                                </div>
                                                                <!--end col-->
                                                            </div>
                                                            <!--end row-->
                                                        </form>
                                                    </div><!-- end card body -->
                                                </div><!-- end card -->
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </div>
                                    <!--end tab-pane-->
                                </div>
                                <!--end tab-content-->
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div><!-- container-fluid -->
            </div><!-- End Page-content -->

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
    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script type="text/javascript">
        
        document.getElementById("sa-updateinfo") && document.getElementById("sa-updateinfo").addEventListener("click", function(event){
        event.returnValue = false;
        Swal.fire({
            title: "Are You Sure?",
            text: "User info is going to be updated.",
            icon: "warning",
            showCancelButton: !0,
            confirmButtonClass: "btn btn-success w-xs me-2 mt-2",
            cancelButtonClass: "btn btn-danger w-xs mt-2",
            confirmButtonText: "Yes, Update!",
            buttonsStyling: !1,
            showCloseButton: !0
            }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                title: "Updated!",
                text: "User info has been updated.",
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
                document.getElementById("sa-updateinfoconfirmed").click();
            }, 2500);
                                
            }
        })
    })
    </script>
</body>

</html>