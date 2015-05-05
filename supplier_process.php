<?php
	include 'process.php';
	
	class Supplier extends Main{
	
		public function addSupplier($person_data)
		{
			$sup_name = $this->connection->real_escape_string($person_data['sup_name']);
			$nick_name = $this->connection->real_escape_string($person_data['nick_name']);
			$address = $this->connection->real_escape_string($person_data['sup_address']);
			$sup_no = $this->connection->real_escape_string($person_data['sup_no']);
			
			if(empty($sup_name) OR empty($sup_no) OR empty($nick_name))
				$data['sup_errors'][] = "Name, nickname and contact number must not be empty";
			if(!is_numeric($sup_no))
				$data['sup_errors'][] = "Contact Number must be a number";	
			if(strlen($sup_no<11) && strlen($sup_no>11))
				$data['sup_errors'][] = "Contact Number must be 11 digits";	
			if($data['sup_errors'] == NULL)
			{
				$insert_supplier = $this->connection->query("INSERT into supplier VALUES('','".$sup_name."','".$nick_name."','".$address."','".$sup_no."')");
				if($insert_supplier === TRUE){
					redirect($data,'view_supplier.php');
				}
			}
			else{
				redirect($data,'add_supplier.php');
			}
		}
		public function viewSupplier(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM supplier");
			while($display = $show->fetch_assoc()){
			$results[] = $display;
			}
			return $results;
		}
		public function editSupplier($supplier_info)
		{
			$sup_id= $this->connection->real_escape_string($supplier_info['sup_id']);
			$sup_name = $this->connection->real_escape_string($supplier_info['sup_name']);
			$nick_name = $this->connection->real_escape_string($supplier_info['nick_name']);
			$address = $this->connection->real_escape_string($supplier_info['sup_address']);
			$sup_no = $this->connection->real_escape_string($supplier_info['sup_no']);
		
			$update_supplier = $this->connection->query("UPDATE supplier SET sup_name = '".$sup_name."',
														nick_name = '".$nick_name."',
														sup_address = '".$address."',
														contact_no = '".$sup_no."'
														WHERE sup_id = '".$sup_id."' ");
			if($update_supplier === TRUE){
				redirect($data,'view_supplier.php');
			}
		}
		public function deleteSupplier($id){
			$delete_supplier = $this->connection->query("DELETE FROM supplier WHERE sup_id = '".$id."' ");
			if($delete_supplier === TRUE){
				redirect($data,'view_supplier.php');
			}
		}
		private $sup_id;
		
		public function setSupplier($id){
			$this->sup_id = $id;
		}
		public function currentSupplier(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM supplier WHERE sup_id = '".$this->sup_id."' ");
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
	//endof class
	}
?>