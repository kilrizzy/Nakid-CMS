<div class="nakid_gallery <?php if( !empty($options['category_display'])){ 
	echo("category_".$options['category_display']); 
} ?>">
<?php	
	if(!empty($options['category_display']) && $options['category_display'] != "hide"){
		echo('<div class="nakid_gallery_categories '.$options['category_display'].'">'."\n");
			echo('<ul>'."\n");
			$firstcat = 0;
			$i = 0;
			foreach($categories as $category){
				$i++;
				if($i==1){
					$firstcat = $category->id;
				}
				echo('<li class="catlink_'.$category->id.'"><a href="javascript:nakid_gallery_show_category('.$category->id.');">'.$category->title.'</a></li>'."\n");
			}
			echo('<div class="clearline">&nbsp;</div>'."\n");
			echo('</ul>'."\n");
		echo('</div>'."\n");
	}
	echo('<div class="nakid_gallery_content">'."\n");
		echo('<div class="nakid_gallery_description">'."\n");
			echo($gallery['description']);
		echo('</div>'."\n");
		
		echo('<div class="nakid_gallery_images">'."\n");
			echo('<ul class="'.$gallery['classes'].'">'."\n");
			foreach($images as $image){
				echo('<li class="'.$image->catclasses.'">'.$image->link.'</li>'."\n");
			}
			echo('<div class="clearline">&nbsp;</div>'."\n");
			echo('</ul>'."\n");
		echo('</div>'."\n");
	echo('</div>'."\n");
	echo('<div class="clearline">&nbsp;</div>'."\n");
?>
</div>
<script type="text/javascript">
<?php
if($options['category_front'] == "first"){
	echo('nakid_gallery_show_category('.$firstcat.');');
}
?>
</script>