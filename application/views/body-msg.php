<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-chat">
	<div class="wrap">
		<div class="inner bg-white2">
			<?PHP echo $header ?>
			<div class="section">
				<div class="d-25 bg-white h-100">
					<?PHP echo $view_user ?>
				</div>
				<div class="chat">
					
				</div>
			</div>
		</div>
	</div>
</div>
<?PHP 
	echo $settings; 
	echo $view_profil;
	echo $alert;
	echo $preview_image;
	echo $forward;
?>

<div class="action-msg _self">
	<div class="_button" 	data-id="" 	category="reply">Reply</div><!--
	--><div class="_button" data-id=""  category="forward">Forward Message</div><!--
	--><div class="_button" data-id="" 	category="edit">Edit</div><!--
	--><div class="_button" data-id="" 	category="delete">Delete</div>
</div>
<div class="action-msg _friend">
	<div class="_button" 	data-id="" 	category="reply">Reply</div><!--
	--><div class="_button" data-id=""  category="forward">Forward Message</div>
</div>