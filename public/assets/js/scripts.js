(function() {
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
    PWA Service Worker + Update System
    =================================*/
    (function() {
        if (!('serviceWorker' in navigator)) return;

        let refreshing = false;

        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register('/service-worker.js?v=2026.04.10');

                if (reg.waiting) showUpdateUI(reg.waiting);

                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                showUpdateUI(newWorker);
                            }
                        });
                    }
                });

                /* 🔥 REGISTER BACKGROUND SYNC */
                if ('sync' in reg) {
                    try {
                        await reg.sync.register('sync-tokens');
                    } catch (e) {
                        console.log('Sync registration failed:', e);
                    }
                }

                /* 🔥 FORCE UPDATE CHECK */
                setInterval(() => reg.update(), 60000);
            } catch (error) {
                console.error('Service worker registration failed:', error);
            }
        });

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            window.location.reload();
        });

        function showUpdateUI(worker) {
            if (document.getElementById('update-toast')) return;

            const toast = document.createElement('div');
            toast.id = 'update-toast';
            toast.innerHTML = `
                <span>New update available</span>
                <button id="update-app-btn">Update</button>
            `;
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #333;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                z-index: 9999;
                display: flex;
                gap: 15px;
                align-items: center;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            `;

            document.body.appendChild(toast);

            document.getElementById('update-app-btn').onclick = () => {
                worker.postMessage({ type: 'SKIP_WAITING' });
            };
        }
    })();

})(); // Remove jQuery dependency
