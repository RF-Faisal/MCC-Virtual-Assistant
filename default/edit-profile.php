<?php
    session_start();

    include 'db_conn.php';
    
    if(isset($_POST['update']))
    {
        $email = $_POST['email'];
        $contact_no = $_POST['contact_no']; 
        $tshirt_size = $_POST['tshirt_size'];
        $house = $_POST['house'];
        $street = $_POST['street'];
        $city = $_POST['city'];            
        $username = $_SESSION['username'];

        $sql = "UPDATE PROFILE set  
            EMAIL = '$email', 
            CONTACT_NO = '$contact_no', 
            TSHIRT_SIZE = '$tshirt_size',
            HOUSE = '$house',
            STREET = '$street',
            CITY = '$city'
            WHERE USERNAME = '$username'";
        
        $stid = oci_parse($conn, $sql);
        $objExe = oci_execute($stid);

        if($objExe)
        {
            oci_commit($conn);

            $sql = "select * from PROFILE where USERNAME='$username'";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            $userr = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

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
        }

        else
        {
            oci_rollback($conn); //rollback transaction
            $e = oci_error($stid);  
            echo "Error Save [".$e['message']."]";  
        }
    }
    
    if(isset($_POST['change_password']))
    {
        $username = $_SESSION['username'];
        $cur_pass_typed = $_POST['password_old'];
        $new_pass_typed = $_POST['new_password_1'];
        $new_pass_retyped = $_POST['new_password_2'];

        if($new_pass_retyped != $new_pass_typed)
        {
            echo "Confirm Password Do Not Match";    
        }
        else
        {
            $sql = "UPDATE PROFILE set 
            PASSWORD = '$new_pass_typed' 
            WHERE USERNAME = '$username' and PASSWORD = '$cur_pass_typed'";

            $stid = oci_parse($conn, $sql);
            $objExe = oci_execute($stid);
            
            if($objExe) oci_commit($conn);
            else
            {
                echo "Incorrect old pass";
                ?>
                <script>
                    alert("Password Change failed");
                </script>
                <?php
            }
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
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xxl-3">
                            <div class="pt-2 pb-5">
                                <div class="text-center">
                                    <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                        <img src="assets/images/users/<?php echo $_SESSION['username'];?>.jpg" class="rounded-circle avatar-xl user-profile-image" alt="user-profile-image">
                                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                            <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                <span class="avatar-title rounded-circle bg-light text-body">
                                                    <i class="ri-camera-fill"></i>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <h3 class="mb-1"><?php echo $_SESSION['name']; ?></h3>
                                    <h5 class=" mb-0">Member</h5>
                                </div>
                            </div>
                            <!--end card-->
                        </div>
                        <!--end col-->
                        <div class="col-xxl-9">
                            <div class="card mt-xxl-n5">
                                <div class="card-header">
                                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                                <i class="fas fa-home"></i> Personal Info
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                                <i class="far fa-user"></i> Change Password
                                            </a>
                                        </li>
                                        <?php if ($_SESSION['type'] == 'Alumni') {?>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#experience" role="tab">
                                                <i class="far fa-envelope"></i> Experience
                                            </a>
                                        </li>
                                        <?php }?>
                                    </ul>
                                </div>
                                <div class="card-body p-4">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                                            <form action="" method="POST">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="emailInput" class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email" id="emailInput" placeholder="Enter your email" value="<?php echo $_SESSION['email'];?>">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="phonenumberInput" class="form-label">Contact No.</label>
                                                            <input type="text" class="form-control" name="contact_no" minlength="11" maxlength="11" id="phonenumberInput" placeholder="Enter your contact no."  value="<?php echo $_SESSION['contact_no'];?>">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="tshirtInput" class="form-label">T-shirt Size</label>
                                                                <select type="radio" class="form-control" name="tshirt_size" id="tshirtInput">
                                                                    <option value="M" <?php if ($_SESSION['tshirt_size'] == 'M') echo 'selected';?>>M</option>
                                                                    <option value="L" <?php if ($_SESSION['tshirt_size'] == 'L') echo 'selected';?>>L</option>
                                                                    <option value="XL" <?php if ($_SESSION['tshirt_size'] == 'XL') echo 'selected';?>>XL</option>
                                                                    <option value="XXL" <?php if ($_SESSION['tshirt_size'] == 'XXL') echo 'selected';?>>XXL</option>
                                                                    <option value="XXXL" <?php if ($_SESSION['tshirt_size'] == 'XXXL') echo 'selected';?>>XXXL</option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="houseInput" class="form-label">House</label>
                                                            <input type="text" class="form-control" name="house" id="houseInput" placeholder="Enter house no." value=<?php echo $_SESSION['house'];?>>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="streetInput" class="form-label">Street</label>
                                                            <input type="text" class="form-control" name="street" id="streetInput" placeholder="Street" value=<?php echo $_SESSION['street'];?>>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="cityInput" class="form-label">City</label>
                                                            <input type="text" class="form-control" name="city" id="cityInput" placeholder="City" value=<?php echo $_SESSION['city'];?>>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
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
                                        </div>
                                        <!--end tab-pane-->
                                        <div class="tab-pane" id="changePassword" role="tabpanel">
                                            <form action="" method="POST">
                                                <div class="row g-2">
                                                    <div class="col-lg-4">
                                                        <div>
                                                            <label for="oldpasswordInput" class="form-label">Old Password</label>
                                                            <input type="password" class="form-control" name="password_old" id="oldpasswordInput" placeholder="Enter Current Password">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-4">
                                                        <div>
                                                            <label for="newpasswordInput" class="form-label">New Password</label>
                                                            <input type="password" class="form-control" name="new_password_1" id="newpasswordInput" placeholder="Enter New Password">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-4">
                                                        <div>
                                                            <label for="confirmpasswordInput" class="form-label">Confirm Password</label>
                                                            <input type="password" class="form-control" name="new_password_2" id="confirmpasswordInput" placeholder="Confirm New Password">
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-12">
                                                        <div class="text-center mt-2">
                                                            <button class="btn btn-dark" id="sa-updatepass"><i class="mdi mdi-form-textbox-password"></i> Change Password</button>
                                                            <button class="btn btn-dark d-none" id="sa-updatepassconfirmed" type="submit" name="change_password">Yes, Update!</button>
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </form>
                                        </div>
                                        <!--end tab-pane-->
                                        <div class="tab-pane" id="experience" role="tabpanel">
                                            <form>
                                                <div id="newlink">
                                                <?php
                                                for ($id = 0; $id < $_SESSION['no_of_exp']; $id++)
                                                {
                                                ?>
                                                    <div id="<?php echo $id;?>">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="companyName" class="form-label">Organization</label>
                                                                    <input type="text" class="form-control" id="companyName" placeholder="companyName" value="<?php echo $_SESSION['organization'][$id];?>">
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="jobTitle" class="form-label">Job Title</label>
                                                                    <input type="text" class="form-control" id="jobTitle" placeholder="jobTitle" value="<?php echo $_SESSION['designation'][$id];?>">
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="StartdatInput" class="form-label">Start Month</label>
                                                                    <input type="text" class="form-control" data-provider="flatpickr" id="StartdatInput" data-date-format="d M, Y" data-deafult-date="24 Nov, 2021" placeholder="Select date" value="<?php echo $_SESSION['exp_start_date'][$id];?>">
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label for="EnddatInput" class="form-label">End Month</label>
                                                                    <input type="text" class="form-control" data-provider="flatpickr" id="EnddatInput" data-date-format="d M, Y" data-deafult-date="24 Nov, 2021" placeholder="Select date" value="<?php echo $_SESSION['exp_end_date'][$id];?>">
                                                                </div>
                                                            </div>
                                                            <!--end col-->
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <a class="btn btn-success" href="javascript:deleteEl(<?php echo $id;?>)">Delete</a>
                                                            </div>
                                                        </div>
                                                        <!--end row-->
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                                </div>
                                                <div id="newForm" style="display: none;">

                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="hstack gap-2">
                                                        <button type="submit" class="btn btn-success" name="update">Update</button>
                                                        <a href="javascript:new_link()" class="btn btn-primary">Add New</a>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </form>
                                        </div>
                                        <!--end tab-pane-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->

                </div>
                <!-- container-fluid -->
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
    <!-- profile-setting init js -->
    <script src="assets/js/pages/profile-setting.init.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script type="text/javascript">
        document.getElementById("sa-updateinfo") && document.getElementById("sa-updateinfo").addEventListener("click", function(event){
        event.returnValue = false;
        Swal.fire({
            title: "Are You Sure?",
            text: "Your info is going to be updated.",
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
                text: "Your info has been updated.",
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
    }), document.getElementById("sa-updatepass") && document.getElementById("sa-updatepass").addEventListener("click", function(event){
        event.returnValue = false;
        Swal.fire({
            title: "Are You Sure?",
            text: "Your password is going to be updated.",
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
                text: "Your password has been updated.",
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
                document.getElementById("sa-updatepassconfirmed").click();
            }, 2500);
                                
            }
        })
    })
    </script>

</body>

</html>