<div class="container">
    <div class="row align-items-center">
        <div class="col-lg-12 d-none d-lg-block">
            <div class="horizontal-menu">
                <nav>
                    <ul id="nav_menu">
                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('home') }}"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                        </li>

                        {{-- School Administrator Links --}}
                        @if (Auth::user()->usertype == 1)
                            <li>
                                <a href="{{ route('register.manager') }}"><i class="fas fa-user-shield"></i><span>Administrators</span></a>
                            </li>
                            <li class="mega-menu">
                                <a href="{{ route('Schools.index') }}"><i class="fas fa-building"></i><span>Schools</span></a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reset.password') }}"><i class="fas fa-key"></i><span>Password Reset</span></a>
                            </li>
                            <li>
                                <a href="{{ route('admin.accounts') }}"><i class="fas fa-user-cog"></i><span>Admin Accounts</span></a>
                            </li>
                            <li>
                                <a href="{{ route('feedback') }}"><i class="fas fa-envelope"></i><span>Messages</span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-shield-alt"></i><span>System Security</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('failed.login.attempts') }}"><i class="fas fa-exclamation-triangle"></i> Failed Login</a></li>
                                    <li><a href="{{ route('locked.otps') }}"><i class="fas fa-lock"></i> Locked OTP</a></li>
                                </ul>
                            </li>
                        @endif

                        {{-- Teacher (Academic Role) --}}
                        @if (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 3)
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Community</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Teachers.index') }}"><i class="fas fa-chalkboard-user"></i> Teachers</a></li>
                                    <li><a href="{{ route('classes.list') }}"><i class="fas fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{ route('Parents.index') }}"><i class="fas fa-heart"></i> Parents</a></li>
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-graduation-cap"></i><span>Academics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Classes.index') }}"><i class="fas fa-school"></i> Classes</a></li>
                                    <li><a href="{{ route('courses.index') }}"><i class="fas fa-book"></i> Courses</a></li>
                                    <li><a href="{{ route('exams.index') }}"><i class="fas fa-pen-to-square"></i> Assessments</a></li>
                                    <li><a href="{{ route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)]) }}"><i class="fas fa-chart-simple"></i> Results</a></li>
                                </ul>
                            </li>

                            {{-- Reports & Analytics --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-chart-line"></i><span>Reports & Analytics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('attendance.fill.form') }}"><i class="fas fa-calendar-check"></i> Attendance Reports</a></li>
                                    @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                        <li><a href="{{ route('get.school.report') }}"><i class="fas fa-newspaper"></i> Daily School Report</a></li>
                                    @endif
                                    <li><a href="{{ route('package.byYear') }}"><i class="fas fa-umbrella-beach"></i> Holiday Packages</a></li>
                                    <li><a href="{{ route('graduate.students.by.year') }}"><i class="fas fa-award"></i> Alumni Reports</a></li>
                                </ul>
                            </li>

                            {{-- Staff & HR --}}
                            @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                <li>
                                    <a href="javascript:void(0)"><i class="fas fa-briefcase"></i><span>Staff & HR</span></a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('roster.by.year') }}"><i class="fas fa-clipboard-list"></i> Duty Rosters</a></li>
                                        <li><a href="{{ route('contract.index') }}"><i class="fas fa-file-signature"></i> Contracts</a></li>
                                    </ul>
                                </li>
                            @endif

                            {{-- System Administration --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-sliders-h"></i><span>Advanced Settings</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('users.lists') }}"><i class="fas fa-key"></i> Password Reset</a></li>
                                    <li><a href="{{ route('Teachers.trashed') }}"><i class="fas fa-trash-alt"></i> Recycle Bin</a></li>
                                </ul>
                            </li>

                            {{-- Announcements --}}
                            <li>
                                <a href="{{ route('sms.form') }}"><i class="fas fa-bullhorn"></i><span>Announcements</span></a>
                            </li>
                        @endif

                        {{-- Management & Services --}}
                        @if (Auth::user()->usertype == 2 || (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2))
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Community</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Teachers.index') }}"><i class="fas fa-chalkboard-user"></i> Teachers</a></li>
                                    <li><a href="{{ route('classes.list') }}"><i class="fas fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{ route('Parents.index') }}"><i class="fas fa-heart"></i> Parents</a></li>
                                    @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                        <li><a href="{{ route('Accountants.index') }}"><i class="fas fa-calculator"></i> Accountants</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-graduation-cap"></i><span>Academics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Classes.index') }}"><i class="fas fa-school"></i> Classes</a></li>
                                    <li><a href="{{ route('courses.index') }}"><i class="fas fa-book"></i> Courses</a></li>
                                    <li><a href="{{ route('exams.index') }}"><i class="fas fa-pen-to-square"></i> Assessments</a></li>
                                    <li><a href="{{ route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)]) }}"><i class="fas fa-chart-simple"></i> Results</a></li>
                                </ul>
                            </li>

                            {{-- Reports & Analytics --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-chart-line"></i><span>Reports & Analytics</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('attendance.fill.form') }}"><i class="fas fa-calendar-check"></i> Attendance Reports</a></li>
                                    @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                        <li><a href="{{ route('get.school.report') }}"><i class="fas fa-newspaper"></i> Daily School Report</a></li>
                                    @endif
                                    <li><a href="{{ route('package.byYear') }}"><i class="fas fa-umbrella-beach"></i> Holiday Packages</a></li>
                                    <li><a href="{{ route('graduate.students.by.year') }}"><i class="fas fa-award"></i> Alumni Reports</a></li>
                                </ul>
                            </li>

                            {{-- System Administration --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-cogs"></i><span>Advanced Settings</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('roles.updateRole') }}"><i class="fas fa-shield-alt"></i> Roles & Permissions</a></li>
                                    <li><a href="{{ route('users.lists') }}"><i class="fas fa-key"></i> Password Reset</a></li>
                                    <li><a href="{{ route('Teachers.trashed') }}"><i class="fas fa-trash-alt"></i> Recycle Bin</a></li>
                                </ul>
                            </li>

                            {{-- Staff & HR --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-briefcase"></i><span>Staff & HR</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('OtherStaffs.index') }}"><i class="fas fa-user-plus"></i> Staffs</a></li>
                                    @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                        <li>
                                            <a href="{{ route('roster.by.year') }}"><i class="fas fa-clipboard-list"></i> Duty Rosters</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('contract.management') }}"><i class="fas fa-file-signature"></i> Contracts</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>

                            {{-- payment reports --}}
                            @if (Auth::user()->school->package == 'premium')
                                @if (Auth::user()->usertype == 2)
                                    <li>
                                        <a href="javascript:void(0)"><i class="fas fa-money-bill-wave"></i><span>Financial Transactions</span></a>
                                        <ul class="submenu">
                                            <li><a href="{{ route('expenditure.all.transactions') }}"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                                            <li><a href="{{ route('payment.report') }}"><i class="fas fa-credit-card"></i> Bills Payment</a></li>
                                        </ul>
                                    </li>
                                @endif
                            @endif

                            {{-- Services --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-concierge-bell"></i><span>Services</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Transportation.index') }}"><i class="fas fa-bus"></i> Transport</a></li>
                                    <li><a href="{{ route('sms.form') }}"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                                </ul>
                            </li>
                        @endif

                        {{-- Contract Requests (Other roles) --}}
                        @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                            @if ((Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 1) || (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 4))
                                <li>
                                    <a href="{{ route('contract.index') }}"><i class="fas fa-file-contract"></i><span>Contracts</span></a>
                                </li>
                            @endif
                        @endif

                        {{-- Accountant Links --}}
                        @if (Auth::user()->usertype == 5)
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)"><i class="fas fa-users"></i><span>Community</span></a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Teachers.index') }}"><i class="fas fa-chalkboard-user"></i> Teachers</a></li>
                                    <li><a href="{{ route('classes.list') }}"><i class="fas fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{ route('OtherStaffs.index') }}"><i class="fas fa-user-friends"></i> Other Staffs</a></li>
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            @if (Auth::user()->school->package === 'premium')
                                <li>
                                    <a href="javascript:void(0)"><i class="fas fa-chart-pie"></i><span>Accounts & Expenses</span></a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('expenses.index') }}"><i class="fas fa-folder-open"></i> Accounts</a></li>
                                        <li><a href="{{ route('expenditure.index') }}"><i class="fas fa-shopping-cart"></i> Expenses</a></li>
                                    </ul>
                                </li>

                                {{-- fees & bills --}}
                                <li>
                                    <a href="javascript:void(0)"><i class="fas fa-file-invoice-dollar"></i><span>Bills & Payment</span></a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="{{ route('bills.index') }}"><i class="fas fa-receipt"></i><span> Bills</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ route('bills.transactions') }}"><i class="fas fa-hand-holding-usd"></i><span> Payments</span></a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- batch --}}
                                <li>
                                    <a href="{{ route('batches.index') }}"><i class="fas fa-layer-group"></i><span>Batches</span></a>
                                </li>

                                {{-- Services --}}
                                <li>
                                    <a href="javascript:void(0)"><i class="fas fa-tools"></i><span>Advanced Settings</span></a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="{{ route('services.index') }}"><i class="fas fa-wrench"></i><span> Services</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ route('fee-structures.index') }}"><i class="fas fa-table-list"></i><span> Fee structure</span></a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- Payroll Management --}}
                                <li>
                                    <a href="javascript:void(0)"><i class="fas fa-coins"></i><span>Payroll</span></a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="{{ route('payroll.index') }}"><i class="fas fa-list"></i> Payrolls</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('heslb.index') }}"><i class="fas fa-university"></i> HESLB</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('deductions.unofficial') }}"><i class="fas fa-hand-holding-heart"></i> Staff Loans</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('employee.statement.index') }}"><i class="fas fa-file-alt"></i> Statements</a>
                                        </li>
                                    </ul>
                                </li>

                                {{-- Reports & Analytics --}}
                                <li>
                                    <a href="javascript:void(0)"><i class="fas fa-chart-line"></i><span>Financial Transactions</span></a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('expenditure.all.transactions') }}"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                                        <li><a href="{{ route('payment.report') }}"><i class="fas fa-credit-card"></i> Bills Payment</a></li>
                                    </ul>
                                </li>
                            @endif
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

<style>
    .sign-out-btn {
        font-weight: bold;
        background: darkred;
        color: white;
        border-radius: 8px;
    }
</style>
