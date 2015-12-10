<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#register_btn').on('click', function(){
			$.post('http://107.155.88.38/gift/api/register', {
				'fb_uid': $('#fb_uid').val(),
				'fb_email': $('#fb_email').val(),
				'type': $('#type').val()	
			}, function(data){
				$('#result').html('<center>Loading ...</center>').html(data);
			});
		});
	});
</script>

FB UID: 
<input type="text" id="fb_uid" /> 
FB Email: 
<input type="text" id="fb_email" /> 
Type: 
<select id="type">
	<option value="facebook">facebook</option>
	<option value="google">google</option>
	<option value="email">email</option>
</select>
<input type="button" id="register_btn" value="REGISTER" />
<br/><br/>
<div id="result"></div>