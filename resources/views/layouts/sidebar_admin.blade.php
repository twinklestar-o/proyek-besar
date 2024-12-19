<!Doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="https://pkm.sman1balige.delcom.org/assets/vendor/adminlte-4.0.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigi n="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambahkan link TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/dbs9ztji4pla4u37q7np5b1o8rq6yv325he848tz6b2xi1a0/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        // Existing TinyMCE Initialization for Sidebar Admin
        tinymce.init({
            selector: '.tinymce-editor', // Update the selector to match dynamically added editors
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
            alignleft aligncenter alignright alignjustify | \
            bullist numlist outdent indent | removeformat | help'
        });

    </script>
    <style>
        /* Extend Tailwind default configuration */
        @layer utilities {
            .bg-yellow-default {
                @apply bg-yellow-400 text-black;
            }

            .hover\:bg-yellow-hover:hover {
                @apply bg-yellow-500 text-black;
            }
        }
    </style>

    <style>
        /* Sidebar container with default settings */
        .app-sidebar {
            width: 250px;
            transition: transform 0.3s ease-in-out;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1040;
            transform: translateX(0);
            /* Default visible state */
        }

        /* Sidebar hidden/collapsed state */
        .app-sidebar.collapsed {
            transform: translateX(-100%);
        }

        /* Adjust content layout for collapsed sidebar */
        .app-wrapper {
            transition: margin-left 0.3s ease-in-out;
            margin-left: 250px;
            /* Default sidebar width */
        }

        .app-wrapper.sidebar-collapsed {
            margin-left: 0;
            /* Adjust margin when sidebar is hidden */
        }
    </style>


</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body sticky top-0 z-50 shadow">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                            style="gap: 10px;">
                            <img src="https://pkm.sman1balige.delcom.org/img/sis/user/photo-1-037d6036-3485-4e1c-8952-838d475f016f.jpg"
                                class="user-image rounded-circle shadow" alt="Photo Profile"
                                style="width: 40px; height: 40px; object-fit: cover;">
                            <span class="d-none d-md-inline" style="text-align: center;">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary"
                                style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                                <img src="https://pkm.sman1balige.delcom.org/img/sis/user/photo-1-037d6036-3485-4e1c-8952-838d475f016f.jpg"
                                    class="rounded-circle shadow" alt="Photo Profile" style="margin-bottom: 10px;">
                                <p>
                                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                    <small>Role: Admin</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <a href="https://pkm.sman1balige.delcom.org/sis/app/user/pengaturan"
                                    class="btn btn-flat" style="color: grey;">
                                    <i class="bi bi-gear"></i> Pengaturan
                                </a>
                                <a href="{{ route('logout') }}" class="btn btn-flat float-end" style="color: red;"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar bg-body-secondary shadow sticky top-0 h-screen overflow-y-auto" data-bs-theme="dark">
            <div class="sidebar-brand">
                <div class="brand-link">
                    <img src="/Logo.png" alt="Institut Teknologi Del" class="brand-image opacity-75 shadow">
                    <span class="brand-text fw-light">Dashboard Admin</span>
                </div>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ url('/admin/home') }}"
                                class="nav-link {{ Request::is('admin/home') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-house"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/log') }}"
                                class="nav-link {{ Request::is('admin/log') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-arrow-left-right"></i>
                                <p>Log Keluar/Masuk</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/absensi-kelas') }}"
                                class="nav-link {{ Request::is('admin/absensi-kelas') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-journal-check"></i>
                                <p>Absensi Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/absensi-asrama') }}"
                                class="nav-link {{ Request::is('admin/absensi-asrama') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-building"></i>
                                <p>Absensi Asrama</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/pelanggaran') }}"
                                class="nav-link {{ Request::is('admin/pelanggaran') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-exclamation-triangle"></i>
                                <p>Pelanggaran</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main">
            <!-- Floating Edit Button -->
            <div id="floatingEditButton" class="fixed top-16 right-5 z-50 shadow-lg rounded-full">
                <button id="editButton" class="p-2 bg-white rounded-full shadow hover:scale-110 transition-transform">
                    <i id="editIcon" class="bi bi-pencil text-orange-500"></i>
                    <span id="editText" class="text-orange-500">Edit</span>
                </button>
            </div>
            @yield('content')
        </main>
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Build with&nbsp;&nbsp;<i class="bi bi-heart text-danger"></i>
            </div>
            <strong>
                Kelompok 5 &copy; 2024
                <a href="#" class="text-decoration-none">Dashboard</a>.
            </strong>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script src="https://pkm.sman1balige.delcom.org/assets/vendor/adminlte-4.0.0/js/adminlte.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/2.1.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>


    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function () {
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editButton = document.getElementById("editButton");
            const editIcon = document.getElementById("editIcon");
            const editText = document.getElementById("editText");
            const editableElements = document.querySelectorAll(".editable");
            let isEditing = false;

            editButton.addEventListener("click", () => {
                isEditing = !isEditing;

                // Toggle contentEditable untuk elemen yang dapat diedit
                editableElements.forEach((element) => {
                    element.contentEditable = isEditing;
                    element.style.border = isEditing ? "1px dashed gray" : "none";
                });

                // Ganti ikon dan teks tombol
                editIcon.classList.toggle("bi-pencil", !isEditing);
                editIcon.classList.toggle("bi-check-circle", isEditing);
                editIcon.style.color = isEditing ? "green" : "orange";
                editText.textContent = isEditing ? "Done" : "Edit";
                editText.style.color = isEditing ? "green" : "orange";

                if (!isEditing) {
                    // Simpan konten yang diperbarui
                    saveChanges(editableElements);
                }
            });

            function saveChanges(elements) {
                const changes = {};
                elements.forEach((element, index) => {
                    changes[`element_${index}`] = element.innerHTML; // Gunakan innerHTML untuk menyimpan konten
                });

                // Kirim data ke server
                fetch("/save-edits", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify(changes),
                })
                    .then((response) => response.json())
                    .then((data) => {
                    })
            }

        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const fullscreenButton = document.querySelector('[data-lte-toggle="fullscreen"]');
            const maximizeIcon = document.querySelector('[data-lte-icon="maximize"]');
            const minimizeIcon = document.querySelector('[data-lte-icon="minimize"]');

            // Toggle fullscreen mode
            fullscreenButton.addEventListener("click", () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().then(() => {
                        maximizeIcon.style.display = "none";
                        minimizeIcon.style.display = "inline";
                    }).catch((err) => {
                        console.error(`Failed to enter fullscreen mode: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen().then(() => {
                        maximizeIcon.style.display = "inline";
                        minimizeIcon.style.display = "none";
                    }).catch((err) => {
                        console.error(`Failed to exit fullscreen mode: ${err.message}`);
                    });
                }
            });
        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebarToggleButton = document.querySelector('[data-lte-toggle="sidebar"]');
            const appWrapper = document.querySelector('.app-wrapper');
            const sidebar = document.querySelector('.app-sidebar');

            // Add event listener for the toggle button
            sidebarToggleButton.addEventListener("click", () => {
                if (sidebar) {
                    sidebar.classList.toggle("collapsed");
                    appWrapper.classList.toggle("sidebar-collapsed");
                }
            });
        });
    </script>

</body>

</html>