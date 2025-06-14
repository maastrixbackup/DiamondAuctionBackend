    {{-- <div class="main-header">
        <div class="main-header-logo"> --}}
    <!-- Logo Header -->
    {{-- <div class="logo-header" data-background-color="dark">
                <a href="index.html" class="logo">
                    <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="navbar brand" class="navbar-brand"
                        height="20" />
                </a>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div> --}}
    <!-- End Logo Header -->
    {{-- </div> --}}
    <!-- Navbar Header -->
    {{-- <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">


                <div class="mx-auto text-center position-absolute start-50 translate-middle-x">
                    <span class="fw-semibold fs-5 text-dark">Welcome, Admin</span>
                </div>

                <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                    <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                            aria-expanded="false" aria-haspopup="true">
                            <i class="fa fa-search"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-search animated fadeIn">
                            <form class="navbar-left navbar-form nav-search">
                                <div class="input-group">
                                    <input type="text" placeholder="Search ..." class="form-control" />
                                </div>
                            </form>
                        </ul>
                    </li>


                    <li class="nav-item topbar-icon dropdown hidden-caret">
                        <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            <span class="notification">4</span>
                        </a>
                        <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                            <li>
                                <div class="dropdown-title">You have 4 new notification</div>
                            </li>
                            <li>
                                <div class="notif-scroll scrollbar-outer">
                                    <div class="notif-center">
                                        <a href="#">
                                            <div class="notif-icon notif-primary">
                                                <i class="fa fa-user-plus"></i>
                                            </div>
                                            <div class="notif-content">
                                                <span class="block"> New user registered </span>
                                                <span class="time">5 minutes ago</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a class="see-all" href="javascript:void(0);">See all notifications<i
                                        class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </li>




                    <li class="nav-item topbar-user dropdown hidden-caret">
                        <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                            aria-expanded="false">
                            <div class="avatar-sm">
                                <img src="{{ asset('assets/img/profile.png') }}" alt="..."
                                    class="avatar-img rounded-circle" />
                            </div>

                        </a>
                        <ul class="dropdown-menu dropdown-user animated fadeIn">
                            <div class="dropdown-user-scroll scrollbar-outer">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg">
                                            <img src="{{ asset('assets/img/profile.png') }}" alt="image profile"
                                                class="avatar-img rounded" />
                                        </div>
                                        <div class="u-text">
                                            <h4>{{ Auth::guard('admin')->user()->name }}</h4>
                                            <p class="text-muted">{{ Auth::guard('admin')->user()->email }}</p>
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="btn btn-xs btn-secondary btn-sm">View
                                                Profile</a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">My Profile</a>
                                    <a class="dropdown-item" href="#">My Balance</a>
                                    <a class="dropdown-item" href="#">Inbox</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Account Setting</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                        onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        <i class="align-middle" data-feather="log-out"></i>
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </div>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav> --}}
    <!-- End Navbar -->
    {{-- </div> --}}



    <!-- HEADER -->
    <header class="fixed top-0 left-0 right-0 h-20 bg-white shadow flex items-center justify-between px-6 z-40">
        <div class="flex items-center space-x-4">
            <svg viewBox="0 0 576 512" fill="#d4af37" class="h-10 w-10">
                <path
                    d="M485.5 0L576 160H474.9L405.7 0h79.8zm-128 0l69.2 160H149.3L218.5 0h139zm-267 0h79.8l-69.2 160H0L90.5 0zM0 192h100.7l123 251.7c1.5 3.1-2.7 5.9-5 3.3L0 192zm148.2 0h279.6l-137 318.2c-1 2.4-4.5 2.4-5.5 0L148.2 192zm204.1 251.7l123-251.7H576L357.3 446.9c-2.3 2.7-6.5-.1-5-3.2z">
                </path>
            </svg>
            <span class="ml-2 text-3xl font-normal font-aleo text-black">DexGems DMCC</span>
        </div>
        <div class="flex-1 flex items-center justify-center">
            <span class="font-bold text-gray-700 text-xl font-aleo">Welcome, Admin</span>
        </div>
        {{-- <div class="flex items-center space-x-4">
            <button class="relative focus:outline-none">
                <i class="fa fa-bell-o w-6 h-6"></i>

                <span class="absolute top-0 right-0 block h-2 w-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>
            <img src="https://randomuser.me/api/portraits/men/44.jpg" alt="Profile"
                class="h-10 w-10 rounded-full border-2 border-brand-gold">
            <button id="sidebar-toggle"
                class="lg:hidden ml-2 p-2 rounded hover:bg-gray-100 focus:outline-none transition">
                <i data-lucide="menu" class="w-7 h-7 text-gray-500"></i>
            </button>
        </div> --}}
        <div class="flex items-center space-x-4">
            <!-- Notification Icon -->
            <button class="relative focus:outline-none">
                <i class="fa fa-bell w-6 h-6"></i>
                <span class="absolute top-0 right-0 block h-2 w-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>

            <!-- Profile Picture and Dropdown -->
            <div class="relative">
                <!-- Profile Image Button -->
                <button id="profileDropdownBtn" class="focus:outline-none">
                    <img src="{{ asset('assets/img/diamond-with-gold.png') }}" alt="Profile"
                        class="h-10 w-10 rounded-full border-2 border-brand-gold">
                </button>

                <!-- Dropdown Menu -->
                <div id="profileDropdown"
                    class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        View Profile
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar Toggle -->
            <button id="sidebar-toggle"
                class="lg:hidden ml-2 p-2 rounded hover:bg-gray-100 focus:outline-none transition">
                <i data-lucide="menu" class="w-7 h-7 text-gray-500"></i>
            </button>
        </div>


    </header>
