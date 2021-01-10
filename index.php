<?php
if(isset($_POST['submit']) && !empty($_POST['common_name'])){
	if(file_exists('publicCAcert/'.$_POST['cert'].'.cbcert')){
		$CAkey = file_get_contents('publicCAkey/'.$_POST['cert'].'.cbck');
		$CAcert = file_get_contents('publicCAcert/'.$_POST['cert'].'.cbcert');
	}
	$e = $_POST['email'];
	$c = $_POST['country'];
	$s = $_POST['state'];
	$cn = $_POST['common_name'];
	$o = $_POST['organization'];
	$ou = $_POST['organizational_unit'];
	$l = $_POST['locality'];
	$crypt = $_POST['crypt'];
	$length = $_POST['length'];
	$date = 86400*90;
	$post = [
		 'email' => $e,
		 'country' => $c,
		 'state' => $s,
		 'common_name' => $cn,
		 'organization' => $o,
		 'organizational_unit' => $ou,
		 'locality' => $l,
		 'CA_key' => base64_encode($CAkey),
		 'CA_cert' => $CAcert,
		 'date' => $date,
		 'length' => $length
		];
	if(strlen($c) <= 2){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://catboom-dns.ml/api/cert-signing/v1/");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		$json = json_decode($server_output, true);
		if($json['success'] == true){
			$color = 'green';
		}else{
			$color = 'red';
		}
		echo '<h1 style="color:'.$color.'">'.$json['msg'].'</h1>';
	}else{
		echo '<p style="color:red;text-align:center">Country have 2 character only</p>';
	}
}
echo '<!DOCTYPE html>
<html>
	<head>
		<title>CatBoom Certificate</title>
	</head>
	<body>
		<table align="center">
			<tr>
				<td style="border:1px solid black">
					<form method="post">
						<label for="country">Country:</label>
						<input type="text" size="2" name="country" maxlength="2" required>
						<br>
						<label for="state">State:</label>
						<input type="text" name="state">
						<br>
						<label for="locality">Locality:</label>
						<input type="text" name="locality">
						<br>
						<label for="organization">Organization:</label>
						<input type="text" name="organization">
						<br>
						<label for="organizational_unit">Organizational Unit:</label>
						<input type="text" name="organizational_unit">
						<br>
						<label for="email">Email:</label>
						<input type="email" name="email" required>
						<br>
						<label for="common_name">Common Name:</label>
						<input type="text" name="common_name" required>
						<br>
						<label for="cert">CA cert:</label>
						<select name="cert">
';
$dr = scandir('publicCAcert/');
	foreach($dr as $cd){
		if(end(explode('.',$cd)) == 'cbcert'){
			echo '							<option value="'.explode('.',$cd)[0].'">'.explode('.',$cd)[0].'</option>
';
		}
	}
echo '						</select>
						<br>
						<label for="cert">Key Length:</label>
						<select name="length">
							<option value="2048">2048</option>
							<option value="4056">4056</option>
							<option value="8112">8112</option>
							<option value="16224">16224</option>
						</select>
						<br>
						<input type="submit" name="submit" value="Create">
					</form">
				</td>
			</tr>
		</table>
	</body>
</html>';
?>
