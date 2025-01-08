<?php

include_once('includes/config.php');

require 'vendor/autoload.php'; // Include the Composer autoloader

use Intervention\Image\ImageManagerStatic as Image;

// show PHP errors
ini_set('display_errors', 1);

// output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

$action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

if ($action == 'email_invoice'){

	$fileId = $_POST['id'];
	$emailId = $_POST['email'];
	$invoice_type = $_POST['invoice_type'];
	$custom_email = $_POST['custom_email'];

	require_once('class.phpmailer.php');

	$mail = new PHPMailer(); // defaults to using php "mail()"

	$mail->AddReplyTo(EMAIL_FROM, EMAIL_NAME);
	$mail->SetFrom(EMAIL_FROM, EMAIL_NAME);
	$mail->AddAddress($emailId, "");

	$mail->Subject = EMAIL_SUBJECT;
	//$mail->AltBody = EMAIL_BODY; // optional, comment out and test
	if (empty($custom_email)){
		if($invoice_type == 'invoice'){
			$mail->MsgHTML(EMAIL_BODY_INVOICE);
		} else if($invoice_type == 'quote'){
			$mail->MsgHTML(EMAIL_BODY_QUOTE);
		} else if($invoice_type == 'receipt'){
			$mail->MsgHTML(EMAIL_BODY_RECEIPT);
		}
	} else {
		$mail->MsgHTML($custom_email);
	}

	$mail->AddAttachment("./invoices/".$fileId.".pdf"); // attachment

	if(!$mail->Send()) {
		 //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mail->ErrorInfo.'</pre>'
	    ));
	} else {
	   echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Invoice has been successfully send to the customer'
		));
	}

}
// download invoice csv sheet
if ($action == 'download_csv'){

	header("Content-type: text/csv"); 

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
	}
 
    $file_name = 'invoice-export-'.date('d-m-Y').'.csv';   // file name
    $file_path = 'downloads/'.$file_name; // file path

	$file = fopen($file_path, "w"); // open a file in write mode
    chmod($file_path, 0777);    // set the file permission

    $query_table_columns_data = "SELECT * 
									FROM invoices i
									JOIN customers c
									ON c.invoice = i.invoice
									WHERE i.invoice = c.invoice
									ORDER BY i.invoice";

    if ($result_column_data = mysqli_query($mysqli, $query_table_columns_data)) {

    	// fetch table fields data
        while ($column_data = $result_column_data->fetch_row()) {

            $table_column_data = array();
            foreach($column_data as $data) {
                $table_column_data[] = $data;
            }

            // Format array as CSV and write to file pointer
            fputcsv($file, $table_column_data, ",", '"');
        }

	}

    //if saving success
    if ($result_column_data = mysqli_query($mysqli, $query_table_columns_data)) {
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'CSV has been generated and is available in the /downloads folder for future reference, you can download by <a href="downloads/'.$file_name.'">clicking here</a>.'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

 
    // close file pointer
    fclose($file);

    $mysqli->close();

}

// Create customer
if ($action == 'create_customer'){

	// invoice customer information
	// billing
	$customer_name = $_POST['customer_name']; // customer name
	$customer_email = $_POST['customer_email']; // customer email
	$customer_address_1 = $_POST['customer_address_1']; // customer address
	$customer_address_2 = $_POST['customer_address_2']; // customer address
	$customer_town = $_POST['customer_town']; // customer town
	$customer_county = $_POST['customer_county']; // customer county
	$customer_postcode = $_POST['customer_postcode']; // customer postcode
	$customer_phone = $_POST['customer_phone']; // customer phone number
	
	//shipping
	$customer_name_ship = $_POST['customer_name_ship']; // customer name (shipping)
	$customer_address_1_ship = $_POST['customer_address_1_ship']; // customer address (shipping)
	$customer_address_2_ship = $_POST['customer_address_2_ship']; // customer address (shipping)
	$customer_town_ship = $_POST['customer_town_ship']; // customer town (shipping)
	$customer_county_ship = $_POST['customer_county_ship']; // customer county (shipping)
	$customer_postcode_ship = $_POST['customer_postcode_ship']; // customer postcode (shipping)

	$query = "INSERT INTO store_customers (
					name,
					email,
					address_1,
					address_2,
					town,
					county,
					postcode,
					phone,
					name_ship,
					address_1_ship,
					address_2_ship,
					town_ship,
					county_ship,
					postcode_ship
				) VALUES (
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?
				);
			";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param(
		'ssssssssssssss',
		$customer_name,$customer_email,$customer_address_1,$customer_address_2,$customer_town,$customer_county,$customer_postcode,
		$customer_phone,$customer_name_ship,$customer_address_1_ship,$customer_address_2_ship,$customer_town_ship,$customer_county_ship,$customer_postcode_ship);

	if($stmt->execute()){
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Customer has been created successfully!'
		));
	} else {
		// if unable to create invoice
		echo json_encode(array(
			'status' => 'Error',
			'message' => 'There has been an error, please try again.'
			// debug
			//'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
		));
	}

	//close database connection
	$mysqli->close();
}

// Create invoice
if ($action == 'create_invoice'){
	$invoice_number = $_POST['invoice_id']; // invoice number

	// invoice customer information
	// billing
	$customer_name = $_POST['customer_name']; // customer name
	$customer_address_1 = $_POST['customer_address_1']; // customer address
	$customer_town = $_POST['customer_town']; // customer country
	$customer_postcode = $_POST['customer_postcode']; // customer zipcode

	$customer_email = $_POST['customer_email']; // customer email
	$customer_county = $_POST['customer_county']; // customer city
	$customer_phone = $_POST['customer_phone']; // customer phone number
	
	$customer_address_2 = ""; // customer address

	//shipping
	$customer_name_ship = $_POST['customer_name_ship']; // customer name (shipping)
	$customer_county_ship = $_POST['customer_county_ship']; // customer city (shipping)
	$customer_address_1_ship = $_POST['customer_address_1_ship']; // customer address (shipping)
	$customer_town_ship = $_POST['customer_town_ship']; // customer country (shipping)
	$customer_postcode_ship = $_POST['customer_postcode_ship']; // customer zipcode (shipping)

	$customer_address_2_ship = ""; // customer address (shipping)

	// insert customer details into database
	$query = "INSERT INTO customers (
					invoice,
					name,
					email,
					address_1,
					address_2,
					town,
					county,
					postcode,
					phone,
					name_ship,
					address_1_ship,
					address_2_ship,
					town_ship,
					county_ship,
					postcode_ship
				) VALUES (
					'".$invoice_number."',
					'".$customer_name."',
					'".$customer_email."',
					'".$customer_address_1."',
					'".$customer_address_2."',
					'".$customer_town."',
					'".$customer_county."',
					'".$customer_postcode."',
					'".$customer_phone."',
					'".$customer_name_ship."',
					'".$customer_address_1_ship."',
					'".$customer_address_2_ship."',
					'".$customer_town_ship."',
					'".$customer_county_ship."',
					'".$customer_postcode_ship."'
				);
			";

	// insert customer details into database
	$query .= "INSERT INTO store_customers (
					name,
					email,
					address_1,
					address_2,
					town,
					county,
					postcode,
					phone,
					name_ship,
					address_1_ship,
					address_2_ship,
					town_ship,
					county_ship,
					postcode_ship
				) VALUES (
					'".$customer_name."',
					'".$customer_email."',
					'".$customer_address_1."',
					'".$customer_address_2."',
					'".$customer_town."',
					'".$customer_county."',
					'".$customer_postcode."',
					'".$customer_phone."',
					'".$customer_name_ship."',
					'".$customer_address_1_ship."',
					'".$customer_address_2_ship."',
					'".$customer_town_ship."',
					'".$customer_county_ship."',
					'".$customer_postcode_ship."'
				);
			";

	// invoice details
	$invoice_date = $_POST['invoice_date']; // invoice date
	$invoice_due_date = $_POST['invoice_due_date']; // invoice due date
	$invoice_subtotal = $_POST['invoice_subtotal']; // invoice sub-total
	$invoice_shipping = $_POST['invoice_shipping']; // invoice shipping amount
	$invoice_discount = $_POST['invoice_discount']; // invoice discount
	$invoice_vat = $_POST['invoice_vat']; // invoice vat
	$invoice_total = $_POST['invoice_total']; // invoice total
	$invoice_notes = $_POST['invoice_notes']; // Invoice notes
	$invoice_type = $_POST['invoice_type']; // Invoice type
	$invoice_status = $_POST['invoice_status']; // Invoice status

	// insert invoice into database
	$query .= "INSERT INTO invoices (
					invoice,
					invoice_date, 
					invoice_due_date, 
					subtotal, 
					shipping, 
					discount, 
					vat, 
					total,
					notes,
					invoice_type,
					status
				) VALUES (
				  	'".$invoice_number."',
				  	'".$invoice_date."',
				  	'".$invoice_due_date."',
				  	'".$invoice_subtotal."',
				  	'".$invoice_shipping."',
				  	'".$invoice_discount."',
				  	'".$invoice_vat."',
				  	'".$invoice_total."',
				  	'".$invoice_notes."',
				  	'".$invoice_type."',
				  	'".$invoice_status."'
			    );
			";

	// invoice product items
	foreach($_POST['invoice_product'] as $key => $value) {
	    $item_product = $value;
	    // $item_description = $_POST['invoice_product_desc'][$key];
	    $item_qty = $_POST['invoice_product_qty'][$key];
	    $item_price = $_POST['invoice_product_price'][$key];
	    $item_discount = $_POST['invoice_product_discount'][$key];
	    $item_subtotal = $_POST['invoice_product_sub'][$key];

	    // insert invoice items into database
		$query .= "INSERT INTO invoice_items (
				invoice,
				product,
				qty,
				price,
				discount,
				subtotal
			) VALUES (
				'".$invoice_number."',
				'".$item_product."',
				'".$item_qty."',
				'".$item_price."',
				'".$item_discount."',
				'".$item_subtotal."'
			);
		";

	}

	header('Content-Type: application/json');

	// execute the query
	if($mysqli -> multi_query($query)){
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Quotation has been created successfully!'
		));

	} else {
		// if unable to create invoice
		echo json_encode(array(
			'status' => 'Error',
			'message' => 'There has been an error, please try again.'
			// debug
			//'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
		));
	}

	//close database connection
	$mysqli->close();

}

// Adding new product
if($action == 'delete_invoice') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM invoices WHERE invoice = ".$id.";";
	$query .= "DELETE FROM customers WHERE invoice = ".$id.";";
	$query .= "DELETE FROM invoice_items WHERE invoice = ".$id.";";

	unlink('invoices/'.$id.'.pdf');

	if($mysqli -> multi_query($query)) {
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Quotation has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

// Adding new product
if($action == 'update_customer') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$getID = $_POST['id']; // id

	// invoice customer information
	// billing
	$customer_name = $_POST['customer_name']; // customer name
	$customer_email = $_POST['customer_email']; // customer email
	$customer_address_1 = $_POST['customer_address_1']; // customer address
	$customer_address_2 = $_POST['customer_address_2']; // customer address
	$customer_town = $_POST['customer_town']; // customer town
	$customer_county = $_POST['customer_county']; // customer county
	$customer_postcode = $_POST['customer_postcode']; // customer postcode
	$customer_phone = $_POST['customer_phone']; // customer phone number
	
	//shipping
	$customer_name_ship = $_POST['customer_name_ship']; // customer name (shipping)
	$customer_address_1_ship = $_POST['customer_address_1_ship']; // customer address (shipping)
	$customer_address_2_ship = $_POST['customer_address_2_ship']; // customer address (shipping)
	$customer_town_ship = $_POST['customer_town_ship']; // customer town (shipping)
	$customer_county_ship = $_POST['customer_county_ship']; // customer county (shipping)
	$customer_postcode_ship = $_POST['customer_postcode_ship']; // customer postcode (shipping)

	// the query
	$query = "UPDATE store_customers SET
				name = ?,
				email = ?,
				address_1 = ?,
				address_2 = ?,
				town = ?,
				county = ?,
				postcode = ?,
				phone = ?,

				name_ship = ?,
				address_1_ship = ?,
				address_2_ship = ?,
				town_ship = ?,
				county_ship = ?,
				postcode_ship = ?

				WHERE id = ?

			";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param(
		'sssssssssssssss',
		$customer_name,$customer_email,$customer_address_1,$customer_address_2,$customer_town,$customer_county,$customer_postcode,
		$customer_phone,$customer_name_ship,$customer_address_1_ship,$customer_address_2_ship,$customer_town_ship,$customer_county_ship,$customer_postcode_ship,$getID);

	//execute the query
	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Customer has been updated successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	//close database connection
	$mysqli->close();
	
}

// Update product
if($action == 'update_product') {
	try {
        // Collect form data
        $productId = isset($_POST['id']) ? $_POST['id'] : '';
        $productName = isset($_POST['product_name']) ? $_POST['product_name'] : '';
        $productDesc = isset($_POST['product_desc']) ? $_POST['product_desc'] : '';
        $productPrice = isset($_POST['product_price']) ? $_POST['product_price'] : '';
        $productOldImage = isset($_POST['product_image_old']) ? $_POST['product_image_old'] : '';

        // Check if a file was uploaded
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {

            $imagePath = "uploads/" . $productOldImage;
            // Check if the image file exists and delete it
            if (!file_exists($imagePath)) {
                //throw new Exception("Error: Image file does not exist.");
            } else {
                if (!unlink($imagePath)) {
                    //throw new Exception("Error: Could not delete the image.");
                }
            }

            $file = $_FILES['product_image'];
    
		    // Allowed file extensions
		    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
		    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

		    // Check if the file extension is allowed
		    if (in_array($file_extension, $allowed_extensions)) {

		        // Define the target directory and filename
		        $target_dir = "uploads/";
		        $filename = "product_" . date('YmdHis') . "." . $file_extension;
		        $file_path = $target_dir . $filename;

		        // Move the file to the uploads directory
		        if (move_uploaded_file($file['tmp_name'], $file_path)) {
		            
		            // Resize the image using Intervention Image (resize to 800px width)
		            $img = Image::make($file_path)->resize(100, 100, function ($constraint) {
		                $constraint->aspectRatio();
		                $constraint->upsize(); // Prevent image from being upsized
		            });

		            // Save the resized image
		            $img->save($file_path);
		        } else {
		        	throw new Exception("Error: Failed to move the uploaded file.");
		        }

		    } else {
		        throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
		    }

            // Database insert query
            $product_image = $filename;  // Store the image filename
        } else {
        	$product_image = $productOldImage;
        }

        // Prepare SQL query
        $query = "UPDATE products SET product_name = ?, product_desc = ?, product_price = ?, product_image = ? WHERE product_id = ?";

        // Prepare statement
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            throw new Exception('SQL Error: ' . $mysqli->error);
        }

        // Bind parameters. 's' = string, 'i' = integer, 'd' = double, 'b' = blob
        $stmt->bind_param('sssss', $productName, $productDesc, $productPrice, $product_image, $productId);

        // Execute query
        if ($stmt->execute()) {
            // Success response
            echo json_encode(array(
                'status' => 'Success',
                'message' => 'Material has been updated successfully!'
            ));
        } else {
            // Error response
            throw new Exception('Database error: ' . $stmt->error);
        }


    } catch (Exception $e) {
        // Catch any exception and display the error message
        echo json_encode(array(
            'status' => 'Error',
            'message' => 'Error: ' . $e->getMessage()
        ));
    }

	//close database connection
	$mysqli->close();
}


// Adding new product
if($action == 'update_invoice') {

	if(!empty($_GET)){
		$_POST = $_GET;
	}

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["update_id"];

	// invoice customer information
	// billing
	$customer_name = $_POST['customer_name']; // customer name
	$customer_email = $_POST['customer_email']; // customer email
	$customer_address_1 = $_POST['customer_address_1']; // customer address
	$customer_address_2 = $_POST['customer_address_2']; // customer address
	$customer_town = $_POST['customer_town']; // customer town
	$customer_county = $_POST['customer_county']; // customer county
	$customer_postcode = $_POST['customer_postcode']; // customer postcode
	$customer_phone = $_POST['customer_phone']; // customer phone number
	
	//shipping
	$customer_name_ship = $_POST['customer_name_ship']; // customer name (shipping)
	$customer_address_1_ship = $_POST['customer_address_1_ship']; // customer address (shipping)
	$customer_address_2_ship = $_POST['customer_address_2_ship']; // customer address (shipping)
	$customer_town_ship = $_POST['customer_town_ship']; // customer town (shipping)
	$customer_county_ship = $_POST['customer_county_ship']; // customer county (shipping)
	$customer_postcode_ship = $_POST['customer_postcode_ship']; // customer postcode (shipping)

	// invoice details
	$invoice_number = $_POST['invoice_id']; // invoice number
	$invoice_date = $_POST['invoice_date']; // invoice date
	$invoice_due_date = $_POST['invoice_due_date']; // invoice due date
	$invoice_subtotal = $_POST['invoice_subtotal']; // invoice sub-total
	$invoice_shipping = $_POST['invoice_shipping']; // invoice shipping amount
	$invoice_discount = $_POST['invoice_discount']; // invoice discount
	$invoice_vat = $_POST['invoice_vat']; // invoice vat
	$invoice_total = $_POST['invoice_total']; // invoice total
	$invoice_notes = $_POST['invoice_notes']; // Invoice notes
	$invoice_type = $_POST['invoice_type']; // Invoice type
	$invoice_status = $_POST['invoice_status']; // Invoice status

	// insert invoice into database
	$query = "UPDATE invoices SET
				invoice = '".$invoice_number."',
				invoice_date = '".$invoice_date."',
				invoice_due_date = '".$invoice_due_date."',
				subtotal = '".$invoice_subtotal."',
				shipping = '".$invoice_shipping."',
				discount = '".$invoice_discount."',
				vat = '".$invoice_vat."',
				total = '".$invoice_total."',
				notes = '".$invoice_notes."',
				invoice_type = '".$invoice_type."',
				status = '".$invoice_status."'
			 WHERE invoice = '".$id."'
			";
	$mysqli->query($query);


	// insert customer details into database
	$query = "UPDATE customers SET
			invoice = '".$invoice_number."',
			name = '".$customer_name."',
			email = '".$customer_email."',
			address_1 = '".$customer_address_1."',
			address_2 = '".$customer_address_2."',
			town = '".$customer_town."',
			county = '".$customer_county."',
			postcode = '".$customer_postcode."',
			phone = '".$customer_phone."',
			name_ship = '".$customer_name_ship."',
			address_1_ship = '".$customer_address_1_ship."',
			address_2_ship = '".$customer_address_2_ship."',
			town_ship = '".$customer_town_ship."',
			county_ship = '".$customer_county_ship."',
			postcode_ship = '".$customer_postcode_ship."'
		WHERE invoice = '".$id."'
	";
	$mysqli->query($query);
	
	$query = "DELETE FROM invoice_items WHERE invoice = ".$id.";";
	$mysqli->query($query);

	// invoice product items
	foreach($_POST['invoice_product'] as $key => $value) {
	    $item_product = $value;
	    // $item_description = $_POST['invoice_product_desc'][$key];
	    $item_qty = $_POST['invoice_product_qty'][$key];
	    $item_price = $_POST['invoice_product_price'][$key];
	    $item_discount = $_POST['invoice_product_discount'][$key];
	    $item_subtotal = $_POST['invoice_product_sub'][$key];

	    // insert invoice items into database
		$query = "INSERT INTO invoice_items (
				invoice,
				product,
				qty,
				price,
				discount,
				subtotal
			) VALUES (
				'".$invoice_number."',
				'".$item_product."',
				'".$item_qty."',
				'".$item_price."',
				'".$item_discount."',
				'".$item_subtotal."'
			);
		";
		$mysqli->query($query);

	}

	header('Content-Type: application/json'); // Ensure the response is JSON
	$response = array();

	if ($mysqli->error) {
		$response = array(
			'status' => 'Error',
			'message' => 'Error Found: ' . $mysqli->error
		);
	} else {
		$response = array(
			'status' => 'Success',
			'message' => 'Quotation has been updated successfully!'
		);
	}

	echo json_encode($response); // Always return JSON

	// close connection 
	$mysqli->close();
	exit; // Ensure no extra output

}

// Adding new product
if($action == 'delete_product') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM products WHERE product_id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s',$id);

	//execute the query
	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Material has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

// Login to system
if($action == 'login') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	session_start();

    extract($_POST);

    $username = mysqli_real_escape_string($mysqli,$_POST['username']);
    $pass_encrypt = md5(mysqli_real_escape_string($mysqli,$_POST['password']));

    $query = "SELECT * FROM `users` WHERE username='$username' AND `password` = '$pass_encrypt'";

    $results = mysqli_query($mysqli,$query) or die (mysqli_error());
    $count = mysqli_num_rows($results);

    if($count > 0) {
		$row = $results->fetch_assoc();

		$_SESSION['login_username'] = $row['username'];

		// processing remember me option and setting cookie with long expiry date
		if (isset($_POST['remember'])) {	
			session_set_cookie_params('604800'); //one week (value in seconds)
			session_regenerate_id(true);
		}  
		
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Login was a success! Transfering you to the system now, hold tight!'
		));
    } else {
    	echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'Login incorrect, does not exist or simply a problem! Try again!'
	    ));
    }
}

// Adding new product
if($action == 'add_product') {
	try {
        // Collect form data
        $productName = isset($_POST['product_name']) ? $_POST['product_name'] : '';
        $productDesc = isset($_POST['product_desc']) ? $_POST['product_desc'] : '';
        $productPrice = isset($_POST['product_price']) ? $_POST['product_price'] : '';

        // Check if a file was uploaded
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $file = $_FILES['product_image'];
    
		    // Allowed file extensions
		    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
		    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

		    // Check if the file extension is allowed
		    if (in_array($file_extension, $allowed_extensions)) {

		        // Define the target directory and filename
		        $target_dir = "uploads/";
		        $filename = "product_" . date('YmdHis') . "." . $file_extension;
		        $file_path = $target_dir . $filename;

		        // Move the file to the uploads directory
		        if (move_uploaded_file($file['tmp_name'], $file_path)) {
		            
		            // Resize the image using Intervention Image (resize to 800px width)
		            $img = Image::make($file_path)->resize(100, 100, function ($constraint) {
		                $constraint->aspectRatio();
		                $constraint->upsize(); // Prevent image from being upsized
		            });

		            // Save the resized image
		            $img->save($file_path);
		        } else {
		        	throw new Exception("Error: Failed to move the uploaded file.");
		        }

		    } else {
		        throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
		    }

            // Database insert query
            $product_image = $filename;  // Store the image filename

            // Prepare SQL query
            $query = "INSERT INTO products (product_name, product_desc, product_price, product_image) VALUES (?, ?, ?, ?)";

            // Prepare statement
            $stmt = $mysqli->prepare($query);
            if ($stmt === false) {
                throw new Exception('SQL Error: ' . $mysqli->error);
            }

            // Bind parameters. 's' = string, 'i' = integer, 'd' = double, 'b' = blob
            $stmt->bind_param('ssss', $productName, $productDesc, $productPrice, $product_image);

            // Execute query
            if ($stmt->execute()) {
                // Success response
                echo json_encode(array(
                    'status' => 'Success',
                    'message' => 'Material has been added successfully!'
                ));
            } else {
                // Error response
                throw new Exception('Database error: ' . $stmt->error);
            }

        } else {
            throw new Exception("No valid file uploaded or file upload error: " . $_FILES['product_image']['error']);
        }

    } catch (Exception $e) {
        // Catch any exception and display the error message
        echo json_encode(array(
            'status' => 'Error',
            'message' => 'Error: ' . $e->getMessage()
        ));
    }

	//close database connection
	$mysqli->close();
}

// Adding new user
if($action == 'add_user') {

	$user_name = $_POST['name'];
	$user_username = $_POST['username'];
	$user_email = $_POST['email'];
	$user_phone = $_POST['phone'];
	$user_password = $_POST['password'];

	//our insert query query
	$query  = "INSERT INTO users
				(
					name,
					username,
					email,
					phone,
					password
				)
				VALUES (
					?,
					?, 
                	?,
                	?,
                	?
                );
              ";

    header('Content-Type: application/json');

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	$user_password = md5($user_password);
	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('sssss',$user_name,$user_username,$user_email,$user_phone,$user_password);

	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'User has been added successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	//close database connection
	$mysqli->close();
}

// Update product
if($action == 'update_user') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	// user information
	$getID = $_POST['id']; // id
	$name = $_POST['name']; // name
	$username = $_POST['username']; // username
	$email = $_POST['email']; // email
	$phone = $_POST['phone']; // phone
	$password = $_POST['password']; // password

	if($password == ''){
		// the query
		$query = "UPDATE users SET
					name = ?,
					username = ?,
					email = ?,
					phone = ?
				 WHERE id = ?
				";
	} else {
		// the query
		$query = "UPDATE users SET
					name = ?,
					username = ?,
					email = ?,
					phone = ?,
					password =?
				 WHERE id = ?
				";
	}

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	if($password == ''){
		/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
		$stmt->bind_param(
			'sssss',
			$name,$username,$email,$phone,$getID
		);
	} else {
		$password = md5($password);
		/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
		$stmt->bind_param(
			'ssssss',
			$name,$username,$email,$phone,$password,$getID
		);
	}

	//execute the query
	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'User has been updated successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	//close database connection
	$mysqli->close();
	
}

// Delete User
if($action == 'delete_user') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM users WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s',$id);

	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'User has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

// Delete User
if($action == 'delete_customer') {

	// output any connection error
	if ($mysqli->connect_error) {
	    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM store_customers WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if($stmt === false) {
	  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s',$id);

	if($stmt->execute()){
	    //if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message'=> 'Customer has been deleted successfully!'
		));

	} else {
	    //if unable to create new record
	    echo json_encode(array(
	    	'status' => 'Error',
	    	//'message'=> 'There has been an error, please try again.'
	    	'message' => 'There has been an error, please try again.<pre>'.$mysqli->error.'</pre><pre>'.$query.'</pre>'
	    ));
	}

	// close connection 
	$mysqli->close();

}

?>