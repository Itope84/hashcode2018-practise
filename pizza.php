<?php

echo "small pizza".'<br>';
$input = fopen("small.in", 'r') or die('failed to open');
#store the description array into individual variables
list($t_rows, $t_columns, $min_each_ing, $max_total_ing) = getDescription($input);
$total_cells = $t_rows*$t_columns;
echo "Total Cells:".$total_cells.'<br>';

function getDescription($file){
	// read the first line, this contains descriptions
	$desc = fgets($file);
	echo $desc.'<br>';
	// explode the first line into an array
	$desc = explode(' ', $desc);
	return $desc;
}


// // get the other lines which are the pizza arrangement
function getPizza($file){
	$i=0;
	$pizza = array();
	while (!feof($file)) {
		$i++;
		$pizza[$i] = fgets($file);
	}	
	unset($pizza[$i]); #delete the last linebreak
	$pizza = array_values($pizza); #reset the array keys
	// set each pizza row into an array of individual element
	foreach ($pizza as $row => $value) {
		global $t_columns;
		$pizza[$row] = str_split($pizza[$row]);
		// unset the last element as that is just a line break
		unset($pizza[$row][$t_columns]);
		$pizza[$row] = array_values($pizza[$row]);
	}
	return $pizza;
}
$pizza = getPizza($input);


function limitExceeded($items){
	global $max_total_ing;
	if ($items>=$max_total_ing) {
			return true;
	}
	else{
		return false;
	}
}

$slices = 0;

$output = fopen("example.txt", 'w') or die('failed to open');

foreach ($pizza as $row => $column) {
	$starting_point = 0;
	for ($i=0; $i<$t_columns; $i++) { 
		// echo $i;
		// for each column, I want to find the fiorst M, once i do, i wanna stop and do other stuff, if M is not found at all, just skip to the next row.
		$m = 0;
		$t = 0;
		$start = $starting_point;
		$end = 0;
		$items = 0;
		$last_m = 0;
		if ($pizza[$row][$i]=='M') {
			
			$m++;
			$start = $last_m = $end= $i;
			$items++;
			$cursor = $i+1; //move to the next element
			// find other Ms until M is up to min_each_ing 
			while ($m<$min_each_ing && $cursor<$t_columns) {
				// if current position-first m ($starting_point) is > max_total_ing, end and start from the m after the first m.
				
				if ($items>=$max_total_ing) {
					break;
				}
				switch ($pizza[$row][$j]) {
					case 'M':
						$m++;
						$last_m = $counter;
						$items++;
						$end = $counter;
						break;
				// we also want to count the number of t's in betweeen.	
					case 'T':
						$t++;
						$items++;
						$end = $counter;
						break;
					default:
						break;

				}
				
				$counter++;
				
			}
			// if at the end of the while loop, the no of m<min_each_ing, then we want to start counting from the next m.
			if ($m<$min_each_ing) {
				$starting_point = $i+1;
				continue;
			}

			$rem_t = $min_each_ing-$t;
			$rem_ing = (int)$max_total_ing-$items;
			// if remaining t>0 and remaining total >= rem_t, count backwards from starting point to find Ts
			if ($rem_ing>=$rem_t) {
				if ($rem_t>0) {
					for ($k=$start; $k>=0; $k--){
						if ($k<$starting_point) {
							break;
						}
						if ($items==$max_total_ing) {
							break;
						}
						if ($pizza[$row][$k]=='T') {
							$t++;
							$rem_t--;
							$items++;
							$start = $k;
						}
						
						
					}

					if ($rem_t>0) {
						for ($l=$last_m; $l<$t_columns; $l++){
							
						if ($items==$max_total_ing) {
							break;
						}
							if ($pizza[$row][$l]=='T') {
								$t++;
								$rem_t--;
								$items++;
								$end = $l;
							}
							
							if ($rem_t==0) {
								break;
							}
						}
					}
					if ($rem_t>0) {
						$starting_point = $i+1;
				
						continue;
					}
				}
			
			}
			else{
				$starting_point=$i+1;
				continue;
			}
			// modify starting point as the last item's position and start again
			$slices++;
			$pattern = $row." ".$start." ".$row." ".$end."\n";
			echo $pattern;
			fwrite($output, $pattern) or die("Could not write to file");
				$i = $starting_point = $end;
				$starting_point++;
				
		}

	}
}

echo $slices;
fwrite($output, $slices);
fclose($output);
fclose($input);


?>