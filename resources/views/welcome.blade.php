<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shule | App</title>
  <link rel="icon" type="image/png" href="{{asset('assets/img/favicon/favicon.png')}}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
    .navbar-custom {
      background-color: #ccdfee;
      color: rgb(39, 75, 109);
      border-bottom: 3px solid orange;/* Thick dark blue bottom border */
    }

    .navbar-custom .navbar-brand {
      color: rgb(35, 66, 112); /* Brand name color */
      font-weight: bold; /* Make the brand name bold */
      font-size: 1.5rem;
    }

    .navbar-custom .navbar-nav .nav-link {
      color: rgb(49, 75, 118);
    }

    .navbar-custom .navbar-dark .navbar-nav .nav-link.active,
    .navbar-custom .navbar-dark .navbar-nav .show > .nav-link {
      color: rgb(49, 75, 118);
    }

    .navbar-custom .navbar-dark .navbar-nav .nav-link {
      color: rgb(49, 75, 118);
    }

    .navbar-custom .navbar-dark .navbar-nav .nav-link:hover,
    .navbar-custom .navbar-dark .navbar-nav .nav-link:focus {
      color: rgb(49, 75, 118);
    }

    /* Border radius for cards */
    .card {
      border-radius: 15px; /* Adjust the value as needed */
      box-shadow: 0px 4px 8px rgba(16, 15, 15, 0.1); /* Add a bottom shadow */
    }

    /* Customization for images */
    .card-img-top {
      height: 100%;
      object-fit: cover;
      border-radius: 15px; /* Ensure rounded border for images */
    }

    /* Adjust spacing between paragraphs */
    .card-body p {
      margin-bottom: 1px; /* Adjust spacing between paragraphs */
    }

    /* Profile image style */
    .profile-img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      margin-right: 10px;
    }

    /* Customization for card header */
    .card-header {
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      background-color: transparent;
      color: black; /* Card header font color */
      font-weight: bold; /* Make the card header font bold */
      box-shadow: 0px 4px 8px rgba(56, 52, 52, 0.1); /* Add a bottom shadow */
      font-size: 1.5rem; /* Increase font size of card header */
    }

    /* Customization for card titles */
    .card-title {
      background-color: #28a745; /* Success color */
      color: white; /* Text color */
      padding: 1px 3px; /* Padding for better appearance */
      border-radius: 8px; /* Add border radius */
    }
  </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
        <div class="container">
            <img src="{{asset('assets/img/logo/shuleapp_transparent.png')}}" alt="" class="profile-img">
            <a class="navbar-brand" href="#">ShuleApp</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    @if (Route::has('users.form'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('users.form')}}">Register</a>
                    </li>
                    @endif
                    @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('login')}}">Login</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

  <div class="container mt-3">
    <div class="row">
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-header">
            {{-- <img src="{{asset('assets/img/logo/sms logo3.jpg')}}" alt="" class="profile-img"> --}}
            Contact Us
          </div>
          <div class="card-body">
            <span class="card-title">Service Provide:</span>
            <p class="card-text">Graphics Designer & Multimedia Technology</p>
            <span class="card-title">Contact Us</span>
            <p class="card-text">+255 753 671 658 or +255 622 704 021</p>
            <p class="card-text">matekelefreyson@gmail.com</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mb-3">
          <img src="{{asset('assets/img/cards/studying.jpg')}}" class="card-img-top" alt="Wiring Image">
        </div>
      </div>
    </div>
  </div>
</body>
</html>
