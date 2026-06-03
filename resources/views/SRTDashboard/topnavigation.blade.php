<div class="dashboard-header bg-white shadow-sm">
    <div class="container-fluid px-2 px-md-3">
        <div class="d-flex align-items-center justify-content-between py-1 py-md-2">
            {{-- Logo & School Info Section --}}
            <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                @php
                    use Illuminate\Support\Facades\Storage;

                    $school = Auth::user()->school;
                    $schoolName = $school && $school->school_name
                        ? $school->school_name
                        : 'ShuleApp - Admin Panel';

                    // Safe logo path with fallback
                    $logoFile = 'new_logo.png';
                    if ($school && $school->logo && !empty($school->logo)) {
                        $logoFile = $school->logo;
                    }

                    // Check if file actually exists in storage
                    $logoExists = Storage::disk('public')->exists('logo/' . $logoFile);
                    $logoPath = $logoExists
                        ? Storage::url('logo/' . $logoFile)
                        : Storage::url('logo/new_logo.png');

                    // Fix for usertype display - use switch for better readability
                    $usertype = Auth::user()->usertype;
                    switch($usertype) {
                        case 1: $roleDisplay = 'Administrator'; break;
                        case 2: $roleDisplay = 'Manager'; break;
                        case 3: $roleDisplay = 'Teacher'; break;
                        case 4: $roleDisplay = 'Parent'; break;
                        case 5: $roleDisplay = 'Accountant'; break;
                        default: $roleDisplay = 'Staff';
                    }

                    // Profile image handling
                    $imageName = Auth::user()->image;
                    $defaultImage = Auth::user()->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg';

                    $profileImageExists = $imageName && Storage::disk('public')->exists('profile/' . $imageName);
                    $avatarImage = $profileImageExists
                        ? Storage::url('profile/' . $imageName)
                        : Storage::url('profile/' . $defaultImage);
                @endphp

                <div class="brand-container d-flex align-items-center">
                    <img src="{{ $logoPath }}"
                         alt="{{ $schoolName }} Logo"
                         class="school-logo-img me-2"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='{{ Storage::url("logo/new_logo.png") }}';">

                    <div class="school-text-info">
                        <a href="{{ route('home') }}" class="school-name-link text-decoration-none fw-bold d-block" title="{{ $schoolName }}">
                            {{ Str::limit($schoolName, 40) }}
                        </a>
                        @if($school && $school->postal_address && $school->postal_name)
                            <span class="school-address-text d-none d-sm-block" title="{{ $school->postal_address }} - {{ $school->postal_name }}">
                                {{ Str::limit(strtoupper($school->postal_address . ' - ' . $school->postal_name), 50) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Profile & Actions Section --}}
            <div class="profile-actions d-flex align-items-center">
                <div class="profile-dropdown-container d-flex align-items-center">
                    {{-- Desktop Profile Info --}}
                    <div class="profile-text me-2 d-none d-sm-block">
                        <div class="profile-top-name fw-bold" title="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}">
                            Hi, {{ Str::limit(ucwords(strtolower(Auth::user()->first_name)), 15) }}
                        </div>
                        <div class="profile-role">
                            {{ $roleDisplay }}
                        </div>
                    </div>

                    {{-- Profile Image with Dropdown Toggle --}}
                    <div class="dropdown">
                        <button class="btn p-0 border-0 bg-transparent d-flex align-items-center"
                                type="button"
                                id="userMenuDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-label="User menu"
                                style="cursor: pointer;">
                            <img src="{{ $avatarImage }}"
                                 alt="Profile"
                                 class="profile-avatar rounded-circle"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ Storage::url("profile/avatar.jpg") }}';">
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm border-0" aria-labelledby="userMenuDropdown">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('show.profile') }}">
                                    <i class="fas fa-user me-2" style="color: #667eea; width: 18px;"></i>
                                    My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('change.password') }}">
                                    <i class="fas fa-key me-2" style="color: #667eea; width: 18px;"></i>
                                    Change Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button type="button"
                                        class="dropdown-item py-2 signout-btn"
                                        onclick="confirmSignout()"
                                        style="background: none; border: none; width: 100%; text-align: left;">
                                    <i class="fas fa-sign-out-alt me-2" style="color: #e74a3b; width: 18px;"></i>
                                    <span style="color: #e74a3b; font-weight: 600;">Sign Out</span>
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

<!-- Styles -->
<style>
    /* Header Styles - Sticky */
    .dashboard-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(200, 129, 42, 0.2);
        background: #ffffff;
        width: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    /* Logo Image - Fixed sizes (no percentages) */
    .school-logo-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 50%;
        border: 1.5px solid #e9ecef;
        padding: 1px;
        background-color: #f8f9fa;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .school-logo-img:hover {
        transform: scale(1.02);
        border-color: #667eea;
    }

    /* Profile Avatar */
    .profile-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #667eea;
        padding: 2px;
        background-color: #fff;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .profile-avatar:hover {
        transform: scale(1.02);
        border-color: #764ba2;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .school-logo-img {
            width: 50px;
            height: 50px;
        }
        .profile-avatar {
            width: 40px;
            height: 40px;
        }
    }

    @media (min-width: 768px) {
        .school-logo-img {
            width: 55px;
            height: 55px;
        }
        .profile-avatar {
            width: 45px;
            height: 45px;
        }
    }

    @media (min-width: 992px) {
        .school-logo-img {
            width: 65px;
            height: 65px;
        }
        .profile-avatar {
            width: 50px;
            height: 50px;
        }
    }

    /* School Name Styling */
    .school-name-link {
        font-size: 0.85rem;
        color: #2d3748;
        line-height: 1.3;
        text-transform: uppercase;
        font-weight: 600;
        transition: color 0.2s ease;
        display: inline-block;
    }

    .school-name-link:hover {
        color: #667eea;
    }

    @media (min-width: 768px) {
        .school-name-link {
            font-size: 1rem;
        }
    }

    /* School Address */
    .school-address-text {
        font-size: 0.7rem;
        color: #718096;
        line-height: 1.2;
    }

    @media (min-width: 768px) {
        .school-address-text {
            font-size: 0.7rem;
        }
    }

    /* Profile Text */
    .profile-text {
        text-align: right;
    }

    .profile-top-name {
        font-size: 0.85rem;
        color: #2d3748;
        font-weight: 600;
        line-height: 1.3;
    }

    .profile-role {
        font-size: 0.65rem;
        color: #718096;
        line-height: 1.2;
    }

    @media (min-width: 768px) {
        .profile-top-name {
            font-size: 0.9rem;
        }
        .profile-role {
            font-size: 0.7rem;
        }
    }

    /* Dropdown Menu */
    .dropdown-menu {
        animation: dropdownFade 0.2s ease;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 0;
        min-width: 200px;
    }

    .dropdown-item {
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .dropdown-item i {
        font-size: 0.85rem;
        width: 22px;
    }

    .dropdown-item:hover {
        background-color: #f8fafc;
        padding-left: 1.5rem;
    }

    .signout-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .signout-btn:hover {
        background-color: #fef2f2 !important;
    }

    .signout-btn:hover span,
    .signout-btn:hover i {
        color: #dc2626 !important;
    }

    /* Hide default dropdown toggle arrow */
    .dropdown-toggle::after {
        display: none;
    }

    .btn:focus,
    .btn:active {
        outline: none;
        box-shadow: none;
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

    /* Container spacing */
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    @media (min-width: 768px) {
        .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
    }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // SweetAlert2 Signout Confirmation
    function confirmSignout() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to sign out of the system.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Sign Out',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            backdrop: true,
            allowOutsideClick: false,
            customClass: {
                popup: 'swal-popup-custom',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Signing out...',
                    text: 'Please wait while we log you out.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the logout form after a tiny delay (for better UX)
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 300);
            }
        });
    }

    // Close dropdown when clicking outside (cleanup)
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.dropdown');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        if (dropdown && dropdownMenu && !dropdown.contains(event.target)) {
            const bootstrapDropdown = bootstrap.Dropdown.getInstance(document.querySelector('#userMenuDropdown'));
            if (bootstrapDropdown) {
                bootstrapDropdown.hide();
            }
        }
    });
</script>

<!-- Custom SweetAlert Styles -->
<style>
    .swal-popup-custom {
        border-radius: 16px !important;
        padding: 1.2rem !important;
    }

    .swal-confirm-btn {
        border-radius: 30px !important;
        padding: 0.5rem 1.8rem !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
        letter-spacing: 0.3px !important;
    }

    .swal-cancel-btn {
        border-radius: 30px !important;
        padding: 0.5rem 1.8rem !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
        letter-spacing: 0.3px !important;
    }
</style>
