
(function($) {
    "use strict";

    /*================================
    Preloader
    ==================================*/

    var preloader = $('#preloader');
    $(window).on('load', function() {
        setTimeout(function() {
            preloader.fadeOut('slow', function() { $(this).remove(); });
        }, 300)
    });

    /*================================
    sidebar collapsing
    ==================================*/
    if (window.innerWidth <= 1364) {
        $('.page-container').addClass('sbar_collapsed');
    }
    $('.nav-btn').on('click', function() {
        $('.page-container').toggleClass('sbar_collapsed');
    });

    /*================================
    Start Footer resizer
    ==================================*/
    var e = function() {
        var e = (window.innerHeight > 0 ? window.innerHeight : this.screen.height) - 5;
        (e -= 67) < 1 && (e = 1), e > 67 && $(".main-content").css("min-height", e + "px")
    };
    $(window).ready(e), $(window).on("resize", e);

    /*================================
    sidebar menu
    ==================================*/
    $("#menu").metisMenu();

    /*================================
    slimscroll activation
    ==================================*/
    $('.menu-inner').slimScroll({
        height: 'auto'
    });
    $('.nofity-list').slimScroll({
        height: '435px'
    });
    $('.timeline-area').slimScroll({
        height: '500px'
    });
    $('.recent-activity').slimScroll({
        height: 'calc(100vh - 114px)'
    });
    $('.settings-list').slimScroll({
        height: 'calc(100vh - 158px)'
    });

    /*==================================
    script for service worker
    ===================================*/
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
        .then(() => console.log('Service Worker Registered'));
    }


    /*=========================================
    show loader
    ==========================================*/
    // function showLoading() {
    //     document.getElementById("loading-bar").style.display = "block";
    // }
    // function hideLoading() {
    //     document.getElementById("loading-bar").style.display = "none";
    // }

    // document.addEventListener("click", function() {
    //     showLoading();
    //     setTimeout(hideLoading, 2000); // Simulate loading time
    // });

    /*================================
    stickey Header
    ==================================*/
    $(window).on('scroll', function() {
        var scroll = $(window).scrollTop(),
            mainHeader = $('#sticky-header'),
            mainHeaderHeight = mainHeader.innerHeight();

        // console.log(mainHeader.innerHeight());
        if (scroll > 1) {
            $("#sticky-header").addClass("sticky-menu");
        } else {
            $("#sticky-header").removeClass("sticky-menu");
        }
    });

    /*================================
    form bootstrap validation
    ==================================*/
    $('[data-toggle="popover"]').popover()

    /*------------- Start form Validation -------------*/
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);

    /*================================
    datatable active
    ==================================*/
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            responsive: true
        });
    }
    if ($('#dataTable2').length) {
        $('#dataTable2').DataTable({
            responsive: true
        });
    }
    if ($('#dataTable3').length) {
        $('#dataTable3').DataTable({
            responsive: true
        });
    }


    /*================================
    Slicknav mobile menu
    ==================================*/
    $('ul#nav_menu').slicknav({
        prependTo: "#mobile_menu"
    });

    /*================================
    login form
    ==================================*/
    $('.form-gp input').on('focus', function() {
        $(this).parent('.form-gp').addClass('focused');
    });
    $('.form-gp input').on('focusout', function() {
        if ($(this).val().length === 0) {
            $(this).parent('.form-gp').removeClass('focused');
        }
    });

    /*================================
    slider-area background setting
    ==================================*/
    $('.settings-btn, .offset-close').on('click', function() {
        $('.offset-area').toggleClass('show_hide');
        $('.settings-btn').toggleClass('active');
    });

    /*================================
    Owl Carousel
    ==================================*/
    function slider_area() {
        var owl = $('.testimonial-carousel').owlCarousel({
            margin: 50,
            loop: true,
            autoplay: false,
            nav: false,
            dots: true,
            responsive: {
                0: {
                    items: 1
                },
                450: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1000: {
                    items: 2
                },
                1360: {
                    items: 1
                },
                1600: {
                    items: 2
                }
            }
        });
    }
    slider_area();

    /*================================
    Fullscreen Page
    ==================================*/

    if ($('#full-view').length) {

        var requestFullscreen = function(ele) {
            if (ele.requestFullscreen) {
                ele.requestFullscreen();
            } else if (ele.webkitRequestFullscreen) {
                ele.webkitRequestFullscreen();
            } else if (ele.mozRequestFullScreen) {
                ele.mozRequestFullScreen();
            } else if (ele.msRequestFullscreen) {
                ele.msRequestFullscreen();
            } else {
                console.log('Fullscreen API is not supported.');
            }
        };

        var exitFullscreen = function() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else {
                console.log('Fullscreen API is not supported.');
            }
        };

        var fsDocButton = document.getElementById('full-view');
        var fsExitDocButton = document.getElementById('full-view-exit');

        fsDocButton.addEventListener('click', function(e) {
            e.preventDefault();
            requestFullscreen(document.documentElement);
            $('body').addClass('expanded');
        });

        fsExitDocButton.addEventListener('click', function(e) {
            e.preventDefault();
            exitFullscreen();
            $('body').removeClass('expanded');
        });
    }

    // show loader
    document.addEventListener("DOMContentLoaded", () => {
        const loader = document.getElementById("loading-overlay");

        // Onyesha loader wakati wowote user anapobofya link
        document.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => {
                loader.style.display = "flex";
            });
        });

        // Ondoa loader baada ya kurender
        window.addEventListener("load", () => {
            setTimeout(() => {
                loader.style.display = "none";
            }, 1000);
        });
    });


    // prompt to android users to install the app
    let deferredPrompt;

    // Hifadhi event ya installation
    window.addEventListener("beforeinstallprompt", (event) => {
        event.preventDefault();
        deferredPrompt = event;
        checkInstallation();
    });

    // Fungua notification kila user akifungua app ikiwa hajainstall
    function checkInstallation() {
        if (window.matchMedia("(display-mode: standalone)").matches || window.navigator.standalone) {
            console.log("PWA already installed.");
            return;
        }

        // Toa notification kila baada ya sekunde 30
        setTimeout(() => {
            showInstallNotification();
        }, 30000);

        // Onyesha button ya install ikiwa user anataka manual installation
        document.getElementById("install-button").style.display = "block";
    }

    // Notification ya kusakinisha
    function showInstallNotification() {
        if ("Notification" in window && Notification.permission === "granted") {
            new Notification("Install ShuleApp", {
                body: "For a better experience, install ShuleApp on your device.",
                icon: "/icons/icon.png",
                actions: [{ action: "install", title: "Install Now" }]
            });
        } else if ("Notification" in window && Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    showInstallNotification();
                }
            });
        }
    }

    // Ikiwa user anabofya "Install Now" kwenye notification
    self.addEventListener("notificationclick", (event) => {
        if (event.action === "install") {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === "accepted") {
                        console.log("User installed the app.");
                    } else {
                        console.log("User dismissed the installation.");
                    }
                    deferredPrompt = null;
                });
            }
        }
    });

    // Button ya Manual Installation
    document.getElementById("install-button").addEventListener("click", () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === "accepted") {
                    console.log("User installed the app.");
                } else {
                    console.log("User dismissed the installation.");
                }
                deferredPrompt = null;
            });
        }
    });


    // prompt IOS users to install the app
    // Angalia kama ni iPhone/iPad
    if (/iPhone|iPad/i.test(navigator.userAgent)) {
        if (!window.matchMedia('(display-mode: standalone)').matches) {
            alert("For a better experience, install ShuleApp: Click 'Share' → 'Add to Home Screen'.");
        }
    }

//prompt message kwa users wa laptop


})(jQuery);
