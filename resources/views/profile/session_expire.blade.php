@if(Auth::check())
    <script type="text/javascript">
        // Function to check session expiry
        function checkSessionExpiry() {
            fetch('/check-session-expiry')
                .then(response => response.json())
                .then(data => {
                    if (data.session_expiring_soon) {
                        let remainingTime = data.remaining_time; // Time left before expiry
                        let warningTime = 5 * 60 * 1000; // 5 minutes in milliseconds

                        // Show a prompt when there's 5 minutes left
                        setTimeout(function() {
                            if (confirm('Your session will expire in 5 minutes. Do you want to extend it?')) {
                                // User clicked Yes, extend the session
                                fetch('/extend-session', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('Session extended for another hour.');
                                    }
                                });
                            } else {
                                // User clicked No, allow session to expire
                                alert('Session will expire soon, you will be logged out.');
                            }
                        }, remainingTime - warningTime); // 5 minutes before expiry
                    }
                });
        }

        // Check session expiry every minute (or as needed)
        setInterval(checkSessionExpiry, 60000); // 60,000 ms = 1 minute
    </script>
@endif
