<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shule | App</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon/favicon.png') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
    /* Navbar customization */
    .navbar-custom {
      background-color: #ccdfee;
      color: rgb(39, 75, 109);
      border-bottom: 3px solid orange;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-custom .navbar-brand {
      color: rgb(35, 66, 112);
      font-weight: bold;
      font-size: 1.5rem;
    }

    .navbar-custom .navbar-nav .nav-link {
      color: rgb(49, 75, 118);
    }

    /* Hero section styling */
    .hero {
      position: relative;
      width: 100%;
      height: 100vh;
      overflow: hidden;
    }

    .carousel-item {
      height: 100vh;
      background-size: cover;
      background-position: center;
      transition: opacity 1s ease-in-out;
    }

    .carousel-item-next, .carousel-item-prev, .carousel-item.active {
      transition: opacity 1s ease-in-out;
    }

    .carousel-caption {
      position: absolute;
      top: 60%;
      transform: translateY(-50%);
      text-align: center;
      color: white;
    }

    .carousel-caption h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .carousel-caption p {
      font-size: 1.3rem;
      color: #07db78;
      font-weight: bold;
    }

    /* Blur effect */
    .carousel-item-next, .carousel-item-prev {
      filter: blur(8px);
      opacity: 0;
    }

    .carousel-item.active {
      filter: none;
      opacity: 1;
    }

    .section {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .bg-light {
      background-color: #f8f9fa;
    }

    /* Media queries for responsiveness */
    @media (max-width: 768px) {
      .hero {
        height: 60vh;
      }

      .carousel-caption {
        top: 50%;
        font-size: 1rem;
      }

      .carousel-caption h1 {
        font-size: 1.5rem;
      }

      .carousel-caption p {
        font-size: 1rem;
      }

      .section {
        height: auto;
        padding: 20px 0;
      }

      .card {
        width: 100%;
        margin-bottom: 20px;
      }
    }

    @media (max-width: 576px) {
      .carousel-caption h1 {
        font-size: 1.2rem;
      }

      .carousel-caption p {
        font-size: 0.8rem;
      }

      .card img {
        height: auto;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  @include('SRTDashboard.preloader')

  <nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
    <div class="container">
      <img src="{{ asset('assets/img/logo/shuleapp_transparent.png') }}" alt="" class="rounded-circle" style="width:70px; object-fit:cover;">
      <a class="navbar-brand" href="{{route('welcome')}}">ShuleApp</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          @if (Route::has('users.form'))
          <li class="nav-item">
            <a class="nav-link btn btn-outline-primary btn-sm" href="{{ route('users.form') }}">Sign Up</a>
          </li>
          @endif
        </ul>
      </div>
    </div>
  </nav>

  <section class="hero">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#heroCarousel" data-slide-to="1"></li>
        <li data-target="#heroCarousel" data-slide-to="2"></li>
        <li data-target="#heroCarousel" data-slide-to="3"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('{{ asset('assets/img/cards/paper 1.jpeg') }}');">
          <div class="carousel-caption">
            <h1>Welcome to ShuleApp</h1>
            <p>Your Ultimate Education Management Solution</p>
            <a href="{{route('login')}}" class="btn btn-primary btn-lg">Get Started Now</a>
          </div>
        </div>
        <div class="carousel-item" style="background-image: url('{{ asset('assets/img/cards/paper 2.jpg') }}');">
          <div class="carousel-caption">
            <h1>Efficient Data Management</h1>
            <p>Streamline your educational processes</p>
            <a href="{{route('login')}}" class="btn btn-primary btn-lg">Get Started Now</a>
          </div>
        </div>
        <div class="carousel-item" style="background-image: url('{{ asset('assets/img/cards/paper 3.jpg') }}');">
          <div class="carousel-caption">
            <h1>Innovative Solutions</h1>
            <p>Enhance learning with technology</p>
            <a href="{{route('login')}}" class="btn btn-primary btn-lg">Get Started Now</a>
          </div>
        </div>
        <div class="carousel-item" style="background-image: url('{{ asset('assets/img/cards/paper 4.jpg') }}');">
          <div class="carousel-caption">
            <h1>Join to Our Community</h1>
            <p>Be part of the future of education</p>
            <a href="{{route('login')}}" class="btn btn-primary btn-lg">Get Started Now</a>
          </div>
        </div>
      </div>
      <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </section>

  <section class="section" id="features" style="background: rgb(232, 167, 221)">
    <div class="container">
      <h2 class="text-center mb-4">MAIN FEATURES</h2>
      <div class="row features">
        <div class="col-md-4">
            <div class="card">
                <img src="{{asset('assets/img/features/feature 1.png')}}" class="card-img-top" alt="...">
                <div class="card-body">
                  <h5 class="card-title">Reports Management</h5>
                  <p class="card-text">The reporting capabilities of ShuleApp systems are flexible enough to get reports from attendance, grades/scores, and student Reports</p>
                </div>
              </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <img src="{{asset('assets/img/features/feature 2.png')}}" class="card-img-top" alt="...">
                <div class="card-body">
                  <h5 class="card-title">Parents Portal</h5>
                  <p class="card-text">The ShuleApp gives parents a level of control over their child's performance at school than ever seen before. Don't plan to miss using this Amazing Application</p>
                </div>
              </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <img src="{{asset('assets/img/features/feature 3.jpeg')}}" class="card-img-top" alt="...">
                <div class="card-body">
                  <h5 class="card-title">Centralized Data Management</h5>
                  <p class="card-text">Our system provides a centralized platform for managing all staff data, ensuring that information is organized, accessible, and secure.</p>
                </div>
              </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="contact" style="background:rgb(159, 240, 214)">
    <div class="container">
      <h2 class="text-center mb-4">CONTACT US</h2>
      <div class="row">
        <div class="col-md-6">
          <h4>ShuleApp - Admin</h4>
          <p>Address: Dodoma, Tanzania</p>
          <p>Email: pianop477@gmail.com</p>
          <p>Phone: +255 678 669 000</p>
          <p>&copy; Copyright <a href="#">ShuleApp</a> {{date('Y')}}</p>
        </div>
        <div class="col-md-6">
          <form method="POST" action="{{route('send.feedback.message') . ('#contact')}}" class="needs-validation" novalidate>
            @csrf
            <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter your Name" value="{{old('name')}}">
              @error('name')
                  <div class="text-danger">{{$message}}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" placeholder="Enter your Email Address" name="email" value="{{old('email')}}">
              @error('email')
                  <div class="text-danger">{{$message}}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="message">Message:</label>
              <textarea class="form-control" id="message" rows="3" placeholder="Enter your Message here" name="message">{{old('message')}}</textarea>
              @error('message')
                  <div class="text-danger">{{$message}}</div>
              @enderror
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>
  @include('sweetalert::alert')
</body>
</html>
