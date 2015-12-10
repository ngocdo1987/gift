<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#search_btn').on('click', function(){
			$.post('http://107.155.88.38/gift/api/search_amz_products', {
				'search': $('#search').val(),
				'category': $('#category').val(),
				'page': $('#page').val()	
			}, function(data){
				$('#result').html('<center>Loading ...</center>').html(data);
			});
		});
	});
</script>

Search: 
<input type="text" id="search" /> 
Category: 
<select id="category">
	<?php foreach($categories as $category) { ?>	
	<option value="<?=$category->category_key?>"><?=$category->category_value?></option>
	<?php } ?>
</select> 
Page: 
<input type="text" id="page" value="1" /> 
<input type="button" id="search_btn" value="SEARCH" />
<br/><br/>
<div id="result"></div>