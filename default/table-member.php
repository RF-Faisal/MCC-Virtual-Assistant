<?php
    session_start();

    if($_SESSION['role'] != 'Moderator' && $_SESSION['role'] != 'President' && $_SESSION['role'] != 'Secretary') header("Location: sign-in.php");
    include 'db_conn.php';

    $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    $columns = array('student_id','profile.username','name','team_name','dept','reward_point','rating','rank');
    $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
    $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

    $sql = "select * from PROFILE, MEMBER where PROFILE.USERNAME=MEMBER.USERNAME ORDER BY $column $sort_order";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    $no_of_members=oci_fetch_all($stid, $members, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    $up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order); 
	$asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';

    if(isset($_POST['search']))
    {
        $username = strtolower($_POST['username']);
        $name = strtolower($_POST['name']);
        $teamname = strtolower($_POST['teamname']);
        $studentid = strtolower($_POST['studentid']);
        $department = strtolower($_POST['department']);
        $rewardpointleft = (int)($_POST['rewardpointleft']);
        $rewardpointright = (int)($_POST['rewardpointright']);
        $ratingleft = (float)($_POST['ratingleft']);
        $ratingright = (float)($_POST['ratingright']);
        $rankleft = (int)($_POST['rankleft']);
        $rankright = (int)($_POST['rankright']);

        $sql = "select * from PROFILE, MEMBER where PROFILE.USERNAME=MEMBER.USERNAME";
        if ($username != '') $sql .= " and LOWER(USERNAME) like '%$username%'";
        if ($name != '') $sql .= " and LOWER(name) like '%$name%'";
        if ($teamname != '')
        {
            $sql .= " and LOWER(TEAM_NAME)";
            $sql .= $teamname == "-1" ? " is null" : " like '%$teamname%'";
        }
        if ($studentid != '') $sql .= " and student_id like '$studentid%'";
        if ($department != '') $sql .= " and LOWER(department) like '$department'";
        if ($rewardpointleft != '') $sql .= " and REWARD_POINT >= $rewardpointleft";
        if ($rewardpointright != '') $sql .= " and REWARD_POINT <= $rewardpointright";
        if ($ratingleft != '') $sql .= " and RATING >= $ratingleft";
        if ($ratingright != '') $sql .= " and RATING <= $ratingright";
        if ($rankleft != '') $sql .= " and RANK >= $rankleft";
        if ($rankright != '') $sql .= " and RANK <= $rankright";
        
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $no_of_members=oci_fetch_all($stid, $members, null, null, OCI_FETCHSTATEMENT_BY_ROW);
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
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h2 class="mb-sm-0">All Members</h2>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->                    
                    <div class="row">
                        <div class="col-xl-12 col-lg-8">
                            <div>
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="row g-0">
                                            <form action="" method="POST">
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="username" id="usernameInput" placeholder="Username Like">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="name" id="nameInput" placeholder="Name Like">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="teamname" id="teamnameInput" placeholder="Team Name Like">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="studentid" id="studentidInput" placeholder="Student ID Starts With">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="department" id="departmentInput" placeholder="Department Equals">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="rewardpointleft" id="rewardpointInput" placeholder="Reward Points Greater Than or Equal">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="rewardpointright" id="rewardpointInput" placeholder="Reward Points Lesser Than or Equal">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="ratingleft" id="ratingInput" placeholder="MCC Rating Greater Than or Equal">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="ratingright" id="ratingInput" placeholder="MCC Rating Lesser Than or Equal">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="rankleft" id="rankInput" placeholder="Rank Greater Than or Equal">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" name="rankright" id="rankInput" placeholder="Rank Lesser Than or Equal">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-3">
                                                        <div class="hstack gap-2 justify-content-center mb-3">
                                                            <button class="btn btn-dark w-100"  type="submit" name="search"><i class="mdi mdi-account-search-outline"></i> Search Members</button>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </form>
                                            <!-- Bordered Tables -->
                                            <table class="table table-hover table-bordered table-nowrap" style="text-align: center;">
                                                <thead class="table-light">
                                                        <th scope="col"><a href="?column=profile.username&order=<?php echo $asc_or_desc; ?>">Username</a></th>
                                                        <th scope="col"><a href="?column=name&order=<?php echo $asc_or_desc; ?>">Name</a></th>
                                                        <th scope="col"><a href="?column=team_name&order=<?php echo $asc_or_desc; ?>">Team Name</a></th>
                                                        <th scope="col"><a href="?column=student_id&order=<?php echo $asc_or_desc; ?>">Student ID</a></th>
                                                        <th scope="col"><a href="?column=dept&order=<?php echo $asc_or_desc; ?>">Department</a></th>
                                                        <th scope="col"><a href="?column=reward_point&order=<?php echo $asc_or_desc; ?>">Reward Points</a></a></th>
                                                        <th scope="col"><a href="?column=rating&order=<?php echo $asc_or_desc; ?>">MCC Rating</a></th>
                                                        <th scope="col"><a href="?column=rank&order=<?php echo $asc_or_desc; ?>">Rank</a></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                for ($id = 0; $id < $no_of_members; $id++)
                                                {
                                                ?>
                                                    <tr>
                                                        <th scope="row"><a href="/user-profile.php?un=<?php echo $members[$id]['USERNAME'];?>"><?php echo $members[$id]['USERNAME'];?></a></th>
                                                        <td><a href="/user-profile.php?un=<?php echo $members[$id]['USERNAME'];?>"><?php echo $members[$id]['NAME'];?></a></td>
                                                        <td><?php echo $members[$id]['TEAM_NAME'];?></span></td>
                                                        <td><a href="/user-profile.php?un=<?php echo $members[$id]['USERNAME'];?>"><?php echo $members[$id]['STUDENT_ID'];?></a></td>
                                                        <td><?php echo $members[$id]['DEPT'];?></td>
                                                        <td><?php echo $members[$id]['REWARD_POINT'];?></td>
                                                        <td><?php echo $members[$id]['RATING'];?></td>
                                                        <td><?php echo $members[$id]['RANK'];?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table> 
                                        </div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
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
    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>
    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>    

</body>
</html>