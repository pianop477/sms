<!-- Font Awesome 6 (Iko stable na inafanya kazi vizuri) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Custom styling for icons */
    .icon-primary { color: #3b82f6; }
    .icon-success { color: #10b981; }
    .icon-warning { color: #f59e0b; }
    .icon-danger { color: #ef4444; }
    .icon-purple { color: #8b5cf6; }
    .icon-pink { color: #ec4899; }
    .icon-cyan { color: #06b6d4; }
    .icon-indigo { color: #6366f1; }
    .icon-amber { color: #fbbf24; }
    .icon-gray { color: #6b7280; }

    /* Icon with background circle */
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    .icon-circle i {
        font-size: 16px;
    }
    .icon-circle:hover {
        background: rgba(59, 130, 246, 0.2);
        transform: scale(1.05);
    }

    /* Gradient icons */
    .icon-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    /* Pulse animation */
    .icon-pulse {
        animation: pulse-glow 2s infinite;
    }
    @keyframes pulse-glow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; text-shadow: 0 0 5px currentColor; }
    }

    /* Spin animation */
    .icon-spin {
        animation: spin 2s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Hover effects */
    .icon-hover-zoom {
        transition: transform 0.2s ease;
    }
    .icon-hover-zoom:hover {
        transform: scale(1.1);
    }

    .icon-hover-bounce:hover {
        animation: bounce 0.4s ease;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }

    /* Fix for navigation menu icons spacing */
    #nav_menu li a i {
        margin-right: 6px;
        width: 20px;
        text-align: center;
    }

    /* Submenu icons */
    .submenu li a i {
        margin-right: 7px;
        width: 18px;
        font-size: 14px;
    }
</style>
<div class="container">
    <div class="row align-items-center">
        <div class="col-lg-12 d-none d-lg-block">
            <div class="horizontal-menu">
                <nav>
                    <ul id="nav_menu">
                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('home') }}">
                                <i class="fas fa-tachometer-alt icon-primary icon-hover-zoom"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        {{-- School Administrator Links --}}
                        @if (Auth::user()->usertype == 1)
                            <li>
                                <a href="{{ route('register.manager') }}">
                                    <i class="fas fa-user-shield icon-purple"></i>
                                    <span>Administrators</span>
                                </a>
                            </li>
                            <li class="mega-menu">
                                <a href="{{ route('Schools.index') }}">
                                    <i class="fas fa-building icon-cyan"></i>
                                    <span>Schools</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reset.password') }}">
                                    <i class="fas fa-key icon-warning"></i>
                                    <span>Password Reset</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.accounts') }}">
                                    <i class="fas fa-user-cog icon-primary"></i>
                                    <span>Admin Accounts</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('feedback') }}">
                                    <i class="fas fa-envelope icon-pink"></i>
                                    <span>Messages</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-shield-alt icon-danger"></i>
                                    <span>System Security</span>
                                </a>
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
                                <a href="javascript:void(0)">
                                    <i class="fas fa-users icon-purple"></i>
                                    <span>Community</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Teachers.index') }}"><i class="fas fa-chalkboard-user"></i> Teachers</a></li>
                                    <li><a href="{{ route('classes.list') }}"><i class="fas fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{ route('Parents.index') }}"><i class="fas fa-heart icon-pink"></i> Parents</a></li>
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-graduation-cap icon-success icon-gradient"></i>
                                    <span>Academics</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Classes.index') }}"><i class="fas fa-chalkboard"></i> Classes</a></li>
                                    <li><a href="{{ route('courses.index') }}"><i class="fas fa-book-open"></i> Courses</a></li>
                                    <li><a href="{{ route('exams.index') }}"><i class="fas fa-pen-to-square"></i> Assessments</a></li>
                                    <li><a href="{{ route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)]) }}"><i class="fas fa-chart-bar icon-pulse"></i> Results</a></li>
                                </ul>
                            </li>

                            {{-- Reports & Analytics --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-chart-line icon-warning"></i>
                                    <span>Reports & Analytics</span>
                                </a>
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
                                    <a href="javascript:void(0)">
                                        <i class="fas fa-briefcase icon-cyan"></i>
                                        <span>Staff & HR</span>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('roster.by.year') }}"><i class="fas fa-clipboard-list"></i> Duty Rosters</a></li>
                                        <li><a href="{{ route('contract.index') }}"><i class="fas fa-file-signature"></i> Contracts</a></li>
                                    </ul>
                                </li>
                            @endif

                            {{-- System Administration --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-sliders-h icon-indigo"></i>
                                    <span>Advanced Settings</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('users.lists') }}"><i class="fas fa-key"></i> Password Reset</a></li>
                                    <li><a href="{{ route('Teachers.trashed') }}"><i class="fas fa-trash-alt icon-danger"></i> Recycle Bin</a></li>
                                </ul>
                            </li>

                            {{-- Announcements --}}
                            <li>
                                <a href="{{ route('sms.form') }}">
                                    <i class="fas fa-bullhorn icon-pink icon-pulse"></i>
                                    <span>Announcements</span>
                                </a>
                            </li>
                        @endif

                        {{-- Management & Services --}}
                        @if (Auth::user()->usertype == 2 || (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 2))
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-users icon-purple"></i>
                                    <span>Community</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Teachers.index') }}"><i class="fas fa-chalkboard-user"></i> Teachers</a></li>
                                    <li><a href="{{ route('classes.list') }}"><i class="fas fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{ route('Parents.index') }}"><i class="fas fa-heart icon-pink"></i> Parents</a></li>
                                    @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                        <li><a href="{{ route('Accountants.index') }}"><i class="fas fa-calculator"></i> Accountants</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{-- Academic Management --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-graduation-cap icon-success"></i>
                                    <span>Academics</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Classes.index') }}"><i class="fas fa-chalkboard"></i> Classes</a></li>
                                    <li><a href="{{ route('courses.index') }}"><i class="fas fa-book-open"></i> Courses</a></li>
                                    <li><a href="{{ route('exams.index') }}"><i class="fas fa-pen-to-square"></i> Assessments</a></li>
                                    <li><a href="{{ route('results.general', ['school' => Hashids::encode(Auth::user()->school_id)]) }}"><i class="fas fa-chart-bar"></i> Results</a></li>
                                </ul>
                            </li>

                            {{-- Reports & Analytics --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-chart-line icon-warning"></i>
                                    <span>Reports & Analytics</span>
                                </a>
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
                                <a href="javascript:void(0)">
                                    <i class="fas fa-cogs icon-indigo"></i>
                                    <span>Advanced Settings</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('roles.updateRole') }}"><i class="fas fa-shield-alt"></i> Roles & Permissions</a></li>
                                    <li><a href="{{ route('users.lists') }}"><i class="fas fa-key"></i> Password Reset</a></li>
                                    <li><a href="{{ route('Teachers.trashed') }}"><i class="fas fa-trash-alt icon-danger"></i> Recycle Bin</a></li>
                                </ul>
                            </li>

                            {{-- Staff & HR --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-briefcase icon-cyan"></i>
                                    <span>Staff & HR</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('OtherStaffs.index') }}"><i class="fas fa-user-plus"></i> Staffs</a></li>
                                    @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                                        <li><a href="{{ route('roster.by.year') }}"><i class="fas fa-clipboard-list"></i> Duty Rosters</a></li>
                                        <li><a href="{{ route('contract.management') }}"><i class="fas fa-file-signature"></i> Contracts</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{-- Financial Transactions --}}
                            @if (Auth::user()->school->package == 'premium')
                                @if (Auth::user()->usertype == 2)
                                    <li>
                                        <a href="javascript:void(0)">
                                            <i class="fas fa-money-bill-wave icon-amber"></i>
                                            <span>Financial Report</span>
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="{{ route('expenditure.all.transactions') }}"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                                            <li><a href="{{ route('payment.report') }}"><i class="fas fa-credit-card"></i> Bills Payment</a></li>
                                        </ul>
                                    </li>
                                @endif
                            @endif

                            {{-- Services --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-handshake icon-success"></i>
                                    <span>Services</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Transportation.index') }}"><i class="fas fa-bus"></i> Transport</a></li>
                                    <li><a href="{{ route('sms.form') }}"><i class="fas fa-bullhorn icon-pink"></i> Announcements</a></li>
                                </ul>
                            </li>
                        @endif

                        {{-- Contract Requests --}}
                        @if (Auth::user()->school === null || Auth::user()->school->package == 'premium')
                            @if ((Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 1) || (Auth::user()->usertype == 3 && Auth::user()->teacher->role_id == 4))
                                <li>
                                    <a href="{{ route('contract.index') }}">
                                        <i class="fas fa-file-contract icon-cyan"></i>
                                        <span>Contracts</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        {{-- Accountant Links --}}
                        @if (Auth::user()->usertype == 5)
                            {{-- Community --}}
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fas fa-users icon-purple"></i>
                                    <span>Community</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('Teachers.index') }}"><i class="fas fa-chalkboard-user"></i> Teachers</a></li>
                                    <li><a href="{{ route('classes.list') }}"><i class="fas fa-user-graduate"></i> Students</a></li>
                                    <li><a href="{{ route('OtherStaffs.index') }}"><i class="fas fa-user-friends"></i> Other Staffs</a></li>
                                </ul>
                            </li>

                            {{-- Accounts & Expenses --}}
                            @if (Auth::user()->school->package === 'premium')
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="fas fa-chart-pie icon-warning"></i>
                                        <span>Accounts & Expenses</span>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('expenses.index') }}"><i class="fas fa-folder-open"></i> Accounts</a></li>
                                        <li><a href="{{ route('expenditure.index') }}"><i class="fas fa-shopping-cart"></i> Expenses</a></li>
                                    </ul>
                                </li>

                                {{-- Bills & Payment --}}
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="fas fa-file-invoice-dollar icon-success"></i>
                                        <span>Bills & Payment</span>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('bills.index') }}"><i class="fas fa-receipt"></i> Bills</a></li>
                                        <li><a href="{{ route('bills.transactions') }}"><i class="fas fa-hand-holding-usd"></i> Payments</a></li>
                                    </ul>
                                </li>

                                {{-- Batches --}}
                                <li>
                                    <a href="{{ route('batches.index') }}">
                                        <i class="fas fa-layer-group icon-primary"></i>
                                        <span>Batches</span>
                                    </a>
                                </li>

                                {{-- Advanced Settings --}}
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="fas fa-tools icon-indigo"></i>
                                        <span>Advanced Settings</span>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('services.index') }}"><i class="fas fa-wrench"></i> Services</a></li>
                                        <li><a href="{{ route('fee-structures.index') }}"><i class="fas fa-table-list"></i> Fee structure</a></li>
                                    </ul>
                                </li>

                                {{-- Payroll Management --}}
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="fas fa-coins icon-amber"></i>
                                        <span>Payroll</span>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('payroll.index') }}"><i class="fas fa-list"></i> Payrolls</a></li>
                                        <li><a href="{{ route('heslb.index') }}"><i class="fas fa-university"></i> HESLB</a></li>
                                        <li><a href="{{ route('deductions.unofficial') }}"><i class="fas fa-hand-holding-heart"></i> Staff Loans</a></li>
                                        <li><a href="{{ route('employee.statement.index') }}"><i class="fas fa-file-alt"></i> Statements</a></li>
                                    </ul>
                                </li>

                                {{-- Financial Transactions --}}
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="fas fa-chart-line icon-success"></i>
                                        <span>Financial Report</span>
                                    </a>
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
