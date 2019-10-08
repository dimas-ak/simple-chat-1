<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Page Title</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" media="screen" href="<?PHP echo $css ?>" />
		<link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">
	</head>
	<body>
		<?PHP echo $body ?>
		<script src="<?PHP echo $jquery ?>" type="text/javascript"></script>
		<script src="<?PHP echo $io ?>" 	type="text/javascript"></script>
		<script src="<?PHP echo $js ?>" 	type="text/javascript"></script>
		<script type="text/javascript">
		$(document).ready(function(){
			let http   = (window.location.protocol === 'http:') ? "http://" : "https://";
			let socket = io( http + window.location.hostname + ":3000" );
			//menambahkan atau edit pesan/chat
			$(document).on('submit', '.form-send', function (x) { 
				x.preventDefault(); 
				let ini    = $(this),
					url    = ini.attr('action'),
					reply  = ini.find(".reply"),
					_file  = ini.find("#_photo"),
					data   = ini.serialize(),
					cate   = ini.attr('category'), //kategori form apakah edit atau add
					data_id= ini.attr('data-id'), //mendapatkan id chat jika kategori == edit
					msg    = ini.find("textarea"), //mendapatkan input'an
					from   = ini.find("#_from"), //mendapatkankan id dari pengirim
					_chat  = $("body").find(".chat .chat-wrap"), //mencari body .chat-wrap
					photo  = ini.find('._photo'), //mendapatkan url photo si pengirim
					to     = ini.find("#_to"); //mendapatkan id untuk siapa yang akan di kirim

				let final_url = (cate === 'add') ? url : url + data_id;

				$.ajax({
					url 	    : final_url,
					type	    : "POST",
                    data	    : new FormData(ini[0]), //penggunaan FormData
					processData : false,
					contentType : false,
					mimeType    : "multipart/form-data",
					beforeSend  : function()
					{
						msg.attr('disabled', true);
					},
					success: function(data)
					{
						let json = JSON.parse(data);
						if(!json.error)
						{
							//mengirimkan pesan (add)
							if(cate === 'add')
							{
								//menampilkan pesan ke si pengirim
								_chat.append(appendPesan(json.pesan, false));
								//mengirimkan data pesan ke socket io
								socket.emit("kirim_pesan", json.pesan);
								msg.attr('disabled', false);
								_file.val("");
								msg.val("");
							}
							else
							{
								if(json.category_msg === 'edit')
								{
									//menampilkan pesan yang sudah di edit ke si pengirim
									editDeletePesan(json.pesan, true);
									//mengirimkan data pesan yang sudah di edit ke socket io
									socket.emit("edit_pesan", json.pesan);
								}
								else
								{
									reply.removeClass('aktif');
									_chat.append(appendPesan(json.pesan, false));
									socket.emit("kirim_pesan", json.pesan);
								}

								msg.attr('disabled', false);
								msg.val("");

								//mengembalikan form ke mode add
								ini.attr('data-id',"");
								ini.attr('category','add');
							}
							
							//memberitahukan bahwa user sudah meng-submit form
							let pesan = {status_ngetik: false, _for: 0};
							//menerapkan data ngetik ke socket io
							socket.emit("is_ngetik", pesan);
						}
						else
						{
							msg.attr('disabled', false);}
							_file.val("");
							msg.focus();
					}
				});
			});
			
			//sedang ngetik
			$(document).on('input', 'textarea.send-input', function (x) { 
				let pesan  = {status_ngetik: false, _for: 0, _from: 0, replaceFor : 0},
					_for   = $(this).parents('form.form-send').find("#_to").val(); //mendapatkan id user yang sedang ngetik
					_from  = $(this).parents('form.form-send').find("#_from").val();
				if($(this).val().trim().length !== 0)
				{
					pesan.status_ngetik = true;
					pesan._for = _for;
					pesan._from = _from;
				}
				else
				{
					pesan.replaceFor = _from;
					pesan._for = _for;
					pesan._from = _from;
					pesan.status_ngetik = false;
				}
				//menerapkan data ngetik ke socket io
				socket.emit("is_ngetik", pesan);
			});

			//menghapus info sedang ngetik jika user membuka chat di lain user
			$(document).on('click', '.show-msg', function(x) {
				x.preventDefault();
				let pesan = { status_ngetik : false, _for : 0};
				socket.emit('is_ngetik', pesan);
			});

			//mendapatkan pesan
			socket.on("kirim_pesan", function(pesan){
				let user	= $("body").find(".show-msg"), //a-href dari .show-msg yang berada di dalam kontak user
					_chat   = $("body").find(".chat .chat-wrap"),
					msg  	= pesan.msg,
					from 	= pesan.from,
					to   	= pesan.to;
				for(let i = 0; i < user.length; i++)
				{
					let _user   = user.eq(i),
						user_to = _user.attr('for'),
						attr_user = _user.attr('user');
					if(!_user.hasClass('aktif') && from === user_to && to === attr_user) //jika pesan "DARI/FROM" sama dengan attribute "for" di element .show-msg dan tidak mempunyai class aktif. Ini berlaku hanya untuk menu kontak user sebelah kiri
					{
						setTimeout(function () {
							_user.find(".msg-new span").html(msg);
						}, 500);
					}
					// menghapus pesan is ngetik
					else if(_user.hasClass('aktif') && from === user_to && to === attr_user)
					{
						_user.find(".msg-new span").html("");
					}
					
					if(_user.hasClass('aktif') && from === user_to && to === attr_user)//jika pesan "DARI/FROM" sama dengan attribute "for" di element .show-msg dan mempunyai class aktif. Ini hanya berlaku untuk menu chat sebelah kanan
					{
						_chat.append(appendPesan(pesan, true));
					}
				}
			});

			//mendapatkan pesan yang sudah di edit.
			socket.on('edit_pesan', function(data){
				editDeletePesan(data, true);
			});

			//mendapatkan pesan yang sudah di terhapus.
			socket.on('delete_pesan', function(data){
				editDeletePesan(data, false, true);
			});

			//mendapatkan info apakah user yang bersangkutan sedang ngetik
			socket.on('is_ngetik', function(data){
				is_ngetik(data);
			});
			
			//EDIT ATAU DELETE PESAN/CHAT 
			$(document).on("click", '.action-msg ._button', function () { 
				let ini        = $(this),
					action_msg = ini.parent(),
					category   = ini.attr('category'),
					data_id    = ini.attr('data-id');
				
				let final_url  = "<?PHP echo $delete ?>";
				if(category === 'delete')
				{
					$.ajax({
						url	 : final_url + data_id,
						type : "POST",
						success: function(data)
						{
							let json = JSON.parse(data);
							console.log(json);
							if(json.msg === 'success')
							{
								//menghapus pesan
								editDeletePesan(json.pesan, false);
								//mengirimkan info bahwa pesan telah dihapus ke socket io
								socket.emit("delete_pesan", json.pesan);
							}

						}
					});
				}
				else if(category === 'edit')
				{
					let form 	  = $("body").find(".chat form"),
						textarea  = form.find("textarea"),
						reply	  = form.find('.reply'),
						reply_inp = form.find('#_reply'),
						msg 	  = "",
						chat 	  = $("body").find(".inner-chat .chat-wrap .contact");
					for(let i = 0; i < chat.length; i++)
					{
						if(chat.eq(i).attr('data-id') === data_id)
						{
							msg = chat.eq(i).find(".msg > pre").text();
						}
					}
					reply_inp.val("");
					reply.removeClass('aktif');
					form.attr('category', 'edit');
					form.attr('data-id', data_id);
					textarea.val(msg);
				}
				else if(category === 'reply')
				{
					let form 	  = $("body").find(".chat form"),
						img       = form.find(".reply img"),
						msg 	  = form.find(".reply pre"),
						input	  = form.find('.form-send-inner textarea'),
						reply_inp = form.find('#_reply'),
						reply	  = form.find('.reply'),
						getData   = setDataMessage(),
						src_img   = (typeof getData.img === 'undefined') ? null : getData.img;

					reply.addClass('aktif');

					reply_inp.val("reply");

					form.attr('category','edit');
					form.attr('data-id',data_id);
					
					input.focus();

					img.attr('src', src_img);
					msg.html(getData.msg);
				}
				else if(category === 'forward')
				{
					let forward = $('body').find('.forward');
					forward.addClass('aktif');
					forward.attr('data-msg', data_id);
					forwardContact();
				}
				action_msg.removeClass('aktif');
			});
		});
		</script>
	</body>
</html>