<!DOCTYPE html>
<html lang="en">

<?php
// Start a session to store user data across multiple pages
session_start();

// Include a file that connects to the database
include('./db_connect.php');

// Start output buffering
ob_start();

// Check if 'system' session variable is not set
if (!isset($_SESSION['system'])) {
  // Fetch system settings from the database and store them in the session
  $system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
  foreach ($system as $k => $v) {
    $_SESSION['system'][$k] = $v;
  }
}

// Flush the output buffer
ob_end_flush();
?>

<head>
  <!-- Meta tags for character set and viewport -->
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <!-- Set the title of the page based on the 'name' from system settings -->
  <title><?php echo $_SESSION['system']['name'] ?></title>

  <!-- Include a header file -->
  <?php include('./header.php'); ?>

  <?php
  // If the user is already logged in, redirect to the home page
  if (isset($_SESSION['login_id']))
    header("location:index.php?page=home");
  ?>
</head>

<style>
  /* Styling for the body and card */
  body {
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-image: url('./assets/images/Wall 1.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
  }

  /* Styling for the login card */
  .card {
    max-width: 400px;
    padding: 20px;
    border-radius: 10px;
    background-image: url('./assets/images/Equal Offers logo.png');
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
  }

  /* Styling for the login form */
  #login-form {
    text-align: center;
  }
</style>

<body>
  <!-- Main content of the page -->
  <main id="main" class="d-flex align-items-center justify-content-center h-100">
    <div class="card">
      <div class="card-body">
        <!-- Login form -->
        <form id="login-form" class="text-center">
          <div class="form-group">
            <!-- Input field for username -->
            <input type="text" id="username" name="username" class="form-control" placeholder="Username">
          </div>
          <div style="margin-bottom: 1rem;"></div>
          <div class="form-group">
            <!-- Input field for password -->
            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
          </div>
          <div style="margin-bottom: 12rem;"></div>
          <!-- Submit button for the login form -->
          <button type="submit" class="btn btn-primary">Login</button>
          <!-- Added sign up link -->
          <p class="mt-3">Don't have an account? <a href="signup.php">Sign up</a></p>
        </form>
      </div>
    </div>
  </main>
</body>

<script>
  // JavaScript code for handling form submission via AJAX
  $('#login-form').submit(function (e) {
    e.preventDefault(); // Prevent the default form submission
    $('#login-form button[type="button"]').attr('disabled', true).html('Logging in...'); // Disable the submit button and update its text

    // Remove any existing error messages
    if ($(this).find('.alert-danger').length > 0)
      $(this).find('.alert-danger').remove();

    // Perform an AJAX request to handle login
    $.ajax({
      url: 'ajax.php?action=login', // URL to the server-side script
      method: 'POST', // HTTP method
      data: $(this).serialize(), // Serialize form data for submission
      error: err => {
        console.log(err);
        // If an error occurs, enable the submit button and update its text
        $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
      },
      success: function (resp) {
        // If the login is successful, redirect to the home page
        if (resp == 1) {
          window.location.href = 'index.php?page=home';
        } else {
          // If login fails, display an error message and enable the submit button
          $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
          $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
        }
      }
    });
  });
</script>
</html>
