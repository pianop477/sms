<footer>
    <div class="footer-area fixed-bottom text-white">
        @php
            $startYear = 2025;
            $currentYear = date('Y');
        @endphp

        <span class="">
            &copy; <strong>
                {{ $startYear == $currentYear ? $startYear : $startYear . ' - ' . $currentYear }}
                ShuleApp. All Rights Reserved.
            </strong>
        </span>
    </div>
</footer>
