(function ($) {
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
    Sidebar menu (MetisMenu)
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const menu = document.getElementById('menu');
        if (menu) {
            $('#menu').metisMenu();
        }
    });

    /*================================
    Slimscroll activation
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
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
                $(element.selector).slimScroll({
                    height: element.height
                });
            }
        });
    });

    /*================================
    Service Worker Registration
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker
                .register('/service-worker.js')
                .then(() => console.log('Service Worker Registered'))
                .catch((error) => console.error('Service Worker Registration Failed:', error));
        }
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
    DataTable Initialization
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const dataTables = ['#dataTable', '#dataTable2', '#dataTable3'];
        dataTables.forEach((table) => {
            const el = document.querySelector(table);
            if (el) {
                $(table).DataTable({
                    responsive: true
                });
            }
        });
    });

    /*================================
    Slicknav Mobile Menu
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const navMenu = document.querySelector('ul#nav_menu');
        if (navMenu) {
            $('ul#nav_menu').slicknav({
                prependTo: '#mobile_menu'
            });
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
                settingsBtn.classList.toggle('active');
            });
        }
    });

    /*================================
    Owl Carousel Initialization
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const testimonialCarousel = document.querySelector('.testimonial-carousel');
        if (testimonialCarousel) {
            $('.testimonial-carousel').owlCarousel({
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
    PWA Installation Prompt
    ==================================*/
    document.addEventListener('DOMContentLoaded', () => {
        const installButton = createInstallButton();
        let deferredPrompt;

        // PWA Installation Prompt
        if ('beforeinstallprompt' in window) {
            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault(); // Prevent the default browser prompt
                deferredPrompt = event;
                showInstallButton(installButton);

                installButton.addEventListener('click', handleInstallButtonClick(deferredPrompt, installButton), { once: true });
            });
        }

        // iOS Installation Prompt
        if (isIosDevice() && !isAppStandalone()) {
            alert("For a better experience, install ShuleApp: Click 'Share' → 'Add to Home Screen'.");
        }

        // Check if the app is already installed
        if (isAppStandalone()) {
            installButton.style.display = 'none'; // Hide install button if app is already installed
        }

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            registerServiceWorker();
        }
    });

    // Function to create the install button
    function createInstallButton() {
        const button = document.createElement('button');
        button.id = 'install-button';
        button.textContent = 'Install App';
        button.style.display = 'none';
        button.style.position = 'fixed';
        button.style.bottom = '50px';
        button.style.right = '20px';
        button.style.padding = '10px 20px';
        button.style.backgroundColor = '#007bff';
        button.style.color = '#fff';
        button.style.border = 'none';
        button.style.borderRadius = '5px';
        button.style.cursor = 'pointer';
        button.style.zIndex = '1000';
        document.body.appendChild(button);

        return button;
    }

    // Function to show the install button
    function showInstallButton(installButton) {
        installButton.style.display = 'block';
    }

    // Handler for the install button click event
    function handleInstallButtonClick(deferredPrompt, installButton) {
        return async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const choiceResult = await deferredPrompt.userChoice;
                console.log('User choice:', choiceResult.outcome === 'accepted' ? 'User accepted the install prompt.' : 'User dismissed the install prompt.');

                deferredPrompt = null;
                installButton.style.display = 'none'; // Hide the button after usage
            }
        };
    }

    // Check if the device is iOS
    function isIosDevice() {
        return /iPhone|iPad/i.test(navigator.userAgent);
    }

    // Check if the app is running in standalone mode (installed)
    function isAppStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    }

    // Service Worker registration and updates
    function registerServiceWorker() {
        navigator.serviceWorker.register('/service-worker.js').then((registration) => {
            console.log('Service Worker registered successfully:', registration);

            // Check for updates every 30 seconds
            setInterval(() => {
                registration.update();
            }, 30000);

            // Ensure updates are checked for standalone apps
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage('checkForUpdate');
            }
        }).catch((err) => {
            console.error('Error during service worker registration:', err);
        });

        // Listen for new service worker activation
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            setTimeout(() => {
                alert('New updates are available for ShuleApp!');
                location.reload(); // Refresh the page automatically to activate the new service worker
            }, 1000);
        });
    }


    })(jQuery);
