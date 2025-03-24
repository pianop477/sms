// JAVASCRIPT FIX
function checkSessionStatus() {
    fetch('/session/check', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.session_expired) {
            // alert('Session has expired, please login');
            Swal.fire({
                position: 'top-end',
                icon: 'info',
                title: 'Session has expired, please login',
                showConfirmButton: false,
                timer: 5000,
                toast: true
            });
            window.location.href = '/login';
        }
        if (data.session_expiring_soon) {
            let userResponse = confirm(`Your session will expire in ${Math.floor(data.remaining_time / 60)} minutes. Extend?`);
            if (userResponse) {
                extendSession();
            }
        }
    })
    .catch(error => console.error('Error checking session:', error));
}

function extendSession() {
    fetch('/session/extend', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.session_extended) {
            // alert('Session Extended!');
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Session has been Extended!',
                showConfirmButton: false,
                timer: 5000,
                toast: true
            });
        } else {
            // alert('Session has expired, please login');
            Swal.fire({
                position: 'top-end',
                icon: 'info',
                title: 'Session has expired, please login',
                showConfirmButton: false,
                timer: 5000,
                toast: true
            });
            window.location.href = '/login';
        }
    })
    .catch(error => console.error('Error extending session:', error));
}

setInterval(checkSessionStatus, 60000); // Check session kila dakika 1
