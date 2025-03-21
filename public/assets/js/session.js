
    function checkSessionStatus() {
        fetch("{{ route('session.check') }}") // Middleware inatoa status ya session
            .then(response => response.json())
            .then(data => {
                if (data.session_expired) {
                    alert("Session has expired, please login");
                    window.location.href = "{{ route('login') }}";
                }
                if (data.session_expiring_soon) {
                    let userResponse = confirm("Your session will expired in " + Math.floor(data.remaining_time / 60) + " minutes. Do you want to extend?");

                    if (userResponse) {
                        extendSession(); // Mwite function ya kuongeza muda
                    }
                }
            })
            .catch(error => console.error("Error checking session:", error));
    }

    function extendSession() {
        fetch("{{ route('session.extend') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.session_extended) {
                alert("Session imeongezwa!");
            } else {
                alert("Session has expired, please login");
                window.location.href = "{{ route('login') }}";
            }
        })
        .catch(error => console.error("Error extending session:", error));
    }

    setInterval(checkSessionStatus, 60000); // Cheki session kila dakika 1

