<?php

$column0 = 'nama_tahun_akademik';
$column1 = 'kd_semester';
$column2 = 'nama_semester';
$column3 = 'uuid_periode';
$column4 = 'sort';
$column5 = 'periode_akademik';

$name_database = 'classroo_uii_gateway';
$name_table = 'graduation_dropdown_academic_period';
$urlx = 'https://cloud-api.uii.ac.id';
$api = 'v1/graduation/dropdown/academic-period';

$content_controller = "<?php
public function get_data()
{
	$"."urlx = '".$urlx."';

    $"."token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJzdmMtbG9naW4tdWlpIiwic3ViIjoiMDcxMDAyMjM5LWQ1ZWQyOTI1OWI0YjliNGNmNTdjMjRjNmY1ODhjZGQxIiwiaWF0IjoxNjc3NDYwODk5LCJleHAiOjE2Nzc3NjMyOTl9.4DZWkJ6p-BmidtgW6M8pdDDxyx8Nm3rmhW4E31BqAHI';
	//$"."token = $"."this->session->userdata('token');

	//$"."urlx =  $"."this->session->userdata('api_url');
	//$"."urlx =  $"."this->session->userdata('api_url');
	
	$"."url = $"."urlx.'/".$api."';
 
	$"."x_app = '0f243731-6883-11e8-bf86-005056806fe5'; 
	$"."x_menu  ='137dc466-3b09-11ed-ba02-000c29b46a1e';

		$"."ch = curl_init($"."url);
		curl_setopt($"."ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($"."ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Bearer ' . $"."token,
			'x-app: ' .$"."x_app,
			'x-menu: ' .$"."x_menu,	
		));
		
		$"."result_profil = curl_exec($"."ch);	
		
		///json hasil
		//echo $"."result_profil;
		 
		
		$"."coba = json_decode($"."result_profil);
		$"."data_sync = json_encode($"."coba->data,true);
		$"."array = json_decode($"."data_sync, true);
		curl_close($"."ch);

			$"."data ='';
			$"."array = json_decode($"."data_sync, true);

		foreach($"."array as $"."row) //Extract the Array Values by using Foreach Loop
		{
			$"."data = array (
			    
			    
			    	'".$column0."' => $"."row['".$column0."'],	
                	'".$column1."' => $"."row['".$column1."'],
                	'".$column2."' => $"."row['".$column2."'],
                	'".$column3."' => $"."row['".$column3."'],
                	'".$column4."' => $"."row['".$column4."'],
                	'".$column5."' => $"."row['".$column5."'],
 			 
			);
     
			
			$"."this->load->database();
	        $"."this->db = $"."this->load->database('".$name_database."', TRUE);
	
			$"."this->db->replace('".$name_table."', $"."data);
			//$"."insert = $"."this->sk->replace($"."data);
			 			
		}
		
		//header('location: '.base_url('academic_adm_lecturer_dpa'));
		//echo 'selesai'; ?>
}";		
?>
<?php
//membuat folder
if (!is_dir('cloud-api/frontend/ci/controllers')) {mkdir('cloud-api/frontend/ci/controllers', 0777, true);}
$fp = fopen("cloud-api/frontend/ci/controllers/".$name_table."_controller_get_data.php","wb"); if( $fp == false ){ }else{ fwrite($fp,$content_controller); fclose($fp);}
?>
