/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.autoGrow_minHeight = 500;
	config.toolbar = 'Full';
	config.toolbar_Full =
	[
		['Source','-','PasteText','PasteFromWord','-','SpellChecker', 'Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		['NumberedList','BulletedList','-','Blockquote'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Anchor'],
		['Image','Flash','Table','HorizontalRule','SpecialChar'],
		'/',
		['Styles','Format','Font','FontSize'],
		['Bold','Italic','Underline','-','Subscript','Superscript'],
		['TextColor','BGColor'],
		['Maximize']
	];
};
