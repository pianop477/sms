@extends('SRTDashboard.frame')
@section('content')
    <h2>Register your biometric device</h2>

<button id="register-btn">Register Biometric</button>

<script type="module">
import { startRegistration } from '@simplewebauthn/browser';

document.getElementById('register-btn').addEventListener('click', async () => {
    const response = await fetch('{{ route('webauthn.options') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });

    const options = await response.json();

    const attestation = await startRegistration(options);

    const verify = await fetch('{{ route('webauthn.verify') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(attestation)
    });

    const result = await verify.json();

    if (result.success) {
        alert('Biometric device registered successfully!');
    } else {
        alert('Registration failed');
    }
});
</script>

@endsection
