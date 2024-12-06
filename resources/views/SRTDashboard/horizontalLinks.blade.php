<div class="container">
    <div class="row align-items-center">
        <div class="col-lg-12  d-none d-lg-block">
            <div class="horizontal-menu">
                <nav>
                    <ul id="nav_menu">
                        <li>
                            <a href="{{route('home')}}"><i class="ti-dashboard"></i><span>dashboard</span></a>
                        </li>

                        {{-- school administrator links --}}
                        @if (Auth::user()->usertype == 1 )
                            <li>
                                <a href="{{route('register.manager')}}"><i class="fa fa-users"></i><span> Managers</span></a>
                            </li>
                            <li class="mega-menu">
                                <a href="{{route('Schools.index')}}"><i class="fas fa-building"></i> <span>Schools</span></a>
                            </li>
                            <li>
                                <a href="{{route('admin.reset.password')}}"><i class="ti-unlock"></i><span>User Password Reset</span></a>
                            </li>
                            <li>
                                <a href="{{route('feedback')}}"><i class="ti-email"></i><span>Users Feedback</span></a>
                            </li>
                        @endif

                        {{-- school academic teacher navigation links --}}
                        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3 )
                            <li>
                                <a href="{{route('Teachers.index')}}"><i class="fa fa-user-tie"></i><span> Teachers</span></a>
                            </li>
                            <li>
                                <a href="{{route('Parents.index')}}"><i class="fa fa-user-shield"></i><span> Parents</span></a>
                            </li>
                            <li>
                                <a href="{{route('classes.list')}}"><i class="fa fa-user-graduate"></i><span> Students</span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="ti-desktop"></i><span>Class & Courses</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Classes.index')}}"><i class="ti-blackboard"></i><span> Classes</span></a></li>
                                    <li><a href="{{route('courses.index')}}"><i class="ti-book"></i><span> Courses</span></a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{route('attendance.fill.form')}}"><i class="fa fa-list"></i><span> Attendance Report</span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-list-check"></i><span>Exams & Results</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('exams.index')}}"><i class="ti-pencil-alt"></i>Examinations</a></li>
                                    <li><a href="{{route('results.general', Auth::user()->school_id)}}"><i class="ti-layers-alt"></i>Results</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{route('Teachers.trashed')}}"><i class="fa fa-trash"></i><span> Dustbin</span></a>
                            </li>
                        @endif
                        @if (Auth::user()->usertype == 2 || Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2)
                            <li>
                                <a href="{{route('Teachers.index')}}"><i class="fa fa-user-tie"></i><span> Teachers</span></a>
                            </li>
                            <li>
                                <a href="{{route('Parents.index')}}"><i class="fa fa-user-shield"></i><span> Parents</span></a>
                            </li>
                            <li>
                                <a href="{{route('classes.list')}}"><i class="fa fa-user-graduate"></i><span> Students</span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="ti-desktop"></i><span>Class & Courses</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('Classes.index')}}"><i class="ti-blackboard"></i><span> Classes</span></a></li>
                                    <li><a href="{{route('courses.index')}}"><i class="ti-book"></i><span> Courses</span></a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-file"></i><span>Report</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('attendance.fill.form')}}"><i class="ti-list"></i>Attendance</a></li>
                                    <a href="{{route('Transportation.index')}}"><i class="ti-car"></i><span> School Bus</span></a>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-list-check"></i><span>Exams & Results</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('exams.index')}}"><i class="ti-pencil-alt"></i>Examinations</a></li>
                                    <li><a href="{{route('results.general', Auth::user()->school_id)}}"><i class="ti-layers-alt"></i>Results</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-cogs"></i><span>Advanced</span></a>
                                <ul class="submenu">
                                    <li><a href="{{route('roles.updateRole')}}"><i class="ti-unlock"></i>Roles & Permission</a></li>
                                    <li><a href="{{route('users.lists')}}"><i class="fas fa-user-lock"></i>User Password Reset</a></li>
                                    <li><a href="{{route('Teachers.trashed')}}"><i class="fas fa-trash"></i>Trash</a></li>
                                </ul>
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
