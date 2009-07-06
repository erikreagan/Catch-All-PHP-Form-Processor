<?php

/*
  Catch-All PHP Form Processor by Erik Reagan

  Version 1.5.1 dated July 5th, 2009
  Author Site: http://erikreagan.com for support
  Project Page: http://www.erikreagan.com/projects/2008/05/catch-all-php-form-processor/
  For the most updated documentation and script visit the project page above
*/

// Configure These Variables

$usingTemplate      = FALSE;   // Set to true if you are using this form within your own site (a php include for example).
$customForm         = "path/to-your/form.php";   // If you are using your own template or site define the path to your contact form which should be it's own .php file
$emailRecipient     = "erik@erikreagan.com";   // The email address the form results should be sent to
$bccRecipient       = "";   // The email address that you would like a blind carbon copy sent to (optional)
$forwardUser        = TRUE;   // Set to false if you do NOT want to forward user to a new page or site after form completion
$finalDestination   = "/";   // Page or website you would like the form to forward to once submitted
$subject            = "My PHP Form";   // The Subject of the Email when it is sent
$subjectIsInForm    = TRUE;   // Set to true if the email Subject is filled in by the user in your form. If set to true this overrides the preceeding $subject variable
$subjectField       = "required-subject";  // If your subject is defined in your form put the field name here.
$fromName           = "Your Name";   // "From" Name when when sent
$fromNameIsInForm   = TRUE;   // Set to true if the Name the email results should be From is supplied in your form
$fromNameField      = "required-your-name";   // If your from name is defined in your form put the field name here
$fromEmail          = "yourhandle@domain.com";  // "From Email" address when sent. For multiple separate by comma (user1@domainname.com,user2@yahoo.com,etc@gmail.com)
$fromEmailIsInForm  = TRUE;   // Set to true if the Email address the email results should be From is supplied in your form
$fromEmailField     = "required-your-email";   // If your from email address is defined in your form put the field name here
$includeTimestamp   = TRUE;   // Set to false if you do not want the date and timestamp included in your emailed results
$includeBlankFields = TRUE;   // Set to false if you do not wish to email fields that aren't filled in 
$emailHTML          = TRUE;   // Set to true if you prefer HTML formatted emails. Set to false if you prefer plain text. (HTML is widely accepted and looks much better)
$headerTroubles     = FALSE;   // Only set this to true if youe email headers aren't being sent correctly from your server.

////////////////////////////////////////////////////////////////////////////////////
//   You shouldn't edit below this line unless you're familiar enough with PHP.   // 
//        While this script is only an intermediate level of PHP I do not         //
//       guarantee any support on modified files. Proceed at your own risk        //
////////////////////////////////////////////////////////////////////////////////////

define('EMAIL_RECIPIENT', $emailRecipient);
define('BCC_RECIPIENT', $bccRecipient);
define('FINAL_DESTINATION', $finalDestination);
if ( $subjectIsInForm ) { define('SUBJECT', $_POST[$subjectField]); } else { define('SUBJECT', $subject); }
if ( $fromNameIsInForm ) { define('FROM_NAME', $_POST[$fromNameField]); } else { define('FROM_NAME', $fromName); }
if ( $fromEmailIsInForm ) { define('FROM_EMAIL', $_POST[$fromEmailField]); } else { define('FROM_EMAIL', $fromEmail); }
if ( $headerTroubles ) { define('HEADER_TRAIL', "\n"); } else { define('HEADER_TRAIL', "\r\n"); }

// Start by checking to see if we're emailing the final results
if ( array_key_exists('emailnow', $_POST) ) {
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . HEADER_TRAIL;
	if ( ! $emailHTML ) {
		$headers .= 'Content-type: text;' . HEADER_TRAIL;
	} else {
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . HEADER_TRAIL;
	}

	// Additional headers
	$headers .= "From: ".FROM_NAME." <".FROM_EMAIL.">" . HEADER_TRAIL;
	$headers .= "Bcc: ".BCC_RECIPIENT . HEADER_TRAIL;
	$title = SUBJECT;
	// Timestamp for inclusion
	$timestamp = date('M jS, Y')." at ".date('g:ia');

	if ( ! $emailHTML ) {
		$message = "A new form has been submitted\n\n";
		foreach($_POST as $field => $data) {
			if ( is_array($data) ) { $data = implode(", ", $data); }
			$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
			$field = str_replace('ignore ','',$field);
			$field = ucwords(str_replace('required ', '', $field));
			$message .= ((strtolower($field) == "submit") || (strtolower($field) == "emailnow")) ? "" : "\n$field\n   ".stripslashes($data)."\n";
		}
			if ( $timestamp ) { $message .= "\nForm Submitted on $timestamp\n"; }
	} else {
		$message = "	<html>
		<head>
		  <title>$title</title>
		</head>
		<body>

		  <p>A new form has been submitted</p>

		  <table cellpadding='0' cellspacing= '0'>\n
		";
		foreach($_POST as $field => $data) {
			if ( is_array($data) ) { $data = implode(", ", $data); }
			$field = preg_replace('/[-_]/',' ',strtolower(str_replace('required','',$field)));
         // $data = htmlspecialchars($data);
			$message .= ((strtolower($field) == "submit") || (strtolower($field) == "emailnow")) ? "" : "    <tr style='margin:4px'>
		      <td style='width:200px;border-bottom:1px solid #c0c0c0;'>".ucwords($field)."</td><td style='border-bottom: 1px solid #c0c0c0'>".stripslashes($data)."</td>
		    </tr>\n
		";
		}
		if ( $timestamp ) {
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

	if ( mail(EMAIL_RECIPIENT, SUBJECT, $message, $headers) ) {
		$block = "<div id=\"top\">\n\n<h2>Thank You</h2>\n\n</div>\n\n<p class=\"sent\">Your form has been submitted.\n";
		if ( $forwardUser ) {
			$block .= "If you are not redirected shortly please <a href=\"".FINAL_DESTINATION."\">click here</a>.</p>\n\n<script type=\"text/javascript\">setTimeout('window.location=\"".FINAL_DESTINATION."\"',5000)</script>\n";
		} else { $block .= "</p>\n\n"; }
	} else {
		$block = "<div id=\"top\">\n\n<h2>I'm sorry</h2>\n\n</div>\n\n<p class=\"sent\">Your form has not been submitted. There may be a problem with the server. Please contact the administrator.\n";
	}

// If it's not ready to email then run errors and display back form data
} elseif ( array_key_exists('submit', $_POST) ){
	   
	// Check required fields for any data
	foreach ($_POST as $field => $data) {
		$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
		if ( (strstr($field,'required')) && (empty($data)) ) {
			$field = ucwords(str_replace('required','',$field));
			$errors[] = "A required field was left blank: <strong>$field</strong>";
		}
		// Error checking on select boxes when the defailt is "didnotchoose" (see README)
		if ( (is_array($data)) && (in_array('didnotchoose',$data)) && count($data) == 1 ) {
			$field = ucwords(str_replace('required','',$field));
			$errors[] = "A required field was left blank: <strong>$field</strong>";
		}
	}
	
	// Check to standard email field to validate
	foreach ($_POST as $field => $data) {
		$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "", $field));
		$data = strtolower($data);
		if ( (strstr($field,'email')) && (!empty($data)) && (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $data)) ) {
			$errors[] = "This email address is not valid: <strong>$data</strong>";
		}
	}
	
	// Setup HTML display of form values
	$title = "Form Results";
	$block = "<div id=\"top\">\n    <h2>Form Results</h2>\n    <h5>Please Review Your Information</h5>\n    <h5 class=\"red\">Form Not Yet Submitted</h5>\n</div>\n\n<div id=\"results\">\n</div>\n\n<div id=\"results\">\n\n";

	// Run the error report and display it if needed
	if ( ! empty($errors) ) {
		if ( count($errors) > 1 ) { $error_text = "Errors"; } else { $error_text = "Error"; }
		$block .= " <div class=\"error\">\n   <p><strong>$error_text in Form</strong></p>\n";
		$block .= "   <ul>";
		foreach ($errors as $field => $data) {
			$block .= "\n     <li>$data</li>";
		}
		$block .= "\n   </ul>\n   <p><a href=\"javascript:history.go(-1)\">Go back and try again.</a></p>\n </div>\n\n";
	}
	$block .= " <ul id=\"display\">";
	
	
	foreach($_POST as $field => $data) {
		$field = preg_replace('/[-_]/',' ',strtolower(str_replace('required','',$field)));
		if ( ! strstr($field,'ignore') ) {
			// Parse out arrays comma-separated
			if ( is_array($data) ) { $data = implode(", ", $data); }
			$data = str_replace('didnotchoose, ', '', $data);
         // $data = htmlspecialchars($data);
			if ( ($data == "") || ($data == "didnotchoose") ) { $data = "<strong>[ left blank ]</strong>"; }
         if ( strtolower($field) != "submit" ) {
            $block .= "\n   <li><strong>".str_replace('ignore', '', ucwords($field)). ":</strong> <span>".stripslashes($data)."</span></li>";
         }
		}
	}
	$block .= "\n  </ul>\n\n</div>\n\n";
	// Setup hidden form for email submission 
	$block .= "<div id=\"email\">\n    <form name=\"emailit\" action=\"\" method=\"post\" accept-charset=\"utf-8\">\n";
	$block .= "\t<input type='hidden' name='emailnow' value='emailnow' id='emailnow' />\n";
	foreach ($_POST as $field => $data) {
		if ( is_array($data) ) { $data = implode(", ", $data);	}
		$value = str_replace('didnotchoose, ', '', $data);
		if ( $data == "" ) { $data = "[ left blank ]"; }
		if ( ! $includeBlankFields ) {
			if ( ($field !== "submit") && ($data !== "[ left blank ]") ) {
				$block .= "\t<input type=\"hidden\" name=\"".$field."\" value=\"".htmlspecialchars(stripslashes($value))."\" id=\"".$field."\" />\n";
			}
		} else { 
			if ( $field !== "submit" ) {
				$block .= "\t<input type=\"hidden\" name=\"".$field."\" value=\"".htmlspecialchars(stripslashes($value))."\" id=\"".$field."\" />\n";
			}
		}
	}
	
	if ( ! empty($errors) ) {
		$block .= "\t<p><input type=\"button\" name=\"back\" value=\"Go Back\" id=\"back\" onclick=\"history.go(-1);\" /></p>\n    </form>\n</div>\n";
	} else {
		$block .= "\t<p><input type=\"button\" name=\"print\" value=\"Print Results\" id=\"print\" onclick=\"window.print();\" /><input type=\"submit\" name=\"submit\" value=\"Send Results\" id=\"submit\" /></p>\n    </form>\n</div>\n";
	}
	
} else {
	if ( ! $usingTemplate ) {
		$block = "\n<p>There was an error processing the form.<p>\n\n";
	} else {
		include($customForm);
	}
}


if ( ! $usingTemplate ) : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="author" content="Erik Reagan, erikreagan.com"/>
<style type="text/css" media="screen,print">
* {
   margin: 0;
   padding: 0;
   font-family: "Lucida Grande",Georgia,Tahoma,Arial,Serif;
}
body { position: relative; }
#top {
   position: fixed;
   width: 100%;
   top: 0px;
   left: 0px;
   padding: 14px;
   display: block;
   background: #efefef;
   border-bottom: 1px solid #c0c0c0;
   z-index: 2;
}
h5.red { color: #dd3c10; }
.error {
   background: #ffebe8;
   border: 1px solid #dd3c10;
   padding: 10px;
   margin-bottom: 15px;
}
.error p,.error li { font-size: 9pt; padding: 0 !important; margin: 0; }
.error ul { list-style: disc; margin: 12px; }
.error ul li { margin-left: 15px; padding-left: 10px; }
#results { margin: 100px 14px 14px 14px; }
ul { list-style: none; margin: 0px; z-index: 1; }
ul#display li {
   position: relative;
   padding: 4px 0;
   clear: both;
   font-size: 14px;
}
ul#display li strong { padding-right: 75%; }
/* So the Key and Value do not overlap */
#display li span {
   width: 75%;
   border-bottom: 1px solid #777;
   float: right;
   margin-top: -17px;  /* This (more or less) lines up the top of the value with the top of the key */
   top: 1px;  /* This is needed to make the 1px border show at 100% width otherwise the key covers the border. */
   padding-left:25%;
   padding-bottom: 4px;
}
p { margin: 10px; }
p.sent { margin: 80px 14px 14px 14px; }
#email {
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   text-align: right;
   z-index: 3;
}
#email p { padding: 14px; }
#email input#submit,#email input#print,#email input#back {
   cursor: pointer;
   color: #00f;
   padding: 8px;
   background: none;  /* Without at least one of these Safari won't style the button in a custom fashion */
   border: 2px solid #ccc;  /* Without at least one of these Safari won't style the button in a custom fashion */
   font-size: .6em;
   text-transform: uppercase;
   margin-left: 7px;
}
#email input#submit:hover,#email input#print:hover,#email input#back:hover {
   background: #ddd;
   color: #f00;
}

</style>
<style type="text/css" media="print"> h5.red, #email { display: none; } </style>
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

if ( ! $usingTemplate ) : ?>
</body>
</html>
<?php endif ?>