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

$form->show_errors('ul_id','ul_class');
?>
   
   

   
   
<?php if ($form->show_form) : ?>
   
<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	<p>First Name: <?php echo $form->create_field('required_first_name','Erik','text')?></p>
	<p>Last Name: <?php echo $form->create_field('required_last_name','','text')?></p>
	<p>Email Address: <?php echo $form->create_field('email_address','erikidealdesignfirm.com','text')?><br/></p>
	<p>Are you over 18?</p>
	<p><?php echo $form->create_field('are_you_over_18','Yes','radio')?>Yes &nbsp;&nbsp;&nbsp;<?php echo $form->create_field('are_you_over_18','No','radio')?>No<br/></p>
	<p>Additional Comments:</p>
	<p><?php echo $form->create_field('additional_comments','Testing something out "like quotation marks" and other stuff too.
<strong>it\'s an apostrophe</strong>','textarea',array('rows'=>'3','cols'=>'50'))?><br/><br/></p>
	<p><?php echo $form->create_field('vehicle[]','Bike','checkbox')?> I have a bike&nbsp;&nbsp;&nbsp;<?php echo $form->create_field('vehicle[]','Car','checkbox')?> I have a car&nbsp;&nbsp;&nbsp;<?php echo $form->create_field('vehicle[]','Airplane','checkbox')?> I have an airplane:<br/></p>
	<p>Upload File</p>
	<p><input type="file" name="photo_upload" value="" id="attachment" />
	<p>Favorite Types of Cars</p>
	<p><?php echo $form->create_select_open('car[]',TRUE)?> 
	<?php echo $form->create_field('car[]',array('Volvo','Volvo'),'option')?> 
	<?php echo $form->create_field('car[]',array('Saab','Saab'),'option')?> 
	<?php echo $form->create_field('car[]',array('Opel','Opel'),'option')?> 
	<?php echo $form->create_field('car[]',array('Audi','Audi'),'option')?> 
	<?php echo $form->create_select_close()?></p>
   <p><?php echo $form->create_field('submit','Submit Results','submit') ?></p>
</form>

<?php endif; ?>

<?php if($form->stage == 'review') : ?>

<?php $form->show_review_results('ul_id','ul_class'); ?>

<?php elseif($form->stage == 'sent') : ?>

<p>Success!</p>
   
<?php else : ?>
   
<p>Hmmm, that was unexpected</p>

<?php endif; ?>

</body>
</html>