<?php
class Task
{

	// database connection and table name
	private $conn;

	// object properties
	public $ID;
	public $title;
	public $description;


	// constructor with $db as database connection
	public function __construct($db)
	{
		$this->conn = $db;
	}


	public function read($assignedUserId = false)
	{

		// select all query
		$query = '';


		$query .= "SELECT c.company_name,p.project_name, u1.nickname as assigned_user_nickname,u2.nickname as creator_user_nickname, COUNT(n.ID) as notes_count, t.* FROM tasks t LEFT JOIN users u1 ON u1.ID=t.assigned_user_id LEFT JOIN users u2 ON u2.ID=t.creator_user_id INNER JOIN projects p ON t.related_table_id=p.ID INNER JOIN companies c ON c.ID=p.company_id LEFT JOIN notes n ON t.ID=n.related_table_id AND n.related_table='tasks' WHERE t.related_table='projects' AND t.status = 'not_started'";
		$query .= " AND t.assigned_user_id=" . $assignedUserId;
		$query .= " GROUP BY t.ID";
		$query .= " ORDER BY t.due_date ASC";
		//$query .= " LIMIT 1000, 500";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	public function update()
	{

		// update query
		//$query = "UPDATE tasks SET title=:title,description=:description WHERE ID=:ID";
		$query = "UPDATE tasks SET title=:title, description=:description WHERE ID=:ID";

		$stmt = $this->conn->prepare($query);

		// sanitise
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->description = htmlspecialchars($this->description);

		// bind new values
		$stmt->bindParam(':ID', $this->ID);
		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':description', $this->description);

		// execute the query
		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	public function update_dates()
	{
		$this->dueDate = htmlspecialchars($this->dueDate);

		$query = "UPDATE tasks SET due_date=:dueDate WHERE ID=:ID";

		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(':ID', $this->ID);
		$stmt->bindParam(':dueDate', $this->dueDate);

		if ($stmt->execute())
			return true;
		return false;
	}
}
