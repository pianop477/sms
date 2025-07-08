<div id="mypreloader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center; z-index: 9999;">
    <div class="spinner-border text-primary" role="status">
        <img src="{{asset('assets/img/loader/hejema.gif')}}" width="300px;" alt="">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("submit", function (event) {
                // Angalia kama form imepita validation
                if (!form.checkValidity()) {
                    event.preventDefault(); // Acha submission ikiwa validation haijakamilika
                    form.reportValidity(); // Onyesha errors za validation
                    return;
                }

                // Onyesha preloader kama validation imekamilika
                let preloader = document.getElementById("mypreloader");
                preloader.style.display = "flex";
            });
        });
    });
</script>
