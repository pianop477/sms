<!DOCTYPE html>
<html>
<head>
    <title>Barua ya Kukubaliwa</title>
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
        body { font-family: "DejaVu Sans", "Times New Roman", serif;}
        .container { border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px;  }
        .signature { margin-top: 50px; text-align: center; }

        .qr-wrapper {
            position: absolute;
            right: 8cm;
            bottom: 1cm;
            text-align: center;
            font-size: 7pt;
        }

        .qr-wrapper img {
            width: 90px;
            height: 90px;
            border: 1px solid #ccc;
        }

        .validation-text {
            margin-top: 4px;
            font-style: italic;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 15px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding: 4px 15px;
            text-align: center;
            background-color: white;
            z-index: 1000;
        }
        footer .page-number:after {
            /* content: "Page " counter(page); */
        }
        footer .copyright {
            float: left;
            margin-left: 10px;
        }
        footer .printed {
            float: right;
            margin-right: 10px;
        }
        /* Clear floats */
        footer:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <!-- Kichwa cha Barua / Header -->
    <div class="container">
        <div class="logo">
            <img src="{{storage_path('app/public/logo/'.$contract ['logo'])}}" alt="" style="max-width: 70px;">
        </div>
        <div class="header" style="">
            <h3>{{$contract['school_name']}}</h3>
            <h5>{{$contract ['postal_address']}} - {{$contract ['postal_name']}}</h5>
            <h5>{{$contract ['country']}}</h5>
            {{-- <h5>Barua ya Kukubaliwa</h5> --}}
        </div>
    </div>
    <!-- Maudhui ya Barua -->
    <div class="content">
        <p style="text-transform: capitalize; text-align:right">Tarehe: <strong>{{ \Carbon\Carbon::parse($contract['approved_at'])->toFormattedDateString() }}</strong></p>
        <p style="text-transform: capitalize">Kwa: <strong>{{ $contract['first_name'] }} {{ $contract['last_name'] }}</strong></p>
        <p style="text-transform: capitalize">Anuani: <strong>{{ $contract['address'] ?? 'Hajajazwa' }}</strong></p>

        <p style="text-transform: uppercase; text-align:center">YAH:
            <span style="text-decoration: underline"><strong>KUINGIA MKATABA MPYA NA {{ strtoupper($contract['school_name']) }}</strong></span></p>
        <p style="text-align: justify">Ndugu
            <span style="text-transform: capitalize"><strong>{{ strtoupper($contract['first_name']) }} {{ strtoupper($contract['last_name']) }}</strong></span>,</p>
        <p style="text-align: justify">
            Tuna furaha kukuthibitishia idhini ya kuongeza mkataba wako na <span style="text-transform: uppercase">
            <strong>{{ $contract['school_name'] }}</strong></span>. Mkataba huu ni halali kwa muda wa
            <strong> mieze {{ $contract['duration'] }} </strong>, kuanzia tarehe <strong>
            {{ \Carbon\Carbon::parse($contract['start_date'])->format('d-m-Y H:i') }}</strong>
            hadi tarehe <strong>{{ \Carbon\Carbon::parse($contract['end_date'])->format('d-m-Y H:i') }}</strong>.</p>
            <p style="text-align: justify">Barua hii inaashiria dhamira yetu ya pamoja ya kutimiza masharti na kanuni zilizoelezwa katika mkataba.
            Tafadhali hakikisha kuwa wajibu wote unatekelezwa kwa kufuata ratiba iliyowekwa.</p>
            <p style="text-align: justify">
            Endapo una maswali yoyote au unahitaji ufafanuzi zaidi, tafadhali wasiliana nasi moja kwa moja kupitia ofisi kuu.</p>
            <p style="text-align: justify">Asante kwa ushirikiano wako. Tunatazamia ushirikiano wenye mafanikio.
        </p>
    </div>

    <!-- Sehemu ya Sahihi -->
    <div class="signature">
        <p>Wako katika Kazi,</p>

        <p><strong>{{ $contract['authorized_person_name'] ?? 'MWAKILISHI ALIYEIDHINISHWA' }}</strong></p>
        {{-- <p>Wadhifa: <strong>{{ $contract['authorized_person_position'] ?? 'Meneja' }}</strong></p> --}}
    </div>

    <div class="qr-wrapper">
        <img src="{{ $qrImage }}" alt="Msimbo wa QR">
        <div class="validation-text">Scan to verify</div>
    </div>

    <!-- Kijachini / Footer -->
    <footer>
        <span class="copyright">
        &copy; {{ ucwords(strtolower(Auth::user()->school->school_name)) }} – {{ date('Y') }}
        </span>
        <span class="printed">
        Imechapishwa: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>
</body>
</html>
