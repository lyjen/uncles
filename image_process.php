<?php
	
	class Purchase extends Main{
		define ("MAX_SIZE","1000"); 

		function getExtension($str) {
			 $i = strrpos($str,".");
			 if (!$i) { return ""; }
			 $l = strlen($str) - $i;
			 $ext = substr($str,$i+1,$l);
			 return $ext;
		}
		
		function image_filter(){
			$image=$_FILES['image']['name'];
			if ($image) 
			{	
				$filename = stripslashes($_FILES['image']['name']);
				$extension = getExtension($filename);
				$extension = strtolower($extension);
				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
				{
					$data['unsuccess'][] = "Unknown extension!";			
				}
				else
				{
					$size=filesize($_FILES['image']['tmp_name']);
					if ($size > MAX_SIZE*1024)
					{
						$data['unsuccess'][] = "You have exceeded the size limit!";
					}
					
					$image_name=time().'.'.$extension;
					$newname="../img/PO/".$image_name;
					$copied = copy($_FILES['image']['tmp_name'], $newname);
					if (!$copied) 
					{
						$data['unsuccess'][] = "Copy unsuccessfull!";
					}
				}
			}	
		}

?>