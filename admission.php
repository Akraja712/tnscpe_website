<?php
session_start();
require 'db.php'; // Ensure db.php includes your database connection

// Initialize variables for pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 10; // Number of records to display per page

// Calculate the starting record for the query based on pagination
$start_from = ($page - 1) * $records_per_page;

// Fetch admission details from the database with category name and center name
$sql = "SELECT a.*, c.name AS category_name, ce.center_name
        FROM admission a
        LEFT JOIN category c ON a.category_id = c.id
        LEFT JOIN center ce ON a.center_id = ce.id
        ORDER BY a.id DESC
        LIMIT $start_from, $records_per_page";

$result = $conn->query($sql);

// Check if query was successful
if ($result && $result->num_rows > 0) {
    // Admission details found
    // Fetch all rows and store in an array
    $admissions = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // No admission details found
    $admissions = [];
}

// Count total number of records for pagination
$total_records_sql = "SELECT COUNT(*) AS total_records FROM admission";
$total_records_result = $conn->query($total_records_sql);

if ($total_records_result) {
    $total_records = $total_records_result->fetch_assoc()['total_records'];
    // Calculate total pages
    $total_pages = ceil($total_records / $records_per_page);
} else {
    $total_records = 0;
    $total_pages = 1; // Default to 1 page if no records or error
}

// Define current URL with pagination parameter
$url = $_SERVER['PHP_SELF'];

// Close the database connection
$conn->close();

// Check if redirected with success parameter and display success message
$status = isset($_GET['status']) ? $_GET['status'] : '';
$success_message = '';

if ($status === 'success' && isset($_SESSION['admission_added']) && $_SESSION['admission_added']) {
    $success_message = 'New admission record added successfully!';
    // Unset the session variable to prevent displaying the message on subsequent page loads
    unset($_SESSION['admission_added']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admission Details</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <!-- Font Awesome CSS for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Bootstrap Table CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.3/bootstrap-table.min.css">
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
      max-width: 100%;
    }
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      background-color: #343a40;
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      padding: 10px 20px;
    }
    .card-body {
      padding: 20px;
    }
    .table-container {
      margin-top: 20px;
    }
    .btn-admission {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 5px;
      cursor: pointer;
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
          <a class="nav-link" href="center.php?center_code=<?php echo $_SESSION['center_code']; ?>">
            <i class="fas fa-building"></i> Center Profile
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admission.php"><i class="fas fa-poll"></i> Admission</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container">
  <?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $success_message; ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  <?php endif; ?>


    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            Admission Details
          </div>
          <div class="col text-right">
            <a href="add_admission.php" class="btn btn-admission">New Admission</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-container">
          <table id="admissionTable" data-toggle="table" data-pagination="true" data-search="true" data-sortable="true">
            <thead>
              <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Candidate Name</th>
                <th>Father's Name</th>
                <th>Mother's Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Category Name</th>
                <th>Id Proof Type</th>
                <th>Id Proof No</th>
                <th>Center Name</th>
                <th>Employeed</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($admissions as $admission): ?>
                <tr>
                  <td><?php echo $admission['id']; ?></td>
                  <td>
    <?php
    // Assuming $admission['image'] holds the image path relative to the domain or full URL
    $image_url = $admission['image'];

    // Check if the image URL starts with "https://tnscpe.graymatterworks.com/"
    if (!empty($image_url) && strpos($image_url, 'https://tnscpe.graymatterworks.com/') === 0) {
        // Display the image from the first domain
        echo '<img src="' . $image_url . '" class="img-thumbnail" style="max-width: 100px;">';
    } else {
        // Display the image from the backup domain
        echo '<img src="https://tnscpewebsite.graymatterworks.com/' . $image_url . '" class="img-thumbnail" style="max-width: 100px;">';
    }
    ?>
</td>


                  <td><?php echo $admission['candidate_name']; ?></td>
                  <td><?php echo $admission['fathers_name']; ?></td>
                  <td><?php echo $admission['mothers_name']; ?></td>
                  <td><?php echo $admission['dob']; ?></td>
                  <td><?php echo $admission['gender']; ?></td>
                  <td><?php echo $admission['category_name']; ?></td>
                  <td><?php echo $admission['id_proof_type']; ?></td>
                  <td><?php echo $admission['id_proof_no']; ?></td>
                  <td><?php echo $admission['center_name']; ?></td>
                  <td>
                    <?php
                    // Assuming $admission is your array containing data from the database
                    $employeed = $admission['employeed']; // Get the 'employeed' value from $admission

                    if ($employeed == 1) {
                        echo '<span style="color: green;">Yes</span>';
                    } else {
                        echo '<span style="color: red;">No</span>';
                    }
                    ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- Pagination -->
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <!-- Previous Page Link -->
      <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
        <a class="page-link" href="<?php echo $url . '?page=' . ($page - 1); ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>

      <!-- Page Links -->
      <?php
      // Limit the number of displayed pages around the current page
      $num_links = 5; // Adjust this number based on your preference
      $start_page = max(1, $page - floor($num_links / 2));
      $end_page = min($total_pages, $start_page + $num_links - 1);

      for ($i = $start_page; $i <= $end_page; $i++) {
        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
      }
      ?>

      <!-- Next Page Link -->
      <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
        <a class="page-link" href="<?php echo $url . '?page=' . ($page + 1); ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <!-- Bootstrap Table JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.3/bootstrap-table.min.js"></script>
</body>
</html>
