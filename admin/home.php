<?php include 'db_connect.php' ?>


<div class="containe-fluid"style="background-image: url('./assets/images/green1.jpg');">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
                    <hr>
                </div>
            </div>      			
        </div>
    </div>
</div>