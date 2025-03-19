@if(Auth::check())
    <script type="text/javascript">
        function checkSessionExpiry() {
    fetch('/check-session-expiry')
        .then(response => response.json())
        .then(data => {
            if (data.session_expiring_soon) {
                let remainingTime = data.remaining_time * 1000;
                let warningTime = 5 * 60 * 1000;

                if (remainingTime <= warningTime) {
                    if (confirm('Your session will expire in 5 minutes. Do you want to extend it?')) {
                        extendSession();
                    } else {
                        alert('Session will expire soon, you will be signed out.');
                        window.location.href = '/logout';
                    }
                }
            }
        })
        .catch(error => {
            console.log("Session check failed:", error);
            window.location.href = '/logout';
        });
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
        if (data.success) {
            alert('Session extended for 1 hour.');
        }
    });
}

setInterval(checkSessionExpiry, 30000);

    </script>
@endif
