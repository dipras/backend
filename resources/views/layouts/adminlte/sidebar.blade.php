<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    {{-- Menu For Admin Access --}}
    {{-- @can('isAdmin') --}}

        @include(
            'layouts.adminlte.sidebar-child', [
                "menus" => config('adminlte.menu_sidebar.admin')
            ]
        )

    {{-- Menu For TUK Access --}}
    {{-- @elsecan('isTuk') --}}

    {{-- Menu For Assesor Access --}}
    {{-- @else --}}

    {{-- @endcan --}}


        {{-- Log Out Button in Sidebar --}}
        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>{{ trans('theme.logout') }}</p>
            </a>
        </li>
    </ul>
</nav>
