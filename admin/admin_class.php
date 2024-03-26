<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		// Extracting values from the $_POST array
		extract($_POST);
	
		// Query the database for a user with the provided username and hashed password
		$qry = $this->db->query("SELECT * FROM users WHERE username = '".$username."' AND password = '".md5($password)."' ");
	
		// Check if a user with the provided credentials exists
		if($qry->num_rows > 0){
			// Fetch user data and store it in the session, excluding the 'password' field
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
	
			// Check if the logged-in user is not an admin (type != 1)
			if ($_SESSION['login_type'] != 1){
				// If not an admin, clear the session and return 2 to indicate an error
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				return 2;
				exit;
			}
	
			return 1; // Return 1 to indicate successful login for an admin
		} else {
			return 3; // Return 3 to indicate login failure
		}
	}

	function logout(){
		// Destroy the session and clear session variables
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
	
		// Redirect to the login page after logout
		header("location: login.php");
	}


	
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		// Extracting values from the $_POST array
		extract($_POST);
	
		// Building the data string for insertion into the 'users' table
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = '1' "; // Set user type to admin
	
		// Checking if a user with the same name already exists
		$chk = $this->db->query("SELECT * FROM users WHERE name = '$name' ")->num_rows;
	
		// If a user with the same name exists, return 2 to indicate an error
		if ($chk > 0){
			return 2;
			exit;
		}
	
		// Inserting the new user data into the 'users' table
		$save = $this->db->query("INSERT INTO users SET ".$data);
	
		// If the user data is successfully saved, attempt to log in the new user
		if ($save){
			$login = $this->login();
	
			// If login is successful, return the login status
			if ($login)
				return $login;
		}
	}
	function update_account(){
		// Extracting values from the $_POST array
		extract($_POST);
	
		// Building the data string for updating the user's account information
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
	
		// Checking if a password is provided and updating it if necessary
		if (!empty($password))
			$data .= ", password = '".md5($password)."' ";
	
		// Checking if another user with the same email exists
		$chk = $this->db->query("SELECT * FROM users WHERE username = '$email' AND id != '{$_SESSION['login_id']}' ")->num_rows;
	
		// If another user with the same email exists, return 2 to indicate an error
		if ($chk > 0){
			return 2;
			exit;
		}
	
		// Updating the user's account information in the 'users' table
		$save = $this->db->query("UPDATE users SET $data WHERE id = '{$_SESSION['login_id']}' ");
	
		// If the update is successful, proceed to update additional user data
		if ($save){
			$data = '';
	
			// Building the data string for updating additional user data
			foreach($_POST as $k => $v){
				if ($k == 'password')
					continue;
	
				if (empty($data) && !is_numeric($k))
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
	
			// Checking if an image file is provided and updating the avatar field accordingly
			if ($_FILES['img']['tmp_name'] != ''){
				$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname);
				$data .= ", avatar = '$fname' ";
			}
	
			// Updating additional user data in the 'alumnus_bio' table
			$save_alumni = $this->db->query("UPDATE alumnus_bio SET $data WHERE id = '{$_SESSION['bio']['id']}' ");
	
			// If all updates are successful, log out the user, log in again, and return 1
			if ($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
	
				$login = $this->login2();
	
				if ($login)
					return 1;
			}
		}
	}

	function save_settings(){
		// Extracting values from the $_POST array
		extract($_POST);
	
		// Building the data string for updating system settings
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
	
		// Checking if an image file is provided and updating the cover_img field accordingly
		if ($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";
		}
	
		// Checking if system settings already exist
		$chk = $this->db->query("SELECT * FROM system_settings");
	
		// If system settings exist, update them. Otherwise, insert a new record.
		if ($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings SET ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings SET ".$data);
		}
	
		// If the update or insert is successful, retrieve the updated system settings
		if ($save){
			$query = $this->db->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
	
			// Update the $_SESSION['system'] array with the new system settings
			foreach ($query as $key => $value) {
				if (!is_numeric($key))
					$_SESSION['system'][$key] = $value;
			}
	
			return 1; // Return 1 to indicate success
		}
	}
	
	function save_category(){
		// Extracting values from the $_POST array
		extract($_POST);
	
		// Building the data string for saving or updating a category
		$data = " name = '$name' ";
	
		// Checking if the category ID is empty. If empty, insert a new record. Otherwise, update the existing record.
		if (empty($id)){
			$save = $this->db->query("INSERT INTO categories SET $data");
		}else{
			$save = $this->db->query("UPDATE categories SET $data WHERE id = $id");
		}
	
		// If the save operation is successful, return 1 to indicate success
		if ($save)
			return 1;
	}
	
	function delete_category(){
		// Extracting values from the $_POST array
		extract($_POST);
	
		// Deleting a category based on the provided ID
		$delete = $this->db->query("DELETE FROM categories WHERE id = ".$id);
	
		// If the delete operation is successful, return 1 to indicate success
		if ($delete){
			return 1;
		}
	}
	// This function saves a product to the database by inserting a new record or updating an existing one
function save_product(){
    // Extract the POST data into variables for easier access
    extract($_POST);
    
    // Initialize empty data string
    $data = "";
    
    // Loop through the POST data
    foreach($_POST as $k => $v){
        // Skip the id and img keys since we handle those separately
        if(!in_array($k, array('id','img')) && !is_numeric($k)){
            // Append the key/value to the data string
            if(empty($data)){
                $data .= " $k='$v' "; 
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
        
    // If no id provided, insert a new record 
    if(empty($id)){
        $save = $this->db->query("INSERT INTO products set $data");
        $id = $this->db->insert_id;
    }else{
        // Otherwise update the existing record with the id provided
        $save = $this->db->query("UPDATE products set $data where id = $id");
    }

    // If the save was successful
    if($save){

        // Check if an image was uploaded
        if($_FILES['img']['tmp_name'] != ''){
            // Get the file extension
            $ftype= explode('.',$_FILES['img']['name']);
            $ftype= end($ftype);
            
            // Generate a filename using the product id
            $fname =$id.'.'.$ftype;
            
            // Delete any existing image file
            if(is_file('assets/uploads/'. $fname))
                unlink('assets/uploads/'. $fname);
                
            // Move the uploaded image 
            $move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
            
            // Update the image filename in the database
            $save = $this->db->query("UPDATE products set img_fname='$fname' where id = $id");
        }
        
        // Return 1 to indicate success
        return 1;
    }
}

// Deletes a product by id 
function delete_product(){
    // Extract id
    extract($_POST);
    
    // Execute delete query
    $delete = $this->db->query("DELETE FROM products where id = ".$id);
    
    // Return 1 if successful
    if($delete){
        return 1;
    }
}

// Gets the latest bid amount for a product
function get_latest_bid(){
    // Extract product id
    extract($_POST);
    
    // Query to get latest bid by product id ordered by bid amount
    $get = $this->db->query("SELECT * FROM bids where product_id = $product_id order by bid_amount desc limit 1 ");
    
    // Set bid to amount or 0 if no rows
    $bid = $get->num_rows > 0 ? $get->fetch_array()['bid_amount'] : 0 ;
    
    // Return bid amount
    return $bid;
}

}