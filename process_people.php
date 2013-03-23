<?php
	define('PEOPLE_FILE', './twlyreader-prototype/json/index_people_interp.json');
	
	if(count($argv) < 2) {
		echo "Usage: php process_people.php 蔣委員孝嚴";
		return;
	}

	$target = $argv[1];

	$people = json_decode(file_get_contents(PEOPLE_FILE), true);
	$doc_ids = $people[$target];

	$terms = array();
	foreach($doc_ids as $id) {
		$doc_raw = file_get_contents("./twlyreader-prototype/json/{$id}_interp.json");
		$doc = json_decode($doc_raw, true);
		$lines = $doc[0][1];
		$temp = array();
		$line_str = "";
		foreach($lines as &$line) {
			$line_str .= $line['content'];
		}
		$q = "<?xml version=\"1.0\"?><methodCall><methodName>splitTerms</methodName><params><param><value>$line_str</value></param></params></methodCall>";
		$response = array();
		exec("curl -d '$q' http://localhost:5566 -XPOST", $response);
		foreach($response as $r) {
			preg_match('/^<value><string>(.*)<\/string><\/value>$/', $r, $matches);
			if(!empty($matches)) {
				$term = $matches[1];

				if(isset($temp[$term]['tf'])) {
					$temp[$term]['tf']++;
				} else  {
					$temp[$term]['tf'] = 1;
					$temp[$term]['doc_id'] = array($id);
				}
			}
		}
		foreach($temp as $k=>$t) {
			if(isset($terms[$k])) {
				$terms[$k]['tf'] += $temp[$k]['tf'];
				$terms[$k]['df']++ ;
			} else {
				$terms[$k]['tf'] = $temp[$k]['tf'];
				$terms[$k]['df'] = 1 ;
				if(isset($terms[$k]['doc_id'])) {
					$terms[$k]['doc_id'] = $temp[$k]['doc_id'][0];
				} else {
					$terms[$k]['doc_id'] = $temp[$k]['doc_id'];
				}
			} 
		}
	}

	file_put_contents('./data/'.$target.'.json', json_encode($terms));
