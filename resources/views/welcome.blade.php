<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShuleApp</title>
  <link rel="shortcut icon" type="image/png" href="{{asset('assets/img/favicon/favicon.ico')}}">
  <link rel="icon" type="image/png" href="{{asset('assets/img/favicon/favicon-16x16.png')}}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/scrollreveal"></script>
  <style>
    body {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body class="bg-gray-50">

  <!-- Header -->
  <header class="fixed top-0 w-full bg-white shadow-md z-50">
    <div class="container mx-auto flex justify-between items-center p-4">
      <div class="text-2xl font-bold text-blue-600">ShuleApp</div>
      <nav class="hidden md:flex space-x-8">
        <a href="#home" class="hover:text-blue-600 font-semibold">Home</a>
        <a href="#features" class="hover:text-blue-600 font-semibold">Features</a>
        <a href="#contact" class="hover:text-blue-600 font-semibold">Contact</a>
      </nav>
      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <button id="menu-toggle" class="focus:outline-none">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
          </svg>
        </button>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white p-4 space-y-4">
      <a href="#home" class="block text-gray-700">Home</a>
      <a href="#features" class="block text-gray-700">Features</a>
      <a href="#contact" class="block text-gray-700">Contact</a>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="home" class="h-screen bg-cover bg-center relative" style="background-image: url('{{ asset('assets/img/bg/bg-2.jpeg') }}');">
    <div class="absolute inset-0 bg-opacity-50"></div>
    <div class="container mx-auto h-full flex flex-col justify-center items-center text-center relative z-10 text-white pt-40">
      <h1 class="text-4xl md:text-6xl font-bold mb-6">Karibu ShuleApp</h1>
      <p class="text-xl md:text-2xl mb-8">Suluhisho lako Bora la Usimamizi wa Elimu</p>
      <a href="{{route('login')}}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-lg font-semibold transition">Anza Sasa</a>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="py-20 bg-white">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-bold mb-12 text-gray-800">Huduma Zinazopatikana ShuleApp</h2>
      <div class="grid md:grid-cols-3 gap-12">

        <!-- Attendance -->
        <div class="p-8 shadow-lg rounded-xl hover:scale-105 transition transform bg-gray-50">
          <div class="text-blue-600 mb-4">
            <svg class="mx-auto w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold mb-2">Mahudhurio ya Wanafunzi</h3>
          <p class="text-gray-600">Fuatilia mahudhurio ya kila mwanafunzi kwa haraka, kwa usahihi wa hali ya juu kila siku.</p>
        </div>

        <!-- Matokeo -->
        <div class="p-8 shadow-lg rounded-xl hover:scale-105 transition transform bg-gray-50">
          <div class="text-green-600 mb-4">
            <svg class="mx-auto w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M9 17v-2a4 4 0 014-4h6"></path>
              <path d="M13 7h6"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold mb-2">Matokeo ya Mitihani</h3>
          <p class="text-gray-600">Rekodi, changanua na sambaza matokeo ya wanafunzi kwa urahisi bila usumbufu.</p>
        </div>

        <!-- Records -->
        <div class="p-8 shadow-lg rounded-xl hover:scale-105 transition transform bg-gray-50">
          <div class="text-purple-600 mb-4">
            <svg class="mx-auto w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M4 4h16v16H4z"></path>
              <path d="M8 2v4"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold mb-2">Usimamizi wa Taarifa</h3>
          <p class="text-gray-600">Hifadhi taarifa muhimu za shule kama kumbukumbu za wanafunzi, walimu na wazazi kwa usalama wa hali ya juu.</p>
        </div>

        <!-- Bulk SMS -->
        <div class="p-8 shadow-lg rounded-xl hover:scale-105 transition transform bg-gray-50">
          <div class="text-yellow-500 mb-4">
            <svg class="mx-auto w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
              <path d="M7 10l5 5 5-5"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold mb-2">Bulk SMS</h3>
          <p class="text-gray-600">Tuma ujumbe wa pamoja kwa wazazi kwa sekunde chache tu, bila bughudha.</p>
        </div>

        <!-- Security -->
        <div class="p-8 shadow-lg rounded-xl hover:scale-105 transition transform bg-gray-50">
          <div class="text-red-500 mb-4">
            <svg class="mx-auto w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold mb-2">Usalama wa Data</h3>
          <p class="text-gray-600">Data yako inalindwa kwa viwango vya juu vya usalama kuhakikisha faragha na uhakika wa taarifa zako.</p>
        </div>

        <!-- Parent Portal -->
        <div class="p-8 shadow-lg rounded-xl hover:scale-105 transition transform bg-gray-50">
          <div class="text-indigo-600 mb-4">
            <svg class="mx-auto w-14 h-14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M16 12a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0H8"></path>
              <path d="M12 14v6"></path>
              <path d="M8 18h8"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold mb-2">Portal ya Wazazi</h3>
          <p class="text-gray-600">Wazazi hupata taarifa za mahudhurio, matokeo, na matukio moja kwa moja kwa urahisi mtandaoni.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- Testimonials Section -->

  <!-- Contact Section -->
  <section id="contact" class="py-20 bg-gray-100">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-12 text-gray-800">Wasiliana Nasi</h2>
      <p>Kwa Msaada: <a href="tel:+255678669000">0678 669 000</a></p>
      <form class="max-w-2xl mx-auto space-y-6 needs-validation" novalidate action="{{route('send.feedback.message') . ('#contact')}}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Jina lako" value="{{old('name')}}" class="w-full border border-gray-300 p-4 rounded-lg">
        @error('name')
            <span class="" style="color: red;">{{$message}}</span>
        @enderror
        <input type="text" name="phone" placeholder="Namba ya simu" value="{{old('phone')}}" class="w-full border border-gray-300 p-4 rounded-lg">
        @error('phone')
            <span class="" style="color: red;">{{$message}}</span>
        @enderror
        <textarea placeholder="Ujumbe wako" name="message" class="w-full border border-gray-300 p-4 rounded-lg" rows="5" required>{{old('message')}}</textarea>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-full transition" id="saveButton">Tuma Ujumbe</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-gray-400 py-8 text-center">
    <p>©{{date('Y')}} ShuleApp. Haki Zote Zimehifadhiwa.</p>
  </footer>

  @include('sweetalert::alert')
  <script>
    // Mobile menu toggle
    document.getElementById('menu-toggle').addEventListener('click', function() {
      document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    // Scroll animations
    ScrollReveal().reveal('section', {
      delay: 200,
      distance: '50px',
      duration: 1000,
      easing: 'ease-in-out',
      origin: 'bottom'
    });

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector(".needs-validation");
        const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

        if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Zuia submission ya haraka

            // Disable button na badilisha maandishi
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="inline-block w-5 h-5 border-4 border-t-4 border-white rounded-full animate-spin"></span> Please Wait...`;

            // Hakikisha form haina errors kabla ya kutuma
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                submitButton.disabled = false; // Warudishe button kama kuna errors
                submitButton.innerHTML = "Tuma Ujumbe";
                return;
            }

            // Chelewesha submission kidogo ili button ibadilike kwanza
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
  </script>

</body>
</html>
