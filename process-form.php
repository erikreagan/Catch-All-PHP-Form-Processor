<?php

/*
  Catch-All PHP Form Processor by Erik Reagan

  Version 1.5 dated June 7th, 2008
  Author Site: http://erikreagan.com for support
  Project Page: http://www.erikreagan.com/projects/2008/05/catch-all-php-form-processor/
  For the most updated documentation and script visit the project page above
*/

// Configure These Variables

$usingTemplate      = false;   // Set to true if you are using this form within your own site (a php include for example).
$customForm         = "path/to-your/form.php";   // If you are using your own template or site define the path to your contact form which should be it's own .php file
$emailRecipient     = "handle@domainname.com";   // The email address the form results should be sent to
$bccRecipient       = "";   // The email address that you would like a blind carbon copy sent to (optional)
$forwardUser        = true;   // Set to false if you do NOT want to forward user to a new page or site after form completion
$finalDestination   = "/";   // Page or website you would like the form to forward to once submitted
$subject            = "My PHP Form";   // The Subject of the Email when it is sent
$subjectIsInForm    = true;   // Set to true if the email Subject is filled in by the user in your form. If set to true this overrides the preceeding $subject variable
$subjectField       = "required-subject";  // If your subject is defined in your form put the field name here.
$fromName           = "Your Name";   // "From" Name when when sent
$fromNameIsInForm   = true;   // Set to true if the Name the email results should be From is supplied in your form
$fromNameField      = "required-your-name";   // If your from name is defined in your form put the field name here
$fromEmail          = "yourhandle@domain.com";  // "From Email" address when sent. For multiple separate by comma (user1@domainname.com,user2@yahoo.com,etc@gmail.com)
$fromEmailIsInForm  = true;   // Set to true if the Email address the email results should be From is supplied in your form
$fromEmailField     = "required-your-email";   // If your from email address is defined in your form put the field name here
$includeTimestamp   = true;   // Set to false if you do not want the date and timestamp included in your emailed results
$includeBlankFields = true;   // Set to false if you do not wish to email fields that aren't filled in 
$emailHTML          = true;   // Set to true if you prefer HTML formatted emails. Set to false if you prefer plain text. (HTML is widely accepted and looks much better)
$headerTroubles     = false;   // Only set this to true if youe email headers aren't being sent correctly from your server.

////////////////////////////////////////////////////////////////////////////////////
//   You shouldn't edit below this line unless you're familiar enough with PHP.   // 
//        While this script is only an intermediate level of PHP I do not         //
//       guarantee any support on modified files. Proceed at your own risk        //
////////////////////////////////////////////////////////////////////////////////////

define('EMAIL_RECIPIENT', $emailRecipient);
define('BCC_RECIPIENT', $bccRecipient);
define('FINAL_DESTINATION', $finalDestination);
if ($subjectIsInForm) { define('SUBJECT', $_POST[$subjectField]); } else { define('SUBJECT', $subject); }
if ($fromNameIsInForm) { define('FROM_NAME', $_POST[$fromNameField]); } else { define('FROM_NAME', $fromName); }
if ($fromEmailIsInForm) { define('FROM_EMAIL', $_POST[$fromEmailField]); } else { define('FROM_EMAIL', $fromEmail); }
if ($headerTroubles) { define('HEADER_TRAIL', "\n"); } else { define('HEADER_TRAIL', "\r\n"); }

// Start by checking to see if we're emailing the final results
if (array_key_exists('emailnow', $_POST)) {
	$to  = EMAIL_RECIPIENT;
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . HEADER_TRAIL;
	if (!$emailHTML) {
		$headers .= 'Content-type: text;' . HEADER_TRAIL;
	} else {
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . HEADER_TRAIL;
	}

	// Additional headers
	$headers .= "From: ".FROM_NAME." <".FROM_EMAIL.">" . HEADER_TRAIL;
	$headers .= "Bcc: ".BCC_RECIPIENT . HEADER_TRAIL;
	$title = SUBJECT;
	$timestamp = date('g:ia');
	$date = date('M jS, Y');

	if (!$emailHTML) {
		$message = "Here are the results from the form submitted on $date\n\n";
		foreach($_POST as $key2 => $value2) {
			if (is_array($value2)) { $value2 = implode(", ", $value2); }
			$key2 = str_replace('_', ' ', $key2);
			$key2 = str_replace('-', ' ', $key2);
			$key2 = str_replace('ignore ','',$key2);
			$key2 = ucwords(str_replace('required ', '', $key2));
			$value2 = htmlspecialchars($value2);
			$message .= ((strtolower($key2) == "submit") || (strtolower($key2) == "emailnow")) ? "" : "\n$key2\n   $value2\n";
		}
			if ($timestamp) { $message .= "\nForm Submitted on $date at $timestamp\n"; }
	} else {
		$message = "	<html>
		<head>
		  <title>$title</title>
		</head>
		<body>

		  <p>Here are the results from the form submitted on $date</p>

		  <table cellpadding='0' cellspacing= '0'>\n
		";
		foreach($_POST as $key2 => $value2) {
			if (is_array($value2)) { $value2 = implode(", ", $value2); }
			$key2 = str_replace('_', ' ', strtolower($key2));
			$key2 = str_replace('-', ' ', $key2);
			$key2 = str_replace('ignore ','',$key2);
			$key2 = ucwords(str_replace('required', '', $key2));
			$value2 = htmlspecialchars($value2);
			$message .= ((strtolower($key2) == "submit") || (strtolower($key2) == "emailnow")) ? "" : "    <tr style='margin:4px'>
		      <td style='width:200px;border-bottom:1px solid #c0c0c0;'>$key2</td><td style='border-bottom: 1px solid #c0c0c0'>$value2</td>
		    </tr>\n
		";
		}
		if ($timestamp) {
			$message .= "    <tr style='margin:4px'>
		      <td style='width:200px;border-bottom:1px solid #c0c0c0;'>Form Submitted on</td><td style='border-bottom: 1px solid #c0c0c0'>$date at $timestamp</td>
		    </tr>\n
		";
		}
		$message .= "
		  </table>

		</body>
		</html>
		";
	}

	if (mail($to, SUBJECT, $message, $headers)) {
		$block = "<div id=\"top\">\n\n<h2>Thank You</h2>\n\n</div>\n\n<p class=\"sent\">Your form has been submitted.\n";
		if ($forwardUser) {
			$block .= "If you are not redirected shortly please <a href=\"".FINAL_DESTINATION."\">click here</a>.</p>\n\n<script type=\"text/javascript\">setTimeout('window.location=\"".FINAL_DESTINATION."\"',5000)</script>\n";
		} else { $block .= "</p>\n\n"; }
	} else {
		$block = "<div id=\"top\">\n\n<h2>I'm sorry</h2>\n\n</div>\n\n<p class=\"sent\">Your form has not been submitted. There may be a problem with the server. Please contact the administrator.\n";
	}

// If it's not ready to email then run errors and display back form data
} else if ((in_array('submit', $_POST)) || (array_key_exists('submit', $_POST))){
	
	// Check required fields for any data
	foreach ($_POST as $check => $info) {
		$check = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $check));
		if ((strstr($check,'required')) && (empty($info))) {
			$check = ucwords(str_replace('required','',$check));
			$errors[] = "A required field was left blank: <strong>$check</strong>";
		}
		if ((is_array($info)) && (in_array('didnotchoose',$info)) && count($info) == 1) {
			$check = ucwords(str_replace('required','',$check));
			$errors[] = "A required field was left blank: <strong>$check</strong>";
		}
	}
	
	// Check to standard email field to validate
	foreach ($_POST as $check => $info) {
		$check = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "", $check));
		$info = strtolower($info);
		if ((strstr($check,'email')) && (!empty($info)) && (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $info))) {
			$errors[] = "This email address is not valid: <strong>$info</strong>";
		}
	}
	
	// Setup HTML display of form values
	$title = "Form Results";
	$block = "<div id=\"top\">\n    <h2>Form Results</h2>\n    <h5>Please Review Your Information</h5>\n    <h5 class=\"red\">Form Not Yet Submitted</h5>\n</div>\n\n<div id=\"results\">\n</div>\n\n<div id=\"results\">\n\n";

	// Run the error report and display it if needed
	if (!empty($errors)) {
		if (count($errors) > 1) { $Error = "Errors"; } else { $Error = "Error"; }
		$block .= " <div class=\"error\">\n   <p><strong>$Error in Form</strong></p>\n";
		$block .= "   <ul>";
		foreach ($errors as $field => $data) {
			$block .= "\n     <li>$data</li>";
		}
		$block .= "\n   </ul>\n   <p><a href=\"javascript:history.go(-1)\">Go back and try again.</a></p>\n </div>\n\n";
	}
	$block .= " <ul id=\"display\">";
	
	
	foreach($_POST as $key1 => $value1) {
		$key1 = str_replace('_', ' ', strtolower($key1));
		$key1 = str_replace('-', ' ', $key1);
		$key1 = str_replace('required', '', $key1);
		if (!strstr($key1,'ignore')) {
			if (is_array($value1)) { $value1 = implode(", ", $value1); }
			$value1 = str_replace('didnotchoose, ', '', $value1);
			$value1 = htmlspecialchars($value1);
			if (($value1 == "") || ($value1 == "didnotchoose")) { $value1 = "<strong>[ left blank ]</strong>"; }
			$block .= ((strtolower($key1) == "submit") || (strtolower($key1) == "emailnow")) ? "" : "\n   <li><strong>".str_replace('ignore', '', ucwords($key1)). ":</strong> <span>".stripslashes($value1)."</span></li>";
		}
	}
	$block .= "\n  </ul>\n</div>\n\n";
	// Setup hidden form for email submission 
	$block .= "<div id=\"email\">\n    <form name=\"emailit\" action=\"\" method=\"post\" accept-charset=\"utf-8\">\n";
	$block .= "\t<input type='hidden' name='emailnow' value='emailnow' id='emailnow' />\n";
	foreach ($_POST as $field => $value) {
		if (is_array($value)) { $value = implode(", ", $value);	}
		$value = str_replace('didnotchoose, ', '', $value);
		$value = htmlspecialchars($value);
		if ($value == "") { $value = "[ left blank ]"; }
		if (!$includeBlankFields) {
			if (($field !== "submit") && ($value !== "[ left blank ]")) {
				$block .= "\t<input type='hidden' name='".$field."' value='".htmlspecialchars(stripslashes($value))."' id='".$field."' />\n";
			}
		} else { 
			if ($field !== "submit") {
				$block .= "\t<input type='hidden' name='".$field."' value='".htmlspecialchars(stripslashes($value))."' id='".$field."' />\n";
			}
		}
	}
	
	if (!empty($errors)) {
		$block .= "\t<p><input type=\"button\" name=\"back\" value=\"Go Back\" id=\"back\" onclick=\"history.go(-1);\" /></p>\n    </form>\n</div>\n";
	} else {
		$block .= "\t<p><input type=\"button\" name=\"print\" value=\"Print Results\" id=\"print\" onclick=\"window.print();\" /><input type=\"submit\" name=\"submit\" value=\"Send Results\" id=\"submit\" /></p>\n    </form>\n</div>\n";
	}
	
} else {
	if ($usingTemplate) {
		$block = "\n<p>There was an error processing the form.<p>\n\n";
	} else {
		include($customForm);
	}
}


if (!$usingTemplate): ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="author" content="Erik Reagan, erikreagan.com"/>
<style type="text/css" media="screen,print">
*{margin:0;padding:0;font-family:"Lucida Grande",Georgia,Tahoma,Arial,Serif;}
body{position:relative;}
#top{position:fixed;width:100%;top:0px;left:0px;padding:14px;display:block;background:#efefef;border-bottom:1px solid #c0c0c0;z-index:2;}
h5.red{color:#dd3c10;}
.error{background:#ffebe8;border:1px solid #dd3c10;padding:10px;margin-bottom:15px;}
.error p,.error li{font-size:9pt;padding:0 !important;margin:0;}
.error ul{list-style:disc;margin:12px;}
.error ul li{margin-left:15px;padding-left:10px;}
#results{margin:100px 14px 14px 14px;}
ul{list-style:none;margin:0px;z-index:1;}
ul#display li{position:relative;padding:4px 0;clear:both;font-size:14px;}
ul#display li strong{padding-right:75%;}
/* So the Key and Value do not overlap */
#display li span{width:75%;border-bottom:1px solid #777;float:right;margin-top:-17px;/* This (more or less) lines up the top of the value with the top of the key */
top:1px;/* This is needed to make the 1px border show at 100% width otherwise the key covers the border. */
padding-left:25%;padding-bottom:4px;}
p{margin:10px;}
p.sent{margin:80px 14px 14px 14px;}
#email{position:fixed;top:0;left:0;width:100%;text-align:right;z-index:3;}
#email p{padding:14px;}
#email input#submit,#email input#print,#email input#back{cursor:pointer;color:#00f;padding:8px;background:none;/* Without at least one of these Safari won't style the button in a custom fashion */
border:2px solid #ccc;/* Without at least one of these Safari won't style the button in a custom fashion */ 
font-size:.6em;text-transform:uppercase;margin-left:7px;}
#email input#submit:hover,#email input#print:hover,#email input#back:hover{background:#ddd;color:#f00;}
</style>
<style type="text/css" media="print">h5.red, #email { display: none; }</style>
<!-- Some CSS hacks to get the fixed positioning to work in IE6 and younger -->
<!--[if lte IE 6]>
<style type="text/css" media="screen,print">
	html, body { height: 100%; overflow: auto; }
	#top, #email { position: absolute; }
</style>
<![endif]-->
</head>
<body>
<?php endif;

echo $block;

if (!$usingTemplate): ?>
</body>
</html>
<?php endif ?>