<?php
/**
 * @file views-view-unformatted.tpl.php
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
?>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
	<?php 
		//$count = 1;
		$total = count($rows);
		print_r($total);
		foreach ($rows as $id => $row): 
			
			$Thumbvid = $id+1;

			$getSize = getSizeThumb($Thumbvid);
			
			$starture = false;
			$closure = false;

			if($Thumbvid%1 == 0){
				$message = "1 ";
			}
			if($Thumbvid%2 == 0){
				$message = "2 ";
			}
			
			
			/*if 3 open*/
			if($Thumbvid%3 == 0 && $Thumbvid%6 != 0)
			{
				$message = "3 ";
				$starture = true;
			}
			if($Thumbvid%4 == 0){
				$message = "4 ";
				$closure = true;
			}
			/*if 4 close*/
			

			/*if 5 open*/
			
			if($Thumbvid%5 == 0)
			{
				$message = "5 ";
				$starture = true;
			}
			
			
			if($Thumbvid%6 == 0){
				$message = "6";
				$closure = true;
			}
			
			/*if 6 close*/
			
			
		
			
		/*	
			if($Thumbvid%3 == 0 && $Thumbvid%6 != 0){
				$starture = true;
				print "VID MOD 3 NOT 6";
			}

			if($Thumbvid%5 == 0){
				$starture = true;
				print "VID MOD 5";
			}
		*/	
			
		/*
			if($Thumbvid%4 == 0){
				$closure = true;
				print "VID MOD 4";
			}

			if($Thumbvid%3 != 0 && $Thumbvid%6 == 0){
				$closure = true;
				print "VID MOD 6 NOT MOD 3";
			}

			if($Thumbvid%2 != 0 && $Thumbvid%6 == 0){
				$closure = true;
				print "VID MOD 6 NOT MOD 2";
			}
			
			if($Thumbvid == $total){
				$closure = true;
				//populated entire batch
				print "VID = TOTAL";
			}
		*/	

			
			if($starture)
			{
				?><!--OPENED-->
				<div class="smallwrap test views-row"><?php
			}
			?>
			  <div class="<?php echo $getSize;?> <?php print $classes_array[$id]; ?>">	
					<style>
						.THUMBS{background:blue; display:block;}
					</style>
					<?php 
						//echo '<div class="THUMBS">'.$Thumbvid.' <br>message:  '.$message.'</div>';
						print $row;			
					?>
			  </div>
		  
			<?php
			if($closure)
			{
				?></div><!--CLOSED--><?php
			}
  
		  ?>
		  
	<?php 
		endforeach; 
	?>