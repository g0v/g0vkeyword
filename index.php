<?php

$people = array(
	"毛部長治國",
	"吳院長敦義",
	"曾部長勇夫"
);

$results = array();
$total_counts = array();
foreach ($people as $person) {
	$ret = exec("php people.php $person");
	$p_words = json_decode($ret, true);
	$results[$person] = $p_words;
}

foreach($people as $person) {
	$total = 0;
	foreach($results[$person] as $word) {
		$total += $word['tf'];
	}
	$total_counts[$person] = $total;
}

foreach($people as $person) {
	$total_number = $total_counts[$person];
	foreach($results[$person] as &$word) {
		$word['percentage'] = $word['tf']/$total_number;
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"></meta>
		<title>政府關鍵字</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
	</head>
	<body>
		<div class="container">
			<h1>政府關鍵字</h1>
			<?php foreach($people as $person) { ?>
			<!-- <div class="btn"><a href="#<?php echo $person;?>"><?php echo $person;?></a></div> -->
			<?php } ?>

			<?php foreach($people as $person) { ?>
				<div class="row span3">
				<h3 id="<?php echo $person;?>"><?php echo $person; ?></h3>
				<ul class="unstyled well">
				<?php foreach($results[$person] as $term => $tf) { ?>
					<li>
						<h4><?php echo $term; ?></h4>
						<div class="progress <?php if($tf['percentage']*200 > 20) {echo "progress-danger";} ?>">
						  <div class="bar" style="width:<?php echo $tf['percentage']*100*2;?>%;"></div>
						</div>
					</li>
				<?php } ?>
				</ul>
</div>
			<?php } ?>
		</div>
	</body>
</html>
