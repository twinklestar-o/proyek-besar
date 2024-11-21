<!doctype html>
<html lang="id">

<!--begin::Head-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="\assets\images\logo.png">
    {{-- <link rel="stylesheet" href="https://pkm.sman1balige.delcom.org/assets/vendor/source-sans-3-5.0.12/index.css"> --}}
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    {{-- <link rel="stylesheet" href="https://pkm.sman1balige.delcom.org/assets/vendor/overlayscrollbars-2.3.0/styles/overlayscrollbars.min.css"> --}}
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="https://pkm.sman1balige.delcom.org/assets/vendor/adminlte-4.0.0/css/adminlte.min.css">
    <!--end::Required Plugin(AdminLTE)-->
    {{-- <link rel="stylesheet" href="https://pkm.sman1balige.delcom.org/assets/vendor/datatables.net-bs5-2.1.2/css/dataTables.bootstrap5.min.css"> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"><!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)--> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous">
    <!-- OPTIONAL SCRIPTS -->

    <style>
        .note-editable {
            height: 100% !important;
        }
    </style>

</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" >
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->

                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">

                    <!--begin::Fullscreen Toggle-->
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                        </a>
                    </li>
                    <!--end::Fullscreen Toggle-->

                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu">

                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="https://pkm.sman1balige.delcom.org/img/sis/user/photo-1-037d6036-3485-4e1c-8952-838d475f016f.jpg"
                                class="user-image rounded-circle shadow" alt="Photo Profile">
                            <span class="d-none d-md-inline">User Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-primary">
                                <img src="https://pkm.sman1balige.delcom.org/img/sis/user/photo-1-037d6036-3485-4e1c-8952-838d475f016f.jpg"
                                    class="rounded-circle shadow" alt="Photo Profile">
                                <p>
                                    User Admin
                                    <small>Role: Admin</small>
                                </p>
                            </li>
                            <!--end::User Image-->

                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                <a href="https://pkm.sman1balige.delcom.org/sis/app/user/pengaturan"
                                    class="btn btn-default btn-flat">Pengaturan</a>
                                <a href="" class="btn btn-default btn-flat float-end">Keluar</a>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->

        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="https://pkm.sman1balige.delcom.org/sis/app/dashboard" class="brand-link">
                    <!--begin::Brand Image-->
                    <img src="\assets\images\logo.png" alt="Institut Teknologi Del"
                        class="brand-image opacity-75 shadow">
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">Dashboard</span>
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->

            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <a href="#" class="nav-link ">
                                <i class="nav-icon bi bi-house"></i>
                                <p>Home</p>
                            </a>
                        </li>

                        <li class="nav-item  menu-open ">
                            <a href="" class="nav-link">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/sarana-prasarana" class="nav-link ">
                                <i class="nav-icon bi bi-arrow-left-right"></i>
                                <p>Log Keluar/Masuk</p>
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-card-checklist"></i>
                                <p>
                                    Absensi
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href=""
                                        class="nav-link ">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Absensi Kelas</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="" class="nav-link ">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Absensi Kampus</p>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="/kelola-pengguna" class="nav-link ">
                                <i class="nav-icon bi bi-exclamation-triangle"></i>
                                <p>Pelanggaran</p>
                            </a>
                        </li>

                    </ul>
                    <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->

        <!--begin::App Main-->
        <main class="app-main">
            @yield('content')
        </main>
        <!--end::App Main-->

        <!--begin::Footer-->
        <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Build with&nbsp;&nbsp;<i class="bi bi-heart text-danger"></i>
            </div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                Kelompok 5 &copy; 2024
                <a href="#" class="text-decoration-none">Dashboard</a>.
            </strong>
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Script-->

    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/jquery-3.5.1/dist/jquery.min.js"></script>

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
        src="https://pkm.sman1balige.delcom.org/assets/vendor/overlayscrollbars-2.3.0/browser/overlayscrollbars.browser.es6.min.js">
    </script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/core-2.11.8/dist/umd/popper.min.js"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->

    <!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/bootstrap-5.3.2/dist/js/bootstrap.min.js"></script>
    <!--end::Required Plugin(Bootstrap 5)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/adminlte-4.0.0/js/adminlte.min.js"></script>
    <!--end::Required Plugin(AdminLTE)-->

    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/datatables.net-2.1.2/js/dataTables.min.js"></script>
    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/datatables.net-bs5-2.1.2/js/dataTables.bootstrap5.min.js">
    </script>

    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!-- OPTIONAL SCRIPTS -->

    <!--end::Script-->

</body>
<!--end::Body-->

</html>