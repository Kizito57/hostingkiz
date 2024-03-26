<!DOCTYPE html>
<html lang="en">
	
<?php session_start(); ?>

<!-- Comment: Start the PHP session to manage user data across pages -->

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : '' ?></title>
 	 <!-- Comment: Set the character set, viewport, and page title using system name from the session -->

<?php
  if(!isset($_SESSION['login_id']))
    header('location:login.php');

     // Comment: Redirect to the login page if the user is not logged in
 include('./header.php'); 
 

   // Comment: Include a header file, possibly for common HTML head elements
    // include('./auth.php');
    // Comment: (Possibly) Include authentication-related code
 date_default_timezone_set('Africa/Nairobi');
 ?>

</head>
<style>
	body{
        background: #80808045;
  }
  .modal-dialog.large {
    width: 80% !important;
    max-width: unset;
  }
  .modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
  }
  #viewer_modal .btn-close {
    position: absolute;
    z-index: 999999;
    /*right: -4.5em;*/
    background: unset;
    color: white;
    border: unset;
    font-size: 27px;
    top: 0;
}
#viewer_modal .modal-dialog {
        width: 80%;
    max-width: unset;
    height: calc(90%);
    max-height: unset;
}
  #viewer_modal .modal-content {
       background: black;
    border: unset;
    height: calc(100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  #viewer_modal img,#viewer_modal video{
    max-height: calc(100%);
    max-width: calc(100%);
  }
</style>

<body>
	<?php include 'topbar.php' ?>
	<?php include 'navbar.php' ?>
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
  </div>
  
  <main id="view-panel" >
      <?php $page = isset($_GET['page']) ? $_GET['page'] :'home'; ?>
  	<?php include $page.'.php' ?>
  	

  </main>

  <div id="preloader"></div>
  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
</body>
<script>
   // Function to start the preloader
	 window.start_load = function(){
    $('body').prepend('<di id="preloader2"></di>')
  }
   // Function to end the preloader by fading it out and removing it
  window.end_load = function(){
    $('#preloader2').fadeOut('fast', function() {
        $(this).remove();
      })
  }
  // Function to display a modal for viewing images or videos
 window.viewer_modal = function($src = ''){
    start_load()// Start the preloader
    // Extract the file extension from the source
    var t = $src.split('.')
    t = t[1]

    // Create the appropriate view element (image or video) based on the file extension
    if(t =='mp4'){
      var view = $("<video src='"+$src+"' controls autoplay></video>")
    }else{
      var view = $("<img src='"+$src+"' />")
    }

    // Remove any existing video or image elements in the modal content
    $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
     // Append the new view element to the modal content
    $('#viewer_modal .modal-content').append(view)
     // Display the viewer modal with specific settings
    $('#viewer_modal').modal({
            show:true,
            backdrop:'static',
            keyboard:false,
            focus:true
          })
          end_load()  // End the preloader

}
// Function to display a generic modal with content loaded via AJAX
  window.uni_modal = function($title = '' , $url='',$size=""){
    start_load() // Start the preloader

     // Make an AJAX request to load content from the specified URL
    $.ajax({
        url:$url,
        error:err=>{
            console.log()
            alert("An error occured")
        },
        success:function(resp){
            if(resp){
                // Set the title and body of the modal with the loaded content
                $('#uni_modal .modal-title').html($title)
                $('#uni_modal .modal-body').html(resp)

                  // Adjust the size of the modal if specified
                if($size != ''){
                    $('#uni_modal .modal-dialog').addClass($size)
                }else{
                    $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
                }
                // Display the modal with specific settings
                $('#uni_modal').modal({
                  show:true,
                  backdrop:'static',
                  keyboard:false,
                  focus:true
                })
                end_load() // End the preloader
            }
        }
    })
}

 // Function to set up a confirmation modal with a message and callback function
window._conf = function($msg='',$func='',$params = []){
   // Set the onclick attribute of the confirm button with the callback function and parameters
     $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
     // Set the message in the modal body
     $('#confirm_modal .modal-body').html($msg)
     // Display the confirmation modal
     $('#confirm_modal').modal('show')
  }
   // Function to display a Bootstrap toast notification
   window.alert_toast= function($msg = 'TEST',$bg = 'success'){
    // Remove existing background classes
      $('#alert_toast').removeClass('bg-success')
      $('#alert_toast').removeClass('bg-danger')
      $('#alert_toast').removeClass('bg-info')
      $('#alert_toast').removeClass('bg-warning')

      // Add the specified background class
    if($bg == 'success')
      $('#alert_toast').addClass('bg-success')
    if($bg == 'danger')
      $('#alert_toast').addClass('bg-danger')
    if($bg == 'info')
      $('#alert_toast').addClass('bg-info')
    if($bg == 'warning')
      $('#alert_toast').addClass('bg-warning')
     // Set the toast message and display it
    $('#alert_toast .toast-body').html($msg)
    $('#alert_toast').toast({delay:3000}).toast('show');
  }

  // On document ready, fade out and remove the initial preloader
  $(document).ready(function(){
    $('#preloader').fadeOut('fast', function() {
        $(this).remove();
      })
  })

   // Initialize date-time pickers with specific settings
  $('.datetimepicker').datetimepicker({
      format:'Y/m/d H:i',
      startDate: '+3d'
  })

  // Initialize select2 dropdowns with specific settings
  $('.select2').select2({
    placeholder:"Please select here",
    width: "100%"
  })
</script>	
</html>