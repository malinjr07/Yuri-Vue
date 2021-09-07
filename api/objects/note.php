<?php
	class Note {
		
		// database connection and table name
		private $conn;
		
		// object properties
		public $ID;
        public $relatedTable;
        public $relatedTableId;
		public $noteText;
        public $noteAdditionalText;
		public $noteDateTime;
        public $creatorUserId;
		
		
		// constructor with $db as database connection
		public function __construct($db) {
			$this->conn = $db;
		}
		
		
		public function read($relatedTable, $relatedTableId) {
			
			// select all query
			$query = '';
			
			
			$query .= "SELECT n.*,u.nickname as creator_user_nickname FROM notes n INNER JOIN users u ON n.creator_user_id=u.ID WHERE related_table=:relatedTable AND related_table_id=:relatedTableId ORDER BY n.note_datetime DESC";
			
			// prepare query statement
			$stmt = $this->conn->prepare($query);

            // bind new values
			$stmt->bindParam(':relatedTable', $relatedTable);
            $stmt->bindParam(':relatedTableId', $relatedTableId);
			
			// execute query
			$stmt->execute();
			
			return $stmt;
		}
		
		public function create() {
			
			// update query
			//$query = "UPDATE tasks SET title=:title,description=:description WHERE ID=:ID";
			$query = "INSERT INTO notes SET related_table=:relatedTable, related_table_id=:relatedTableId, note_text=:noteText, note_additional_text=:noteAdditionalText, creator_user_id=:creatorUserId, note_datetime=NOW()";
			
			$stmt = $this->conn->prepare($query);
			
			// sanitise
			//$this->title = htmlspecialchars(strip_tags($this->title));
			
			// bind new values
			$stmt->bindParam(':relatedTable', $this->relatedTable);
			$stmt->bindParam(':relatedTableId', $this->relatedTableId);
			$stmt->bindParam(':noteText', $this->noteText);
			$stmt->bindParam(':noteAdditionalText', $this->noteAdditionalText);
			$stmt->bindParam(':creatorUserId', $this->creatorUserId);
			
			// execute the query
			if($stmt->execute()) {
				return true;
			}
			
			return false;
		}
		
	}
?>