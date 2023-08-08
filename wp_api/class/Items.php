<?php
class Items
{

	private $itemsTable = "wp_posts";
	public $id;
	public $post_type;
	public $post_title;
	public $post_modified;
	private $conn;

	public function __construct($db)
	{
		$this->conn = $db;
	}

	function read($page, $perpage, $id = 0)
	{

		if ($id) {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE ID = ?");
			$stmt->bind_param("i", $id);

			$stmt->execute();
			$result['dd'] = $stmt->get_result();
			$row_count = mysqli_num_rows($result['dd']);
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " LIMIT " . ($page - 1) * $perpage . "," . $perpage);

			$stmts = $this->conn->prepare("SELECT * FROM " . $this->itemsTable);
			$stmts->execute();
			$result['dd'] = $stmts->get_result();
			$row_count = mysqli_num_rows($result['dd']);
		}

		$stmt->execute();
		$result['d'] = $stmt->get_result();
		$result['row_count'] = $row_count;
		return $result;
	}



	function update()
	{

		$stmt = $this->conn->prepare("
			UPDATE " . $this->itemsTable . " 
			SET post_type= ?, post_title = ?, post_modified = ?
			WHERE ID = ?");

		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->post_type = htmlspecialchars(strip_tags($this->post_type));
		$this->post_title = htmlspecialchars(strip_tags($this->post_title));
		$this->post_modified = htmlspecialchars(strip_tags($this->post_modified));


		$stmt->bind_param("ssis", $this->post_type, $this->post_title, $this->post_modified, $this->id);

		if ($this->post_type == "page") {
			if ($stmt->execute()) {
				return true;
			}
		}else{
			return false;
		}


		return false;
	}

	 function getByid($id = 0){
		if ($id) {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE ID = ?");
			$stmt->bind_param("i", $id);

			$stmt->execute();
			$result['dd'] = $stmt->get_result();
			$row_count = mysqli_num_rows($result['dd']);
		} 

		$stmt->execute();
		$result['d'] = $stmt->get_result();
		$result['row_count'] = $row_count;
		return $result;
	}
}
