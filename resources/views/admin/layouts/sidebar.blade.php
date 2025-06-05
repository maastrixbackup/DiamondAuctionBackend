<aside id="sidebar"
    class="fixed top-0 h-screen w-64 bg-[#2E3744] text-white shadow-lg pt-24 z-30 flex flex-col justify-between">

    <nav class="flex flex-col space-y-1 px-0 text-sm font-medium">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.dashboard') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-home me-2"></i> Dashboard
        </a>

        <a href="{{ route('admin.lots.index') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.lots.*') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-boxes me-2"></i> Lot Management
        </a>

        <a href="{{ route('admin.seller') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.seller', 'admin.sellerDetails') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-user-tie me-2"></i> Sellers
        </a>

        <a href="{{ route('admin.bidder') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.bidder', 'admin.bidderDetails') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-users me-2"></i> Bidders
        </a>

        <a href="{{ route('admin.admin') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.admin', 'admin.adminDetails') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-user me-2"></i> Admins
        </a>

        <a href="{{ route('admin.category.index') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.category.*') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-layer-group me-2"></i> Categories
        </a>

        <a href="{{ route('admin.viewingRequest') }}"
           class="flex items-center px-4 py-3 hover:bg-[#3B4756] transition rounded-r-full {{ request()->routeIs('admin.viewingRequest') ? 'bg-[#3B4756]' : '' }}">
            <i class="fa fa-envelope me-2"></i> Viewing Slots
        </a>
    </nav>

    <div class="pb-8"></div>
</aside>
