<div id="header">
  <div id="logo">
    <h1><a href="<?php echo site_url(); ?>"><?php echo($this->config->item('theme_name')); ?> <span><?php echo($this->config->item('theme_slogan')); ?></span></a></h1>
  </div>
  <div id="navigation">
  	<?php 
	function makemenu($m){
		$out = "<ul>";
		foreach($m as $i){
			$class = "";
			if($i['link'] == "system/coming_soon"){
				$class = "comingsoon";
			}
			$out .= "<li class='".$i['name']." ".$class."'>";
			if(!empty($i['link'])){
				$link = $i['link'];
				$target = "";
				if(strstr($link,"http")){
					$target = " target = '_blank' ";
				}else{
					$link = site_url($link);
				}
				$out .= "<a href='".$link."' ".$target.">";
			}
			$out .= $i['title'];
			if(!empty($i['link'])){
				$out .= "</a>";
			}
			if(isset($i['children'])){
				$out .= makemenu($i['children']);
			}
			$out .= "</li>\n";
		}
		$out .= "</ul>";
		return $out;
	}
	echo makemenu($menu);
	?>
  </div>
</div>
