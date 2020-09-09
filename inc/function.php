<?php
// Heaven Functions

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset( $_POST['mcemail'] ) ) {
	require_once("mcapi.class.php");
	$email = filter_var(trim($_POST["mcemail"]), FILTER_SANITIZE_EMAIL);
	// Check that data was sent to the mailer.
	if ( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Set a 400 (bad request) response code and exit.
		http_response_code(400);
		echo "Oops! There was a problem with your submission. Please complete the form and try again.";
		exit;
	}
	
	$mc_api = 'YOUR_API_KEY';
	$mc_listid = 'YOUR_LIST_ID';
	
	$api = new MCAPI($mc_api);
	$merge_vars = array('FNAME'=> '', 'LNAME'=> '');
	
	// Submit subscriber data to MailChimp
	// For parameters doc, refer to: http://apidocs.mailchimp.com/api/1.3/listsubscribe.func.php
	$retval = $api->listSubscribe( $mc_listid, $_POST["mcemail"], $merge_vars, 'html', false, true );
	
	if ($api->errorCode){
		echo "Please try again.";
	} else {
		echo "Thank you, you have been added to our mailing list.";
	}
	
}elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset( $_POST['message'] ) ) {
	// Get the form fields and remove whitespace.
	$name = strip_tags(trim($_POST["name"]));
			$name = str_replace(array("\r","\n"),array(" "," "),$name);
	$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
	$message = trim($_POST["message"]);

	// Check that data was sent to the mailer.
	if ( empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Set a 400 (bad request) response code and exit.
		http_response_code(400);
		echo "Oops! There was a problem with your submission. Please complete the form and try again.";
		exit;
	}

	// Set the recipient email address.
	// FIXME: Update this to your desired email address.
	$recipient = "cjroberts2013@gmail.com";

	// Set the email subject.
	$subject = "BFM contact from $name";

	// Build the email content.
	$email_content = "Name: $name\n";
	$email_content .= "Email: $email\n\n";
	$email_content .= "Message:\n$message\n";

	// Build the email headers.
	$email_headers = "From: $name <$email>";

	// Send the email.
	if (mail($recipient, $subject, $email_content, $email_headers)) {
		// Set a 200 (okay) response code.
		http_response_code(200);
		echo "Thank You! Your message has been sent.";
	} else {
		// Set a 500 (internal server error) response code.
		http_response_code(500);
		echo "Oops! Something went wrong and we couldn't send your message.";
	}

} else {
	// Not a POST request, set a 403 (forbidden) response code.
	http_response_code(403);
	echo "There was a problem with your submission, please try again.";
}