<?php
 		
$ex_txt ="";
$have_cnt=0;
$now_cnt=0;
//ログ取得先のディレクトリを指定してその中のファイル分ループ
$files = scandir("read_csv");

$out = array('A' =>  array(),
			  "B" =>  array(),
		 	  "C" =>  array(),
			  "D" =>  array(),
		);
$shopArray = array('A' =>  array(),
			  "B" =>  array(),
		 	  "C" =>  array(),
			  "D" =>  array(),);

ini_set('auto_detect_line_endings', 1);
$records[] =null;
foreach ($files as $file){
	$filename = "read_csv" .DIRECTORY_SEPARATOR. $file;
	if(is_file($filename)){

		$ext = substr($filename, strrpos($filename, '.') + 1);
		if(strcasecmp("csv", $ext) != 0) continue;
		

		if (!$fp = fopen($filename ,"r")) {
		    print "ファイルを開けませんでした！";
		    exit();
	  	}
	  	 echo $filename ."<br>";
	  	$labels = fgetcsv($fp);
	  	$l_cnt = 0;
	  	$whose = null;
	  	$item = null;
	  	$data = null;
	  	$price = null;
	  	$shop = null;

	  	foreach ($labels as $label) {
	  		if($label == "item") $item = $l_cnt;
	  		else if($label == "price") $price = $l_cnt;
	  		else if($label == "whose") $whose = $l_cnt;
	  		else if($label == "shop") $shop = $l_cnt;
	  		$l_cnt++;
	  	}		 

		while (($line = fgetcsv($fp)) !== FALSE) {//fgetcsvは文字列の""を外してくれる
			if(($rec = array_search($line[$shop], $shopArray[$line[$whose]])) !==false) {
				if(array_key_exists($line[$item], $out[$line[$whose]][$rec])) {
					$out[$line[$whose]][$rec][$line[$item]] += intval($line[$price]);
				}else{
					$out[$line[$whose]][$rec] += array($line[$item] => intval($line[$price]));
				} 
	
			} else {
				 $current = count($shopArray[$line[$whose]]);
				$shopArray[$line[$whose]][$current] = $line[$shop];
				$out[$line[$whose]][$current] =   array($line[$item] => intval($line[$price]));
			}
			
		}
	}
}

fclose($fp);
write_csv($out,$shopArray);
function write_csv($out,$shopArray){

	$text = "whose,shop,item,price\n";

	foreach ($out as $whose => $value) {
		$whose_cnt=true;

		foreach ($value as $num => $value2) {
			$shop_cnt=true;

			foreach ($value2 as $item => $price) {
		
		 		$text .= ($whose_cnt ? $whose : "") .",". ($shop_cnt ? $shopArray[$whose][$num] : "") .",". $item .",". $price ."\n";
		 		$whose_cnt=false;
				$shop_cnt=false;
		 	}
		}	
	}

 	//$text = mb_convert_encoding($text, "SJIS", "UTF-8");
	
	file_put_contents("./out.csv",$text);
		
}
?>