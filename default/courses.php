<?php
    session_start();

    if($_SESSION['username'] == NULL) header("Location: sign-in.php");
    include 'db_conn.php';

    $sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'dd Mon yyyy'";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    $sql = "select category, count(category) from course group by category";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    $no_of_category=oci_fetch_all($stid, $categories, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    $columns = array('course_id','course_title','price','start_time','duration');
    $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];

    $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';
    $sql = "select * from course ORDER BY $column $sort_order";
    $stid = oci_parse($conn, $sql);
    $exc = oci_execute($stid);
    $no_of_course = oci_fetch_all($stid, $courses, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    $up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order); 
	$asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
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
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h5 class="fs-15">Filters</h5>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <a href="courses.php" class="text-decoration-underline" id="clearall">Clear All</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion accordion-flush filter-accordion">
                                    <div class="card-body border-bottom">
                                        <div>
                                            <p class="text-muted text-uppercase fs-14 fw-bold mb-2">Courses</p>
                                            <ul class="list-unstyled mb-1 filter-list">
                                            <?php
                                            for ($id = 0; $id < $no_of_category; $id++)
                                            {
                                            ?>
                                                <li>
                                                    <a href="#" class="d-flex py-1 align-items-center">
                                                        <div class="flex-grow-1">
                                                            <h5 class="fs-14 mb-1 listname"><?php echo $categories[$id]['CATEGORY'];?></h5>
                                                        </div>
                                                        <div class="flex-grow-0 ms-2">
                                                            <span class="fs-14 fw-semibold listname"><?php echo $categories[$id]['COUNT(CATEGORY)'];?></span>
                                                        </div>
                                                    </a>
                                                </li>
                                            <?php
                                            }
                                            ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="card-body border-bottom">
                                        <p class="text-muted text-uppercase fs-14 fw-bold mb-3">Required Points</p>
                                        <div id="product-price-range"></div>
                                        <div class="formCost d-flex gap-2 align-items-center mt-3 mb-1">
                                            <input class="form-control form-control-md" type="text" id="minCost" value="0"> <span class="fs-14 fw-semibold text-muted">to</span> <input class="form-control form-control-md" type="text" id="maxCost" value="10000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl-9 col-lg-8">
                            <div>
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="row g-4">
                                            <?php
                                            if($_SESSION['role'] == 'Moderator' || $_SESSION['role'] == 'President' || $_SESSION['role'] == 'Secretary')
                                            {
                                            ?>
                                            <div class="col-sm-auto">
                                                <div>
                                                    <a href="add-course.php" class="btn btn-success" id="addproduct-btn"><i class="mdi mdi-book-plus-outline"></i> Add Course</a>
                                                </div>
                                            </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="col-sm" id="product-list">
                                                <div class="d-flex justify-content-sm-end">
                                                    <div class="search-box ms-2">
                                                        <input type="text" class="form-control" id="searchProductList" placeholder="Search Courses">
                                                        <i class="ri-search-line search-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="tab-content text-muted">
                                            <div class="tab-pane active" id="productnav-all" role="tabpanel">
                                                <div id="table-product-list-all" class="fs-14 fw-medium"></div>
                                            </div>
                                            <!-- end tab pane -->
                                        </div>
                                        <!-- end tab content -->
                                    </div>
                                    <!-- end card body -->
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
    
    <script type="text/javascript">
        var productListAllData = [
            <?php
            for ($id = 0; $id < $no_of_course; $id++)
            {
            ?>
                {
                    id: "<?php echo $courses[$id]['COURSE_ID'];?>",
                    img: "assets/images/courses/Thumbnail (<?php echo $id+1;?>).png",
                    title: "<?php echo $courses[$id]['COURSE_TITLE'];?>",
                    category: "<?php echo $courses[$id]['CATEGORY'];?>",
                    overview: "<?php echo $courses[$id]['OVERVIEW'];?>",
                    price: "<?php echo $courses[$id]['PRICE'];?>",
                    start: "<?php echo $courses[$id]['START_TIME'];?>",
                    duration: "<?php echo $courses[$id]['DURATION'];?>"
                },
            <?php
            }
            ?>
        ],
        inputValueJson = sessionStorage.getItem("inputValue");
        inputValueJson && (inputValueJson = JSON.parse(inputValueJson)).forEach(e => {
        productListAllData.unshift(e)
        });
        var editinputValueJson = sessionStorage.getItem("editInputValue");
        editinputValueJson && (editinputValueJson = JSON.parse(editinputValueJson), productListAllData = productListAllData.map(function(e) {
        return e.id == editinputValueJson.id ? editinputValueJson : e
        })), document.getElementById("product-list").addEventListener("click", function() {
        sessionStorage.setItem("editInputValue", "")
        });
        var productListAll = new gridjs.Grid({
            columns: [{
                name: gridjs.html('<a href="?column=course_title&order=<?php echo $asc_or_desc; ?>">Course Title</a>'),
                width: "350px",
                data: function(e) {
                    return gridjs.html('<div class="d-flex align-items-center"><div class="flex-shrink-0 me-3"><div class="avatar-md bg-light rounded p-1"><img src="' + e.img + '" alt="" class="img-fluid d-block"></div></div><div class="flex-grow-1"><a href="apps-ecommerce-product-details.html" class="text-dark">' + e.title + '</a></div></div>')
                }
            }, {
                name: gridjs.html('<a href="?column=price&order=<?php echo $asc_or_desc; ?>">Required Points</a>'),
                width: "130px",
                data: function(e) {
                    return e.price
                }              
            }, {
                name: gridjs.html('<a href="?column=start_time&order=<?php echo $asc_or_desc; ?>">Starts From</a>'),
                width: "120px",
                data: function(e) {
                    return e.start
                }              
            }, {
                name: gridjs.html('<a href="?column=duration&order=<?php echo $asc_or_desc; ?>">Duration</a>'),
                width: "100px",
                data: function(e) {
                    return e.duration + ' days'
                }       
            },  
            //  {
            //     name: gridjs.html('<a href="">Enroll</a>'),
            //     width: "120px",
            //     data: function(e) {
            //        return gridjs.html('<button class="btn btn-success w-45" type="submit" name="submit">Enroll Now!</button>')
                    
            //     }       
            //}
        ],
            pagination: {
                limit: 7
            },
            sort: !1,
            data: productListAllData
        }).render(document.getElementById("table-product-list-all")),

        productListPublishedData = [],
        productListPublished = new gridjs.Grid({data: productListPublishedData}).render(document.getElementById("table-product-list-all")),
        searchProductList = document.getElementById("searchProductList");
        searchProductList.addEventListener("keyup", function() {
        var e = searchProductList.value.toLowerCase();

        function t(e, t) {
            return e.filter(function(e) {
                return (-1 !== e.title.toLowerCase().indexOf(t.toLowerCase())) || (-1 !== e.price.toLowerCase().indexOf(t.toLowerCase())) || (-1 !== e.start.toLowerCase().indexOf(t.toLowerCase())) || (-1 !== e.duration.toLowerCase().indexOf(t.toLowerCase()))
            })
        }
        var i = t(productListAllData, e),
            e = t(productListPublishedData, e);
        productListAll.updateConfig({
            data: i
        }).forceRender(), productListPublished.updateConfig({
            data: e
        }).forceRender(), checkRemoveItem()
        }), document.querySelectorAll(".filter-list a").forEach(function(r) {
        r.addEventListener("click", function() {
            var e = document.querySelector(".filter-list a.active");
            e && e.classList.remove("active"), r.classList.add("active");
            var t = r.querySelector(".listname").innerHTML,
                i = productListAllData.filter(e => e.category === t),
                e = productListPublishedData.filter(e => e.category === t);
            productListAll.updateConfig({
                data: i
            }).forceRender(), productListPublished.updateConfig({
                data: e
            }).forceRender(), checkRemoveItem()
        })
        });

        var slider = document.getElementById("product-price-range");
        noUiSlider.create(slider, {
        start: [0, 1e4],
        step: 100,
        margin: 100,
        connect: !0,
        behaviour: "tap-drag",
        range: {
            min: 0,
            max: 1e4
        },
        format: wNumb({
            decimals: 0
        })
        });

        var minCostInput = document.getElementById("minCost"),
        maxCostInput = document.getElementById("maxCost"),
        filterDataAll = "",
        filterDataPublished = "";
        slider.noUiSlider.on("update", function(e, t) {
        var i = productListAllData,
            r = productListPublishedData;
        t ? maxCostInput.value = e[t] : minCostInput.value = e[t];
        var s = maxCostInput.value,
            a = minCostInput.value;
        filterDataAll = i.filter(e => parseFloat(e.price) >= a && parseFloat(e.price) <= s), filterDataPublished = r.filter(e => parseFloat(e.price) >= a && parseFloat(e.price) <= s), productListAll.updateConfig({
            data: filterDataAll
        }).forceRender(), productListPublished.updateConfig({
            data: filterDataPublished
        }).forceRender(), checkRemoveItem()
        }), minCostInput.addEventListener("change", function() {
        slider.noUiSlider.set([this.value, null])
        }), maxCostInput.addEventListener("change", function() {
        slider.noUiSlider.set([null, this.value])
        });
        
        var filterChoicesInput = new Choices(document.getElementById("filter-choices-input"), {
        addItems: !0,
        delimiter: ",",
        editItems: !0,
        maxItemCount: 10,
        removeItems: !0,
        removeItemButton: !0
        });
        
        document.querySelectorAll(".filter-accordion .accordion-item").forEach(function(r) {
        var s = r.querySelectorAll(".filter-check .form-check .form-check-input:checked").length;
        r.querySelector(".filter-badge").innerHTML = s, r.querySelectorAll(".form-check .form-check-input").forEach(function(t) {
            var i = t.value;
            t.checked && filterChoicesInput.setValue([i]), t.addEventListener("click", function(e) {
                t.checked ? (s++, r.querySelector(".filter-badge").innerHTML = s, r.querySelector(".filter-badge").style.display = 0 < s ? "block" : "none", filterChoicesInput.setValue([i])) : filterChoicesInput.removeActiveItemsByValue(i)
            }), filterChoicesInput.passedElement.element.addEventListener("removeItem", function(e) {
                e.detail.value == i && (t.checked = !1, s--, r.querySelector(".filter-badge").innerHTML = s, r.querySelector(".filter-badge").style.display = 0 < s ? "block" : "none")
            }, !1), document.getElementById("clearall").addEventListener("click", function() {
                t.checked = !1, filterChoicesInput.removeActiveItemsByValue(i), s = 0, r.querySelector(".filter-badge").innerHTML = s, r.querySelector(".filter-badge").style.display = 0 < s ? "block" : "none", productListAll.updateConfig({
                    data: productListAllData
                }).forceRender(), productListPublished.updateConfig({
                    data: productListPublishedData
                }).forceRender()
            })
        })
        });

        function removeItems() {
        document.getElementById("removeItemModal").addEventListener("show.bs.modal", function(e) {
            isSelected = 0, document.getElementById("delete-product").addEventListener("click", function() {
                document.querySelectorAll(".gridjs-table tr").forEach(function(e) {
                    var t, i = "";

                    function r(e, t) {
                        return e.filter(function(e) {
                            return e.id != t
                        })
                    }
                    e.classList.contains("gridjs-tr-selected") && (t = e.querySelector(".form-check-input").value, i = r(productListAllData, t), t = r(productListPublishedData, t), productListAllData = i, productListPublishedData = t, e.remove())
                }), document.getElementById("btn-close").click(), document.getElementById("selection-element") && (document.getElementById("selection-element").style.display = "none"), checkboxes.checked = !1
            })
        })
        }

        function removeSingleItem() {
        var s;
        document.querySelectorAll(".remove-list").forEach(function(r) {
            r.addEventListener("click", function(e) {
                s = r.getAttribute("data-id"), document.getElementById("delete-product").addEventListener("click", function() {
                    function e(e, t) {
                        return e.filter(function(e) {
                            return e.id != t
                        })
                    }
                    var t = e(productListAllData, s),
                        i = e(productListPublishedData, s);
                    productListAllData = t, productListPublishedData = i, r.closest(".gridjs-tr").remove()
                })
            })
        });
        
        var i;
        document.querySelectorAll(".edit-list").forEach(function(t) {
            t.addEventListener("click", function(e) {
                i = t.getAttribute("data-edit-id"), productListAllData = productListAllData.map(function(e) {
                    return e.id == i && sessionStorage.setItem("editInputValue", JSON.stringify(e)), e
                })
            })
        })
        }
    </script>
</body>
</html>