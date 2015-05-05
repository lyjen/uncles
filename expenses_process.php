<?php
	
	include 'process.php';
	
	class Expenses extends Main{
		
		public function viewStores(){
		
			$results = array();
			$stores = $this->connection->query("SELECT * FROM stores ORDER BY store_name ASC");
			while($show = $stores->fetch_assoc()){
				$results[] = $show;
			}
			return $results;

		}
		public function addStore($store_data)
		{
			$store_name = $this->connection->real_escape_string($store_data['store_name']);
			$store_address = $this->connection->real_escape_string($store_data['store_address']);
			$store_mail = $this->connection->real_escape_string($store_data['store_mail']);
			$store_contact = $this->connection->real_escape_string($store_data['store_contact']);
			$store_fax = $this->connection->real_escape_string($store_data['store_fax']);
			$store_tin = $this->connection->real_escape_string($store_data['store_tin']);
			
			if(empty($store_name))
				$data['add_errors'][] = "Please add store name";
			if(empty($store_address))
				$data['add_errors'][] = "Please add store address";
			if(empty($store_contact))
				$data['add_errors'][] = "Please add contact number";
			
			if(!is_numeric($store_contact))
				$data['add_errors'][] = "Contact Number must be a number";	

			if($data['add_errors'] == NULL)
			{
				$add_store = $this->connection->query("INSERT into stores VALUES('','".$store_name."','".$store_address."','".$store_contact."','".$store_mail."','".$store_fax."','".$store_tin."',NOW())");

				if($add_store === TRUE){
					//$quo_id = $this->connection->query("SELECT * FROM quotation ORDER BY quo_id DESC")->fetch_assoc();
					//$id = $quo_id['quo_id'];
					redirect($data,'view_store.php');
				}
			}
			else{
				redirect($data,'add_store.php');
			}
		}
		
		private $store_id;		
		public function setStore($store_id){
			$this->store_id = $store_id;
		}
		public function currentStore(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM stores where store_id = '".$this->store_id."' "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function expStore(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM gastos where store_id = '".$this->store_id."'"); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function expItems($store_id){
			$results = array();
			$show = $this->connection->query("SELECT * FROM gastos
											LEFT JOIN items ON gastos.exp_id = items.exp_id
											WHERE store_id='".$store_id."'"); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function totalCurrentStore($store_id){
				$results = array();
			$show = $this->connection->query("SELECT sum(total_amount) as total FROM gastos WHERE store_id='".$store_id."'"); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function updateStore($info)
		{
			$store_name = $this->connection->real_escape_string($info['store_name']);
			$store_address = $this->connection->real_escape_string($info['store_address']);
			$store_mail = $this->connection->real_escape_string($info['store_mail']);
			$store_contact = $this->connection->real_escape_string($info['store_contact']);
			$store_fax = $this->connection->real_escape_string($info['store_fax']);
			$store_tin = $this->connection->real_escape_string($info['store_tin']);
			$store_id = $this->connection->real_escape_string($info['store_id']);
	
			$update_store = $this->connection->query("UPDATE stores SET store_name = '".$store_name."',
														store_address = '".$store_address."',
														store_contact = '".$store_contact."',
														store_mail = '".$store_mail."',
														store_tin = '".$store_tin."',
														store_fax = '".$store_fax."'
														WHERE store_id = '".$store_id."' ");
			if($update_store === TRUE){
				redirect($data,'view_store.php?view='.$store_id);
			}
		}
		public function deleteStore($id){
			$delete_store = $this->connection->query("DELETE FROM stores where store_id = '".$id."' ");
			if($delete_store === TRUE){
				redirect($data,'view_store.php');
			}
		}
		public function viewExpenses(){
		
			$results = array();
			$header = $this->connection->query("SELECT * FROM gastos ORDER BY date_covered DESC");
			while($get = $header->fetch_assoc()){
				$results[] = $get;
			}
			return $results;

		}
		
		public function addExp($exp_data)
		{
			$store_id = $this->connection->real_escape_string($exp_data['store_id']);
			$store_name = $this->connection->real_escape_string($exp_data['store_name']);
			$store_address = $this->connection->real_escape_string($exp_data['store_address']);
			$store_tin = $this->connection->real_escape_string($exp_data['store_tin']);
			$date_cover = $this->connection->real_escape_string($exp_data['date_cover']);
			
			if(empty($store_name) AND empty($store_id))
				$data['exp_errors'][] = "Please add store name";
			if(empty($date_cover))
				$data['exp_errors'][] = "Please add date_covered";
		
			if($data['exp_errors'] == NULL)
			{
				$add_expenses = $this->connection->query("INSERT into gastos VALUES('','".$store_id."','".$store_name."','".$store_address."','".$store_tin."','".$date_cover."','',NOW()) ");
				//`exp_id`, `store_id`, `store_name`, `store_address`, `tin_no`, `exp_amount`, `date_covered`, `date_added`
				$exp = $this->connection->query("SELECT * FROM gastos ORDER BY exp_id DESC")->fetch_assoc();
				if($add_expenses === TRUE){
					redirect($data,'expense_items.php?id='.$exp['exp_id']);
				}
			}
			else{
				redirect($data,'add_expenses.php');
			}
		}
		private $head_id;		
		public function setHead($head_id){
			$this->head_id = $head_id;
		}
		public function currentHead(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM gastos where exp_id = '".$this->head_id."' "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function updateHead($up)
		{
			$exp_name = $this->connection->real_escape_string($up['exp_name']);
			$exp_address = $this->connection->real_escape_string($up['exp_address']);
			$exp_tin = $this->connection->real_escape_string($up['exp_tin']);
			$date_covered = $this->connection->real_escape_string($up['date_covered']);
			$exp_id = $this->connection->real_escape_string($up['exp_id']);
	
			$update_head = $this->connection->query("UPDATE gastos SET exp_name = '".$exp_name."',
														exp_address = '".$exp_address."',
														exp_tin = '".$exp_tin."',
														date_covered = '".$date_covered."'
														WHERE exp_id = '".$exp_id."' ");
			if($update_head === TRUE){
				redirect($data,'expense_items.php?id='.$exp_id);
			}
		}
		public function deleteExpense($id){
			$delete_exp = $this->connection->query("DELETE FROM gastos where exp_id = '".$id."' ");
			if($delete_exp === TRUE){
				$data['success_delete'][] = 'Successfully Deleted';
				redirect($data,'view_expenses.php');
			}
		}
		
		private $exp;
		public function setExp($exp){
			$this->exp = $exp;
		}
		public function viewItems(){
		
			$results = array();
			$main = $this->connection->query("SELECT * FROM items WHERE exp_id = '".$this->exp."' ");
			while($get = $main->fetch_array()){
				$results[] = $get;
			}
			return $results;

		}
		public function addItems($input)
		{
			$qty = $this->connection->real_escape_string($input['qty']);
			$item_desc = $this->connection->real_escape_string($input['item_desc']);
			$item_amount = $this->connection->real_escape_string($input['item_amount']);
			$exp_id = $this->connection->real_escape_string($input['exp_id']);
			$total = $qty * $item_amount;
			
			if(empty($qty))
				$data['item_errors'][] = "Please add quantity";
			if(empty($item_desc))
				$data['item_errors'][] = "Please add about item";
			if(empty($item_amount))
				$data['item_errors'][] = "Please add amount";
		
			if($data['item_errors'] == NULL)
			{
				$add_item = $this->connection->query("INSERT into items VALUES('','".$exp_id."','".$qty."','".$item_desc."','".$item_amount."','".$total."') ");
				//`item_id`, `exp_id`, `qty`, `item_desc`, `amount`, `total`
				
				if($add_item === TRUE){
					$for_total = $this->connection->query("SELECT SUM(total) from items where exp_id ='".$exp_id."' ")->fetch_assoc();
					$update_total = $this->connection->query("UPDATE gastos SET total_amount='".$for_total['SUM(total)']."' WHERE exp_id='".$exp_id."'");
					redirect($data,'expense_items.php?id='.$exp_id);
				}
			}
			else{
				redirect($data,'expense_items.php?id='.$exp_id);
			}
		}
		private $item_id;		
		public function setItem($item_id){
			$this->item_id = $item_id;
		}
		public function currentItem(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM items where item_id = '".$this->item_id."' "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		
		public function updateItem($update)
		{
			$qty = $this->connection->real_escape_string($update['qty']);
			$item_desc = $this->connection->real_escape_string($update['item_desc']);
			$item_amount = $this->connection->real_escape_string($update['item_amount']);
			$item_id = $this->connection->real_escape_string($update['item_id']);
			$exp_id = $this->connection->real_escape_string($update['exp_id']);
			$total = $qty * $item_amount;
	
			$update_item = $this->connection->query("UPDATE items SET qty = '".$qty."',
														item_desc = '".$item_desc."',
														amount = '".$item_amount."',
														total = '".$total."'
														WHERE item_id = '".$item_id."' ");
			if($update_item === TRUE){
				$for_total = $this->connection->query("SELECT SUM(total) from items where exp_id ='".$exp_id."' ")->fetch_assoc();
				$update_total = $this->connection->query("UPDATE gastos SET total_amount='".$for_total['SUM(total)']."' WHERE exp_id='".$exp_id."'");
				redirect($data,'expense_items.php?id='.$exp_id);
			}
		}
		
		public function deleteItem($id,$xid){
			$delete_exp = $this->connection->query("DELETE FROM items where item_id = '".$id."' ");
			
			
			if($delete_exp === TRUE){
				$for_total = $this->connection->query("SELECT SUM(total) from items where exp_id ='".$xid."' ")->fetch_assoc();
				$update_total = $this->connection->query("UPDATE gastos SET total_amount='".$for_total['SUM(total)']."' WHERE exp_id='".$xid."'");
				if($update_total === TRUE)
				redirect($data,'expense_items.php?id='.$xid);
			}
		}
		
		private $exp_id;
		
		public function setExpense($exp_id){
			$this->exp_id = $exp_id;
		}
		
		public function currentExpense(){
		
			$results = array();
			$main = $this->connection->query("SELECT * FROM gastos WHERE exp_id = '".$this->exp_id."' ");
			while($get = $main->fetch_assoc()){
				$results[] = $get;
			}
			return $results;

		}
		public function searchExpense($query){
			$result = array();
			$this->query = $query;
			$show = $this->connection->query("SELECT * FROM items					
												WHERE  item_desc LIKE'%".$this->query."%' ");
			while($display = $show->fetch_assoc()){
				$result[] = $display;
			}
			return $result;
		}
		
		private $date_from;
		private $date_to;
		
		public function selectExpenses($from,$to){
		$this->date_from = $from;
		$this->date_to = $to;
		
		$results = array();
		$selected = $this->connection->query("SELECT * FROM `gastos` WHERE `date_covered` BETWEEN '".$this->date_from."' AND '".$this->date_to."' ORDER BY date_covered");
		while($get = $selected->fetch_assoc()){
			$results[] = $get;
		}
		return $results;

		}
		public function selectTotal(){
			$total = array();
			$selection = $this->connection->query("SELECT SUM(total_amount) as Total FROM gastos WHERE `date_covered` BETWEEN '".$this->date_from."' AND '".$this->date_to."'")->fetch_assoc();
			$total[] = $selection; 
			return $total;
		}
	//endof class
	}
?>