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
        console.log('DOMContentLoaded event triggered');

        let deferredPrompt;
        const installButton = document.createElement('button');
        installButton.id = 'install-button';
        installButton.textContent = 'Install App';
        installButton.style.display = 'none'; // Ficha kitufe mwanzoni
        installButton.style.position = 'fixed'; // Weka kitufe kwenye eneo maalum
        installButton.style.bottom = '50px'; // Chini ya ukurasa
        installButton.style.right = '20px'; // Upande wa kulia
        installButton.style.padding = '10px 20px'; // Padding ya kitufe
        installButton.style.backgroundColor = '#007bff'; // Rangi ya kitufe
        installButton.style.color = '#fff'; // Rangi ya maandishi
        installButton.style.border = 'none'; // Ondoa mpaka
        installButton.style.borderRadius = '5px'; // Pinda pembe za kitufe
        installButton.style.cursor = 'pointer'; // Badilisha mwonekano wa kielelezo
        installButton.style.zIndex = '1000'; // Hakikisha kitufe kiko juu ya vitu vingine

        // Ongeza kitufe kwenye DOM
        document.body.appendChild(installButton);
        console.log('Install button created and added to DOM:', installButton); // Debugging

        // Sikiliza kabla ya install prompt
        window.addEventListener('beforeinstallprompt', (event) => {
            console.log('beforeinstallprompt event triggered'); // Debugging
            event.preventDefault();
            deferredPrompt = event;
            installButton.style.display = 'block'; // Onyesha kitufe
            console.log('Install button displayed'); // Debugging

            // Ongeza event listener kwa kitufe
            installButton.addEventListener('click', async () => {
                console.log('Install button clicked'); // Debugging
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const choiceResult = await deferredPrompt.userChoice;
                    console.log('User choice:', choiceResult); // Debugging

                    if (choiceResult.outcome === 'accepted') {
                        console.log('Mtumiaji alikubali kufunga programu.');
                    } else {
                        console.log('Mtumiaji alikataa kufunga programu.');
                    }

                    deferredPrompt = null;
                    installButton.style.display = 'none'; // Ficha kitufe baada ya matumizi
                    console.log('Install button hidden after use'); // Debugging
                }
            }, { once: true }); // Tumia { once: true } ili kuzuia matumizi ya mara kwa mara
        });

        // Ficha kitufe ikiwa programu tayari imefungwa
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            console.log('Already installed: hiding button');
            installButton.style.display = 'none'; // Ikiwa tayari ni app, ficha kitufe
        } else {
            console.log('App haijafungwa bado');
        }

        // Angalia kama PWA inasaidiwa
        if ('beforeinstallprompt' in window) {
            console.log('PWA inasaidiwa kwenye browser hii.');
        } else {
            console.log('PWA haisaidiiwi kwenye browser hii.');
        }

        /*================================
         iOS Installation Prompt
        ==================================*/
        document.addEventListener('DOMContentLoaded', () => {
            if (/iPhone|iPad/i.test(navigator.userAgent)) {
                if (!window.matchMedia('(display-mode: standalone)').matches) {
                    alert("For a better experience, install ShuleApp: Click 'Share' → 'Add to Home Screen'.");
                }
            }
        });
    });

    //allow automatic updates
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js').then((registration) => {
            console.log('Service Worker registered:', registration);

            // Cheki updates kila baada ya sekunde 30
            setInterval(() => {
                registration.update();
            }, 30000);

            // Hakikisha standalone app pia inapata update
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage('checkForUpdate');
            }
        });

        // Sikiliza kama kuna service worker mpya inakua activated
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            setTimeout(() => {
                alert('New Updates for ShuleApp is Available');
                location.reload(); // Refresh page automatically
            }, 1000);
        });
    }

    })(jQuery);
