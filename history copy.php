<?php
session_start();
require  'config.php';
if($_SESSION['Level'] != "user"){
    header("location:login.php");
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM pesanan JOIN users ON pesanan.ID_user = users.ID JOIN lapangan ON pesanan.ID_lapangan = lapangan.ID WHERE users.ID='$user_id'";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <link rel="stylesheet" href="./css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/bootstrap.css">

  <script src="./js/jquery-3.2.1.slim.min.js"></script>
  <script src="./js/popper.min.js"></script>
  <script src="./js/bootstrap.min.js" ></script>

</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="user_home.php">Penyewaan</a>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item active">
        <a class="nav-link" href="user_home.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="booking.php ">Booking</a>
    </li>
    </ul>
    <!-- <form class="form-inline my-2 my-lg-0"> -->
<!-- Acc opt drop -->
<div class="btn-group m-3">
  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <?php echo "$username" ?> 
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="">Profil</a>
    <a class="dropdown-item" href="history.php">Riwayat</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah anda ingin logout')">Keluar</a>
  </div>
</div>
  </div>
</nav>

<div class="row">


  <div class="col-4">
    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-success text-success" disabled>Berlangsung</a>
      
      <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Pesanan | <?php echo $row['Nama']?></a>
      <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" role="tab" aria-controls="messages">Messages</a>
      <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings" role="tab" aria-controls="settings">Settings</a>
      <a class="list-group-item list-group-item-danger text-dark" disabled>Selesai</a>
    </div>
  </div>
  <div class="col-8">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
        <div class="text-center">
            <h3 class="font-weight-normal mt-3">Reservasi | <?php echo $row['Nama']  ?></h3>
            <img src="./img/Garasi-Futsal.jpg" alt="" style="width: 74vh; height: 40vh;">
            <h6> <?php  echo json_encode($row)?> </h6> 
                <form method="post" action="proses.php">
                <div class="row mt-3">
                  <div class="col-6">
                    <label for="" class="mt-1">Nama</label>
                    <input type="text" class="form-control mt-2" value="<?php echo $username ?>" placeholder="Name" readonly autofocus="" name="name">
                  </div>
                  <div class="col-6">
                    <label for="" class="mt-1">No. telp</label>
                    <input type="text" class="form-control mt-2" placeholder="<?php echo $row['Telepon'] ?>" readonly autofocus="" name="phone">
                  </div>
                </div>
                <div class="row">
                  <div class="col-4">
                    <label for="" class="mt-1">Tanggal</label>
                    <input type="text"  class="form-control mt-2" placeholder="<?php echo $row['Tanggal'] ?>" readonly name="date">  
                  </div>
                  <div class="col-4">
                    <label for="" class="mt-1">Waktu</label>
                    <input type="text"  class="form-control mt-2" placeholder="<?php echo $row['Jam'] ?>" readonly name="time">
                  </div>
                  <div class="col-4">
                    <label for="" class="mt-1">Lama sewa</label>
                    <input type="number"  class="form-control mt-2" placeholder="<?php echo $row['durasi'] ?>" readonly name="hour">
                  </div>
                </div>
                <h4 class="text-center font-weight-normal mt-3">Tambahan</h4>
                <div class="row">
                  <div class="col-6">
                    <label for="" class="">Sepatu </label>
                    <input type="number"  class="form-control mt-2" placeholder="<?php echo $row['Tambahan_1'] ?>" readonly name="shoes">
                  </div>
                  <div class="col-6">
                    <label for="" class="">kostum </label>
                    <input type="number"  class="form-control mt-2" placeholder="<?php echo $row['Tambahan_2'] ?>" readonly name="costume">
                  </div>
                </div>
                <div class="row">
                  <div class="col-4">
                    <label for="" class="">Total</label>
                    <input type="text" class="form-control mt-2" placeholder="<?php echo $row['total_harga'] ?>" readonly autofocus="" name="pay">
                  </div>
                  <div class="col-4">
                  <label for="" class="">Bayar</label>
                    <input type="text" class="form-control mt-2" placeholder="<?php echo $row['bayar'] ?>" readonly autofocus="" name="pay">
                  </div>
                  <div class="col-4">
                  <label for="" class="">Kembali</label>
                    <input type="text" class="form-control mt-2" placeholder="<?php echo $row['kembali'] ?>" readonly autofocus="" name="pay">
                  </div>
                </div>
                <!-- Hidden Input -->
                <input type="hidden" class="form-control mt-2" value="<?php echo $row['Harga'] ?>" required autofocus="" name="field_price">
                <input type="hidden" class="form-control mt-2" value="<?php echo $row['ID'] ?>" required autofocus="" name="field_id">
                <input type="hidden" class="form-control mt-2" value="<?php echo "$user_id" ?>" required autofocus="" name="user_id">
            <button class="btn btn-lg btn-danger btn-block mt-2" type="submit" value="submit" name="end">selesai</button>
            </form>
        </div>
      </div>
      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list"></div>
      <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">...</div>
    </div>
  </div>

</div>
</body>
</html>