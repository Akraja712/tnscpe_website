<?php
session_start();
require 'db.php'; // Ensure db.php includes your database connection

$DOMAIN_URL = "https://tnscpe.graymatterworks.com/";

// Fetch categories from the database
$sql_categories = "SELECT id, name FROM category";
$result_categories = $conn->query($sql_categories);

// Generate options for the category dropdown
$category_options = '';
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $id = $row['id'];
        $name = htmlspecialchars($row['name']);
        $category_options .= "<option value='$id'>$name</option>";
    }
}

// Fetch centers from the database
$sql_centers = "SELECT id, center_name FROM center"; // Adjust table name as per your database structure
$result_centers = $conn->query($sql_centers);

// Generate options for the center dropdown
$center_options = '';
if ($result_centers && $result_centers->num_rows > 0) {
    while ($row = $result_centers->fetch_assoc()) {
        $center_id = $row['id'];
        $center_name = htmlspecialchars($row['center_name']);
        $center_options .= "<option value='$center_id'>$center_name</option>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $candidate_name = mysqli_real_escape_string($conn, $_POST['candidate_name']);
    $fathers_name = mysqli_real_escape_string($conn, $_POST['fathers_name']);
    $mothers_name = mysqli_real_escape_string($conn, $_POST['mothers_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $id_proof_type = mysqli_real_escape_string($conn, $_POST['id_proof_type']);
    $id_proof_no = mysqli_real_escape_string($conn, $_POST['id_proof_no']);
    $employeed = ($_POST['employeed'] == 'Yes') ? 1 : 0;
    $center_id = mysqli_real_escape_string($conn, $_POST['center_id']);

    // Handle image upload
    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        // Process image upload
        $target_dir = "upload/images/";
        $temp_name = $_FILES["image"]["tmp_name"];
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . strtolower($extension);
        $target_path = $_SERVER['DOCUMENT_ROOT'] . $DOMAIN_URL . $target_dir;
        $full_path = $target_path . $filename;

        if (move_uploaded_file($temp_name, $full_path)) {
            $upload_image = $target_dir . $filename;

            // Insert data into the database
            $sql = "INSERT INTO admission (candidate_name, image, fathers_name, mothers_name, dob, gender, category_id, id_proof_type, id_proof_no, employeed, center_id) 
                    VALUES ('$candidate_name', '$upload_image','$fathers_name','$mothers_name','$dob','$gender','$category_id','$id_proof_type','$id_proof_no','$employeed','$center_id')";
            if ($conn->query($sql) === TRUE) {
                // Redirect with success message
                $_SESSION['admission_added'] = true;
                header("Location: admission.php?status=success");
                exit();
            } else {
                echo '<p class="alert alert-danger">Error: ' . $sql . '<br>' . $conn->error . '</p>';
            }
        } else {
            echo '<p class="alert alert-danger">Failed to upload image.</p>';
        }
    } else {
        // If no image uploaded
        $sql = "INSERT INTO admission (candidate_name, fathers_name, mothers_name, dob, gender, category_id, id_proof_type, id_proof_no, employeed, center_id) 
                VALUES ('$candidate_name','$fathers_name','$mothers_name','$dob','$gender','$category_id','$id_proof_type','$id_proof_no','$employeed','$center_id')";
        if ($conn->query($sql) === TRUE) {
            // Redirect with success message
            header("Location: admission.php?status=success");
            exit();
        } else {
            echo '<p class="alert alert-danger">Error: ' . $sql . '<br>' . $conn->error . '</p>';
        }
    }
}

// Close database connection
$conn->close();
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
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col">
            Admission Details
          </div>
        
        </div>
      </div>
      <div class="card-body">
      <form action="add_admission.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" name="candidate_name" class="form-control" placeholder="Candidate Name" required>
          </div>
          <div class="form-group">
            <input type="text" name="fathers_name" class="form-control" placeholder="Father's Name" required>
          </div>
          <div class="form-group">
            <input type="text" name="mothers_name" class="form-control" placeholder="Mother's Name" required>
          </div>
          <div class="form-group">
            <input type="date" name="dob" class="form-control" required>
          </div>
          <div class="form-group">
            <select name="gender" class="form-control" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group">
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php echo $category_options; ?>
                        </select>
                    </div>
                    <div class="form-group">
    <select name="id_proof_type" class="form-control" required onchange="updateIdProofNumberLength()">
        <option value="">Select ID Type</option>
        <option value="aadhaarcard">Aadhaar Card</option>
        <option value="hsc">HSC</option>
        <option value="sslc">SSLC</option>
    </select>
</div>
<div class="form-group">
    <input type="text" name="id_proof_no" class="form-control" placeholder="ID Proof No" required>
</div>

          <div class="form-group">
            <select name="employeed" class="form-control" required>
              <option value="">Employeed?</option>
              <option value="Yes">Yes</option>
              <option value="No">No</option>
            </select>
          </div>
          <div class="form-group">
    <select name="center_id" class="form-control" required>
        <option value="">Select Center</option>
        <?php echo $center_options; ?>
    </select>
</div>
          <div class="form-group">
            <input type="file" name="image" class="form-control-file" accept="image/*" required>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script>
    function updateIdProofNumberLength() {
        var idProofType = document.getElementsByName('id_proof_type')[0].value;
        var idProofNoInput = document.getElementsByName('id_proof_no')[0];

        if (idProofType === 'aadhaarcard') {
            idProofNoInput.setAttribute('maxlength', '12');
            idProofNoInput.setAttribute('minlength', '12');
        } else if (idProofType === 'hsc') {
            idProofNoInput.setAttribute('maxlength', '6');
            idProofNoInput.setAttribute('minlength', '6');
        } else if (idProofType === 'sslc') {
            idProofNoInput.setAttribute('maxlength', '7');
            idProofNoInput.setAttribute('minlength', '7');
        } else {
            idProofNoInput.removeAttribute('maxlength');
            idProofNoInput.removeAttribute('minlength');
        }
    }
</script>



</body>
</html>
