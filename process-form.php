<?php

/**
 * @package Catch-All PHP Form Processor
 * @version 2.0.3
 * @author Erik Reagan <http://erikreagan.com>
 * @copyright Copyright (c) 2008-2009 Erik Reagan
 * @see http://www.erikreagan.com/projects/2008/05/catch-all-php-form-processor/
 */

require(dirname(__FILE__).'/process-form-settings.php');



/**
 * You shouldn't edit below this line unless you're familiar enough with PHP
 * While this script is only an intermediate level of PHP I do not guarantee
 * any support on modified files. Proceed at your own risk
 */


/**
 * Definitions from settings file
 **/
 
   define('USING_TEMPLATE', $using_template);
   define('CUSTOM_FORM', $custom_form);
   define('EMAIL_RECIPIENT', $email_recipient);
   define('BCC_RECIPIENT', $bcc_recipient);
   define('FORWARD_USER', $forward_user);
   define('FINAL_DESTINATION', $final_destination);


   /**
    * Subject
    **/
   if ( $subject_in_form )
   {
      define('SUBJECT', $_POST[$subject_field]);
   } else {
      define('SUBJECT', $subject);
   }


   /**
    * From Name
    **/

   if ( $from_name_in_form )
   {
      define('FROM_NAME', $_POST[$from_name_field]);
   } else {
      define('FROM_NAME', $from_name);
   }


   /**
    * From Email
    **/

   if ( $from_email_in_form )
   {
      define('FROM_EMAIL', $_POST[$from_email_field]);
   } else {
      define('FROM_EMAIL', $from_email);
   }
      
      
      
   define('BYPASS_REVIEW', $bypass_review);
   define('INCLUDE_TIMESTAMP', $include_timestamp);
   define('INCLUDE_BLANK_FIELDS', $include_blank_fields);
   define('EMAIL_HTML', $email_html);

   /**
    * Header troubles. Some poor quality Unix mail transfer agents replace
    * LF by CRLF automatically (which leads to doubling CR if CRLF is used).
    * This should be a last resort, as it does not comply with RFC 2822. 
    * @see http://us3.php.net/manual/en/function.mail.php
    **/
   if ( $headerTroubles )
   {
      define('HEADER_TRAIL', "\n");
   } else {
      define('HEADER_TRAIL', "\r\n");
   }
   







if ( array_key_exists('submit', $_POST) )
{
	   
	// Check required fields for any data
	foreach ($_POST as $field => $data)
	{
		$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
		if ( (strstr($field,'required')) && (empty($data)) )
		{
			$field = ucwords(str_replace('required','',$field));
			$errors[] = "A required field was left blank: <strong>$field</strong>";
		}
		// Error checking on select boxes when the defailt is "didnotchoose" (see README)
		if ( (is_array($data)) && (in_array('didnotchoose',$data)) && count($data) == 1 )
		{
			$field = ucwords(str_replace('required','',$field));
			$errors[] = "A required field was left blank: <strong>$field</strong>";
		}
	}
	
	// Check to standard email field to validate
	foreach ($_POST as $field => $data)
	{
		$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "", $field));
		$data = strtolower($data);
		if ( (strstr($field,'email')) && (!empty($data)) && ( ! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $data)) )
		{
			$errors[] = "This email address is not valid: <strong>$data</strong>";
		}
	}

   
   if ( (( ! empty($errors) ) || (BYPASS_REVIEW === FALSE)) && ( ! array_key_exists('senditalready', $_POST)) )
   {
      
      // Setup HTML display of form values
   	$title = "Form Results";
   	$block = "<div id=\"top\">\n    <h2>Form Results</h2>\n    <h5>Please Review Your Information</h5>\n    <h5 class=\"red\">Form Not Yet Submitted</h5>\n</div>\n\n<div id=\"results\">\n</div>\n\n<div id=\"results\">\n\n";

	   // Run the error report and display it if needed
	   if ( ! empty($errors) )
	   {
		   if ( count($errors) > 1 )
		   {
		      $error_text = "Errors";
		   } else {
		      $error_text = "Error";
		   }
		   
   		$block .= " <div class=\"error\">\n   <p><strong>$error_text in Form</strong></p>\n";
   		$block .= "   <ul>";
   		foreach ($errors as $field => $data)
   		{
   			$block .= "\n     <li>$data</li>";
   		}
   		$block .= "\n   </ul>\n   <p><a href=\"javascript:history.go(-1)\">Go back and try again.</a></p>\n </div>\n\n";
   	}

	   $block .= " <ul id=\"display\">";
	
	
   	foreach($_POST as $field => $data)
   	{
   		$field = preg_replace('/[-_]/',' ',strtolower(str_replace('required','',$field)));
   		if ( ! strstr($field,'ignore') )
   		{
   			// Parse out arrays comma-separated
   			if ( is_array($data) )
   			{
   			   $data = implode(", ", $data);
   			}
   			
   			$data = str_replace('didnotchoose, ', '', $data);
            // $data = htmlspecialchars($data);
   			if ( ($data == "") || ($data == "didnotchoose") )
   			{
   			   $data = "<strong>[ left blank ]</strong>";
   			}
            
            if ( strtolower($field) != "submit" )
            {
               $block .= "\n   <li><strong>".str_replace('ignore', '', ucwords($field)). ":</strong> <span>".stripslashes($data)."</span></li>";
            }
   		}
	   }
	   
   	$block .= "\n  </ul>\n\n</div>\n\n";
   	// Setup hidden form for email submission 

   	$block .= "<div id=\"email\">\n    <form name=\"emailit\" action=\"\" method=\"post\" accept-charset=\"utf-8\">\n";
   	
   	$block .= "\t<input type='hidden' name='senditalready' value='goforit' id='goforit' />\n";

   	foreach ($_POST as $field => $data)
   	{
         // Turn arrays into comma separated strings
   		$data = ( is_array($data) ) ? implode(", ", $data) : $data ;
   		// Remove didnotchoose from the new string value
   		$value = str_replace('didnotchoose, ', '', $data);
   		// If form field was left blank we add a string to be shown in the results
   		$data = ($data == "") ? "[ left blank ]" : $data ;
   		
   		if ( ! INCLUDE_BLANK_FIELDS )
   		{
   			if ( ($field !== "submit") && ($data !== "[ left blank ]") )
   			{
   				$block .= "\t<input type=\"hidden\" name=\"".$field."\" value=\"".htmlspecialchars(stripslashes($value))."\" id=\"".$field."\" />\n";
   			}
   		} else { 
   			if ( $field !== "submit" )
   			{
   				$block .= "\t<input type=\"hidden\" name=\"".$field."\" value=\"".htmlspecialchars(stripslashes($value))."\" id=\"".$field."\" />\n";
   			}
   		}
   	}
	
   	if ( ! empty($errors) )
   	{
   		$block .= "\t<p><input type=\"button\" name=\"back\" value=\"Go Back\" id=\"back\" onclick=\"history.go(-1);\" /></p>\n    </form>\n</div>\n";
   	} else {
   		$block .= "\t<p><input type=\"button\" name=\"print\" value=\"Print Results\" id=\"print\" onclick=\"window.print();\" /><input type=\"submit\" name=\"submit\" value=\"Send Results\" id=\"submit\" /></p>\n    </form>\n</div>\n";
   	}

   } else {
      
      // Unset key 'senditalready'
      unset($_POST['senditalready']);
   
      // To send HTML mail, the Content-type header must be set
   	$headers  = 'MIME-Version: 1.0' . HEADER_TRAIL;
   	$headers .= ( ! EMAIL_HTML) ? 'Content-type: text;' . HEADER_TRAIL : 'Content-type: text/html; charset=iso-8859-1' . HEADER_TRAIL ;

   	// Additional headers
   	$headers .= "From: ".FROM_NAME." <".FROM_EMAIL.">" . HEADER_TRAIL;
   	$headers .= "Bcc: ".BCC_RECIPIENT . HEADER_TRAIL;
   	$title = SUBJECT;
   	// Timestamp for inclusion
   	$timestamp = date('M jS, Y')." at ".date('g:ia');

   	if ( ! EMAIL_HTML )
   	{
   		$message = "A new form has been submitted\n\n";
   		
   		foreach($_POST as $field => $data) {
   			if ( is_array($data) ) { $data = implode(", ", $data); }
   			$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
   			$field = str_replace('ignore ','',$field);
   			$field = ucwords(str_replace('required ', '', $field));
   			$message .= ((strtolower($field) == "submit") || (strtolower($field) == "emailnow")) ? "" : "\n$field\n   ".stripslashes($data)."\n";
   		}
		
		   $message .= ( INCLUDE_TIMESTAMP ) ? "\nForm Submitted on $timestamp\n" : NULL ;

   	} else {
   		$message = "	<html>
   		<head>
   		  <title>$title</title>
   		</head>
   		<body>

   		  <p>A new form has been submitted</p>

   		  <table cellpadding='0' cellspacing= '0'>\n
   		";
   		foreach($_POST as $field => $data)
   		{
   			$data = ( is_array($data) ) ? implode(", ", $data) : $data ;

   			$field = preg_replace('/[-_]/',' ',strtolower(str_replace('required','',$field)));
            // $data = htmlspecialchars($data);
   			$message .= ((strtolower($field) == "submit") || (strtolower($field) == "emailnow")) ? "" : "    <tr style='margin:4px'>
   		      <td style='width:200px;border-bottom:1px solid #c0c0c0;'>".ucwords($field)."</td><td style='border-bottom: 1px solid #c0c0c0'>".stripslashes($data)."</td>
   		    </tr>\n
   		";
   		}
   		if ( INCLUDE_TIMESTAMP )
   		{
   			$message .= "    <tr style='margin:4px'>
   		      <td style='width:200px;border-bottom:1px solid #c0c0c0;'>Form Submitted on</td><td style='border-bottom: 1px solid #c0c0c0'>$timestamp</td>
   		    </tr>\n
   		";
   		}
   		$message .= "
   		  </table>

   		</body>
   		</html>
   		";
   	}
      
   	if ( mail(EMAIL_RECIPIENT, SUBJECT, $message, $headers) )
   	{
   		$block = "<div id=\"top\">\n\n<h2>Thank You</h2>\n\n</div>\n\n<p class=\"sent\">Your form has been submitted.\n";
   		if ( FORWARD_USER ) {
   			$block .= "If you are not redirected shortly please <a href=\"".FINAL_DESTINATION."\">click here</a>.</p>\n\n<script type=\"text/javascript\">setTimeout('window.location=\"".FINAL_DESTINATION."\"',5000)</script>\n";
   		} else { $block .= "</p>\n\n"; }
   	} else {
   		$block = "<div id=\"top\">\n\n<h2>I'm sorry</h2>\n\n</div>\n\n<p class=\"sent\">Your form has not been submitted. There may be a problem with the server. Please contact the administrator.\n";
   	}

   }
   
} else {
   if ( ! USING_TEMPLATE )
   {
   	$block = "\n<p>There was an error processing the form.<p>\n\n";
   } else {
   	include(CUSTOM_FORM);
   }
}

if ( ! USING_TEMPLATE ) :

?>
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
<?php

endif;

echo $block;

if ( ! USING_TEMPLATE ) :

?>
</body>
</html>
<?php endif; ?>