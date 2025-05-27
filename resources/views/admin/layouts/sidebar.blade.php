<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 bg-[#232323] text-gray-100 shadow-lg pt-24 transition sidebar-hide lg:sidebar-show z-30 flex flex-col justify-between">
    <nav class="flex flex-col space-y-1 px-0">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center p-3 rounded transition hover:bg-brand-gold/80 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-brand-gold text-white' : '' }}">
            <i class="fa fa-home mx-2"></i> Dashboard
        </a>

        <a href="{{ route('admin.lots.index') }}"
            class="flex items-center p-3 rounded transition hover:bg-brand-gold/80 hover:text-white {{ request()->routeIs('admin.lots.*') ? 'bg-brand-gold text-white' : '' }}">
            <i class="fa fa-boxes mx-2"></i> Lots
        </a>

        <a href="{{ route('admin.seller') }}"
            class="flex items-center p-3 rounded transition hover:bg-brand-gold/80 hover:text-white {{ request()->routeIs('admin.seller', 'admin.sellerDetails') ? 'bg-brand-gold text-white' : '' }}">
            <i class="fa fa-user-tie mx-2"></i> Sellers
        </a>

        <a href="{{ route('admin.bidder') }}"
            class="flex items-center p-3 rounded transition hover:bg-brand-gold/80 hover:text-white {{ request()->routeIs('admin.bidder', 'admin.bidderDetails') ? 'bg-brand-gold text-white' : '' }}">
            <i class="fa fa-users mx-2"></i> Bidders
        </a>

        <a href="{{ route('admin.admin') }}"
            class="flex items-center p-3 rounded transition hover:bg-brand-gold/80 hover:text-white {{ request()->routeIs('admin.admin', 'admin.adminDetails') ? 'bg-brand-gold text-white' : '' }}">
            <i class="fa fa-user mx-2"></i> Admins
        </a>

        <a href="{{ route('admin.category.index') }}"
            class="flex items-center p-3 rounded transition hover:bg-brand-gold/80 hover:text-white {{ request()->routeIs('admin.category.*') ? 'bg-brand-gold text-white' : '' }}">
            <i class="fa fa-layer-group mx-2"></i> Categories
        </a>

        {{-- <li class="nav-item">
            <a data-bs-toggle="collapse" href="#base">
                <i class="fa fa-layer-group"></i>
                <p>Base</p>
                <span class="caret"></span>
            </a>
            <div class="collapse" id="base">
                <ul class="nav nav-collapse">
                    <li>
                        <a href="components/avatars.html">
                            <span class="sub-item">Avatars</span>
                        </a>
                    </li>
                    <li>
                        <a href="components/buttons.html">
                            <span class="sub-item">Buttons</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li> --}}

    </nav>
    <div class="pb-8"></div>
</aside>
