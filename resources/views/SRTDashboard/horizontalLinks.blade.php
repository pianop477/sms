<div class="container">
    <div class="row align-items-center">
        <div class="col-lg-12 d-none d-lg-block">
            <div class="horizontal-menu">
                <nav>
                    <ul id="nav_menu">
                        {{-- Dashboard --}}
                        <li>
                            <a href="{{route('home')}}"><i class="fas fa-dashboard"></i><span>Dashboard</span></a>
                        </li>

                        {{-- School Administrator Links --}}
                        @if (Auth::user()->usertype == 1 )
                            <li>
                                <a href="{{route('register.manager')}}"><i class="fa fa-users"></i><span> Administrators</span></a>
                            </li>
                            <li class="mega-menu">
                                <a href="{{route('Schools.index')}}"><i class="fas fa-university"></i> <span>School Management</span></a>
                            </li>
                            <li>
                                <a href="{{route('admin.reset.password')}}"><i class="ti-key"></i><span>Password Reset</span></a>
                            </li>
                            <li>
                                <a href="{{route('admin.accounts')}}"><i class="fas fa-user-lock"></i><span>Admin Accounts</span></a>
                            </li>
                            <li>
                                <a href="{{route('feedback')}}"><i class="ti-email"></i><span>Messages</span></a>
                            </li>
                            <li>
                                <a href="{{route('failed.login.attempts')}}"><i class="ti-lock"></i><span>System Security</span></a>
                            </li>
                        @endif

                        {{-- Teacher (Academic Role) --}}
                        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3 )
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Community</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Teachers.index')}}"><i class="fa fa-user-tie"></i> Teachers</a></li>
                                    <li><a href="{{route('classes.list')}}"><i class="fa fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{route('Parents.index')}}"><i class="fa fa-user-friends"></i> Parents</a></li>
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-university"></i><span>Academic Management</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Classes.index')}}"><i class="fas fa-chalkboard-teacher"></i> Class Management</a></li>
                                    <li><a href="{{route('courses.index')}}"><i class="fas fa-book-open"></i> Course Management</a></li>
                                    <li><a href="{{route('exams.index')}}"><i class="fas fa-clipboard-list"></i> Assessments</a></li>
                                    <li><a href="{{route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)])}}"><i class="fas fa-chart-bar"></i> Results</a></li>
                                </ul>
                            </li>

                            {{-- Reports & Analytics --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-chart-pie"></i><span>Reports & Analytics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('attendance.fill.form')}}"><i class="fas fa-calendar-check"></i> Attendance Reports</a></li>
                                    <li><a href="{{route('get.school.report')}}"><i class="fas fa-book"></i> Daily School Report</a></li>
                                    <li><a href="{{route('package.byYear')}}"><i class="fas fa-layer-group"></i> Holiday Packages</a></li>
                                    <li><a href="{{route('graduate.students.by.year')}}"><i class="fas fa-graduation-cap"></i> Alumni Reports</a></li>
                                </ul>
                            </li>

                            {{-- Staff & HR --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-file-archive"></i><span>Staff & HR</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('tod.roster.index')}}"><i class="fas fa-file"></i> Duty Rosters</a></li>
                                    <li><a href="{{route('contract.index')}}"><i class="fas fa-briefcase"></i> Manage Contracts</a></li>
                                </ul>
                            </li>

                            {{-- System Administration --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-screwdriver-wrench"></i><span>System Administration</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Teachers.trashed')}}"><i class="fas fa-trash"></i> Recycle Bin</a></li>
                                </ul>
                            </li>

                            {{-- Announcements --}}
                            <li>
                                <a href="{{route('sms.form')}}"><i class="ti-announcement"></i><span>Announcements</span></a>
                            </li>
                        @endif

                        {{-- Management & Services --}}
                        @if (Auth::user()->usertype == 2 || Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Community</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Teachers.index')}}"><i class="fa fa-user-tie"></i> Teachers</a></li>
                                    <li><a href="{{route('classes.list')}}"><i class="fa fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{route('Parents.index')}}"><i class="fa fa-user-friends"></i> Parents</a></li>
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-university"></i><span>Academic Management</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Classes.index')}}"><i class="fas fa-chalkboard-teacher"></i> Class Management</a></li>
                                    <li><a href="{{route('courses.index')}}"><i class="fas fa-book-open"></i> Course Management</a></li>
                                    <li><a href="{{route('exams.index')}}"><i class="fas fa-clipboard-list"></i> Assessments</a></li>
                                    <li><a href="{{route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)])}}"><i class="fas fa-chart-bar"></i> Results</a></li>
                                </ul>
                            </li>

                            {{-- Reports & Analytics --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-chart-pie"></i><span>Reports & Analytics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('attendance.fill.form')}}"><i class="fas fa-calendar-check"></i> Attendance Reports</a></li>
                                    <li><a href="{{route('get.school.report')}}"><i class="fas fa-book"></i> Daily School Report</a></li>
                                    <li><a href="{{route('package.byYear')}}"><i class="fas fa-layer-group"></i> Holiday Packages</a></li>
                                    <li><a href="{{route('graduate.students.by.year')}}"><i class="fas fa-graduation-cap"></i> Alumni Reports</a></li>
                                </ul>
                            </li>

                            {{-- System Administration --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-screwdriver-wrench"></i><span>System Administration</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('roles.updateRole')}}"><i class="ti-unlock"></i> Roles & Permissions</a></li>
                                    <li><a href="{{route('users.lists')}}"><i class="fas fa-key"></i> Password Reset</a></li>
                                    <li><a href="{{route('Teachers.trashed')}}"><i class="fas fa-trash"></i> Recycle Bin</a></li>
                                </ul>
                            </li>

                            {{-- Staff & HR --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-file-archive"></i><span>Staff & HR</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('tod.roster.index')}}"><i class="fas fa-file"></i> Duty Rosters</a></li>
                                    <li><a href="{{route('contract.management')}}"><i class="fas fa-briefcase"></i> Manage Contracts</a></li>
                                </ul>
                            </li>

                            {{-- Services --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-handshake-alt"></i><span>Services</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Transportation.index')}}"><i class="fa fa-bus-alt"></i> Transport Services</a></li>
                                    <li><a href="{{route('sms.form')}}"><i class="ti-announcement"></i> Announcements</a></li>
                                </ul>
                            </li>
                        @endif

                        {{-- Contract Requests (Other roles) --}}
                        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 1 || Auth::user()->usertype ==3 && Auth::user()->teacher->role_id == 4)
                            <li>
                                <a href="{{route('contract.index')}}"><i class="fas fa-briefcase"></i><span> Manage Contracts</span></a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>

        <!-- mobile_menu -->
        <div class="col-12 d-block d-lg-none">
            <div id="mobile_menu"></div>
        </div>
    </div>
</div>
