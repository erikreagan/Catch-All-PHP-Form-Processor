<?php ini_set('display_errors',E_ALL); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <title>Testing New Processor</title>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <meta name="author" content="Ideal Design Firm, LLC"/>
   <meta name="description" content=""/>
   <link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" charset="utf-8"/>
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
</head>

<body>

<?php

require 'catch_all.php';

$form = new Catch_all;

echo "<pre>";
print_r($form->results);
echo "</pre>";



?>
<form action="" method="post" accept-charset="utf-8">
	<p>First Name: <?php echo $form->create_field('required_first_name','Erik','text')?></p>
	<p>Last Name: <?php echo $form->create_field('required_last_name','Reagan','text')?></p>
	<p>Email Address: <?php echo $form->create_field('email_address','erik@idealdesignfirm.com','text')?><br/></p>
	<p>Are you over 18?</p>
	<p><?php echo $form->create_field('are_you_over_18','Yes','radio')?>Yes</p>
	<p><?php echo $form->create_field('are_you_over_18','No','radio')?>No<br/></p>
	<p>Additional Comments:</p>
	<p><textarea name="additional-comments" rows="8" cols="40">Testing something out "like quotation marks" and other stuff too.
<strong>it's an apostrophe</strong></textarea><br/><br/></p>
	<p><?php echo $form->create_field('vehicle[]','Bike','checkbox')?> I have a bike</p>
	<p><?php echo $form->create_field('vehicle[]','Car','checkbox')?> I have a car</p>
	<p><?php echo $form->create_field('vehicle[]','Airplane','checkbox')?> I have an airplane:<br/></p>
	<p>Favorite Types of Cars</p>
	<p><?php echo $form->create_select_open('car[]',TRUE)?> 
	<option value ="Volvo" <?php echo $form->is_selected('car','Volvo',TRUE,'selected')?>>Volvo</option>
	<option value ="Saab" <?php echo $form->is_selected('car','Saab',TRUE,'selected')?>>Saab</option>
	<option value ="Opel" <?php echo $form->is_selected('car','Opel',TRUE,'selected')?>>Opel</option>
	<option value ="Audi" <?php echo $form->is_selected('car','Audi',TRUE,'selected')?>>Audi</option>
	<?php echo $form->create_select_close()?></p>
	<p><input type="submit" name="submit" value="Submit Results" /></p>
</form>
</body>
</html>