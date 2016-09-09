<?php
class Koneksi{
	public $db = "sekisui";
	
	public function connect(){
		return mysqli_connect('fppbaru.fujicon-japan.com', 'fujicon', 'fujicon12345*', $this->db);
	}
	
	public function connectPDO(){
		return $conn = new PDO("mysql:host=fppbaru.fujicon-japan.com; dbname=$this->db", 'fujicon', 'fujicon12345*');
	}
}
?>