<?php

	//include 'process.php';

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