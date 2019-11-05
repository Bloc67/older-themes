// to have some knowledge about touch devices in js too
var supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;

$(function() {
	$('ul.dropmenu, ul.quickbuttons').superfish({delay : 0, speed: 50, sensitivity : 8, interval : 10, timeout : 1});
	// tooltips
	$('.preview').SMFtooltip();
	// find all nested linked images and turn off the border
	$('a.bbc_link img.bbc_img').parent().css('border', '0');
	
	var $th = $(".table_grid thead th");
	var $tr = $(".table_grid tbody tr");
	$tr.each(function(){
		$(this).find('td').each(function(index){
			$(this).attr('data-label', $th.eq(index).text());
		});
	});
});

function fPop_toggle(what)
{
	$(what).slideToggle(150);
}
function fPop_toggle_class(what,cl)
{
	$(what).slideToggle(cl);
}

function fPop_showAtt(what)
{
	$('#attframe').show(0);
	$(what).toggle(0);
}
function fPop_hideAtt()
{
	$('#attframe').hide(0);
}

function fPop_slide(what,speed)
{
	$(".bot_menu_mobile").slideUp(1);
	speed = speed || 150;
	if ($(what).is(":hidden")) {
		$(what).slideToggle(speed);
	}
}

function fPop_slide_sub(what, trigger,topclass)
{
	if (topclass != '') {
		$(topclass).slideUp(0);
		if ($(what).is(":hidden")) {
			$(what).slideToggle(150);
		}
	}
	else {
		$(what).slideToggle(150);
	}
}

function fPop_slide_all(what, speed)
{
	$(what).slideUp(100);
}
function fPop_showImage(what)
{
	$(what).toggle(0);
}
function fPop_showImage_only(what,clas)
{
	$(clas).hide(0);
	$(what).toggle(0);
	$(what + '_title').toggle(0);
}
// The purpose of this code is to fix the height of overflow: auto blocks, because some browsers can't figure it out for themselves.
function smf_codeBoxFix()
{
	var codeFix = $('code');
	$.each(codeFix, function(index, tag)
	{
		if (is_webkit && $(tag).height() < 20)
			$(tag).css({height: ($(tag).height + 20) + 'px'});

		else if (is_ff && ($(tag)[0].scrollWidth > $(tag).innerWidth() || $(tag).innerWidth() == 0))
			$(tag).css({overflow: 'scroll'});

		// Holy conditional, Batman!
		else if (
			'currentStyle' in $(tag) && $(tag)[0].currentStyle.overflow == 'auto'
			&& ($(tag).innerHeight() == '' || $(tag).innerHeight() == 'auto')
			&& ($(tag)[0].scrollWidth > $(tag).innerWidth() || $(tag).innerWidth == 0)
			&& ($(tag).outerHeight() != 0)
		)
			$(tag).css({height: ($(tag).height + 24) + 'px'});
	});
}

// Add a fix for code stuff?
if (is_ie || is_webkit || is_ff)
	addLoadEvent(smf_codeBoxFix);

// Toggles the element height and width styles of an image.
function smc_toggleImageDimensions()
{
	var images = $('img.bbc_img');

	$.each(images, function(key, img)
	{
		if ($(img).hasClass('resized'))
		{
			$(img).css({cursor: 'pointer'});
			$(img).on('click', function()
			{
				var size = $(this)[0].style.width == 'auto' ? '' : 'auto';
				$(this).css({width: size, height: size});
			});
		}
	});
}

// Add a load event for the function above.
addLoadEvent(smc_toggleImageDimensions);

function smf_addButton(stripId, image, options)
{
	$('#' + stripId).append(
		'<a href="' + options.sUrl + '" class="button last" ' + ('sCustom' in options ? options.sCustom : '') + ' ' + ('sId' in options ? ' id="' + options.sId + '_text"' : '') + '>'
			+ options.sText +
		'</a>'
	);
}

