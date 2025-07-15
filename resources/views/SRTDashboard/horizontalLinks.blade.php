<div class="container">
    <div class="row align-items-center">
        <div class="col-lg-12  d-none d-lg-block">
            <div class="horizontal-menu">
                <nav>
                    <ul id="nav_menu">
                        <li>
                            <a href="{{route('home')}}"><i class="fas fa-dashboard"></i><span>dashboard</span></a>
                        </li>

                        {{-- school administrator links --}}
                        @if (Auth::user()->usertype == 1 )
                            <li>
                                <a href="{{route('register.manager')}}"><i class="fa fa-users"></i><span> School Administrators</span></a>
                            </li>
                            <li class="mega-menu">
                                <a href="{{route('Schools.index')}}"><i class="fas fa-university"></i> <span>Schools</span></a>
                            </li>
                            <li>
                                <a href="{{route('admin.reset.password')}}"><i class="ti-key"></i><span>User Password Reset</span></a>
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

                        {{-- school academic teacher navigation links --}}
                        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3 )
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Members</span></a>
                                <ul class="submenu">
                                    <li>
                                        <a href="{{route('Teachers.index')}}"><i class="fa fa-user-tie"></i><span> Teachers</span></a>
                                    </li>
                                    <li>
                                        <a href="{{route('classes.list')}}"><i class="fa fa-user-graduate"></i><span> Students</span></a>
                                    </li>
                                    <li>
                                        <a href="{{route('Parents.index')}}"><i class="fa fa-user-shield"></i><span> Parents</span></a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-university"></i><span>Academics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Classes.index')}}"><i class="fas fa-chalkboard-teacher"></i><span> Classes</span></a></li>
                                    <li><a href="{{route('courses.index')}}"><i class="fas fa-book-open"></i><span> Courses</span></a></li>
                                    <li><a href="{{route('exams.index')}}"><i class="fas fa-clipboard-list"></i>Examination</a></li>
                                    <li><a href="{{route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)])}}"><i class="fas fa-chart-bar"></i>Results</a></li>
                                    {{-- <li><a href="{{route('timetable.settings')}}"><i class="fas fa-file-circle-check"></i> Timetable</a></li> --}}
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-chart-line"></i><span>Records & Report</span></a>
                                <ul class="submenu">
                                    <li>
                                        <a href="{{route('attendance.fill.form')}}"><i class="fas fa-calendar-check"></i><span> Attendance</span></a>
                                    </li>
                                    <li><a href="{{route('package.byYear')}}"><i class="fas fa-layer-group"></i> Holiday Packages</a></li>
                                    <li><a href="{{route('graduate.students.by.year')}}"><i class="fas fa-graduation-cap"></i> Graduate Students</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-file-archive"></i><span>Contracts & Legals</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('contract.index')}}"><i class="fa fa-exchange-alt"></i> Contract Manager</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-screwdriver-wrench"></i><span>System Management</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Teachers.trashed')}}"><i class="fas fa-trash"></i> Recycle bin</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{route('sms.form')}}"><i class="ti-announcement"></i><span> Public Notice</span></a>
                            </li>
                        @endif
                        @if (Auth::user()->usertype == 2 || Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Members</span></a>
                                <ul class="submenu">
                                    <li>
                                        <a href="{{route('Teachers.index')}}"><i class="fa fa-user-tie"></i><span> Teachers</span></a>
                                    </li>
                                    <li>
                                        <a href="{{route('classes.list')}}"><i class="fa fa-user-graduate"></i><span> Students</span></a>
                                    </li>
                                    <li>
                                        <a href="{{route('Parents.index')}}"><i class="fa fa-user-shield"></i><span> Parents</span></a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-university"></i><span>Academics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Classes.index')}}"><i class="fas fa-chalkboard-teacher"></i><span> Classes</span></a></li>
                                    <li><a href="{{route('courses.index')}}"><i class="fas fa-book-open"></i><span> Courses</span></a></li>
                                    <li><a href="{{route('exams.index')}}"><i class="fas fa-clipboard-list"></i>Examination</a></li>
                                    <li><a href="{{route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)])}}"><i class="fas fa-chart-bar"></i>Results</a></li>
                                    {{-- <li><a href="{{route('under.construction.page')}}"><i class="fas fa-file-circle-check"></i> Timetable</a></li> --}}
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-chart-line"></i><span>Records & Report</span></a>
                                <ul class="submenu">
                                    <li>
                                        <a href="{{route('attendance.fill.form')}}"><i class="fas fa-calendar-check"></i><span> Attendance</span></a>
                                    </li>
                                    <li><a href="{{route('package.byYear')}}"><i class="fas fa-layer-group"></i> Holiday Packages</a></li>
                                    <li><a href="{{route('graduate.students')}}"><i class="fas fa-graduation-cap"></i> Graduate Students</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-screwdriver-wrench"></i><span>System Management</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('roles.updateRole')}}"><i class="ti-unlock"></i>Roles & Permission</a></li>
                                    <li><a href="{{route('users.lists')}}"><i class="fas fa-key"></i>User Password Reset</a></li>
                                    <li><a href="{{route('Teachers.trashed')}}"><i class="fas fa-trash"></i> Recycle bin</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-file-archive"></i><span>Contracts & Legals</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('contract.management')}}"><i class="fa fa-exchange-alt"></i> Contracts Requests</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-handshake-alt"></i><span>Services</span></a>
                                <ul class="submenu">
                                    <li>
                                        <a href="{{route('Transportation.index')}}"><i class="fa fa-bus-alt"></i><span> School Bus</span></a>
                                    </li>
                                    <li>
                                        <a href="{{route('sms.form')}}"><i class="ti-announcement"></i><span> Public Notice</span></a>
                                    </li>
                                </ul>
                            </li>

                        @endif
                        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 1 || Auth::user()->usertype ==3 && Auth::user()->teacher->role_id == 4)
                            <li>
                                <a href="{{route('contract.index')}}"><i class="fa fa-exchange-alt"></i><span> Contract Requests</span></a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        <!-- nav and search button -->
        {{-- <div class="col-lg-2 clearfix">
            <div class="search-box">
                <form action="#">
                    <input type="text" name="search" placeholder="Search..." required>
                    <i class="ti-search"></i>
                </form>
            </div>
        </div> --}}
        <!-- mobile_menu -->
        <div class="col-12 d-block d-lg-none">
            <div id="mobile_menu"></div>
        </div>
    </div>
</div>
