<?php
session_start();
require 'config.php'; 

// Jika pengguna sudah login, arahkan ke halaman yang sesuai
if (isset($_SESSION['Level'])) {
    if ($_SESSION['Level'] == 'admin') {
        header("location: admin_home.php");
        exit;
    } elseif ($_SESSION['Level'] == 'user') {
        $redirect_url = $_SESSION['redirect_after_login'] ?? 'user_home.php';
        unset($_SESSION['redirect_after_login']); // Hapus setelah digunakan
        header("location: " . $redirect_url);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Penyewaan Olahraga</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f6; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 56px; 
        }
        .navbar-brand-custom {
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .navbar-brand-custom svg {
            margin-right: 8px;
        }
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            padding: 2rem 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px; 
            padding: 2rem 2.5rem; 
            border-radius: .75rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .login-card .form-signin-heading {
            font-weight: 300;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: #333;
        }
        .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 12px; 
            font-size: 16px;
            margin-bottom: 10px; 
        }
        .form-control:focus {
            z-index: 2;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-login {
            font-size: 1.1rem;
            font-weight: bold;
            padding: 0.75rem;
            margin-top: 1.5rem;
        }
        .register-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-container-login {
            max-width: 400px; 
            margin: 0 auto 1rem auto; 
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="dribbble-icon-brand-login" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 0C3.584 0 0 3.584 0 8s3.584 8 8 8c4.408 0 8-3.584 8-8s-3.592-8-8-8ZM8 14.803c-3.712 0-6.731-3.02-6.731-6.73S4.288 1.27 8 1.27c3.713 0 6.732 3.02 6.732 6.731 0 3.712-3.02 6.732-6.732 6.732Z"/>
    <path d="M12.815 7.122c-.071-.402-.417-.643-.803-.757l-2.124-.603c.323-1.103.91-2.348 1.697-3.297.1-.12.037-.31-.1-.39-.138-.08-.313-.034-.39.103-.85.966-1.493 2.29-1.803 3.444l-2.08-.588c-.46-.127-.81.243-.81.708 0 .133.032.26.093.375l.592 1.085c-.032.356-.024.63.03.976.118.754.627 1.24 1.265 1.508.06.026.12.053.18.078l.892.392c.326-.02.543-.004.796.004.23.007.465.018.712.05.474.06.833.402.833.862 0 .48-.44.824-.928.824H5.77c-.478 0-.845-.385-.845-.858 0-.339.202-.634.484-.767l.654-.306c.13-.06.13-.253.002-.313-.13-.06-.312-.023-.37.09-.343.67-.612 1.41-.612 2.285 0 .44.107.835.323 1.18.21.335.51.585.856.733.26.11.547.175.85.175h2.133c2.25 0 3.828-1.443 3.828-3.447 0-1.26-.522-2.22-1.275-2.865.23-.45.35-.91.35-1.44Z"/>
  </symbol>
</svg>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand-custom" href="index.php">
        <svg width="24" height="24" fill="currentColor"><use xlink:href="#dribbble-icon-brand-login"/></svg>
        Penyewaan Olahraga
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="about_us.php">Tentang Kami</a>
        </li>
      </ul>
      <a class="btn btn-outline-success my-2 my-sm-0" href="login.php" role="button">Masuk</a>
      <a class="btn btn-primary ml-2 my-2 my-sm-0" href="register.php" role="button">Daftar</a>
    </div>
  </div>
</nav>

<div class="login-container">
    <div class="login-card">
        <form method="post" action="proses.php" class="form-signin">
            <div class="text-center mb-4">
                 <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="currentColor" class="bi bi-box-arrow-in-right mb-3 text-primary" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg>
                <h1 class="form-signin-heading">Silakan Masuk</h1>
            </div>

            <div class="alert-container-login">
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
                         htmlspecialchars($_SESSION['error_message']) .
                         '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    unset($_SESSION['error_message']);
                }
                if (isset($_SESSION['success_message'])) {
                     echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
                         htmlspecialchars($_SESSION['success_message']) .
                         '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    unset($_SESSION['success_message']);
                }
                ?>
            </div>
            
            <div class="form-group">
                <label for="inputEmail" class="sr-only">Alamat Email</label>
                <input type="email" id="inputEmail" class="form-control" placeholder="Alamat Email" required autofocus name="email">
            </div>

            <div class="form-group">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" placeholder="Password" required name="password">
            </div>
            
            <button class="btn btn-lg btn-primary btn-block btn-login" type="submit" name="login">Masuk</button>
            
            <p class="text-center register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
            <p class="mt-4 mb-1 text-muted text-center">&copy; <?php echo date("Y"); ?> Penyewaan Olahraga</p>
        </form>
    </div>
</div>

<script src="./js/jquery-3.2.1.slim.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
    window.setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-container-login .alert');
        alerts.forEach(function(alert) {
            $(alert).fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        });
    }, 5000);
</script>
</body>
</html>