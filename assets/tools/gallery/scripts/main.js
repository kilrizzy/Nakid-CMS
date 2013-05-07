jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery(".lightbox").colorbox();
});
function nakid_gallery_show_category(cat){
	jQuery(".nakid_gallery_images li").hide();
	jQuery(".nakid_gallery_categories li").removeClass('active');
	jQuery(".nakid_gallery_images li.category_"+cat).show();
	jQuery(".nakid_gallery_categories li.catlink_"+cat).addClass('active');
}