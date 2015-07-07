<?php
session_start();
require_once '/private/config.ini.php';

$url = 'https://hh.ru/oauth/authorize?response_type=code&client_id=' . $config['api']['clientID'] . '&redirect_uri=' . $config['api']['redirect'];

if (isset($_SESSION['token'])){
	
	$murl = "https://api.hh.ru/vacancies";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $murl);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_SESSION['token']));	
	curl_setopt($ch, CURLOPT_USERAGENT, 'users-parse/1.0 (evoldev@evoldev.com)');
	//curl_setopt($ch, CURLOPT_POSTFIELDS, "text='PHP'");
	$html_content = curl_exec($ch);
	curl_close($ch);
	
	echo "<pre>";
	print_r(json_decode($html_content, true));
	echo "</pre>";
}
else if (isset($_GET['code'])){
	$code = $_GET['code'];
	$url = 'https://hh.ru/oauth/token';
	$data = array(
					'grant_type' => 'authorization_code', 
					'client_id' => $config['api']['clientID'],
					'client_secret' => $config['api']['secret'],
					'code'	=>	$code,
					'redirect_uri'	=>	$config['api']['redirect']
				);
	
	$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	$result_arr = json_decode($result);
	$token = $result_arr->access_token;
	$_SESSION['token'] = $token;
	header('Location: /');
	//print_r($result);
	/*$code = readline("Input token: ");
	echo $code . "\n";*/
	/*$curl = new Curl();
	$curl->setUserAgent('users-parse/1.0 (evoldev@evoldev.com)');
	$a = sprintf("Bearer ".$token);
	$curl->setHeader('Authorization',$a);
	$curl->get('https://api.hh.ru/resumes', $data = [
		'age_from' => 20,
		'age_to' => 21,
		'areas' => 1,
		'gender' => 'female',
		'salary_from' => 20000,
		'salary_to' => 25000,
		'specialization' => 2,
		'experience'=> 'between1And3',
		'per_page' => 50,
		'page' => 1,
		'exp_period' => 'all_time'

	]);

	var_dump($curl->request_headers);
	var_dump($curl->response_headers);
	var_dump( $curl->response);*/
}
else
	echo '<a href="' . $url . '">Auth</a>';

?>