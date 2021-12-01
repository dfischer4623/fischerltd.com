<?php 

	if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        }
        if(!$captcha){
          echo '<h2>Please check the the captcha form.</h2>';
          exit;
        }
        $secretKey = "6LezpKwZAAAAALqeXP1b-Ka_tNhNQcxPY0Gbhars";
        $ip = $_SERVER['REMOTE_ADDR'];
        // post request to server
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
        $response = file_get_contents($url);
        $responseKeys = json_decode($response,true);
        // should return JSON with success as true
        if($responseKeys["success"]) {
                echo '<h2>Thanks for posting comment</h2>';
        } else {
                echo '<h2>You are spammer ! Get the @$%K out</h2>';
        }
	
	$fname = filter_var(htmlspecialchars($_POST['firstName']),FILTER_SANITIZE_STRING); 
	$lname = filter_var(htmlspecialchars($_POST['lastName']),FILTER_SANITIZE_STRING); 
	$cname = filter_var(htmlspecialchars($_POST['compName']),FILTER_SANITIZE_STRING); 
	$emailAddress = filter_var(htmlspecialchars($_POST['eMail']),FILTER_SANITIZE_EMAIL); 
	$phone = filter_var(htmlspecialchars($_POST['phone']),FILTER_SANITIZE_NUMBER_INT);
	$calltime = filter_var(htmlspecialchars($_POST['dateTimeLocal']),FILTER_SANITIZE_STRING);
	$question = filter_var(htmlspecialchars($_POST['question']),FILTER_SANITIZE_STRING);
	
	if (isset($_POST['title'])) 
	{ $title = filter_var(htmlspecialchars($_POST['title']),FILTER_SANITIZE_STRING); } 
	else 
	{$title = " "; } 
	
	if (isset($_POST['radEP'])) 
	{ 	$radEP = filter_var(htmlspecialchars($_POST['radEP']),FILTER_SANITIZE_STRING); } 
	else 
	{ $radEP = " "; } 
	
	if (isset($_POST['dateTimeLocal'])) 
	{ $calltime = filter_var(htmlspecialchars($_POST['dateTimeLocal']),FILTER_SANITIZE_STRING); } 
	else 
	{ $calltime = " "; } 

	$ipaddress = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('America/New_York');
	$timestamp = date('l jS \of F Y h:i:s A O+0500');
	$servername = "localhost:3306";
	$username = "fischerltd";
	$password = "df4623DF!";
	$dbname = "fischerltd";

	$con = mysqli_connect($servername, $username, $password, $dbname);
		if (!$con) {
			echo "Connection failed: " . mysqli_connect_error();
		}		

	if(empty($cname)) {
		goto a;
	}

	$query = "INSERT INTO mfcontacts (title, fname, lname, cname, email, phone, radEP, calltime, question, ipaddress, timestamp)
		VALUES ('$title', '$fname', '$lname', '$cname', '$emailAddress', '$phone', '$radEP', '$calltime', '$question', '$ipaddress', '$timestamp')";
	$constmt = mysqli_query($con, $query);
		if (!$constmt) {
			echo "Query failed: " . mysqli_error($con);
		}		
	
	mysqli_close($con);

	$content = $fname . " " . $lname . " " . $cname . " " . $emailAddress . " " . $phone . " " . $radEP . " " . $calltime . " " . $question;
	
	$SENDGRID_API_KEY='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';
	
	require 'vendor/autoload.php';

	$email = new \SendGrid\Mail\Mail();
	$email ->setFrom("mfischer@fischerltd.com", "Contact Page");
	$email ->setSubject("Contact Page Inquiry");
	$email ->addTo("dfischer@fischerltd.com", "www.fischerltd.com");
	$email ->addContent("text/html",$content);
	$sendgrid = new \SendGrid($SENDGRID_API_KEY);
	try {
		$response = $sendgrid->send($email);
	} catch(Exception $e) {
		echo 'Caught exception: '. $e->getMessage() . "\n";
	}

	$cemail = new \SendGrid\Mail\Mail();
	$cemail ->setFrom("mfischer@fischerltd.com", "Contact Page");
	$cemail ->setSubject("Contact Page Inquiry");
	$cemail ->addTo($emailAddress, "www.fischerltd.com");
	$cemail ->addContent("text/html",$content);
	$csendgrid = new \SendGrid($SENDGRID_API_KEY);
	try {
		$response = $csendgrid->send($cemail);
	} catch(Exception $e) {
		echo 'Caught exception: '. $e->getMessage() . "\n";
	}
	a:
	header("location: home.html");
?>
