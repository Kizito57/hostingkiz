<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to your database (replace with your database credentials)
    $conn = new mysqli('localhost', 'root', '', 'bidding_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // You should perform more validation and sanitation here

    // Include the Action class
    include 'ajax.php';
    $crud = new Action();

    // Call the signup method
    $save = $crud->signup($name, $email, $password);

    if ($save == 1) {
        echo 'Signup successful';
    } elseif ($save == 2) {
        echo 'Email already exists';
    } else {
        echo 'Signup failed';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <form id="signup-form" method="post" action="">
            <h2>Sign Up</h2>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit">Sign Up</button>
            </div>

            <p>Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="signup.js"></script>
</body>

</html>

<style>
    .container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>

<script>
    function validateForm() {
        var name = document.getElementById('name').value;
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;

        if (name === '' || email === '' || password === '') {
            alert('All fields must be filled out');
            return false;
        }

        // You can add more sophisticated validation here

        // If all validations pass, submit the form
        document.getElementById('signup-form').submit();
    }
</script>
