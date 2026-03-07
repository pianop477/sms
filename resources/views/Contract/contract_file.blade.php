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

        body {
            font-family: "DejaVu Sans", "Times New Roman", serif;
        }

        .container {
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .signature {
            margin-top: 50px;
            text-align: center;
        }

        .qr-wrapper {
            position: absolute;
            right: 7.5cm;
            bottom: 0.5cm;
            text-align: center;
            font-size: 7pt;
        }

        .qr-wrapper img {
            width: 120px;
            height: 120px;
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
            height: 10px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding: 4px 15px;
            text-align: center;
            background-color: white;
            z-index: 1000;
        }

        @page {
            orientation: potrait;
            size: A4;
            top: 6mm;
            bottom: 6mm;
            right: 6mm;
            left: 6mm;
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
        @if($contract['logo'])
            <div class="logo">
                <img src="{{ $contract['logo'] }}" alt="School Logo" class="logo" style="max-width: 80px;">
            </div>
        @endif
        <div class="header" style="">
            <h3>{{ $contract['school_name'] }}</h3>
            <h5>{{ $contract['postal_address'] }} - {{ $contract['postal_name'] }}</h5>
            <h5>{{ $contract['country'] }}</h5>
            {{-- <h5>Barua ya Kukubaliwa</h5> --}}
        </div>
    </div>
    @php
        $isProvision = $contract['contract_type'] === 'provision';
    @endphp

    <div class="content">

        <p style="text-align:right">
            Tarehe:
            <strong>{{ \Carbon\Carbon::parse($contract['approved_at'])->toFormattedDateString() }}</strong>
        </p>

        <p>
            <strong>{{ ucfirst($contract['first_name']) }} {{ ucfirst($contract['last_name']) }}</strong>
        </p>
        <p>
            <strong>
                {{ucfirst($contract['address'])}}
            </strong>
        </p>

        <p style="text-transform: uppercase; text-align:center">
            YAH:
            <span style="">
                <strong>
                    @if ($isProvision)
                        KUPEWA MKATABA WA MUDA WA MATAZAMIO
                    @else
                        KUPEWA MKATABA MPYA
                    @endif
                </strong>
            </span>
        </p>

        <p style="text-align: justify">
           Tafadhali, husika na kichwa cha barua hapo juu.
        </p>

        <p style="text-align: justify">
            @if ($isProvision)
                Tunayo furaha kukujulisha kuwa umepewa <strong>mkataba wa muda wa matazamio</strong>
                hapa {{ strtoupper($contract['school_name']) }}, ambacho ndicho kituo chako cha kazi.
                Kwa kipindi hiki utakuwa chini ya uangalizi na kufanyiwa tathmini ya utendaji wako wa kazi.
            @else
                Tunayo furaha kukujulisha kuwa umepewa <strong>mkataba mpya wa ajira</strong>
                hapa {{ strtoupper($contract['school_name']) }}, ambacho ndicho kituo chako cha kazi.
            @endif

            Mkataba huu ni halali kwa muda wa
            <strong>miezi {{ $contract['duration'] }}</strong>,
            kuanzia tarehe
            <strong>{{ \Carbon\Carbon::parse($contract['start_date'])->format('d-m-Y') }}</strong>
            hadi
            <strong>{{ \Carbon\Carbon::parse($contract['end_date'])->format('d-m-Y') }}</strong>.
        </p>

        @if ($isProvision)
            <p style="text-align: justify">
            Kwa kipindi hiki cha utumishi katika Taasisi hii, utalipwa kiasi cha
            <strong>{{ number_format($contract['basic_salary'] + $contract['allowances']) }} TZS</strong>  kama Posho kwa kila mwezi.
        </p>
        @else
        <p style="text-align: justify">
            Kwa kipindi hiki cha utumishi katika Taasisi hii, utalipwa kiasi cha
            <strong>{{ number_format($contract['basic_salary'] + $contract['allowances']) }} TZS</strong>
             kama Mshahara wa kila mwezi kabla ya makato.
        </p>
        @endif

        @if ($isProvision)
            <p style="text-align: justify">
                Baada ya kipindi cha matazamio kukamilika/kuisha,
                utaweza kuthibitishwa rasmi kama mtumishi wetu kwa kuomba mkataba mwingine mpya.
            </p>
        @endif

        @if ($isProvision)
            <p style="text-align: justify">
                Tafadhali hakikisha kuwa unazingatia vigezo na masharti ya Taasisi hii.
                Tunakutakia mafanikio katika majukumu yako mapya.
            </p>
        @else
            <p style="text-align: justify">
                Tafadhali hakikisha kuwa unazingatia vigezo na masharti ya Taasisi hii kama ilivyo elezwa kwenye Mkataba ulioweka sahihi yako.
                Tunakutakia mafanikio katika majukumu yako mapya.
            </p>
        @endif
    </div>

    <div class="signature">
        <p>Wako katika Kazi,</p>

        <p><strong>{{ ucwords(strtolower($contract['authorized_person_name'])) }}</strong></p>
        <p><strong>{{ $contract['position'] }}</strong></p>
    </div>

    <div class="qr-wrapper">
        <img src="{{ $qrImage }}">
        <div class="validation-text">Scan to verify</div>
    </div>

    <!-- Kijachini / Footer -->
    <footer>
        <span class="copyright">
            &copy; {{ ucwords(strtolower($contract['school_name'] ?? 'Shule')) }} – {{ date('Y') }}
        </span>
        <span class="printed">
            Imechapishwa: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>
</body>

</html>
