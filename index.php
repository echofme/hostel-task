<?php
	require_once('comments_handler.php');
?>


<html>	
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<div id='comments'>
			<?php 
				$comments = $Comments_handler->getComments();
				echo $Comments_handler->getCommentsTemplate($comments);
			?>
		</div><br>
		<a id='send' onclick='showForm();'>Написать комментарий</a> 
		
			
		<div id='reply' class="b-popup">
		    <div class="b-popup-content">
		        <input type='text' name='name' id='form_name' placeholder="Ваше имя"><br>
				<textarea placeholder="Ваш комментарий" name='text' id='form_text'></textarea><br>
				<a id='send' onclick='hideForm();'>Отменить</a> <a id='send' onclick='send(0);'>Отправить</a>
		    </div>
		</div>


		<script>
		var cur_id = 0;
		function send(id) {
			var name =  document.getElementById('form_name').value;
			var text =  document.getElementById('form_text').value;
			if (name == "") {
				name = "Гость";
			}
			if (text == "") { 
				alert("Введите комментарий");
				return;
			}
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4) {
	                if (xmlhttp.status == 200) {
	                    location.href = '/index.php';
	                }
	            } 
			} 
			xmlhttp.open("GET", "comments_handler.php?act=send&id="+cur_id+"&text="+encodeURI(text)+"&name="+encodeURI(name), true);
			xmlhttp.send(); 
		}

		function del(id) {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	            if (xmlhttp.readyState == 4) {
	                if (xmlhttp.status == 200) {
	                    if (this.responseText == 1) {
	                    	var el =  document.getElementById(id);
							el.parentNode.removeChild(el);
	                    }
	                }
	            } 
			} 
			xmlhttp.open("GET", "comments_handler.php?act=del&id="+id, true);
			xmlhttp.send();
		}

		function reply(id) {
			cur_id = id;
			showForm();
		}

		function showForm() {
			document.getElementById('reply').style.display = 'block';
		}

		function hideForm() {
			document.getElementById('reply').style.display = 'none';
			cur_id = 0;
		}

		</script>



	</body>
</html>
 	
