<?php
// PHP code goes here if needed
?>

<!-- HTML content starts here -->
<div class="container-fluid">

    <!-- Row with a button to add a new user -->
    <div class="row">
        <div class="col-lg-12">
            <button class="btn btn-success float-right btn-sm" id="new_user"><i class="fa fa-plus"></i> New user</button>
        </div>
    </div>

    <!-- Line break for spacing -->
    <br>

    <!-- Row with a card containing a table -->
    <div class="row">
        <div class="card col-lg-12">
            <div class="card-body">
                <!-- Table displaying user data -->
                <table class="table-striped table-bordered col-md-12">
                    <thead>
                        <!-- Table header with columns -->
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // PHP code to fetch user data from the database
                        include 'db_connect.php';
                        $type = array("", "Admin", "User" );
                        $users = $conn->query("SELECT * FROM users order by name asc");
                        $i = 1;
                        // Loop through each user record and display in the table
                        while ($row = $users->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++ ?></td>
                                <td><?php echo ucwords($row['name']) ?></td>
                                <td><?php echo $row['username'] ?></td>
                                <td><?php echo $type[$row['type']] ?></td>
                                <td>
                                    <!-- Action buttons in a dropdown -->
                                    <center>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success" >Action</button>
                                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <!-- Edit and delete options in the dropdown -->
                                                <a class="dropdown-item edit_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Edit</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item delete_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
                                            </div>
                                        </div>
                                    </center>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript script starts here -->
<script>
    // Initialize the DataTable plugin for the table
    $('table').dataTable();

    // Event handler for the "New User" button click
    $('#new_user').click(function () {
        // Open a modal for managing a new user
        uni_modal('New User', 'manage_user.php')
    })

    // Event handler for the "Edit User" link click
    $('.edit_user').click(function () {
        // Open a modal for editing a specific user based on data-id attribute
        uni_modal('Edit User', 'manage_user.php?id=' + $(this).attr('data-id'))
    })

    // Event handler for the "Delete User" link click
    $('.delete_user').click(function () {
        // Show confirmation modal before deleting a user
        _conf("Are you sure to delete this user?", "delete_user", [$(this).attr('data-id')])
    })

    // Function to delete a user based on the provided user ID
	function delete_user($id) {
    // Perform AJAX request to delete the user
    start_load(); // Display a loading indicator or perform some initial actions

    $.ajax({
        url: 'ajax.php?action=delete_user', // The URL for the AJAX request
        method: 'POST', // HTTP method used for the request
        data: { id: $id }, // Data to be sent with the request, including the user ID
        success: function (resp) {
            // Callback function to handle the response from the server
            if (resp == 1) {
                // If the server response is 1 (indicating successful deletion):
                // Show a success message using a custom alert_toast function
                alert_toast("Data successfully deleted", 'success');

                // Reload the page after a delay of 1500 milliseconds (1.5 seconds)
                setTimeout(function () {
                    location.reload(); // Reload the current page
                }, 1500);
            }
        }
    });
}

</script>
