<?php

/**
 * Catch All PHP Form Processor
 *
 * @author Erik Reagan
 * @version 3.0b1
 * @copyright Erik Reagan, 1 April, 2010
 * @package CatchAllPHPFormProcessor
 * @see http://erikreagan.com/projects/catch-all-php-form-processor/
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported
 **/

ini_set('display_errors',E_ALL);

class Catch_all
{
   
   /**
    * Initial variables for use in our class
    */

   public $email_recipient         = "erik@erikreagan.com";
   public $bcc_recipient           = "";   // Optional
   protected $forward_user         = TRUE;
   public $final_destination       = "/";
   protected $subject_in_form      = TRUE;   // If set to TRUE this overrides the proceeding $subject variable
   public $subject                 = "My PHP Form"; // Define custom Subject value here if above variable is FALSE
   public $subject_field           = "required-subject";  // If your subject is defined in your form put the field name here.
   protected $from_name_in_form    = TRUE;   // Set to true if the Name the email results should be From is supplied in your form
   public $from_name               = "Your Name";   // "From" Name when when sent
   public $from_name_field         = "required-your-name";   // If your from name is defined in your form put the field name here
   protected $from_email_in_form   = TRUE;   // Set to true if the Email address the email results should be From is supplied in your form
   public $from_email_field        = "required-your-email";   // If your from email address is defined in your form put the field name here
   protected $bypass_review        = FALSE;  // Set to TRUE if you want to bypass the review/print screen
   protected $include_timestamp    = TRUE;   // Set to FALSE if you do not want the time/date included in the message
   protected $include_blank_fields = TRUE;   // Set to FALSE if you do not wish to email fields that aren't filled in 
   protected $email_html           = TRUE;   // TRUE sends email in HTML; FALSE sends email in plain text
   protected $header_troubles      = FALSE;  // Only set this to TRUE if youe email headers aren't being sent correctly from your server.
   protected $debug_mode           = TRUE; // Switch on to enable debugging log; no emails will be sent and errors and notes will be displayed to everyone

   /**
    * Initial arrays for use in our class
    */

   public $all_settings            = array(); // compiles all above strings into an array for easy access to all at once (necessary? probably not.)
   public $results                 = array(); // field results upon submission
   public $errors                  = FALSE; // errors for use after submission



   function __construct()
   {
      
      $this->check_for_submission();
      
   }
   
   
   /**
    * Check page for submission
    *
    * @return bool
    * @author Erik Reagan
    **/
   public function check_for_submission()
   {
      
      if ( array_key_exists('submit',$_POST) )
      {
         $this->sanitize_post($_POST);
         return TRUE;
      } else {
         return FALSE;
      }
      
   }
   
   
   
   /**
    * Sanitize Post values
    *
    * @return array
    * @author Erik Reagan
    **/
   public function sanitize_post($post_array)
   {

      // We loop through our post array to sanitize each field that was submitted
      foreach ($post_array as $key => $value) {

         // First we check to see if there are any array created from checkbox groups or multi-selects
         if (is_array($value))
         {
            // Start a blank line for our comma separated list and count through entries to place commas correctly
            $post_array[$key] = '';
            $count = 1;
            foreach ($value as $sub_key => $sub_value) {
               if ($count > 1) $post_array[$key] .= ', ';
               $post_array[$key] .= htmlspecialchars(stripslashes($sub_value));
               $count++;
            }

         } else {
            // Not an array so we just clean it up
            $post_array[$key] = htmlspecialchars(stripslashes($value));
         }
      }
      
      // Create a local variable available to developers to use during form process
      $this->results = $post_array;
      
      unset($_POST);
      
   }
   
   
   
   /**
    * Create new input field or re-generate field after submission
    *
    * @return string
    * @author Erik Reagan
    **/
   public function create_field($name = 'field', $default_value = NULL, $type = 'text')
   {
      
      $field = '';
      if (strpos($name,'[]') !== FALSE)
      {
         $name_key = str_replace('[]','',$name);
         $id = str_replace('[]','',$name);
         $array_boolean = TRUE;
      } else {
         $name_key = $name;
         $id = $name;
         $array_boolean = FALSE;
      }
      
      
      
      switch ($type) {
         case 'textarea':
            echo 'textarea';
            break;
            
         case 'radio':
            $field .= '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="'.$default_value.'"';
            $field .= $this->is_selected($name_key,$default_value,$array_boolean);
            $field .=' />';
            break;
            
         case 'checkbox':
            $field .= '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="'.$default_value.'"';
            $field .= $this->is_selected($name_key,$default_value,TRUE);
            $field .=' />';
            break;
         
         
         default:
            $field .= '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="';
            $field .= $this->get_field($name_key,TRUE,$default_value);
            $field .='" />';
            break;
      }
      
      return $field;
      
   }
   
   
   
   /**
    * Create open tag for select form field type
    *
    * @return string
    * @author Erik Reagan
    **/
   public function create_select_open($name = 'field', $multiple = FALSE)
   {
      
      $id = (strpos($name,'[]') !== FALSE) ? str_replace('[]','',$name) : $name ;
      $multiple = ($multiple) ? 'multiple="multiple"' : '' ;
      
      return '<select name="'.$name.'" id="'.$id.'" '.$multiple.'>';
      
   }
   
   
   
   /**
    * Create close tag for select form field type
    *
    * @return string
    * @author Erik Reagan
    **/
   public function create_select_close()
   {
      return '</select>';
   }
   
   
   /**
    * Get field value
    *
    * @return string / bool
    * @author Erik Reagan
    **/
   public function get_field($field_name, $use_default = FALSE, $default_value = NULL)
   {
      $default_value = ($use_default) ? $default_value : FALSE ;
      
      return (array_key_exists($field_name, $this->results)) ? $this->results[$field_name] : $default_value ;
      
   }
   
   
   
   /**
    * Check if checkbox, radio or option select was checked/selected
    *
    * @return string
    * @author Erik Reagan
    **/
   public function is_selected($haystack = FALSE, $needle = FALSE, $is_array = TRUE, $attribute = 'checked')
   {
      
      if ($haystack == FALSE || $needle == FALSE)
      {
         return;
      }

      if ($is_array)
      {
         if ( (! array_key_exists($haystack, $this->results)) || (count($this->results) === 0))
         {
            return;
         } else {
            return (strpos($this->results[$haystack],$needle) !== FALSE) ? ' '.$attribute.'="'.$attribute.'"' : '' ;
         }
      } else {
         return (array_key_exists($haystack, $this->results) && $this->results[$haystack] == $needle) ? ' '.$attribute.'="'.$attribute.'"' : '' ;
      }
      
   }
   
}
// End of class Catch_all