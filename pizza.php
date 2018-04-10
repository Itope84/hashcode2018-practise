<?php

echo "example pizza";
$input = fopen("example.in", 'r') or die('failed to open');
// read the first line, this contains descriptions
$line_1 = fgets($input);
echo $line_1.'<br>';
// explode the first line into an array
$input_arr = explode(' ', $line_1);
$t_rows = $input_arr[0];
$t_columns = $input_arr[1];
$min_each_ing = $input_arr[2];
$max_total_ing = $input_arr[3];
$total_cells = $t_rows*$t_columns;
echo "Total Cells:".$total_cells.'<br>';

// get the other lines which are descriptions of each array
$i=1;
$pizza = array();
while (!feof($input)) {
	$i++;
	$pizza[$i] = fgets($input);
	
}

unset($pizza[$i]);
$pizza = array_values($pizza);
foreach ($pizza as $row => $value) {
	$pizza[$row] = str_split($pizza[$row]);	
	unset($pizza[$row][$t_columns]);
	$pizza[$row] = array_values($pizza[$row]);
}


echo "Pizza: <br>";
// print_r($pizza);
// foreach ($pizza as $row => $column) {
// 	foreach ($column as $col => $ing) {
// 		echo $ing.' ';
// 	}
// 	echo "<br>";
// }

// open output file;
$output = fopen("example.txt", 'w') or die('failed to open');


$starting_point = 0;
$row_id = 0;
$slices = 0;
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
			$j = $i+1; //move to the next element
			// find other Ms until M is up to min_each_ing 
			while ($m<$min_each_ing && $j<$t_columns) {
				// if current position-first m ($starting_point) is > max_total_ing, end and start from the m after the first m.
				
				if ($items>=$max_total_ing) {
					break;
				}
				switch ($pizza[$row][$j]) {
					case 'M':
						$m++;
						$last_m = $j;
						$end = $j;
						
						break;
				// we also want to count the number of t's in betweeen.	
					case 'T':
						$t++;
						
						$end = $j;
						break;
					default:
						break;

				}
				
				$j++;
				$items = $j-$start;
			}
			// if at the end of the while loop, the no of m<min_each_ing, then we want to start counting from the next m.
			if ($m<$min_each_ing) {
				$starting_point = $i+1;
				continue;
			}

			$rem_t = $min_each_ing-$t;
			$rem_ing = $max_total_ing-$items;
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
								
								$end = $l;
							}
							$items++;
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