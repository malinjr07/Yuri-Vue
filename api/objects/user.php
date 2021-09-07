<?php
	class User {
		
		// database connection and table name
		private $conn;
		
		// object properties
		public $ID;
		public $username;
        public $firstName;
        public $lastName;
		public $nickname;
		
		
		// constructor with $db as database connection
		public function __construct($db) {
			$this->conn = $db;
		}
		
		
		public function read($activeUsersOnly = true) {
			
			// select all query
			$query = '';
			$query .= "SELECT * FROM users";
            if($activeUsersOnly) {
                $query .= " WHERE active='Y'";
            }
            
			$query .= " ORDER BY users.nickname ASC";
			//$query .= " LIMIT 1000, 500";
			
			// prepare query statement
			$stmt = $this->conn->prepare($query);
			
			// execute query
			$stmt->execute();
			
			return $stmt;
		}
		
		public function update() {
			
			return false;
		}
		
	}
?>