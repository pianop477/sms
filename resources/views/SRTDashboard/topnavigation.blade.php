<div class="dashboard-header bg-white shadow-sm">
    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex align-items-center justify-content-between py-2 py-md-2">
            {{-- Logo & School Info Section --}}
            <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                @php
                    $schoolName = Auth::user()->school && Auth::user()->school->school_name
                                ? Auth::user()->school->school_name
                                : 'ShuleApp - Admin';

                    $logoPath = Auth::user()->school && Auth::user()->school->logo
                                ? url('storage/logo/' . Auth::user()->school->logo)
                                : url('storage/logo/new_logo.png');
                @endphp

                <div class="brand-container d-flex align-items-center">
                    <img src="{{ $logoPath }}"
                         alt="School Logo"
                         class="school-logo-img me-2 me-md-3"
                         style="width: 45px; height: 45px; object-fit: cover; border-radius: 12px; border: 2px solid #e9ecef; padding: 2px;">

                    <div class="school-text-info" style="max-width: 150px; overflow: hidden;">
                        <a href="{{ route('home') }}"
                           class="school-name-link text-decoration-none fw-bold d-block"
                           style="font-size: 0.9rem; color: #2d3748; line-height: 1.2; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $schoolName }}
                        </a>
                        @if(Auth::user()->school && Auth::user()->school->address)
                        <span class="school-address-text d-none d-sm-block"
                              style="font-size: 0.7rem; color: #718096; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ Auth::user()->school->address }}
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
                @endphp

                <div class="profile-dropdown-container d-flex align-items-center">
                    {{-- Desktop Profile Info --}}
                    <div class="profile-text me-2 d-none d-sm-block" style="max-width: 120px;">
                        <div class="profile-name fw-bold" style="font-size: 0.85rem; color: #2d3748; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Hi, {{ ucwords(strtolower(Auth::user()->first_name)) }}
                        </div>
                        <div class="profile-role" style="font-size: 0.7rem; color: #718096; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ ucwords(str_replace('_', ' ', Auth::user()->role ?? 'user')) }}
                        </div>
                    </div>

                    {{-- Profile Image with Dropdown Toggle --}}
                    <div class="dropdown">
                        <button class="btn p-0 border-0 bg-transparent d-flex align-items-center" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <img src="{{ $avatarImage }}"
                                 alt="Profile"
                                 class="profile-avatar rounded-circle"
                                 style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #667eea; padding: 2px;">
                            <i class="fas fa-chevron-down ms-1 d-none d-md-inline" style="font-size: 0.7rem; color: #718096;"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm border-0" style="border-radius: 12px; min-width: 200px; padding: 0.5rem 0;">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('show.profile') }}">
                                    <i class="ti-user me-2" style="color: #667eea;"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('change.password') }}">
                                    <i class="ti-key me-2" style="color: #667eea;"></i> Change Password
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
    /* Header Styles */
    .dashboard-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .school-logo-img {
        transition: all 0.3s ease;
    }

    .school-logo-img:hover {
        transform: scale(1.05);
        border-color: #667eea;
    }

    .school-name-link {
        transition: color 0.3s ease;
    }

    .school-name-link:hover {
        color: #667eea !important;
    }

    .profile-avatar {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        border-color: #764ba2 !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Dropdown Menu Styling */
    .dropdown-menu {
        animation: dropdownFade 0.2s ease;
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 0;
    }

    .dropdown-item {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .dropdown-item i {
        font-size: 1rem;
        width: 24px;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateX(5px);
    }

    /* Sign Out Button Styles */
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
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive Adjustments */
    @media (max-width: 375px) {
        .school-text-info {
            max-width: 120px !important;
        }

        .school-name-link {
            font-size: 0.8rem !important;
        }

        .profile-avatar {
            width: 35px !important;
            height: 35px !important;
        }
    }

    @media (min-width: 768px) {
        .school-logo-img {
            width: 50px !important;
            height: 50px !important;
        }

        .school-name-link {
            font-size: 1rem !important;
        }

        .school-address-text {
            font-size: 0.75rem !important;
        }

        .profile-avatar {
            width: 45px !important;
            height: 45px !important;
        }

        .profile-text {
            max-width: 150px !important;
        }

        .profile-name {
            font-size: 0.95rem !important;
        }

        .profile-role {
            font-size: 0.75rem !important;
        }
    }

    /* Fix for Bootstrap dropdown toggle button */
    .btn:focus,
    .btn:active {
        outline: none !important;
        box-shadow: none !important;
    }

    .dropdown-toggle::after {
        display: none; /* Hide default Bootstrap caret */
    }

    /* Container fluid padding adjustment */
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    @media (min-width: 768px) {
        .container-fluid {
            padding-left: 2rem;
            padding-right: 2rem;
        }
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

    // Optional: Add success message after logout (if you want to redirect with message)
    @if(session('status') == 'logged-out')
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: 'You have signed out successfully, welcome back later 🤩',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    });
    @endif

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

<!-- Optional: Custom SweetAlert2 Styles -->
<style>
    .swal-popup-custom {
        border-radius: 20px !important;
        padding: 2rem !important;
    }

    .swal-title-custom {
        color: #2d3748 !important;
        font-size: 1.5rem !important;
        font-weight: 700 !important;
    }

    .swal-confirm-btn {
        border-radius: 50px !important;
        padding: 0.6rem 2rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        box-shadow: 0 4px 10px rgba(231, 74, 59, 0.3) !important;
    }

    .swal-confirm-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 15px rgba(231, 74, 59, 0.4) !important;
    }

    .swal-cancel-btn {
        border-radius: 50px !important;
        padding: 0.6rem 2rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
</style>
