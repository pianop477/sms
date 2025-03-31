<div id="mypreloader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center; z-index: 9999;">
    <div class="spinner-border text-primary" role="status">
        <img src="{{asset('assets/img/loader/loader.gif')}}" width="100px;" alt="">
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
         // Add preloader logic
         document.querySelectorAll("form").forEach(form => {
             form.addEventListener("submit", function (event) {
                 // Onyesha preloader
                 let preloader = document.getElementById("mypreloader");
                 preloader.style.display = "flex";
             });
         });
     });
 </script>
