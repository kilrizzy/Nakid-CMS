<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nakid CMS | A Barebones Content Management System</title>
<link rel="shortcut icon" href="<?php echo (base_url()); ?>assets/themes/<?php echo(NAKID_THEME); ?>/favicon.ico" type="image/x-icon" />
<link href="<?php echo (base_url()); ?>assets/themes/default/css/structure.css" type="text/css" rel="stylesheet" />
<link href="<?php echo (base_url()); ?>assets/themes/default/css/forms.css" type="text/css" rel="stylesheet" />
<link href="<?php echo (base_url()); ?>assets/themes/<?php echo(NAKID_THEME); ?>/css/main.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo (base_url()); ?>assets/addons/jqueryui/css/nakid-theme/jquery-ui-1.8.7.custom.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo (base_url()); ?>assets/addons/jqgrid/css/ui.jqgrid.css" />
 
<script src="<?php echo (base_url()); ?>assets/addons/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="<?php echo (base_url()); ?>assets/addons/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="<?php echo (base_url()); ?>assets/addons/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo (base_url()); ?>assets/addons/colorbox/colorbox.css" />
<script src="<?php echo (base_url()); ?>assets/addons/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo (base_url()); ?>assets/addons/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo (base_url()); ?>assets/addons/ckeditor/adapters/jquery.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo (base_url()); ?>assets/addons/jquery.collapser/collapser.css" />
<script type="text/javascript" src="<?php echo (base_url()); ?>assets/addons/jquery.collapser/jquery.collapser.min.js"></script>
<script>
$(document).ready(function(){
	$(".framepop").colorbox({
		width:"80%", 
		height:"80%", 
		iframe:true
	});
	$('textarea.editor').ckeditor({
		filebrowserBrowseUrl:'<?php echo (base_url()); ?>assets/addons/kcfinder/browse.php?type=files',
		filebrowserImageBrowseUrl:'<?php echo (base_url()); ?>assets/addons/kcfinder/browse.php?type=images',
		filebrowserFlashBrowseUrl:'<?php echo (base_url()); ?>assets/addons/kcfinder/browse.php?type=flash',
		filebrowserUploadUrl:'<?php echo (base_url()); ?>assets/addons/kcfinder/upload.php?type=files',
		filebrowserImageUploadUrl:'<?php echo (base_url()); ?>assets/addons/kcfinder/upload.php?type=images',
		filebrowserFlashUploadUrl:'<?php echo (base_url()); ?>assets/addons/kcfinder/upload.php?type=flash'
	});
	$('.collapse').collapser({
	target: 'next',
	changeText: 0,
	//expandHtml: 'Expand Text',
	//collapseHtml: 'Collapse Text',
	expandClass: 'expArrow',
	collapseClass: 'collArrow'
});
});
function openKCFinder(textarea) {
	window.KCFinder = {
		callBackMultiple: function(files) {
			window.KCFinder = null;
			textarea.value = "";
			for (var i = 0; i < files.length; i++)
				textarea.value += files[i] + "\n";
		}
	};
	window.open('<?php echo (base_url()); ?>assets/addons/kcfinder/browse.php?type=files&dir=files/public',
		'kcfinder_multiple', 'status=0, toolbar=0, location=0, menubar=0, ' +
		'directories=0, resizable=1, scrollbars=0, width=800, height=600'
	);
}
</script>