<?php
session_start();
require 'db.php'; // Ensure db.php includes your database connection

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

if (isset($_GET['registration_no'])) {
    $registration_no = $_GET['registration_no'];

    $sql = "SELECT result.*, student.registration_no AS registration_no
            FROM result
            JOIN student ON result.registration_no_id = student.id
            WHERE student.registration_no = '$registration_no'";
$result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display student details
        $row = $result->fetch_assoc();
        
        // Display other relevant details
    } else {
        echo "No student details found for the given user ID.";
    }

    $conn->close();
} else {
    echo "No user ID provided.";
}
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
        <center><h4>Result:</h4></center>
        <br>
        <p class="card-text"><strong>Registration No:</strong> <?php echo $row['registration_no']; ?></p>
        <p class="card-text"><strong>Year/Semester:</strong> <?php echo $row['year_semester']; ?></p>
        <p class="card-text"><strong>Exam Month and Year:</strong> <?php echo $row['exam_month_year']; ?></p>
        <p class="card-text"><strong>Total Marks:</strong> <?php echo $row['total_marks']; ?></p>
        <p class="card-text"><strong>Obtained Marks:</strong> <?php echo $row['obtained_marks']; ?></p>
        <p class="card-text"><strong>SGPA:</strong> <?php echo $row['sgpa']; ?></p>
        <p class="card-text"><strong>Status:</strong>
         <?php
            if ($row['status'] == 1) {
              echo '<span style="color: green;">Pass</span>';
             } else {
                echo '<span style="color: red;">Fail</span>';
             }
           ?>
                        </p>
        <!-- Additional details as needed -->
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
