<?php
session_start();
require 'db.php'; // Assuming this file contains database connection logic

$errorMsgStudent = ''; // Initialize student login error message variable
$errorMsgCenter = ''; // Initialize center login error message variable

// Student Login Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_login'])) {
    $registration_no = isset($_POST['username']) ? $_POST['username'] : '';
    $dob = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($registration_no) && !empty($dob)) {
        // Format the dob into YYYY-MM-DD format for comparison with database
        $dob_formatted = date('Y-m-d', strtotime($dob));

        // Prepare SQL query to check student credentials (sanitize inputs properly in actual implementation)
        $sql_query = "SELECT id FROM student WHERE registration_no='$registration_no' AND dob='$dob_formatted'";
        $result = $conn->query($sql_query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];

            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['registration_no'] = $registration_no; // Store registration number in session

            // Redirect to student details page
            header("Location: student_details.php?registration_no=$registration_no");
            exit();
        } else {
            $errorMsgStudent = "Invalid registration number or date of birth";
        }
    } else {
        $errorMsgStudent = "Please provide registration number and date of birth";
    }
}

// Center Login Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['center_login'])) {
    $center_username = isset($_POST['center_username']) ? $_POST['center_username'] : '';
    $center_password = isset($_POST['center_password']) ? $_POST['center_password'] : '';

    if (!empty($center_username) && !empty($center_password)) {
        // Prepare SQL query to fetch center details (sanitize inputs properly in actual implementation)
        $sql_query = "SELECT center_code, password FROM center WHERE center_code='$center_username'";
        $result = $conn->query($sql_query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $correct_username = $row['center_code'];
            $correct_password = $row['password'];

            // Verify password
            if ($center_password === $correct_password) {
                // Set session variables
                $_SESSION['center_code'] = $correct_username; // Store center code in session
                $_SESSION['center_loggedin'] = true; // Example session variable for center login

                // Redirect to center dashboard
                header("Location: center.php?center_code=$correct_username");
                exit();
            } else {
                $errorMsgCenter = "Incorrect password";
            }
        } else {
            $errorMsgCenter = "Invalid username";
        }
    } else {
        $errorMsgCenter = "Please provide both username and password";
    }
}

$conn->close(); // Close database connection

// Include HTML or display forms here
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
              <input type="hidden" name="student_login" value="1"> <!-- Hidden field to differentiate student login -->
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
              <!-- Display student login error message if any -->
              <?php if (!empty($errorMsgStudent)) : ?>
                <div class="alert alert-danger mt-4" role="alert">
                  <?php echo $errorMsgStudent; ?>
                </div>
              <?php endif; ?>
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
            <form method="POST" action="index.php">
              <input type="hidden" name="center_login" value="1"> <!-- Hidden field to differentiate center login -->
              <div class="input-group">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" name="center_username" class="form-control" placeholder="Username" required>
              </div>
              <div class="input-group">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" name="center_password" class="form-control" placeholder="Password" required>
              </div>
              <br>
              <center><button type="submit" class="btn btn-success btn-block">Login</button></center>
              <!-- Display center login error message if any -->
              <?php if (!empty($errorMsgCenter)) : ?>
                <div class="alert alert-danger mt-4" role="alert">
                  <?php echo $errorMsgCenter; ?>
                </div>
              <?php endif; ?>
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
