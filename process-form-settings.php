<?php

/**
 * @package Catch-All PHP Form Processor
 * @version 2.0.1
 * @author Erik Reagan <http://erikreagan.com>
 * @copyright Copyright (c) 2008-2009 Erik Reagan
 * @see http://www.erikreagan.com/projects/2008/05/catch-all-php-form-processor/
 */


/**
 * Set the variables for the processing script below. You will find
 * relevant notes in the comments above and to the right of the
 * variable sections.
 */




/**
 * Set to true if you are using this form within your own
 * site (a php include for example)
 * @var bool
 */
   
   $using_template      = FALSE;



/**
 * If you are using your own template or site design, define the
 * path to your contact form which should be it's own .php file
 * Will only be called if $using_template is set to TRUE
 * @var string
 */
   
   $custom_form         = "sample-form-only.php";



/**
 * Email recipient and blind carbon copy recipient
 * @var string
 * @var string
 */
 
   $email_recipient     = "yourname@yourdomain.com";
   $bcc_recipient       = "";   // Optional


/**
 * Forward after submission details
 * @var bool
 * @var string
 */
 
   $forward_user        = TRUE;
   $final_destination   = "/";


/**
 * Form subject details. Subject is either in the form fields
 * or defined here in these settings
 * @var bool
 * @var string
 * @var string
 */

   $subject_in_form     = TRUE;   // If set to TRUE this overrides the proceeding $subject variable
   $subject             = "My PHP Form"; // Define custom Subject value here if above variable is FALSE
   $subject_field       = "required-subject";  // If your subject is defined in your form put the field name here.


/**
 * From name details. Defined either in the form fields
 * or defined here in these settings
 * @var bool
 * @var string
 * @var string
 */
 
   $from_name_in_form   = TRUE;   // Set to true if the Name the email results should be From is supplied in your form
   $from_name           = "Your Name";   // "From" Name when when sent
   $from_name_field      = "required-your-name";   // If your from name is defined in your form put the field name here


/**
 * From email details. Defined either in the form fields
 * or defined here in these settings
 * @var bool
 * @var string
 * @var string
 */
 
   $from_email_in_form  = TRUE;   // Set to true if the Email address the email results should be From is supplied in your form
   $from_email          = "yourhandle@domain.com";  // For multiple separate by comma
   $from_email_field    = "required-your-email";   // If your from email address is defined in your form put the field name here


/**
 * Additional details for form message
 * @var bool
 * @var bool
 * @var bool
 * @var bool
 * @var bool
 */

   $bypass_review          = FALSE;  // Set to TRUE if you want to bypass the review/print screen
   $include_timestamp      = TRUE;   // Set to FALSE if you do not want the time/date included in the message
   $include_blank_fields   = TRUE;   // Set to FALSE if you do not wish to email fields that aren't filled in 
   $email_html             = TRUE;   // TRUE sends email in HTML; FALSE sends email in plain text
   $header_troubles        = FALSE;  // Only set this to TRUE if youe email headers aren't being sent correctly from your server.



?>