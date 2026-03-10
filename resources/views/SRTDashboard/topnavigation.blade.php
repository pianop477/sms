<div class="dashboard-header bg-white shadow-sm">
    <div class="container-fluid px-2 px-md-3">
        <div class="d-flex align-items-center justify-content-between py-1 py-md-2">
            {{-- Logo & School Info Section --}}
            <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                @php
                    $schoolName = Auth::user()->school && Auth::user()->school->school_name
                                ? Auth::user()->school->school_name
                                : 'ShuleApp - Admin Panel';

                    $logoPath = Auth::user()->school && Auth::user()->school->logo
                                ? url('storage/logo/' . Auth::user()->school->logo)
                                : url('storage/logo/new_logo.png');
                @endphp

                <div class="brand-container d-flex align-items-center">
                    <img src="{{ $logoPath }}"
                         alt="School Logo"
                         class="school-logo-img mr-2">

                    <div class="school-text-info">
                        <a href="{{ route('home') }}"
                           class="school-name-link text-decoration-none fw-bold d-block">
                            {{ $schoolName }}
                        </a>
                        @if(Auth::user()->school && Auth::user()->school->postal_address)
                        <span class="school-address-text d-none d-sm-block">
                            {{ strtoupper(Auth::user()->school->postal_address )}} - {{strtoupper(Auth::user()->school->postal_name)}}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Profile & Actions Section --}}
            <div class="profile-actions d-flex align-items-center">
                @php
                    $imageName = Auth()->user()->image;
                    $filePath = storage_path('app/public/profile/' . $imageName);

                    if ($imageName && file_exists($filePath)) {
                        $avatarImage = asset('storage/profile/' . $imageName);
                    } else {
                        $default = auth()->user()->gender == 'male'
                                    ? 'avatar.jpg'
                                    : 'avatar-female.jpg';

                        $avatarImage = asset('storage/profile/' . $default);
                    }

                    // Fix for usertype display
                    $usertype = Auth::user()->usertype;
                    if($usertype == 1) {
                        $roleDisplay = 'Administrator';
                    } elseif($usertype == 2) {
                        $roleDisplay = 'Manager';
                    } elseif($usertype == 3) {
                        $roleDisplay = 'Teacher';
                    } else {
                        $roleDisplay = 'Parent';
                    }
                @endphp

                <div class="profile-dropdown-container d-flex align-items-center">
                    {{-- Desktop Profile Info --}}
                    <div class="profile-text mr-2 d-none d-sm-block">
                        <div class="profile-name fw-bold">
                            Hi, {{ ucwords(strtolower(Auth::user()->first_name)) }}
                        </div>
                        <div class="profile-role">
                            {{ $roleDisplay }}
                        </div>
                    </div>

                    {{-- Profile Image with Dropdown Toggle --}}
                    <div class="dropdown">
                        <button class="btn p-0 border-0 bg-transparent d-flex align-items-center" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <img src="{{ $avatarImage }}"
                                 alt="Profile"
                                 class="profile-avatar rounded-circle">
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm border-0">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('show.profile') }}">
                                    <i class="ti-user mr-2" style="color: #667eea;"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('change.password') }}">
                                    <i class="ti-key mr-2" style="color: #667eea;"></i> Change Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button type="button"
                                        class="dropdown-item py-2 signout-btn"
                                        onclick="confirmSignout()"
                                        style="background: none; border: none; width: 100%; text-align: left;">
                                    <i class="ti-power-off me-2" style="color: #e74a3b;"></i>
                                    <span style="color: #e74a3b; font-weight: 600;">Sign out</span>
                                </button>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add Font Awesome if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Header Styles - Now Sticky! */
    .dashboard-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(200, 129, 42, 0.3);
        background: white;
        width: 100%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    /* Reduced Image Sizes */
    .school-logo-img {
        transition: all 0.3s ease;
        width: 35px;
        height: 35px;
        object-fit: cover;
        border-radius: 8px;
        border: 1.5px solid #e9ecef;
        padding: 1px;
    }

    .profile-avatar {
        transition: all 0.3s ease;
        cursor: pointer;
        width: 35px;
        height: 35px;
        object-fit: cover;
        border: 1.5px solid #667eea;
        padding: 1px;
        border-radius: 50%;
    }

    /* Small Mobile Devices */
    @media (min-width: 481px) {
        .school-logo-img {
            width: 38px !important;
            height: 38px !important;
        }

        .profile-avatar {
            width: 38px !important;
            height: 38px !important;
        }
    }

    /* Tablets */
    @media (min-width: 768px) {
        .school-logo-img {
            width: 42px !important;
            height: 42px !important;
            border-radius: 10px;
            border-width: 2px;
        }

        .profile-avatar {
            width: 42px !important;
            height: 42px !important;
            border-width: 2px;
        }
    }

    /* Laptops/Desktops */
    @media (min-width: 992px) {
        .school-logo-img {
            width: 45px !important;
            height: 45px !important;
            border-radius: 12px;
        }

        .profile-avatar {
            width: 45px !important;
            height: 45px !important;
        }
    }

    /* Large Screens */
    @media (min-width: 1200px) {
        .school-logo-img {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px;
        }

        .profile-avatar {
            width: 48px !important;
            height: 48px !important;
        }
    }

    /* School Name - Reduced sizes */
    .school-name-link {
        font-size: 0.85rem;
        color: #2d3748;
        line-height: 1.2;
        text-transform: uppercase;
        font-weight: 600;
        transition: color 0.3s ease;

        /* Truncate on mobile */
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Small Mobile Devices */
    @media (max-width: 480px) {
        .school-name-link {
            max-width: 85px;
            font-size: 0.8rem;
        }
    }

    /* Tablets - show full name */
    @media (min-width: 768px) {
        .school-name-link {
            max-width: none;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            font-size: 1rem;
            font-weight: 700;
        }
    }

    /* Laptops/Desktops */
    @media (min-width: 992px) {
        .school-name-link {
            font-size: 1.1rem;
        }
    }

    /* Large Screens */
    @media (min-width: 1200px) {
        .school-name-link {
            font-size: 1.2rem;
        }
    }

    /* School Address Text */
    .school-address-text {
        font-size: 0.6rem;
        color: #718096;
        max-width: 130px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (min-width: 768px) {
        .school-address-text {
            max-width: 250px;
            font-size: 0.7rem;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
        }
    }

    /* Profile Text - Reduced */
    .profile-text {
        max-width: 100px;
    }

    .profile-name {
        font-size: 0.75rem;
        color: #2d3748;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 600;
    }

    .profile-role {
        font-size: 0.6rem;
        color: #718096;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (min-width: 768px) {
        .profile-text {
            max-width: 120px;
        }

        .profile-name {
            font-size: 0.85rem;
        }

        .profile-role {
            font-size: 0.7rem;
        }
    }

    /* Hover Effects */
    .school-logo-img:hover {
        transform: scale(1.03);
        border-color: #667eea;
    }

    .school-name-link:hover {
        color: #667eea !important;
    }

    .profile-avatar:hover {
        transform: scale(1.03);
        border-color: #764ba2 !important;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
    }

    /* Dropdown Menu Styling */
    .dropdown-menu {
        animation: dropdownFade 0.2s ease;
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        padding: 0.4rem 0;
        min-width: 180px;
    }

    .dropdown-item {
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .dropdown-item i {
        font-size: 0.9rem;
        width: 22px;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateX(3px);
    }

    /* Sign Out Button */
    .signout-btn {
        transition: all 0.3s ease !important;
        border-radius: 0 !important;
        cursor: pointer;
    }

    .signout-btn:hover {
        background: #fee2e2 !important;
    }

    .signout-btn:hover span,
    .signout-btn:hover i {
        color: #dc2626 !important;
    }

    /* Animation */
    @keyframes dropdownFade {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Fix for Bootstrap dropdown toggle button */
    .btn:focus,
    .btn:active {
        outline: none !important;
        box-shadow: none !important;
    }

    .dropdown-toggle::after {
        display: none;
    }

    /* Container padding */
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    @media (min-width: 768px) {
        .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    }

    /* Add smooth scrolling for the whole page */
    html {
        scroll-behavior: smooth;
    }

    /* Ensure content doesn't hide under fixed header */
    body {
        padding-top: 0;
        margin: 0;
    }
</style>

<script>
    // SweetAlert2 Signout Confirmation
    function confirmSignout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to sign out of the system!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true,
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            },
            customClass: {
                popup: 'swal-popup-custom',
                title: 'swal-title-custom',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please, just wait!',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the logout form
                document.getElementById('logout-form').submit();
            }
        });
    }

    // Prevent dropdown from closing when clicking signout
    document.addEventListener('click', function(e) {
        const signoutBtn = e.target.closest('.signout-btn');
        if (signoutBtn) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
</script>

<!-- Add Animate.css for better animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- Custom SweetAlert2 Styles -->
<style>
    .swal-popup-custom {
        border-radius: 15px !important;
        padding: 1.5rem !important;
    }

    .swal-title-custom {
        color: #2d3748 !important;
        font-size: 1.3rem !important;
        font-weight: 600 !important;
    }

    .swal-confirm-btn {
        border-radius: 30px !important;
        padding: 0.5rem 1.8rem !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        box-shadow: 0 2px 8px rgba(231, 74, 59, 0.2) !important;
    }

    .swal-confirm-btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(231, 74, 59, 0.3) !important;
    }

    .swal-cancel-btn {
        border-radius: 30px !important;
        padding: 0.5rem 1.8rem !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
</style>
