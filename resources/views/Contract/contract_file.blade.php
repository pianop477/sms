<!DOCTYPE html>
<html>
<head>
    <title>Approval Letter</title>
    <style>
         .logo {
            position: absolute;
            left: 10px;
            top: 5px;
            color: inherit;
        }
        .header {
            text-align: center;
            position: relative;
            top: 0;
            left: 0px;
            text-transform: uppercase;
            line-height: 1px;
        }
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; }
        .content { margin-top: 30px; }
        .signature { margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <!-- Letterhead / Header -->
    <div class="container">
        <div class="logo">
            <img src="{{public_path('assets/img/logo/'.$contract ['logo'])}}" alt="" style="max-width: 70px;">
        </div>
        <div class="header" style="">
            <h3>{{$contract['school_name']}}</h3>
            <h5>{{$contract ['postal_address']}} - {{$contract ['postal_name']}}</h5>
            <h5>{{$contract ['country']}}</h5>
            {{-- <h5>Approval letter</h5> --}}
        </div>
    </div>
    <!-- Letter Content -->
    <div class="content">
        <p style="text-transform: capitalize; text-align:right">Date: <strong>{{ \Carbon\Carbon::parse($contract['approved_at'])->toFormattedDateString() }}</strong></p>
        <p style="text-transform: capitalize">To: <strong>{{ $contract['first_name'] }} {{ $contract['last_name'] }}</strong></p>
        <p style="text-transform: capitalize">Address: <strong>{{ $contract['address'] ?? 'N/A' }}</strong></p>

        <p style="text-transform: uppercase; text-align:center">Re:
            <span style="text-decoration: underline"><strong>Approval for Contract with
            {{ $contract['school_name'] }}</strong></span></p> <p style="text-align: justify">Dear
            <span style="text-transform: capitalize"><strong>{{ $contract['first_name'] }}
            {{ $contract['last_name'] }}</strong></span>,</p> <p style="text-align: justify">
            We are pleased to confirm the approval of your contract with <span style="text-transform: uppercase">
            <strong>{{ $contract['school_name'] }}</strong></span>. This contract is valid for
            <strong>{{ $contract['duration'] }} months</strong>, commencing on <strong>
            {{ \Carbon\Carbon::parse($contract['start_date'])->format('d-m-Y H:i') }}</strong>
            and expiring on <strong>{{ \Carbon\Carbon::parse($contract['end_date'])->format('d-m-Y H:i') }}</strong>.</p>
            <p style="text-align: justify">This agreement signifies our mutual commitment to fulfilling the terms and conditions outlined in the contract.
            Please ensure that all obligations are met in accordance with the stipulated timelines.</p> <p style="text-align: justify">
            Should you have any questions or require further clarification, please do not hesitate to contact us directly at the head office.</p>
            <p style="text-align: justify">Thank you for your cooperation and partnership. We look forward to a successful collaboration.
        </p>
    </div>

    <!-- Signature Section -->
    <div class="signature">
        <p>Sincerely,</p>
        <p><strong>{{ $contract['authorized_person_name'] ?? 'Authorized Representative' }}</strong></p>
        <p>Position: <strong>{{ $contract['authorized_person_position'] ?? 'Manager' }}</strong></p>
        <p style="text-transform: capitalize"><strong>{{ $contract['school_name'] }}</strong></p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ now()->year }} <span style="text-transform: capitalize">{{ $contract['school_name'] }}</span>. All rights reserved.</p>
    </div>
</body>
</html>
