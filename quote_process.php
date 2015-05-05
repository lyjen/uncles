<?php
	include 'process.php';
	
	class Quote extends Main{
	
		public function addHeader($header_data)
		{
			$client = $this->connection->real_escape_string($header_data['client']);
			$supplier = $this->connection->real_escape_string($header_data['supplier']);
			$attention = $this->connection->real_escape_string($header_data['attention']);
			$terms = $this->connection->real_escape_string($header_data['terms']);
	
			
			if(empty($client))
				$data['header_errors'][] = "Please select client";
			if(empty($supplier))
				$data['header_errors'][] = "Please select supplier";
			if(empty($attention))
				$data['header_errors'][] = "Attention must not be empty";
			if(empty($terms))
				$data['header_errors'][] = "Terms must not be empty";
			if($data['header_errors'] == NULL)
			{
				$insert_header = $this->connection->query("INSERT into quotation VALUES('','".$client."','".$supplier."','".$attention."','".$terms."',NOW())");
				
				if($insert_header === TRUE){
					//$quo_id = $this->connection->query("SELECT * FROM quotation ORDER BY quo_id DESC")->fetch_assoc();
					//$id = $quo_id['quo_id'];
					redirect($data,'quote_items.php');
				}
			}
			else{
				redirect($data,'add_quotation.php');
			}
		}
		public function viewHeader(){
		
			$results = array();
			$header = $this->connection->query("SELECT * FROM quotation ORDER BY quo_id DESC")->fetch_assoc();
			$results[] = $header;
			return $results;
		
		
		}
		public function quoteItem($item)
		{
			$quo_id = $this->connection->real_escape_string($item['quo_id']);
			$qty = $this->connection->real_escape_string($item['qty']);
			$unit = $this->connection->real_escape_string($item['unit']);
			$sell_desc = $this->connection->real_escape_string($item['quote_desc']);
			$price = $this->connection->real_escape_string($item['item_price']);
			$materials = $this->connection->real_escape_string($item['materials']);
			$total_amount = $qty * $price;
			
			if(empty($qty) OR empty($unit) OR empty($sell_desc) OR empty($price) )
				$data['item_errors'][] = "Fields must not be empty";
	
			if($data['item_errors'] == NULL)
			{
				$insert_item = $this->connection->query("INSERT into sell VALUES('','".$quo_id."','".$qty."','".$unit."','".$sell_desc."','".$price."','".$materials."','".$total_amount."',NOW())");
				if($insert_item === TRUE){
					redirect($data,'quote_items.php');
				}
			}
			else{
				redirect($data,'quote_items.php');
			}
		}
		public function viewQuoted(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM quotation");
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
		public function quotedFiles($client){
			$results = array();
			$display = $this->connection->query("SELECT * FROM quotation WHERE client_id='".$client['id']."' ORDER BY quo_id DESC");
			while($quote = $display->fetch_assoc()){
				$results[] =$quote;
			}
			return $results;
		}
		public function viewFile($quo){
			$results = array();
			$display = $this->connection->query("SELECT * FROM quotation WHERE quo_id='".$quo['id']."' ORDER BY quo_id DESC");
			while($get = $display->fetch_assoc()){
				$results[] =$get;			}
			return $results;
		}
	//endof class
	}
?>