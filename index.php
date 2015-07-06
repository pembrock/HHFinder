<?php

require_once '/private/config.ini.php';

$url = 'https://hh.ru/oauth/authorize?response_type=code&client_id=' . $config['api']['clientID'] . '&redirect_uri=' . $config['api']['redirect'];


if (isset($_GET['code'])){
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
	//print_r($result);

	$murl = "https://api.hh.ru/me";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $murl);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$html_content = curl_exec($ch);
	curl_close($ch);
	
	echo $html_content;
	
}
else
	echo '<a href="' . $url . '">Auth</a>';

?>