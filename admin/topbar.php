<!-- Navigation bar with fixed-top styling, padding, and green background -->
<nav class="navbar navbar-light fixed-top" style="padding: 0; min-height: 3.5rem; background-color: green;">
    <!-- Container for navigation content with top and bottom margin -->
    <div class="container-fluid mt-2 mb-2">
        <!-- Full-width column layout within the container -->
        <div class="col-lg-12">
            <!-- Empty column for spacing on the left (1/12 of the width) -->
            <div class="col-md-1 float-left" style="display: flex;"></div>
            
            <!-- Column for displaying system name (4/12 of the width) in white text -->
            <div class="col-md-4 float-left text-white">
                <large><b><?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : '' ?></b></large>
            </div>
            
            <!-- Column for the right-aligned section -->
            <div class="float-right">
                <!-- Dropdown menu for account settings with right margin -->
                <div class="dropdown mr-4">
                    <!-- Dropdown toggle link with white text -->
                    <a href="#" class="text-white dropdown-toggle" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['login_name'] ?>
                    </a>
                    <!-- Dropdown menu content positioned 2.5em to the left -->
                    <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
                        <!-- Dropdown items for managing account and logging out -->
                        <a class="dropdown-item" href="javascript:void(0)" id="manage_my_account"><i class="fa fa-cog"></i> Manage Account</a>
                        <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Script to handle the click event on the 'Manage Account' dropdown item -->
<script>
    $('#manage_my_account').click(function () {
        // Open a modal to manage the user account with specific parameters
        uni_modal("Manage Account", "manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own")
    })
</script>
