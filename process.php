<html>
<head><title>"Welcome to VCF sorting website"</title></head>

<body>

<table border = "1">
<?php

	$name = $_POST["chrom"];
	#echo "<td>".$name."</td>";
	echo "<br>";
	$mychrom = explode(':', $name);
	$chrom_num = $mychrom[0];
	$genomic_location = 1;

	if($name == '')
	{
		#print("No chromosome found");
		$genomic_location = 0;
	}
	
	$chr_only_flag=0;

	$chr_start_stop = explode('-', $mychrom[1]);
	if (count($mychrom) == 2)
	{
		$chr_start = $chr_start_stop[0];
		$chr_stop = $chr_start_stop[1];
		if(count($chr_start) > 0 && (count($chr_stop) > 0))
		{ 
			$chr_only_flag=1;
		}
		elseif(count($chr_start) > 0 && (count($chr_stop) == 0))
		{
			$chr_only_flag=2;
		}
	}

	$out_file = 'egrep -v "^#" test.vcf > test_n.vcf';
	exec($out_file);

	$cnt=0;
	$avg_depth=0;
	$dp_count=0;
	$total_click = $_POST['Total_number_of_variants'];
	$depth_click = $_POST['Avg_depth_of_variants'];
  	
	$handle = fopen("test_n.vcf", "r");
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			$line_arr = explode("\t", $line);
            // check if user did not enter any genomic location.
			if($genomic_location == 0) {
				$cnt = $cnt+1;
				$dp = $line_arr[7];
				preg_match("/\bDP=(\d+)\b/i", $dp, $matches);
				$dp_count = $dp_count + $matches[1];
				print($line);
				echo "<br><br>";

			}
			// Check if user enters only chromosome number
			elseif($chr_only_flag == 0 && $line_arr[0] == $chrom_num) {
				$cnt = $cnt+1;
				$dp = $line_arr[7];
				preg_match("/\bDP=(\d+)\b/i", $dp, $matches);
				$dp_count = $dp_count + $matches[1];
				print($line);
				echo "<br><br>";

			} 
			// Check if user entered genomic location in the format <chromosome>:<start><stop>
			elseif ($chr_only_flag == 1 && $line_arr[0] == $chrom_num && $line_arr[1] >= $chr_start && $line_arr[1] <= $chr_stop){
					$cnt = $cnt+1;
					$dp = $line_arr[7];
					preg_match("/\bDP=(\d+)\b/i", $dp, $matches);
					$dp_count = $dp_count + $matches[1];
					print($line);
					echo "<br><br>";

						}
			// Check if user enters only <chromosome>:<start>
			elseif ($chr_only_flag == 2 && $line_arr[0] == $chrom_num && $line_arr[1] == $chr_start){
						$cnt = $cnt+1;
						$dp = $line_arr[7];
						preg_match("/\bDP=(\d+)\b/i", $dp, $matches);
						$dp_count = $dp_count + $matches[1];
						print($line);
						echo "<br><br>";

							}
						}
	fclose($handle);
			}
	
	if(!empty($total_click) and !empty($depth_click))
	{
		echo "<br>";
		print("Total count is : ");
		print($cnt);
		echo "<br>";
		$avg_depth=($dp_count/$cnt);
		print("Average depth is : ");
		print($avg_depth);

	}
	elseif(!empty($total_click))
	{
		echo "<br>";
		print("Total count is : ");
		print($cnt);
	}
	elseif(!empty($depth_click))
	{
		echo "<br>";
		$avg_depth=($dp_count/$cnt);
		print("Average depth is : ");
		print($avg_depth);
	}

?>

</table>
</body>

</html>
