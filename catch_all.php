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
   protected $debug_mode           = TRUE; // Switch on to enable debugging log; no emails sent and errors and notes will be displayed
   
   
   /**
    * This is our $stage variable which will be used to determine what to show on the front-end
    */
   
   public $show_form               = TRUE;
   public $stage                   = 'start';

   /**
    * Initial arrays for use in our class
    */

   public $all_settings            = array(); // compiles all above strings into an array for easy access to all at once (necessary? probably not.)
   public $results                 = array(); // field results upon submission
   public $errors                  = FALSE; // errors for use after submission
   
   
   
   /**
    * Constructor, runs each time our class is initialized
    *
    * @return bool
    * @author Erik Reagan
    **/
   function __construct()
   {
      
      $this->check_for_submission();
      
   }
   // End of __construct
   
   
   
   
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
   // End of check_for_submission
   
   
   
   
   /**
    * Sanitize Post values
    *
    * @return array
    * @author Erik Reagan
    **/
   public function sanitize_post($post_array)
   {
      
      // No need to use the submit field
      unset($post_array['submit']);

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
   // End of sanitize post
   
   
   
   
   /**
    * Create new input field or re-generate field after submission
    *
    * @return string
    * @author Erik Reagan
    **/
   public function create_field($name = 'field', $default_value = NULL, $type = 'text', $parameters = NULL)
   {
      
      // Start our field string so we can add to it as we build the field
      $field = '';
      
      // We need to know if we're supposed to treat a field value as a string or an array
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
      
      // Now we build the field based on what type of input we have at hand
      switch ($type) {
         case 'radio':
            $field .= '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="'.$default_value.'"';
            $field .= $this->is_selected($name_key,$default_value,$array_boolean);
            $field .=' />';
            break;
            
         case 'option':
            $field .= (is_array($default_value)) ? '<option value ="'.$default_value[0].'"' : '<option value="'.$default_value.'"' ;
            $check_default_value = (is_array($default_value)) ? $default_value[0] : $default_value ;
            $field .= $this->is_selected($name_key,$check_default_value,$array_boolean,'selected');
            $field .= (is_array($default_value)) ? '>'.$default_value[1].'</option>' : '>'.$default_value.'</option>' ;
            break;
            
         case 'checkbox':
            $field .= '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="'.$default_value.'"';
            $field .= $this->is_selected($name_key,$default_value,$array_boolean);
            $field .=' />';
            break;
            
         case 'textarea':
            $rows = (isset($parameters['rows'])) ? $parameters['rows'] : '8' ;
            $cols = (isset($parameters['cols'])) ? $parameters['cols'] : '40' ;
            $field .= '<textarea name="'.$name_key.'" rows="'.$rows.'" cols="'.$cols.'">'.$default_value.'</textarea>';
            break;
            
         case 'submit':
            $field .= '<input type="submit" name="'.$name_key.'" value="'.$default_value.'" />';
            break;
         
         // Out default is "text" (as seen in the method arguments above)
         default:
            $field .= '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="';
            $field .= $this->get_field($name_key,TRUE,$default_value);
            $field .='" />';
            break;
      }
      
      return $field;
      
   }
   // End of create_field
   
   
   
   
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
   // End of create_select_open
   
   
   
   
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
   // End of create_select_close
   
   
   
   
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
   // End of get_field
   
   
   
   
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
   // End of is_selected
   
   
   
   
   /**
    * Display results from a submitted form
    *
    * @return echo string
    * @author Erik Reagan
    **/
   public function show_review_results($id = FALSE, $class = FALSE)
   {
      
      // If our results array is empty then we don't move any further
      if ( ! count($this->results > 0))
      {
         return;
      }
      
      
      // Configure our unordered list ID and CLASS if applicable
      $ul_properties = '';
      if ($id || $class)
      {
         $id = ($id) ? ' id="'.$id.'"' : '' ;
         $class = ($class) ? ' class="'.$class.'"' : '' ;
         $ul_properties = $id.$class;
      }
      
      $results_block = "<ul{$ul_properties}>";
      
      foreach ($this->results as $key => $value) {
         $key = preg_replace('/[-_]/',' ',strtolower(str_replace('required','',$key)));
         // Don't return any of the fields we are supposed to ignore
   		if ( ! strstr($key,'ignore') )
   		{
   			// Parse out arrays comma-separated
   			if ( is_array($value) )
   			{
   			   $value = implode(", ", $value);
   			}
   			
   			$value = str_replace('didnotchoose, ', '', $value);

   			if ( ($value == "") || ($value == "didnotchoose") )
   			{
   			   $value = "<strong>[ left blank ]</strong>";
   			}
            
            $results_block .= "\n   <li><strong>".str_replace('ignore', '', ucwords($key)). ":</strong> <span>".stripslashes($value)."</span></li>";
   		}
      }
      
      $results_block .= "\n</ul>\n\n";
      
      echo $results_block;
      
   }
   // End of show_review_results
   
   
   
   
   /**
    * Display errors from a submitted form
    *
    * @return echo string
    * @author Erik Reagan
    **/
   public function show_errors($id = FALSE, $class = FALSE)
   {
      
      // If our results array is empty then we don't move any further
      if ( ! count($this->results > 0))
      {
         return;
      }
      
      
      // Configure our unordered list ID and CLASS if applicable
      $ul_properties = '';
      if ($id || $class)
      {
         $id = ($id) ? ' id="'.$id.'"' : '' ;
         $class = ($class) ? ' class="'.$class.'"' : '' ;
         $ul_properties = $id.$class;
      }
      
      $errors_block = "<ul{$ul_properties}>";
      
      // Check required fields for any data
   	foreach ($this->results as $field => $data)
   	{
   		$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", " ", $field));
   		if ( (strstr($field,'required')) && (empty($data)) )
   		{
   			$field = ucwords(str_replace('required','',$field));
   			$errors_block .= "\n    <li>A required field was left blank: <strong>$field</strong></li>";
   		}
   		// Error checking on select boxes when the defailt is "didnotchoose" (see README)
   		if ( (is_array($data)) && (in_array('didnotchoose',$data)) && count($data) == 1 )
   		{
   			$field = ucwords(str_replace('required','',$field));
   			$errors_block .= "\n    <li>A required field was left blank: <strong>$field</strong></li>";
   		}
   	}

   	// Check to standard email field to validate
   	foreach ($this->results as $field => $data)
   	{
   		$field = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "", $field));
   		$data = strtolower($data);
   		if ( (strstr($field,'email')) && (!empty($data)) && ( ! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $data)) )
   		{
   			$errors_block .= "\n    <li>This email address is not valid: <strong>$data</strong></li>";
   		}
   	}
      
      
      $errors_block .= "\n</ul>\n\n";
      
      echo $errors_block;
      
   }
   // End of show_errors
   
}
// End of class Catch_all