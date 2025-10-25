<div id="mypreloader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center; z-index: 9999;">
    <div class="spinner-border text-primary" role="status">
        <img src="{{asset('assets/img/loader/hejema.gif')}}" width="300px;" alt="">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("submit", function (event) {
                // Skip kama form ina attribute ya data-no-preloader
                if (form.hasAttribute('data-no-preloader')) {
                    return;
                }

                // Skip kama form inatumia method ya AJAX
                const submitter = event.submitter;
                if (submitter && submitter.hasAttribute('data-ajax')) {
                    return;
                }

                // Angalia kama form imepita validation
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                    return;
                }

                // Onyesha preloader kama validation imekamilika
                let preloader = document.getElementById("mypreloader");
                if (preloader) {
                    preloader.style.display = "flex";
                }
            });
        });
    });
</script>
