<?php

// DATABASE CONNECTION INFORMATION
$host = "localhost";
$user = "user";
$passwd = "password";
$dbname = "hvz";
$cxn = mysqli_connect($host,$user,$passwd,$dbname) or die ("could not connect to server");

function emailer($cxn)
{
	$mail_type = $_POST["mail_type"];
	$subject = $_POST["subject"];
	$message_text = $_POST["message_text"];
	
	$bcc = getEmails($cxn,$mail_type);
	if(!$bcc)
	{
		return FALSE;
	}
	else
	{
		$composeEmail = composeEmail($bcc,$subject,$message_text,$mail_type);
		if(!$composeEmail)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

function getEmails($cxn,$mail_type)
{
	$query_email = "SELECT * FROM members WHERE status='$mail_type'";
	$result_email = mysqli_query($cxn,$query_email) or die ("could not execute query_email");

	if(!empty($result_email))
	{
		$mailList = "";
		while ($row_rng = mysqli_fetch_array($result_email))
		{
			$mailList = $mailList . $row_rng['email'] . ", ";
		}
		$mailList = $mailList . "humansvszombies@colorado.edu";
		
		return $mailList;
	}
	else
	{
		return FALSE;
	}
}

function composeEmail($bcc,$subject,$message_text,$mail_type)
{
	if(strcmp($mail_type,"human") == 0)
	{
		$mailName = "human";
	}
	elseif(strcmp($mail_type,"zombie") == 0)
	{
		$mailName = "zombie";
	}
	elseif(strcmp($mail_type,"starved") == 0)
	{
		$mailName = "deceased";
	}
	else
	{
		return FALSE;
	}
	
	$to = "";
	$message = "
	<html>
	<body>".
	$message_text.
	"</body>
	</html>";
	
	$headers = "From: Cu HvZ <cuhvz@cuhvz.com>\r\n";
	$headers .= "Bcc: " . $bcc . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if(mail($to, $subject, $message, $headers))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

$output = emailer($cxn);
if($output)
{
	echo "Email should be sent. Check humansvszombies@colorado.edu to confirm it went out. <br> <a href='emailer_form.php'>Back</a>";
}
else
{
	echo "Something went wrong; the email probably did not send. Check humansvszombies@colorado.edu to confirm. <br><a href='emailer_form.php'>Back</a>";
}

?>