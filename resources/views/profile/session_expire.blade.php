@if(Auth::check())
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function checkSessionExpiry() {
                fetch('/check-session-expiry')
                    .then(response => response.json())
                    .then(data => {
                        console.log("Server Response:", data); // Hii itatuambia nini kinakuja kutoka server

                        if (data.session_expired) {
                            alert('Your session has expired. You will be redirected to the login page.');
                            window.location.href = '/login';
                        } else if (data.session_expiring_soon) {
                            let remainingTime = data.remaining_time * 1000; // Convert to milliseconds
                            let warningTime = 60 * 1000; // 1 minute

                            console.log("Session is expiring soon. Remaining time:", remainingTime);

                            if (remainingTime <= warningTime) {
                                let extend = confirm('Your session will expire in 1 minute. Do you want to extend it?');
                                console.log("User selected:", extend ? "YES" : "NO");

                                if (extend) {
                                    extendSession();
                                } else {
                                    alert('Session will expire soon, you will be signed out.');
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error checking session expiry:', error));
            }

            function extendSession() {
                fetch('/extend-session', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Extend Session Response:", data);
                    if (data.success) {
                        alert('Session extended for 5 more minutes.');
                    }
                })
                .catch(error => console.error('Error extending session:', error));
            }

            // Check session expiry every 5 seconds
            setInterval(checkSessionExpiry, 5000);
        });
    </script>
@endif
