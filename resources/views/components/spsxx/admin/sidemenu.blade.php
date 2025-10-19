<nav class="navbar navbar-vertical navbar-expand-lg" data-navbar-appearance="darker">
    {{-- <nav class="navbar navbar-vertical navbar-expand-lg"> --}}
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                @if (Auth::user()->hasRole('SuperAdmin'))
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">Tasks Management</p>
                        <hr class="navbar-vertical-line" />
                        <!-- parent pages-->
                        <div class="nav-item-wrapper">
                            <a class="nav-link dropdown-indicator label-1 {{ Request::is('sps/admin/tasks*') ? 'active' : '' }}"
                                href="#nv-tasks" role="button" data-bs-toggle="collapse" aria-expanded="false"
                                aria-controls="nv-tasks">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper">
                                        <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                    </div>
                                    <span class="nav-link-icon"><span data-feather="check-square"></span></span>
                                    <span class="nav-link-text">Tasks</span>
                                </div>
                            </a>
                            <div class="parent-wrapper label-1">
                                <ul class="nav collapse parent {{ Request::is('exp/admin/tasks*') ? 'show' : '' }}"
                                    data-bs-parent="#navbarVerticalCollapse" id="nv-tasks">
                                    <li class="collapsed-nav-item-title d-none">Tasks</li>
                                    <!-- All Tasks -->
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('exp/admin/tasks') ? 'active' : '' }}"
                                            href="{{ route('chl.admin.tasks.index') }}">
                                            <div class="d-flex align-items-center">
                                                <span class="nav-link-text">All Tasks</span>
                                            </div>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">Settings
                        </p>
                        <hr class="navbar-vertical-line" />
                        <div class="nav-item-wrapper">
                            <a class="nav-link label-1 {{ Request::is('setting/event') ? 'active' : '' }}"
                                href="{{ route('setting.event') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="compass"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Event</span></span>
                                </div>
                            </a>
                        </div>
                        <!-- parent pages-->
                        <div class="nav-item-wrapper">
                            <a class="nav-link label-1 {{ Request::is('setting/venue') ? 'active' : '' }}"
                                href="{{ route('setting.venue') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="compass"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Venue</span></span>
                                </div>
                            </a>
                        </div>

                        <div class="nav-item-wrapper">
                            <a class="nav-link label-1 {{ Request::is('setting/tasks/categories') ? 'active' : '' }}"
                                href="{{ route('setting.category.index') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="compass"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Tasks
                                            Categories</span></span>
                                </div>
                            </a>
                        </div>

                    </li>
                @endif
                @if (Auth::user()->can('roles.permissions.menu'))
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">Roles and Permissions
                        </p>
                        <hr class="navbar-vertical-line" />
                        <!-- parent pages-->
                        <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-list"
                                role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-list">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                        class="nav-link-icon"><span data-feather="file-text"></span></span><span
                                        class="nav-link-text">List</span>
                                </div>
                            </a>
                            <div class="parent-wrapper label-1">
                                <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-list">
                                    <li class="collapsed-nav-item-title d-none">List
                                    </li>
                                    <li class="nav-item"><a
                                            class="nav-link {{ Request::is('sec/groups/list') ? 'active' : '' }}"
                                            href="{{ route('sec.groups.list') }}">
                                            <div class="d-flex align-items-center"><span
                                                    class="nav-link-text">Groups</span>
                                            </div>
                                        </a>
                                        <!-- more inner pages-->
                                    </li>
                                    <li class="nav-item"><a
                                            class="nav-link {{ Request::is('sec/permissions/list') ? 'active' : '' }}"
                                            href="{{ route('sec.perm.list') }}">
                                            <div class="d-flex align-items-center"><span
                                                    class="nav-link-text">Permissions</span>
                                            </div>
                                        </a>
                                        <!-- more inner pages-->
                                    </li>
                                    <li class="nav-item"><a
                                            class="nav-link {{ Request::is('sec/roles/list') ? 'active' : '' }}"
                                            href="{{ route('sec.roles.list') }}">
                                            <div class="d-flex align-items-center"><span
                                                    class="nav-link-text">Roles</span>
                                            </div>
                                        </a>
                                        <!-- more inner pages-->
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- parent pages-->
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/rolesetup/list') ? 'active' : '' }}"
                                href="{{ route('sec.rolesetup.list') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="server"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Roles in
                                            Permission</span></span>
                                </div>
                            </a>
                        </div>

                        @if (Auth::user() && Auth::user()->hasRole('SecurityRole'))
                            <div class="nav-item-wrapper">
                                <a class="nav-link label-1 {{ Request::is('sec/log') ? 'active' : '' }}"
                                    href="{{ route('sec.log') }}" role="button" aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span data-feather="server"></span></span>
                                        <span class="nav-link-text-wrapper">
                                            <span class="nav-link-text">Audit Logs</span>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        @endif

                    </li>
                @endif
                @if (Auth::user()->can('manage.admin.users.menu'))
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">User Management
                        </p>
                        <hr class="navbar-vertical-line" />
                        <!-- parent pages-->
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/adminuser/list') }}"
                                href="{{ route('sec.adminuser.list') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="life-buoy"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">List
                                            Users</span></span>
                                </div>
                            </a>
                        </div>
                        <div class="nav-item-wrapper"><a
                                class="nav-link label-1 {{ Request::is('sec/adminuser/create') }}"
                                href="{{ route('sec.adminuser.create') }}" role="button" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                            data-feather="life-buoy"></span></span><span
                                        class="nav-link-text-wrapper"><span class="nav-link-text">Add
                                            User</span></span>
                                </div>
                            </a>
                        </div>
                    </li>
                @endif
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">General</p>
                    <hr class="navbar-vertical-line" />
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-customization" role="button"
                            data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-customization">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="settings"></span></span><span
                                    class="nav-link-text">Settings</span><span
                                    class="fa-solid fa-circle text-info ms-1 new-page-indicator"
                                    style="font-size: 6px"></span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse"
                                id="nv-customization">
                                <li class="collapsed-nav-item-title d-none">
                                    Customization
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setting.application') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Application Settings</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer">
        <button
            class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span
                class="uil uil-left-arrow-to-left fs-8"></span><span
                class="uil uil-arrow-from-right fs-8"></span><span class="navbar-vertical-footer-text ms-2">Collapsed
                View</span></button>
    </div>
</nav>
