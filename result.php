<?php
session_start();
require 'db.php'; // Ensure db.php includes your database connection

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Initialize variables for form submission and result display
$registration_no = '';
$year_semester = '';
$resultData = null;
$errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['registration_no']) && isset($_GET['year_semester'])) {
        $registration_no = $_GET['registration_no'];
        $year_semester = $_GET['year_semester'];

        // Validate registration_no as numeric (assuming it's an integer)
        if (!is_numeric($registration_no)) {
            $errorMsg = "Invalid registration number.";
        } else {
            // Query to fetch result based on registration number and year_semester
            $sql = "SELECT result.*, student.registration_no AS registration_no
                    FROM result
                    JOIN student ON result.registration_no_id = student.id
                    WHERE student.registration_no = '$registration_no'
                    AND result.year_semester = '$year_semester'";
            $result = $conn->query($sql);

            if ($result === false) {
                $errorMsg = "Database error: " . $conn->error;
            } elseif ($result->num_rows > 0) {
                // Redirect to result_details.php with matched parameters
                header("Location: https://tnscpewebsite.graymatterworks.com/result_details.php?registration_no=$registration_no&year_semester=$year_semester");
                exit();
            } else {
                // Check if registration number exists
                $checkStudentSql = "SELECT * FROM student WHERE registration_no = '$registration_no'";
                $studentResult = $conn->query($checkStudentSql);
                if ($studentResult->num_rows == 0) {
                    $errorMsg = "Invalid registration number.";
                } else {
                    $errorMsg = "No data found for the student in year semester $year_semester.";
                }
            }
        }
    } elseif (empty($_GET['registration_no']) && empty($_GET['year_semester'])) {
        // No parameters provided, do nothing or show a message
    } else {
        $errorMsg = "Please provide both registration number and semester.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Details</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <!-- Font Awesome CSS for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Google Fonts - Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Custom CSS for the student details page */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      margin: 0;
    }
    .container {
      padding: 20px;
      max-width: 800px;
    }
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      position: relative; /* Ensure relative positioning for absolute child */
    }
    .card-img-top {
      position: absolute;
      top: 130px;
      right: 10px;
      width: 100px; /* Adjust width as needed */
      height: 100px; /* Adjust height as needed */
      object-fit: cover; /* Maintain aspect ratio and cover the area */
      border-radius: 5px; /* Rounded corners */
      border: 2px solid #fff; /* White border around the image */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for better contrast */
    }
    .card-header {
      background-color: #343a40;
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    .card-body {
      padding: 30px;
    }
    .card-title {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 20px;
    }
    .card-text {
      margin-bottom: 15px;
    }
    .body-line {
      border-bottom: 1px solid #dee2e6; /* Gray line */
      margin-bottom: 15px;
    }
    .footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 10px 0;
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 100;
    }
    .navbar {
      background-color: #343a40;
      padding: 15px 0;
      z-index: 100;
    }
    .navbar-brand {
      color: white;
      display: flex;
      align-items: center; /* Align items vertically */
    }
    .navbar-brand img {
      width: 50px; /* Set logo width */
      height: 50px; /* Ensure height matches width for a perfect circle */
      border-radius: 50%; /* Make the image circular */
      margin-right: 10px; /* Adjust margin as needed */
    }
    .navbar-nav {
      margin-left: auto;
    }
    .navbar-nav .nav-item .nav-link {
      color: white;
      padding: 10px 15px;
    }
    .navbar-nav .nav-item:first-child .nav-link {
      margin-left: 0; /* Remove left margin for first item */
    }
    .navbar-dark .navbar-nav .nav-link:hover {
      color: #ced4da;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
&nbsp;&nbsp;&nbsp;&nbsp;
    <a class="navbar-brand" href="#">
      <img src="image/logo.jpeg" alt="Logo"> <!-- Replace with your logo image -->
      TNSCPE
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
        <a class="nav-link" href="student_details.php?registration_no=<?php echo $_SESSION['registration_no']; ?>">
            <i class="fas fa-user"></i> Student Profile
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="result.php"><i class="fas fa-poll"></i> Result</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fas fa-file-pdf">&nbsp;</i>Pdf</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <div class="card">
      <div class="card-header">
        Student Result
      </div>
      <div class="card-body">
        <center><h4>Result</h4></center>
        <br>
        
        <!-- Form to input registration number and select semester -->
        <form method="get" action="">
          <div class="form-group">
            <label for="registration_no">Registration Number</label>
            <input type="text" class="form-control" id="registration_no" name="registration_no" required>
          </div>
          <div class="form-group">
            <label for="year_semester">Semester</label>
            <select class="form-control" id="year_semester" name="year_semester" required>
              <option value="">Select Semester</option>
              <?php
              // Fetch distinct semesters from the result table
              require 'db.php';
              $sql = "SELECT DISTINCT year_semester FROM result";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<option value='{$row['year_semester']}'>{$row['year_semester']}</option>";
                  }
              }
              $conn->close();
              ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Display error message if any -->
                <?php if (!empty($errorMsg)) : ?>
                    <div class="alert alert-danger mt-4" role="alert">
                        <?php echo $errorMsg; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>

