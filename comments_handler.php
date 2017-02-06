<?php 

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	require_once('db.php');

	class Comments_handler {

		private $db;

		public function __construct($db) {
			$this->db = $db;
		}

		public function getComments() {
			$result = $this->getDBComments();
			$result = $this->buildTree($result);

			return $result;
		}

		//Функция для формирования иерархического дерева
		private function buildTree($data, $get_id = 0) {
		    
		    $tree = array();
		    $output_node = null;
		    foreach($data as $id => &$row) {
		        if (empty($row['parent_id'])) {
		            $tree[$id] = &$row;
		        } else{
		            $data[$row['parent_id']]['childs'][$id] = &$row;
		        }

		        if (($get_id != 0) && ($id == $get_id)) {
		        	$output_node = &$row;
		        } 
		    }
		    unset($row);

		  	if ($get_id != 0) return $output_node;

		    return $tree;
		}

 		//Формирует комментарии в html   
 		public function getCommentsTemplate($comments, $node_num = 0) {
 			$html = "<ul>";
 			foreach ($comments as $comment) {
	 			$html .= 
	 				"<li id='{$comment['id']}'>
					    <div class='comment'>
					        <div class='name'>
					            {$comment['name']}
					            <span class='date'>".date('d.m.Y H:i',$comment['date'])."</span>            
					       	</div>  
					       <div class='text'>
					       		{$comment['text']}
					       	</div>
					       	<a class='delete' onclick='del({$comment['id']});'>Удалить</a>";
			if ($node_num < 5) {
				$html .= "<a class='reply' onclick='reply({$comment['id']});'>Ответить</a>";
			}
							
			$html .= "</div>";
				if (!empty($comment['childs'])) {
					$html .= $this->getCommentsTemplate($comment['childs'], $node_num+1);
				}
				$html .= "</li>";

 			}
 			$html .= "</ul>";

 			return $html;
 		}

 		public function sendComment($id, $comment, $name) {
 			$this->db->query("INSERT INTO `comments` VALUES (?n, '?i', '?s', '?s', '?i')", null, $id, $comment, $name, time());
 		}

 		public function deleteComment($id) {
			$comments = $this->getDBComments();
			$comments = $this->buildTree($comments, $id);
			$ids = $this->getCommentsIds($comments);
			$this->db->query("DELETE FROM `comments` WHERE `id` IN (?ai)", $ids);
 			
 		}

 		public function getCommentsIds($comments) {
 			$result = array($comments['id']);
			if (array_key_exists('childs', $comments)) {
				foreach ($comments['childs'] as $id => $child) {
					$ids = $this->getCommentsIds($child);
					$result = array_merge($result, $ids);
				}
			} 

 			return $result;
 		}

 		private function getDBComments() {
			$result = array();

			$res = $this->db->query("SELECT * FROM comments");
			while ($data = $res->fetch_assoc()) {
			   	$result[$data['id']] = $data;
			}

			return $result;
		}

	}

	$Comments_handler = new Comments_handler($db);

	if (isset($_GET['act'])) {
		if ($_GET['act'] == "send") { 
			$id = $_GET['id'];
			$name = $_GET['name'];
			$text = $_GET['text'];
			$Comments_handler->sendComment($id, $text, $name);
		}
		if ($_GET['act'] == "del") { 
			$id = $_GET['id'];
			$Comments_handler->deleteComment($id);
			echo "1";
		}
	} 
	