<form action="process-form.php" method="post" accept-charset="utf-8">
	<p>First Name: <input type="text" name="required-first-name" value="" id="fname" /></p>
	<p>Last Name: <input type="text" name="required-last-name" value="" id="lname" /></p>
	<p>Email Address: <input type="text" name="email-address" value="" id="email" /><br/></p>
	<p>Are you over 18?</p>
	<p><input type="radio" name="are-you-over-18" value="Yes" id="yes" />Yes</p>
	<p><input type="radio" name="are-you-over-18" value="No" id="no" />No<br/></p>
	<p>Additional Comments:</p>
	<p><textarea name="additional-comments" rows="8" cols="40"></textarea><br/><br/></p>
	<p><input type="checkbox" id="Vehicle1" name="Vehicle[]" value="Bike" /> I have a bike</p>
	<p><input type="checkbox" id="Vehicle2" name="Vehicle[]" value="Car" /> I have a car</p>
	<p><input type="checkbox" id="Vehicle3" name="Vehicle[]" value="Airplane" /> I have an airplane:<br/></p>
	<p>Favorite Types of Cars</p>
	<p><select name="Car[]" id="Car" multiple="multiple">
	<option value ="Volvo">Volvo</option>
	<option value ="Saab">Saab</option>
	<option value ="Opel">Opel</option>
	<option value ="Audi">Audi</option>
	</select></p>
	<p><input type="submit" name="submit" value="Submit Results" /></p>
</form>