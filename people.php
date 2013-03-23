<?php
	if(empty($_GET['target']) && count($argv) < 2) {
		die('Usage: php '.$argv[0]. ' path_to_json_file');
	}

	if(!empty($_GET['target'])) {
		$target = './data/'.$_GET['target'].'.json';
	} else {
		$target = './data/'.$argv[1].'.json';
	}

	$terms = json_decode(file_get_contents($target), true);
	 $total_tf = array();
	 foreach ($terms as $key => $v) {
	 	$tf = $v['tf'];
	 	foreach ($v['doc_id'] as $id) {
	 		if (isset($total_tf[$id])) {
	 			$total_tf[$id] += $v['tf'];
	 		} else {
	 			$total_tf[$id] = $v['tf'];
	 		}
	 	}
	 }
	$d_size = count($total_tf);

	foreach ($terms as $k => $v) {
		$idf = log($d_size/($v['df']+1));
	}
	$stop_words = explode("\n", file_get_contents('./data/stop_word_list.txt'));

	uasort($terms, function($a, $b) {
		if ($a['tf'] == $b['tf']) {
			return 0;
		}
		return ($a['tf'] > $b['tf']) ? -1 : 1;
	});

	$ret = array();
	$i = 0;
	foreach($terms as $k=>$t) {
		$k = str_replace("　", "", $k);
		if(	mb_strlen($k, 'UTF-8') < 3 ||
			preg_match('/^E\d*$/', $k) ||
			strpos($k, '？') !== false ||
			strpos($k, 'xml.parsers') !== false ||
			in_array($k, $stop_words)) {
			continue;
		}

		if($k[0] == 'E') {
			$k = preg_replace('/^E/','', $k);
		}

		$ret[$k] = array("tf"=> $t['tf']);
		if(++$i == 20) {
			break;
		}
	}

	echo json_encode($ret);

