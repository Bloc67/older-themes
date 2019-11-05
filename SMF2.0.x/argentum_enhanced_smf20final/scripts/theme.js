

// MODIFIED: When a user presses quote, put it in the quick reply box (if expanded).
// *** QuickReply object.
// *** QuickReply object.
//		getElementById("quickreplybox").height = '225px'; 

function sQuickReply(oOptions)
{
	this.opt = oOptions;
	this.bCollapsed = this.opt.bDefaultCollapsed;
}

// When a user presses quote, put it in the quick reply box (if expanded).
sQuickReply.prototype.quote = function (iMessageId, xDeprecated)
{
	// Compatibility with older templates.
	if (typeof(xDeprecated) != 'undefined')
		return true;

	if (this.bCollapsed)
	{
		window.location.href = smf_prepareScriptUrl(this.opt.sScriptUrl) + 'action=post;quote=' + iMessageId + ';topic=' + this.opt.iTopicId + '.' + this.opt.iStart;
		return false;
	}
	else
	{
		document.getElementById("quickreplybox").style.height = '225px';
		// Doing it the XMLhttp way?
		if (window.XMLHttpRequest)
		{
			ajax_indicator(true);
			getXMLDocument(smf_prepareScriptUrl(this.opt.sScriptUrl) + 'action=quotefast;quote=' + iMessageId + ';xml', this.onQuoteReceived);
		}
		// Or with a smart popup!
		else
			reqWin(smf_prepareScriptUrl(this.opt.sScriptUrl) + 'action=quotefast;quote=' + iMessageId, 240, 90);

		return false;
	}
}

// This is the callback function used after the XMLhttp request.
sQuickReply.prototype.onQuoteReceived = function (oXMLDoc)
{
	var sQuoteText = '';

	for (var i = 0; i < oXMLDoc.getElementsByTagName('quote')[0].childNodes.length; i++)
		sQuoteText += oXMLDoc.getElementsByTagName('quote')[0].childNodes[i].nodeValue;

	replaceText(sQuoteText, document.forms.postmodify.message);

	ajax_indicator(false);
}

// The function handling the swapping of the quick reply.
sQuickReply.prototype.swap = function ()
{
	document.getElementById(this.opt.sImageId).src = this.opt.sImagesUrl + "/" + (this.bCollapsed ? this.opt.sImageCollapsed : this.opt.sImageExpanded);
	document.getElementById(this.opt.sContainerId).style.display = this.bCollapsed ? '' : 'none';

	this.bCollapsed = !this.bCollapsed;
}


// The purpose of this code is to fix the height of overflow: auto blocks, because some browsers can't figure it out for themselves.
function smf_codeBoxFix()
{
	var codeFix = document.getElementsByTagName('code');
	for (var i = codeFix.length - 1; i >= 0; i--)
	{
		if (is_webkit && codeFix[i].offsetHeight < 20)
			codeFix[i].style.height = (codeFix[i].offsetHeight + 20) + 'px';

		else if (is_ff && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0))
			codeFix[i].style.overflow = 'scroll';

		else if ('currentStyle' in codeFix[i] && codeFix[i].currentStyle.overflow == 'auto' && (codeFix[i].currentStyle.height == '' || codeFix[i].currentStyle.height == 'auto') && (codeFix[i].scrollWidth > codeFix[i].clientWidth || codeFix[i].clientWidth == 0) && (codeFix[i].offsetHeight != 0))
			codeFix[i].style.height = (codeFix[i].offsetHeight + 24) + 'px';
	}
}

// Add a fix for code stuff?
if ((is_ie && !is_ie4) || is_webkit || is_ff)
	addLoadEvent(smf_codeBoxFix);

// Toggles the element height and width styles of an image.
function smc_toggleImageDimensions()
{
	var oImages = document.getElementsByTagName('IMG');
	for (oImage in oImages)
	{
		// Not a resized image? Skip it.
		if (oImages[oImage].className == undefined || oImages[oImage].className.indexOf('bbc_img resized') == -1)
			continue;

		oImages[oImage].style.cursor = 'pointer';
		oImages[oImage].onclick = function() {
			this.style.width = this.style.height = this.style.width == 'auto' ? null : 'auto';
		};
	}
}

// Add a load event for the function above.
addLoadEvent(smc_toggleImageDimensions);

// Adds a button to a certain button strip.
function smf_addButton(sButtonStripId, bUseImage, oOptions)
{
	var oButtonStrip = document.getElementById(sButtonStripId);
	var aItems = oButtonStrip.getElementsByTagName('span');

	// Remove the 'last' class from the last item.
	if (aItems.length > 0)
	{
		var oLastSpan = aItems[aItems.length - 1];
		oLastSpan.className = oLastSpan.className.replace(/\s*last/, 'position_holder');
	}

	// Add the button.
	var oButtonStripList = oButtonStrip.getElementsByTagName('ul')[0];
	var oNewButton = document.createElement('li');
	setInnerHTML(oNewButton, '<a href="' + oOptions.sUrl + '" ' + ('sCustom' in oOptions ? oOptions.sCustom : '') + '><span class="last"' + ('sId' in oOptions ? ' id="' + oOptions.sId + '"': '') + '>' + oOptions.sText + '</span></a>');

	oButtonStripList.appendChild(oNewButton);
}

// Adds hover events to list items. Used for a versions of IE that don't support this by default.
var smf_addListItemHoverEvents = function()
{
	var cssRule, newSelector;

	// Add a rule for the list item hover event to every stylesheet.
	for (var iStyleSheet = 0; iStyleSheet < document.styleSheets.length; iStyleSheet ++)
		for (var iRule = 0; iRule < document.styleSheets[iStyleSheet].rules.length; iRule ++)
		{
			oCssRule = document.styleSheets[iStyleSheet].rules[iRule];
			if (oCssRule.selectorText.indexOf('LI:hover') != -1)
			{
				sNewSelector = oCssRule.selectorText.replace(/LI:hover/gi, 'LI.iehover');
				document.styleSheets[iStyleSheet].addRule(sNewSelector, oCssRule.style.cssText);
			}
		}

	// Now add handling for these hover events.
	var oListItems = document.getElementsByTagName('LI');
	for (oListItem in oListItems)
	{
		oListItems[oListItem].onmouseover = function() {
			this.className += ' iehover';
		};

		oListItems[oListItem].onmouseout = function() {
			this.className = this.className.replace(new RegExp(' iehover\\b'), '');
		};
	}
}

// Add hover events to list items if the browser requires it.
if (is_ie7down && 'attachEvent' in window)
	window.attachEvent('onload', smf_addListItemHoverEvents);
