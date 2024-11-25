<!DOCTYPE html>
<html>

<head>
  <title>Dashboard Admin</title>
  <link href="{{ asset('assets/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="/Logo.png" alt="Logo" rel="shortcut icon">
  <link rel="stylesheet" href="resources\css\style.css">
  <style>
    html,
    body,
    .container-fluid {
      height: 100vh;
    }

    .container-fluid {
      display: flex;
      align-items: center;
    }

    h5 {
      font-size: 1.3rem;
    }

    form {
      border-top: 1px solid #D9D9D9;
      display: flex;
      flex-direction: column;
    }

    .btn {
      align-self: flex-end;
      width: 100px;
      background: #0078C4;
      border: 2px solid #0078C4;
      color: #ffffff;
      font-weight: 600;
    }

    .btn:hover {
      background: #ffffff;
      border: 2px solid #0078C4;
      color: #0078C4;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-5">
    @yield('content')
  </div>
  <script src="{{ asset('assets/vendor/bootstrap-5.3.3-dist/js/bootstrap.min.js') }}"></script>
</body>

</html>