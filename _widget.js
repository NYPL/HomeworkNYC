/*
$(document).ready(function() {
	init_style_switcher();
});

var cookie_options = { path: '/', expires: 100000 };

function init_style_switcher() {
	$('#scroller').prepend('<div id="style_switcher">change style</div>')
	if($.cookie('homeworkNYC_search_style')) {
		style_num = $.cookie('homeworkNYC_search_style');
	} else {
		style_num = 0;
	}
	change_style(style_num)
	$('#style_switcher').click(function() {
		if(style_num++ > 12) {
			style_num = 0; 
		}
		$.cookie('homeworkNYC_search_style', style_num, cookie_options)
		change_style(style_num)
	});
}

function change_style(style_num) {
	bg = Array(
		// '#66FF33',
		// '#FFCC33',
		'#0099CC',
		// '#FF99CC',
		'#660066',
		// '#999999',
		'#FF3399',
		'#3300FF',
		'#FF3300',
		'#cf0d33',
		'#005400',
		'#7c4618',
		'#d11bd0',
		'#9c78ca'

	);
	txt = bg;
	$('body').css('color',txt[style_num]);
	$('.custom').css('color',txt[style_num]);
}
*/
/*
function escape_for_css(str) {
	return str.replaceAll("[^a-zA-Z0-9]",'');
}

function valid_email(email) {
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
		return true;
	} else {
		return false;
	}
}
*/
