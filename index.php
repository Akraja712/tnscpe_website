<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration_no = isset($_POST['username']) ? $_POST['username'] : '';
    $dob = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($registration_no) && !empty($dob)) {
        // Format the dob into YYYY-MM-DD format for comparison with database
        $dob_formatted = date('Y-m-d', strtotime($dob));
        
        $sql_query = "SELECT id FROM student WHERE registration_no='$registration_no' AND dob='$dob_formatted'";
        $result = $conn->query($sql_query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['registration_no'] = $registration_no; // Store registration number in session
            header("Location: student_details.php?registration_no=$registration_no");
            exit();
        } else {
            $error = "Invalid registration number or date of birth";
        }
    } else {
        $error = "Please provide registration number and date of birth";
    }
    echo "<script>alert('$error');</script>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Two Login Pages with Bootstrap</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <!-- Font Awesome CSS for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Google Fonts - Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    .custom-mt-5 {
      margin-top: 50px;
    }
    .card-body {
      padding: 30px;
    }
    .card-title {
      margin-bottom: 20px;
      font-family: 'Poppins', sans-serif;
    }
    .input-group-text {
      background-color: #28a745;
      border-color: #ced4da;
      color: white;
    }
    .btn {
      width: 150px;
    }
    .input-group {
      margin-bottom: 25px;
    }
    .card {
      height: 380px;
      border-radius: 25px;
    }
  </style>
</head>
<body>
  <br><br>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <!-- Student Login Form -->
      <div class="col-lg-5 col-md-8 col-sm-10 mt-4">
        <div class="card shadow">
          <div class="card-body">
            <h5 class="card-title text-center">Welcome to Student Login</h5>
            <br>
            <!-- Form content for student login -->
            <form method="POST" action="index.php">
              <div class="input-group">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" name="username" class="form-control" placeholder="Registration Number" required>
              </div>
              <div class="input-group">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="date" name="password" class="form-control" placeholder="Password (DD-MM-YYYY)" required>
              </div>
              <p>Use Password as your DOB with (-) like DD-MM-YYYY</p>
              <br>
              <center><button type="submit" class="btn btn-success btn-block">Login</button></center>
            </form>
            
          </div>
        </div>
      </div>

      <!-- Center Login Form -->
      <div class="col-lg-5 col-md-8 col-sm-10 mt-4">
        <div class="card shadow">
          <div class="card-body">
            <h5 class="card-title text-center">Welcome to Center Login</h5>
            <br>
            <!-- Form content for center login -->
            <form method="POST" action="#">
              <div class="input-group">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
              </div>
              <div class="input-group">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="text" name="password" class="form-control" placeholder="Password (DD-MM-YYYY)" required>
              </div>
              <p>Use Password as your DOB with (-) like DD-MM-YYYY</p>
              <br>
              <center><button type="submit" class="btn btn-success btn-block">Login</button></center>
            </form>
           
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies (optional) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
