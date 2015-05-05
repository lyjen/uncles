<?php
	include 'process.php';
	
	class Client extends Main{
	
		public function addClient($client_data)
		{
			$client_name = $this->connection->real_escape_string($client_data['client_name']);
			$client_address = $this->connection->real_escape_string($client_data['client_address']);
			$client_mail = $this->connection->real_escape_string($client_data['client_mail']);
			$client_tel = $this->connection->real_escape_string($client_data['client_tel']);
			$fax = $this->connection->real_escape_string($client_data['fax']);
			$tin = $this->connection->real_escape_string($client_data['tin']);
			
			if(empty($client_name) OR empty($client_address) OR empty($client_mail) OR empty($client_tel))
				$data['client_errors'][] = "Client name, address,email and contact number must not be empty";
			if(filter_var($client_mail,FILTER_VALIDATE_EMAIL)=== FALSE)
				$data['client_errors'][] = "Invalid email";
			if(!is_numeric($client_tel))
				$data['client_errors'][] = "Contact Number, Fax and TIN must be a number and no space";	
			if($data['client_errors'] == NULL)
			{
				$insert_client = $this->connection->query("INSERT into client VALUES('','".$client_name."','".$client_address."','".$client_mail."','".$client_tel."','".$tin."','".$fax."',NOW())");
				if($insert_client === TRUE){
					redirect($data,'view_client.php');
				}
			}
			else{
				redirect($data,'add_client.php');
			}
		}
		public function viewClient(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM client");
			while($display = $show->fetch_assoc()){
			$results[] = $display;
			}
			return $results;
		}
		public function editClient($client_info)
		{
			$client_id= $this->connection->real_escape_string($client_info['client_id']);
			$client_name = $this->connection->real_escape_string($client_info['client_name']);
			$client_address = $this->connection->real_escape_string($client_info['client_address']);
			$client_mail = $this->connection->real_escape_string($client_info['client_mail']);
			$client_tel = $this->connection->real_escape_string($client_info['client_tel']);
			$fax = $this->connection->real_escape_string($client_info['fax']);
			$tin = $this->connection->real_escape_string($client_info['tin']);
			$date_added = $this->connection->real_escape_string($client_info['date_added']);
		
			$update_client = $this->connection->query("UPDATE client SET client_name = '".$client_name."',
														client_address = '".$client_address."',
														client_email = '".$client_mail."',
														contact_no = '".$client_tel."',
														fax = '".$fax."',
														tin = '".$tin."',
														date_added = '".$date_added."' 
														WHERE client_id = '".$client_id."' ");
			if($update_client === TRUE){
				redirect($data,'view_client.php');
			}
		}
		public function deleteClient($id){
			$delete_client = $this->connection->query("DELETE FROM client where client_id = '".$id."' ");
			if($delete_client === TRUE){
				redirect($data,'view_client.php');
			}
		}
		//getting purchase order by client
		
		
	//endof class
	}
	class Purchase extends Main{
		private $client_id;		
		public function setClient($client_id){
			$this->client_id = $client_id;
		}
		
		public function viewOrder(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM purchase where client_id = '".$this->client_id."' "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		
		public function addPO($data)
		{
			$po_num = $this->connection->real_escape_string($data['po_num']);
			$po_desc = $this->connection->real_escape_string($data['po_desc']);
			$po_amount = $this->connection->real_escape_string($data['po_amount']);
			$po_terms = $this->connection->real_escape_string($data['po_terms']);
			$quo_id = $this->connection->real_escape_string($data['quo_id']);
			
			
			if(empty($po_num))
				$data['purchase_errors'][] = "Please don't forget P.O.";
			if(empty($po_desc))
				$data['purchase_errors'][] = "Please add Description";
			if(empty($po_amount))
				$data['purchase_errors'][] = "Please don't forget to add amount";
			if(empty($po_terms))
				$data['purchase_errors'][] = "Please add working terms by supplier";
			
		
			$quote = $this->connection->query("SELECT * FROM quotation WHERE quo_id = '".$quo_id."'")->fetch_assoc();
			$client = $quote['client_id'];

			if($data['purchase_errors'] == NULL)
			{
				$attach_po = $this->connection->query("INSERT into purchase VALUES('','".$po_num."','".$quo_id."','".$client."','".$po_desc."','".$po_amount."','".$po_terms."','',NOW())");
				$attached = $this->connection->query("SELECT * FROM purchase ORDER BY po_id DESC")->fetch_assoc();
				if($attach_po === TRUE){
					//$quo_id = $this->connection->query("SELECT * FROM quotation ORDER BY quo_id DESC")->fetch_assoc();
					//$id = $quo_id['quo_id'];
					redirect($data,'attach_po.php?id='.$attached['po_id']);
				}
			}
			else{
				redirect($data,'add_po.php?id='.$quo_id);
			}
			
		}
		
	}
?>