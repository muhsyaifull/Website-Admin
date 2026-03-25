<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Laravel SB Admin 2')</title>

    <!-- Custom fonts for this template-->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('Images/Login-Logo2.svg') }}" alt="Rumah Atsiri Indonesia"
                        style="width: 36px; height: 36px; object-fit: contain; filter: brightness(0) invert(1);">
                </div>
                <div class="sidebar-brand-text mx-3"
                    style="color: #fff; font-size: 14px; font-weight: 700; line-height: 1.3;">
                    Rumah Atsiri<br>Indonesia
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            @if(auth()->user()->isCashier())
                <!-- Cashier Menu -->
                <div class="sidebar-heading">
                    Cashier Menu
                </div>

                <li class="nav-item {{ request()->routeIs('kasir.booking.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('kasir.booking.create') }}">
                        <i class="fas fa-fw fa-plus-circle"></i>
                        <span>Create Booking</span>
                    </a>
                </li>

            @else
                <!-- Admin & Educator Shared Menu -->
                <div class="sidebar-heading">
                    {{ auth()->user()->isAdmin() ? 'Admin IT Panel' : 'Educator Panel' }}
                </div>

                @if(auth()->user()->isAdmin())
                    <li class="nav-item {{ request()->routeIs('panel.users.*') ? 'active' : '' }}">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers"
                            aria-expanded="false" aria-controls="collapseUsers">
                            <i class="fas fa-fw fa-users"></i>
                            <span>User Management</span>
                        </a>
                        <div id="collapseUsers" class="collapse {{ request()->routeIs('panel.users.*') ? 'show' : '' }}"
                            aria-labelledby="headingUsers" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <h6 class="collapse-header">User Options:</h6>
                                <a class="collapse-item {{ request()->routeIs('panel.users.index') ? 'active' : '' }}"
                                    href="{{ route('panel.users.index') }}">Users List</a>
                                <a class="collapse-item {{ request()->routeIs('panel.users.create') ? 'active' : '' }}"
                                    href="{{ route('panel.users.create') }}">Create User</a>
                            </div>
                        </div>
                    </li>
                @endif

                <li
                    class="nav-item {{ request()->routeIs('panel.packages.*') || request()->routeIs('panel.tours.*') ? 'active' : '' }}">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePackages"
                        aria-expanded="false" aria-controls="collapsePackages">
                        <i class="fas fa-fw fa-box"></i>
                        <span>Package Bundling</span>
                    </a>
                    <div id="collapsePackages"
                        class="collapse {{ request()->routeIs('panel.packages.*') || request()->routeIs('panel.tours.*') ? 'show' : '' }}"
                        aria-labelledby="headingPackages" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Manage Package:</h6>
                            <a class="collapse-item {{ request()->routeIs('panel.packages.index') ? 'active' : '' }}"
                                href="{{ route('panel.packages.index') }}">Package List</a>
                            <a class="collapse-item {{ request()->routeIs('panel.packages.create') ? 'active' : '' }}"
                                href="{{ route('panel.packages.create') }}">Create Package</a>
                            <h6 class="collapse-header">Manage Tour:</h6>
                            <a class="collapse-item {{ request()->routeIs('panel.tours.index') ? 'active' : '' }}"
                                href="{{ route('panel.tours.index') }}">Tour List</a>
                            <a class="collapse-item {{ request()->routeIs('panel.tours.create') ? 'active' : '' }}"
                                href="{{ route('panel.tours.create') }}">Create Tour</a>
                        </div>
                    </div>
                </li>

                <li
                    class="nav-item {{ request()->routeIs('panel.sessions.*') || request()->routeIs('panel.templates.*') ? 'active' : '' }}">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSessions"
                        aria-expanded="false" aria-controls="collapseSessions">
                        <i class="fas fa-fw fa-clock"></i>
                        <span>Session Schedule</span>
                    </a>
                    <div id="collapseSessions"
                        class="collapse {{ request()->routeIs('panel.sessions.*') || request()->routeIs('panel.templates.*') ? 'show' : '' }}"
                        aria-labelledby="headingSessions" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Manage Sessions:</h6>
                            <a class="collapse-item {{ request()->routeIs('panel.sessions.index') ? 'active' : '' }}"
                                href="{{ route('panel.sessions.index') }}">Session List</a>
                            <h6 class="collapse-header">Template:</h6>
                            <a class="collapse-item {{ request()->routeIs('panel.templates.*') ? 'active' : '' }}"
                                href="{{ route('panel.templates.index') }}">Manage Template</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item {{ request()->routeIs('panel.educators.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('panel.educators.index') }}">
                        <i class="fas fa-fw fa-chalkboard-teacher"></i>
                        <span>Educator Data</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('panel.bookings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('panel.bookings.index') }}">
                        <i class="fas fa-fw fa-list"></i>
                        <span>All Bookings</span>
                    </a>
                </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form method="GET" action="{{ route('search') }}"
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control bg-light border-0 small"
                                placeholder="Search users, packages, bookings..." aria-label="Search"
                                value="{{ request('q') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                                <img class="img-profile rounded-circle"
                                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=5a5c69&color=ffffff">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Rumah Atsiri Indonesia {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

    @stack('scripts')

</body>

</html>