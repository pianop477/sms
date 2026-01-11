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
        let deferredPrompt;
        const installButton = createInstallButton();

        // Angalia kama ni iOS na haijawekwa kama PWA
        if (isIosDevice() && !isAppStandalone()) {
            showIosInstallMessage();
        }

        // PWA Installation Prompt kwa Android/Chrome
        window.addEventListener('beforeinstallprompt', (event) => {
            console.log('beforeinstallprompt fired');
            event.preventDefault();
            deferredPrompt = event;
            showInstallButton(installButton);
        });

        installButton.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const choiceResult = await deferredPrompt.userChoice;
                if (choiceResult.outcome === 'accepted') {
                    console.log('User installed the app');
                    installButton.style.display = 'none';
                }
                deferredPrompt = null;
            }
        });

        // Kama tayari app imewekwa, ficha button
        if (isAppStandalone()) {
            installButton.style.display = 'none';
        }

        // Sajili Service Worker
        if ('serviceWorker' in navigator) {
            registerServiceWorker();
        }
    });

    // Fungua button ya Install
    function createInstallButton() {
        let button = document.getElementById('install-button');
        if (button) {
            button.remove();
        }

        button = document.createElement('button');
        button.id = 'install-button';
        button.textContent = 'Install App';
        button.style.display = 'none';
        button.style.position = 'fixed';
        button.style.bottom = '50px';
        button.style.right = '20px';
        button.style.padding = '10px 20px';
        button.style.backgroundColor = '#4CAF50';
        button.style.color = '#fff';
        button.style.border = 'none';
        button.style.borderRadius = '5px';
        button.style.cursor = 'pointer';
        button.style.zIndex = '1000';
        button.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        document.body.appendChild(button);
        return button;
    }

    // Onyesha button ya install
    function showInstallButton(button) {
        button.style.display = 'block';
    }

    // Onyesha ujumbe kwa iOS
    function showIosInstallMessage() {
        const message = document.createElement('div');
        message.textContent = "For a better experience, install ShuleApp: Click 'Share' → 'Add to Home Screen'.";
        message.style.position = 'fixed';
        message.style.bottom = '50px';
        message.style.left = '50%';
        message.style.transform = 'translateX(-50%)';
        message.style.backgroundColor = '#ffc107';
        message.style.color = '#000';
        message.style.padding = '10px';
        message.style.borderRadius = '5px';
        message.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        message.style.zIndex = '1000';
        document.body.appendChild(message);

        // Ujumbe ujifiche baada ya sekunde 10
        setTimeout(() => {
            message.remove();
        }, 10000);
    }

    // Angalia kama ni iOS
    function isIosDevice() {
        return /iPhone|iPad/i.test(navigator.userAgent);
    }

    // Angalia kama app imefunguliwa kama PWA
    function isAppStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    }

    // Sajili Service Worker
    let newWorker;
    function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) return;

        navigator.serviceWorker.register('/service-worker.js')
            .then((registration) => {
                console.log('Service Worker registered:', registration);

                // 1️⃣ If there's already a waiting worker → show update UI
                if (registration.waiting) {
                    showUpdateUI(registration.waiting);
                }

                // 2️⃣ Listen for new SW installation
                registration.addEventListener('updatefound', () => {
                    newWorker = registration.installing;

                    newWorker.addEventListener('statechange', () => {
                        if (
                            newWorker.state === 'installed' &&
                            navigator.serviceWorker.controller
                        ) {
                            showUpdateUI(newWorker);
                        }
                    });
                });
            })
            .catch((err) => {
                console.error('SW registration failed:', err);
            });

        // 3️⃣ Reload page once new SW takes control
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            window.location.reload();
        });
    }

    function showUpdateUI(worker) {
        // Avoid duplicate prompts
        if (document.getElementById('update-toast')) return;

        const toast = document.createElement('div');
        toast.id = 'update-toast';
        toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #0d6efd;
        color: #fff;
        padding: 14px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,.2);
        z-index: 9999;
        font-family: sans-serif;
    `;

        toast.innerHTML = `
        <span>New update available for <b>ShuleApp</b></span>
        <button id="update-btn" style="
            margin-left: 15px;
            padding: 6px 12px;
            border: none;
            background: #fff;
            color: #0d6efd;
            border-radius: 4px;
            cursor: pointer;
        ">Update</button>
    `;

        document.body.appendChild(toast);

        document.getElementById('update-btn').onclick = () => {
            worker.postMessage({ type: 'SKIP_WAITING' });
        };
    }

})(jQuery);
