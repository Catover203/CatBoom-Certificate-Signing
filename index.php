<?php
if(isset($_POST['submit']) && !empty($_POST['common_name'])){
	if(file_exists('cert/'.$_POST['cert'].'.cbcert')){
		$CAkey = file_get_contents('publicCAkey/'.$_POST['cert'].'.cbck');
		$CAcert = file_get_contents('publicCAcert/'.$_POST['cert'].'.cbcert');
		$str = str_replace('----------BEGIN CATBOOM SECURITY CERTIFICATE----------','', str_replace('----------END CATBOOM SECURITY CERTIFICATE----------','',$CAcert));
		$trust_by = base64_decode(explode('/||/',$cr->CBCRP(base64_decode($str)))[0]);
		$trust = 'true';
	}else{
		$trust = 'false';
		$trust_by = 'CatBoom Unknown CA Certificate';
	}
	function kcrypt($name, $data){
	    if(hash($name, $data) && $name != 'md5' or $name != 'base64' or $name != 'sha1'){
	        return hash($name, $data);
	    }elseif($name == 'md5'){
	        return md5($data);
	    }elseif($name == 'base64'){
	        return base64_encode($data);
	    }elseif($name == 'sha1'){
	        return sha1($data);
	    }
	}
	$e = $_POST['email'];
	$c = $_POST['country'];
	$s = $_POST['state'];
	$cn = $_POST['common_name'];
	$o = $_POST['organization'];
	$ou = $_POST['organizational_unit'];
	$l = $_POST['locality'];
	$crypt = $_POST['crypt'];
	$date = 86400*90;
	$post = ['crypt' => $crypt, 'email' => $e, 'country' => $c, 'state' => $s, 'common_name' => $cn, 'organization' => $o, 'organizational_unit' => $ou, 'locality' => $l, 'CA_key' => $CAcert, 'CA_key' => $CAkey];
	if(strlen($c) <= 2){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://catboom-dns.ml/api/cert-signing/v1");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close ($ch);
	$json = json_decode($server_output, true);
	$cert = $json['cert'];
	$key = $json['key'];
	$csr = $json['csr'];
echo '<div>
<div id="certificate">
<table align="center">
<tr>
<td style="border:1px solid black">
<h4>Certificate - '.strlen($cert).' bit | based '.strlen($CAcert).' bit certificate</h4>
<textarea cols="135" rows="35">'.$cert.'</textarea>
</td>
</tr>
</div>
<div id="key">
<tr>
<td style="border:1px solid black">
<h4>Key - '.strlen($key).' bit | based '.strlen($CAkey).' bit key</h4>
<textarea cols="125" rows="35">'.$key.'</textarea>
</td>
</tr>
</div>
<tr>
<td style="border:1px solid black">
<h4>CSR - '.strlen($csr).' bit</h4>
<textarea cols="125" rows="35">'.$csr.'</textarea>
</table>
</div>';
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
<input type="text" size="2" name="country" maxlength="2">
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
<input type="email" name="email">
<br>
<label for="common_name">Common Name:</label>
<input type="text" name="common_name">
<br>
<label for="cert">CA cert:</label>
<select name="cert">
';
$hasher = hash_algos();
$dr = scandir('cert/');
	foreach($dr as $cd){
		if(end(explode('.',$cd)) == 'cbcert'){
			echo '<option value="'.explode('.',$cd)[0].'">'.explode('.',$cd)[0].'</option>
';
		}
	}
echo '</select>
<br>
<label for="crypt">Key Crypto:</label>
<select name="crypt">
';
foreach($hasher as $hlist){
    echo '<option value="'.$hlist.'">'.$hlist.'</option>';
}
echo '
</select>
<br>
<input type="submit" name="submit" value="Create">
</form">
<p>If you have key and csr and want to resign, click <a href="csr.php">here</a>.</p>
</td>
</tr>
</table>
</body>
</html>';
?>
