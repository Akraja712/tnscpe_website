<?php
session_start();
require 'db.php'; // Ensure db.php includes your database connection
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}
// Fetch PDF records from the database
$sql_query = "SELECT id,name, pdf_file FROM pdf";
$result = $conn->query($sql_query);

$base_url = "https://tnscpe.graymatterworks.com/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel with Bootstrap Navbar</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <!-- Font Awesome CSS for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Google Fonts - Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Custom CSS for sidebar and layout */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      margin: 0;
    }
    .main-content {
      padding: 20px;
      width: 100%;
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

    @media (max-width: 992px) {
      .main-content {
        margin-top: 50px; /* Adjust margin to avoid overlapping with fixed navbar */
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
          <a class="nav-link" href="pdf.php"><i class="fas fa-file-pdf">&nbsp;</i>Pdf</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid">
    <!-- Page Content -->
    <div class="main-content">
      <h2>PDF List</h2>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
            <th>ID</th>
              <th>Name</th>
              <th>PDF File</th>
              <th>Link</th>
            </tr>
          </thead>
          <tbody>
          <?php
            if ($result->num_rows > 0) {
                while($pdf = $result->fetch_assoc()) {
                    $pdf_url = $base_url . htmlspecialchars($pdf['pdf_file']);
                    $pdf_file_name = basename($pdf['pdf_file']); // Extract only the file name
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pdf['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($pdf['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($pdf_file_name) . "</td>";
                    echo "<td><a href='" . $pdf_url . "' target='_blank'>Open PDF</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No PDFs found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
