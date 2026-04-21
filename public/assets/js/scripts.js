(function () {
    "use strict";

    /*================================
    Preloader
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 300);
        }
    });

    /*================================
    Sidebar collapsing
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const pageContainer = document.querySelector('.page-container');
        const navBtn = document.querySelector('.nav-btn');

        if (window.innerWidth <= 1364 && pageContainer) {
            pageContainer.classList.add('sbar_collapsed');
        }

        if (navBtn && pageContainer) {
            navBtn.addEventListener('click', () => {
                pageContainer.classList.toggle('sbar_collapsed');
            });
        }
    });

    /*================================
    Footer resizer
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const resizeFooter = () => {
            const windowHeight = window.innerHeight > 0 ? window.innerHeight : screen.height;
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                const height = windowHeight - 67;
                mainContent.style.minHeight = (height > 67 ? height : 67) + 'px';
            }
        };

        resizeFooter();
        window.addEventListener('resize', resizeFooter);
    });

    /*================================
    Sidebar menu (MetisMenu) - Pure JS version
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const menu = document.getElementById('menu');
        if (menu && typeof MetisMenu !== 'undefined') {
            new MetisMenu(menu);
        } else if (menu) {
            console.warn('MetisMenu not loaded');
        }
    });

    /*================================
    Slimscroll activation - Pure JS
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        // SlimScroll ni jQuery plugin, tumia CSS overflow au custom scroll
        const slimScrollElements = [
            { selector: '.menu-inner', height: 'auto' },
            { selector: '.nofity-list', height: '435px' },
            { selector: '.timeline-area', height: '500px' },
            { selector: '.recent-activity', height: 'calc(100vh - 114px)' },
            { selector: '.settings-list', height: 'calc(100vh - 158px)' }
        ];

        slimScrollElements.forEach((element) => {
            const el = document.querySelector(element.selector);
            if (el) {
                // Badala ya slimScroll, tumia CSS overflow
                el.style.overflowY = 'auto';
                if (element.height !== 'auto') {
                    el.style.height = element.height;
                }
            }
        });
    });

    /*================================
    Sticky Header
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const stickyHeader = document.getElementById('sticky-header');
        if (stickyHeader) {
            window.addEventListener('scroll', () => {
                const scroll = window.scrollY;
                if (scroll > 1) {
                    stickyHeader.classList.add('sticky-menu');
                } else {
                    stickyHeader.classList.remove('sticky-menu');
                }
            });
        }
    });

    /*================================
    Bootstrap Form Validation
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, (form) => {
            form.addEventListener('submit', (event) => {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });

    /*================================
    DataTable Initialization - Check if jQuery DataTable exists
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        // Check if jQuery and DataTable are available
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
            const dataTables = ['#dataTable', '#dataTable2', '#dataTable3'];
            dataTables.forEach((table) => {
                const el = document.querySelector(table);
                if (el) {
                    jQuery(table).DataTable({
                        responsive: true
                    });
                }
            });
        } else {
            console.warn('jQuery or DataTable not loaded');
        }
    });

    /*================================
    Slicknav Mobile Menu - Check jQuery
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const navMenu = document.querySelector('ul#nav_menu');
        if (navMenu && typeof jQuery !== 'undefined' && jQuery.fn.slicknav) {
            jQuery('ul#nav_menu').slicknav({
                prependTo: '#mobile_menu'
            });
        } else if (navMenu) {
            console.warn('jQuery or SlickNav not loaded');
        }
    });

    /*================================
    Login Form Focus
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const formInputs = document.querySelectorAll('.form-gp input');
        formInputs.forEach((input) => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            input.addEventListener('focusout', () => {
                if (input.value.length === 0) {
                    input.parentElement.classList.remove('focused');
                }
            });
        });
    });

    /*================================
    Slider Area Background Setting
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const settingsBtn = document.querySelector('.settings-btn');
        const offsetClose = document.querySelector('.offset-close');
        const offsetArea = document.querySelector('.offset-area');

        if (settingsBtn && offsetArea) {
            settingsBtn.addEventListener('click', () => {
                offsetArea.classList.toggle('show_hide');
                settingsBtn.classList.toggle('active');
            });
        }

        if (offsetClose && offsetArea) {
            offsetClose.addEventListener('click', () => {
                offsetArea.classList.toggle('show_hide');
                if (settingsBtn) settingsBtn.classList.toggle('active');
            });
        }
    });

    /*================================
    Owl Carousel Initialization - Check jQuery
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const testimonialCarousel = document.querySelector('.testimonial-carousel');
        if (testimonialCarousel && typeof jQuery !== 'undefined' && jQuery.fn.owlCarousel) {
            jQuery('.testimonial-carousel').owlCarousel({
                margin: 50,
                loop: true,
                autoplay: false,
                nav: false,
                dots: true,
                responsive: {
                    0: { items: 1 },
                    450: { items: 1 },
                    768: { items: 2 },
                    1000: { items: 2 },
                    1360: { items: 1 },
                    1600: { items: 2 }
                }
            });
        } else if (testimonialCarousel) {
            console.warn('jQuery or OwlCarousel not loaded');
        }
    });

    /*================================
    Fullscreen Page
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const fullViewBtn = document.getElementById('full-view');
        const fullViewExitBtn = document.getElementById('full-view-exit');

        if (fullViewBtn && fullViewExitBtn) {
            fullViewBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                }
                document.body.classList.add('expanded');
            });

            fullViewExitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                document.body.classList.remove('expanded');
            });
        }
    });

    /*================================
    Loader
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.getElementById('loading-overlay');
        if (loader) {
            document.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    loader.style.display = 'flex';
                });
            });

            window.addEventListener('load', () => {
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 1000);
            });
        }
    });

    /*================================
     PWA - Dual Banner System
     Banner 1: Install App (Always visible if not installed)
     Banner 2: Update Available (Only when update exists, can be ignored)
 =================================*/
    (function () {
        // Check if running on Android
        const isAndroid = /android/i.test(navigator.userAgent);

        // Only run on Android devices
        if (!isAndroid) {
            console.log('PWA features only enabled on Android devices');
            return;
        }

        if (!('serviceWorker' in navigator)) {
            console.log('Service Worker not supported');
            return;
        }

        let deferredPrompt;
        let refreshing = false;
        let updateToast = null;
        let installBanner = null;

        /* =============================
           BANNER 1: INSTALL APP (Always visible if not installed)
        ============================= */
        function showInstallBanner() {
            // Don't show if already installed
            if (window.matchMedia('(display-mode: standalone)').matches) {
                console.log('App already installed, hiding install banner');
                return;
            }

            // Don't show if banner already exists
            if (document.getElementById('pwa-install-banner')) {
                return;
            }

            // Check if user dismissed banner before (store in localStorage)
            const bannerDismissed = localStorage.getItem('pwa_install_banner_dismissed');
            if (bannerDismissed === 'true') {
                console.log('User previously dismissed install banner');
                return;
            }

            installBanner = document.createElement('div');
            installBanner.id = 'pwa-install-banner';
            installBanner.innerHTML = `
            <div style="
                background: linear-gradient(135deg, #4361ee, #3a0ca3);
                color: white;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                min-width: 280px;
            ">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 28px;">📱</span>
                    <div>
                        <div style="font-weight: bold; font-size: 14px;">Install ShuleApp</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button id="pwa-install-btn" style="
                        background: white;
                        color: #4361ee;
                        border: none;
                        padding: 6px 16px;
                        border-radius: 25px;
                        font-weight: bold;
                        cursor: pointer;
                        font-size: 12px;
                    ">Install</button>
                    <button id="pwa-dismiss-btn" style="
                        background: transparent;
                        color: white;
                        border: 1px solid white;
                        padding: 6px 12px;
                        border-radius: 25px;
                        cursor: pointer;
                        font-size: 12px;
                    ">Later</button>
                </div>
            </div>
        `;

            installBanner.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            animation: slideUp 0.3s ease-out;
        `;

            // Add animation styles
            if (!document.getElementById('pwa-banner-styles')) {
                const style = document.createElement('style');
                style.id = 'pwa-banner-styles';
                style.textContent = `
                @keyframes slideUp {
                    from {
                        transform: translateX(-50%) translateY(100px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(-50%) translateY(0);
                        opacity: 1;
                    }
                }
                @keyframes slideDown {
                    from {
                        transform: translateX(-50%) translateY(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(-50%) translateY(100px);
                        opacity: 0;
                    }
                }
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
                document.head.appendChild(style);
            }

            document.body.appendChild(installBanner);

            // Install button handler
            document.getElementById('pwa-install-btn').addEventListener('click', async () => {
                if (!deferredPrompt) {
                    console.log('No install prompt available');
                    return;
                }

                try {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`Install prompt outcome: ${outcome}`);

                    if (outcome === 'accepted') {
                        console.log('User accepted installation');
                        hideInstallBanner();
                    }
                    deferredPrompt = null;
                } catch (error) {
                    console.error('Install prompt error:', error);
                }
            });

            // Dismiss button handler
            document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
                hideInstallBanner();
                // Store dismissal for 7 days
                localStorage.setItem('pwa_install_banner_dismissed', 'true');
                setTimeout(() => {
                    localStorage.removeItem('pwa_install_banner_dismissed');
                }, 7 * 24 * 60 * 60 * 1000); // 7 days
            });
        }

        function hideInstallBanner() {
            if (installBanner) {
                installBanner.style.animation = 'slideDown 0.3s ease-out';
                setTimeout(() => {
                    if (installBanner && installBanner.remove) {
                        installBanner.remove();
                    }
                }, 300);
            }
        }

        /* =============================
           BANNER 2: UPDATE AVAILABLE (Can be ignored)
        ============================= */
        function showUpdateBanner(worker) {
            // Don't show if already showing
            if (document.getElementById('pwa-update-banner')) {
                return;
            }

            const updateBanner = document.createElement('div');
            updateBanner.id = 'pwa-update-banner';
            updateBanner.innerHTML = `
            <div style="
                background: #333;
                color: white;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                min-width: 280px;
                border-left: 4px solid #4CAF50;
            ">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 24px;">🔄</span>
                    <div>
                        <div style="font-weight: bold; font-size: 13px;">Update Available</div>
                        <div style="font-size: 10px; opacity: 0.8;">New version ready to install</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button id="pwa-update-btn" style="
                        background: #4CAF50;
                        color: white;
                        border: none;
                        padding: 6px 16px;
                        border-radius: 25px;
                        font-weight: bold;
                        cursor: pointer;
                        font-size: 12px;
                    ">Update</button>
                    <button id="pwa-update-dismiss-btn" style="
                        background: transparent;
                        color: white;
                        border: 1px solid #666;
                        padding: 6px 12px;
                        border-radius: 25px;
                        cursor: pointer;
                        font-size: 12px;
                    ">Later</button>
                </div>
            </div>
        `;

            updateBanner.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 10001;
            animation: slideInRight 0.3s ease-out;
        `;

            document.body.appendChild(updateBanner);

            // Update button handler
            document.getElementById('pwa-update-btn').addEventListener('click', () => {
                updateBanner.remove();
                worker.postMessage({ type: 'SKIP_WAITING' });
            });

            // Dismiss button handler
            document.getElementById('pwa-update-dismiss-btn').addEventListener('click', () => {
                updateBanner.remove();
                // Store that user dismissed this update version
                localStorage.setItem('pwa_update_dismissed_' + APP_VERSION, 'true');
            });

            // Auto-hide after 30 seconds if not interacted
            setTimeout(() => {
                if (document.getElementById('pwa-update-banner')) {
                    updateBanner.remove();
                }
            }, 30000);
        }

        /* =============================
           REGISTER SERVICE WORKER
        ============================= */
        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register('/service-worker.js?v=2026.04.21');
                console.log('Service Worker registered:', reg.scope);

                // Check for waiting worker (update available)
                if (reg.waiting) {
                    const updateDismissed = localStorage.getItem('pwa_update_dismissed_' + APP_VERSION);
                    if (!updateDismissed) {
                        showUpdateBanner(reg.waiting);
                    }
                }

                // Listen for new workers
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    console.log('New Service Worker found');

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('Update available');
                            const updateDismissed = localStorage.getItem('pwa_update_dismissed_' + APP_VERSION);
                            if (!updateDismissed) {
                                showUpdateBanner(newWorker);
                            }
                        }
                    });
                });

                /* REGISTER BACKGROUND SYNC */
                if ('sync' in reg) {
                    try {
                        await reg.sync.register('sync-tokens');
                        console.log('Background sync registered');
                    } catch (e) {
                        console.log('Sync registration failed:', e);
                    }
                }

                /* CHECK FOR UPDATES every 30 minutes */
                setInterval(() => reg.update(), 30 * 60 * 1000);

            } catch (error) {
                console.error('Service worker registration failed:', error);
            }
        });

        /* =============================
           HANDLE CONTROLLER CHANGE
        ============================= */
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            console.log('Controller changed, reloading...');
            window.location.reload();
        });

        /* =============================
           PWA INSTALL PROMPT
        ============================= */
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('beforeinstallprompt event fired');
            e.preventDefault();
            deferredPrompt = e;

            // Show install banner after a short delay
            setTimeout(() => {
                showInstallBanner();
            }, 1000);
        });

        /* =============================
           CHECK IF ALREADY INSTALLED
        ============================= */
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed successfully');
            hideInstallBanner();
            deferredPrompt = null;

            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success('ShuleApp installed successfully!');
            }
        });

        /* =============================
           HIDE INSTALL BANNER IF ALREADY INSTALLED
        ============================= */
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('App is already installed (standalone mode)');
            // Banner will not show
        }

        /* =============================
           RESET BANNER PREFERENCES (For testing)
        ============================= */
        window.resetPWA = function () {
            localStorage.removeItem('pwa_install_banner_dismissed');
            localStorage.removeItem('pwa_update_dismissed_' + APP_VERSION);
            console.log('PWA preferences reset');
            location.reload();
        };
    })();

})(); // Remove jQuery dependency
