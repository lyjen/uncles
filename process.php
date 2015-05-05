<?php
	
	$data = NULL;
	
	function redirect($session_data, $url)
	{
		$_SESSION = $session_data;
		header('location:'.$url);
	}
	class Main{
		
		public $connection;
		
		public function __construct(){
			$this->connection = new mysqli('localhost','root','','uncles');
		}
		public function admin($admin_data)
		{
			$email = $this->connection->real_escape_string($admin_data['email']);
			$username = $this->connection->real_escape_string($admin_data['username']);
			$password = $this->connection->real_escape_string($admin_data['password']);
			$re_pass = $this->connection->real_escape_string($admin_data['re_pass']);
			
			if(empty($email))
				$data['errors'][] = "Email must not be empty";
			if(filter_var($email,FILTER_VALIDATE_EMAIL) === FALSE)
				$data['errors'][] = "Invalid email";
			if($password != $re_pass OR $password == NULL)
				$data['errors'][] = "Password do not match";
				
			if($data['errors'] == NULL){	
				$insert_user = $this->connection->query("INSERT into admin VALUES ('','".$username."','".md5($password)."','".$email."',NOW())");
				if($insert_user === TRUE)
				{
				$data['added'] = $email;
				redirect($data, 'signin.php');
				}
			}
			else
			redirect($data, 'register.php');
		}
		public function login($log_data)
		{
			$username = $this->connection->real_escape_string($log_data['username']);
			$password = $this->connection->real_escape_string($log_data['password']);
			
			if(empty($username))
				$data['log_error'][] ="Username is incorrect.";
			if(empty($password))
				$data['log_error'][] ="Password is incorrect.";	
			if($data['log_error'] == NULL){
				$check_user = $this->connection->query("SELECT * FROM admin WHERE admin.username = '".$username."' AND admin.password = '".md5($password)."' ")->fetch_assoc();
		
				if($check_user != NULL)
				{
					$_SESSION['admin_id'] = $check_user['admin_id'];
					$_SESSION['username'] = $check_user['username'];
					$_SESSION['login'] = TRUE;	
					if($_SESSION['login'] == TRUE)
					//setcookie("admin_id", $_SESSION['admin_id'], time()+7200);
						header('location: transact.php');
				}
			}
			else{
				redirect($data,'signin.php');
			}
		}
		//end of class
	}
	//CLIENT
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
			$show = $this->connection->query("SELECT * FROM client ORDER BY client_name ASC");
			while($display = $show->fetch_assoc()){
			$results[] = $display;
			}
			return $results;
		}
		private $client_id;		
		public function setClient($client_id){
			$this->client_id = $client_id;
		}
		public function currentClient(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM client where client_id = '".$this->client_id."' "); 
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
	//PURCHASE ORDER
	class Purchase extends Main{
	
		private $client_id;		
		private $po_id;		
		public function setClient($client_id){
			$this->client_id = $client_id;
		}
		public function viewOrder(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM purchase where client_id = '".$this->client_id."' ORDER BY po_id DESC "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function setPO($po_id){
			$this->po_id = $po_id;
		}
		public function viewPO(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM purchase where po_id = '".$this->po_id."' "); 
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
		public function editPO($info)
		{
			$po_num = $this->connection->real_escape_string($info['po_num']);
			$po_desc = $this->connection->real_escape_string($info['po_desc']);
			$po_amount = $this->connection->real_escape_string($info['po_amount']);
			$po_terms = $this->connection->real_escape_string($info['po_terms']);
			$client_id = $this->connection->real_escape_string($info['client_id']);
			$po_id = $this->connection->real_escape_string($info['po_id']);

			if(empty($po_num) || empty($po_desc) || empty($po_amount) || empty($po_terms)){
				$data['purchase_edit_errors'][] = "Field must not be empty!";
			}
			if($data['purchase_edit_errors'] == NULL){
				$update_po = $this->connection->query("UPDATE purchase SET po_number = '".$po_num."',
															po_desc = '".$po_desc."',
															amount = '".$po_amount."',
															terms = '".$po_terms."'
															WHERE po_id = '".$po_id."' ");
				if($update_po === TRUE){
					redirect($data,'view_po_file.php?id='.$po_id.'&client='.$client_id.'&succesfully_updated');
				}
			}
			else{
				redirect($data,'edit_po.php?id='.$po_id);
			}
		}
		public function addOrder($data)
		{
			$po_num = $this->connection->real_escape_string($data['po_num']);
			$po_desc = $this->connection->real_escape_string($data['po_desc']);
			$po_amount = $this->connection->real_escape_string($data['po_amount']);
			$po_terms = $this->connection->real_escape_string($data['po_terms']);
			$po_client = $this->connection->real_escape_string($data['po_client']);
			
			if(empty($po_client))
				$data['po_errors'][] = "Don't forget Client Name";
			if(empty($po_num))
				$data['po_errors'][] = "Please don't forget P.O.";
			if(empty($po_desc))
				$data['po_errors'][] = "Please add Description";
			if(empty($po_amount))
				$data['po_errors'][] = "Please don't forget to add amount";
			if(empty($po_terms))
				$data['po_errors'][] = "Please add working terms by supplier";
			

			if($data['po_errors'] == NULL)
			{
				$attach_po = $this->connection->query("INSERT into purchase VALUES('','".$po_num."','','".$po_client."','".$po_desc."','".$po_amount."','".$po_terms."','',NOW())");
				$attached = $this->connection->query("SELECT * FROM purchase ORDER BY po_id DESC")->fetch_assoc();
				if($attach_po === TRUE){
					//$quo_id = $this->connection->query("SELECT * FROM quotation ORDER BY quo_id DESC")->fetch_assoc();
					//$id = $quo_id['quo_id'];
					redirect($data,'attach_po.php?id='.$attached['po_id']);
				}
			}
			else{
				redirect($data,'add_order.php');
			}	
		}
		public function deletePO($id){
			$delete_po = $this->connection->query("DELETE FROM purchase where po_id = '".$id."' ");

			if($delete_po === TRUE){
				redirect($data,'view_purchase_order.php');
			}
		}
		public function latestPO(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM purchase ORDER BY date_added DESC LIMIT 0,5 "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
	}
		//end of Purchase
		
	class Sales extends Main{
	
		public function viewSales(){
			
			$results = array();
			$sales = $this->connection->query("SELECT * FROM sales ORDER BY sales_number DESC");
			while($show = $sales->fetch_assoc()){
				$results[] = $show;
		
			}
			return $results;
		}
		
		public function addSales($sales){
			$po_id = $this->connection->real_escape_string($sales['po_id']);
			$client_id = $this->connection->real_escape_string($sales['client_id']);
			$sales_number = $this->connection->real_escape_string($sales['sales_number']);
			$sales_person = $this->connection->real_escape_string($sales['sales_person']);
			$sales_date = $this->connection->real_escape_string($sales['sales_date']);
			$sales_amount = $this->connection->real_escape_string($sales['sales_amount']);
			$stat = 0;
			
			if(empty($sales_number))
				$data['sales_errors'][] = "Please don't forget S.I. Number.";
			if(empty($sales_person))
				$data['sales_errors'][] = "Please don't forget Sales Person.";
			if(empty($sales_date))
				$data['sales_errors'][] = "Please add Description";
			if(empty($sales_amount))
				$data['sales_errors'][] = "Please don't forget to add amount";
				

			if($data['sales_errors'] == NULL)
			{
				$attach_sales = $this->connection->query("INSERT into sales VALUES('','".$stat."','".$sales_number."','".$po_id."','".$sales_person."','".$sales_date."','".$sales_amount."','',NOW())");
				$attached = $this->connection->query("SELECT * FROM sales ORDER BY sales_id DESC")->fetch_assoc();
				if($attach_sales === TRUE){
					//$quo_id = $this->connection->query("SELECT * FROM quotation ORDER BY quo_id DESC")->fetch_assoc();
					//$id = $quo_id['quo_id'];
					redirect($data,'attach_sales.php?id='.$attached['sales_id'].'&client='.$client_id.'&attached_now');
				}
			}
			else{
				redirect($data,'add_sales.php?id='.$po_id);
			}	
		}
		private $si_id;
		public function setSI($si_id){
			$this->si_id = $si_id;
		}
		public function viewSaleInvoice(){
			$results = array();
			$show = $this->connection->query("SELECT * FROM sales where sales_id = '".$this->si_id."' "); 
			while($display = $show->fetch_assoc()){
				$results[] = $display;
			}
			return $results;
		}
		public function editSales($data)
		{
			$si_id = $this->connection->real_escape_string($data['si_id']);
			$client_id = $this->connection->real_escape_string($data['client_id']);
			$sales_number = $this->connection->real_escape_string($data['sales_number']);
			$sales_person = $this->connection->real_escape_string($data['sales_person']);
			$sales_date = $this->connection->real_escape_string($data['sales_date']);
			$sales_amount = $this->connection->real_escape_string($data['sales_amount']);
			$status = $this->connection->real_escape_string($data['status']);

			if(empty($sales_number))
				$data['sales_errors'][] = "Please don't forget S.I. Number.";
			if(empty($sales_person))
				$data['sales_errors'][] = "Please don't forget Sales Person.";
			if(empty($sales_date))
				$data['sales_errors'][] = "Please add Description";
			if(empty($sales_amount))
				$data['sales_errors'][] = "Please don't forget to add amount";
			if($data['sales_edit_errors'] == NULL){
				$update_po = $this->connection->query("UPDATE sales SET sales_number = '".$sales_number."',
															status = '".$status."',
															sup_id = '".$sales_person."',
															sales_date = '".$sales_date."',
															sales_collected = '".$sales_amount."'
															WHERE sales_id = '".$si_id."' ");
				if($update_po === TRUE){
					redirect($data,'view_sales_file.php?id='.$si_id.'&client='.$client_id.'&succesfully_updated');
				}
			}
			else{
				redirect($data,'edit_sales.php?id='.$si_id);
			}
		}
		public function deleteSales($id){
			$delete_po = $this->connection->query("DELETE FROM sales where sales_id = '".$id."' ");

			if($delete_po === TRUE){
				redirect($data,'view_sales.php');
			}
		}
		private $date_from;
		private $date_to;
		
		public function selectSales($from,$to){
		$this->date_from = $from;
		$this->date_to = $to;
		
		$results = array();
		$selected = $this->connection->query("SELECT * FROM `sales` WHERE `sales_date` BETWEEN '".$this->date_from."' AND '".$this->date_to."' ORDER BY sales_date");
		while($get = $selected->fetch_assoc()){
			$results[] = $get;
		}
		return $results;

		}
		public function selectTotal(){
			$total = array();
			$selection = $this->connection->query("SELECT SUM(sales_collected) as Total FROM sales WHERE `sales_date` BETWEEN '".$this->date_from."' AND '".$this->date_to."'")->fetch_assoc();
			$total[] = $selection; 
			return $total;
		}
	}
	class Search extends Main{
		private $query;
		public function searchClient($value){
			$result = array();
			$this->query = $value;
			$show = $this->connection->query("SELECT * FROM client WHERE (`client_name` LIKE '%".$this->query."%') OR (`client_address` LIKE '%".$this->query."%')");
			while($display = $show->fetch_array()){
				$result[] = $display;
			}
			return $result;
		}
		public function searchPerson($val){
			$result = array();
			$this->query = $val;
			$show = $this->connection->query("SELECT * FROM supplier WHERE (`sup_name` LIKE '%".$this->query."%') OR (`sup_address` LIKE '%".$this->query."%') OR (`nick_name` LIKE '%".$this->query."%')");
			while($display = $show->fetch_array()){
				$result[] = $display;
			}
			return $result;
		}
		public function searchPO($query){
			$result = array();
			$this->query = $query;
			$show = $this->connection->query("SELECT * FROM purchase WHERE (`po_number` LIKE '%".$this->query."%') OR (`po_desc` LIKE '%".$this->query."%') OR (`amount` LIKE '%".$this->query."%') ORDER BY po_id DESC");
			while($display = $show->fetch_array()){
				$result[] = $display;
			}
			return $result;
		}
		public function searchSales($query){
			$result = array();
			$this->query = $query;
			$show = $this->connection->query("SELECT sales.*,purchase.* from purchase
											LEFT JOIN sales ON purchase.po_id = sales.po_id
											WHERE  (`po_number` LIKE '%".$this->query."%') OR (`po_desc` LIKE '%".$this->query."%') OR (`amount` LIKE '%".$this->query."%') 
											OR (`sales_number` LIKE '%".$this->query."%')
											GROUP BY purchase.po_id DESC");
			while($display = $show->fetch_assoc()){
				$result[] = $display;
			}
			return $result;
		}
		
	}//sales.sales_id, sales.sales_collected,purchase.amount,purchase.po_id, purchase.po_desc,purchase.po_number,client.client_id,client.client_name FROM sales
?>