// -------------------------------------------------
// artViper's mooSlide 3.2.1 revamp for mooTools 1.2
// -------------------------------------------------
// if you make significant changes, extensiosn etc
// please drop us a copy at admin@artviper.net
// -------------------------------------------------
// more mootools based stuff can be found at:
// ------------- www.artviper.net ------------------

	var mooSlide2 = new Class({
		options:	{
						slideSpeed: 500,
						fadeSpeed:	500,
						effects:	Fx.Transitions.linear,
						toggler:	"myToggle",
						contentID:	 null,
						removeOnClick: true,
						from:		'bottom',
						opacity:	1,
						height:		0,
						isOpen:		0,
						executeFunction: null,
						loadExternal: null,
						request: null						
					},
					
		initialize:	function(options){
			this.setOptions(options);
			if(options['toggler']) this.toggler = options['toggler'];
			if(options['content']) this.content = $(options['content']);
			if(options['height']) this.height = options['height'];
			if(options['opacity']) this.opacity = options['opacity'];
			if(options['slideSpeed']) this.slideSpeed = options['slideSpeed'];
			if(options['fadeSpeed']) this.fadeSpeed = options['fadeSpeed'];
			if(options['removeOnClick']) this.removeOnClick = options['removeOnClick'];
			if(options['from']) this.from = options['from'];
			if(options['executeFunction']) this.executeFunction = options['executeFunction'];
			if(options['loadExternal']) this.loadExternal = options['loadExternal'];
			
			if(this.removeOnClick){	
			$(this.content).addEvent('click',this.clearit.bindWithEvent(this));
			}	
			
			if(this.loadExternal){			
				this.request = new Request({ url: this.loadExternal, method: 'get' });
				this.request.addEvent('success',this.loadExt.bindWithEvent(this));				
				this.request.send();
			}
		
			if(options['effects']){
				this.effects = options['effects'];
			}else{
				this.effects = Fx.Transitions.linear;
			}
			this.content.setStyle('opacity','1');
			this.content.setStyle('visibility','hidden');	
			$(this.content).setStyle('z-index','5000');	
			$(this.toggler).addEvent('click',this.toggle.bindWithEvent(this));
		
		},
		
		clearit: function(){
			
			var myEffects = new Fx.Morph(this.content, {duration: this.fadeSpeed, transition: Fx.Transitions.linear});
			myEffects.start({
   				 'opacity': [1, 0]
			});;
					this.isOpen = 0;
					var p = new Function(this.executeFunction);
					p();
			
		},

		run: function(){
			var top =  window.getHeight().toInt() + window.getScrollTop().toInt();
			var width;
			
			if (document.documentElement && document.documentElement.clientWidth) {
				width=document.documentElement.clientWidth;
			}else if (document.body) {
				width=document.body.clientWidth;
			}
			
			var pad1 = $(this.content).getStyle('padding-left').toInt();
			var pad2 = $(this.content).getStyle('padding-right').toInt();
			
			width =  width - (pad1+pad2+5);
			
			if(!window.ie){
				//width -= 15;
			}
			
			if(!this.isOpen){
			
				$(this.content).setStyle('position','absolute');			
				$(this.content).setStyle('top',top);
				$(this.content).setStyle('height',this.height);
			    $(this.content).setStyle('visibility','visible');
				$(this.content).setStyle('opacity',this.opacity);
				$(this.content).setStyle('width',width);
				$(this.content).setStyle('left','0');
				
				
						
				var end;
				if(this.from == "bottom"){				
					end = top - this.height;
				}else{
					end = window.getScrollTop() - this.height;
				}
				
				if(this.from == "bottom"){
				
					var myEffect = new Fx.Morph(this.content, {duration: this.slideSpeed, transition: this.effects});
					var totalEnd = end+this.height;
				
 					myEffect.start({
   					 'top': [totalEnd, end]
					});
					this.isOpen = 1;
				
				}else{
					
				var myEffect = new Fx.Morph(this.content, {duration: this.slideSpeed, transition: this.effects});
				var totalEnd = end+this.height;
				
 				myEffect.start({
   				 'top': [end, totalEnd]
				});
					
					this.isOpen = 1;
				}
			
			}else{
			var myEffects = new Fx.Morph(this.content, {duration: this.fadeSpeed, transition: Fx.Transitions.linear});
			myEffects.start({
   				 'opacity': [1, 0]
			});
				
				this.isOpen = 0;
				var p = new Function(this.executeFunction);
				p();
			}
		},
		
	
		loadExt: function(response,xml){
				$(this.content).set('html', response);
				
		},
					
		
		toggle: function(e){
			e = new Event(e).stop();
			var top =  window.getHeight().toInt() + window.getScrollTop().toInt();
			var width;
			
			if (document.documentElement && document.documentElement.clientWidth) {
				width=document.documentElement.clientWidth;
			}else if (document.body) {
				width=document.body.clientWidth;
			}
			
			var pad1 = $(this.content).getStyle('padding-left').toInt();
			var pad2 = $(this.content).getStyle('padding-right').toInt();
			
			width =  width - (pad1+pad2+5);
			
			if(!window.ie){
				//width -= 15;
			}
			
			if(!this.isOpen){
		
				$(this.content).setStyle('position','absolute');			
				$(this.content).setStyle('top',top);
				$(this.content).setStyle('height',this.height);
			    $(this.content).setStyle('visibility','visible');
				$(this.content).setStyle('opacity',this.opacity);
				$(this.content).setStyle('width',width);
				$(this.content).setStyle('left','0');
				
				var end;
				if(this.from == "bottom"){				
					end = top - this.height;
				}else{
					end = window.getScrollTop() - this.height;
				}
				
				if(this.from == "bottom"){
				
					var myEffect = new Fx.Morph(this.content, {duration: this.slideSpeed, transition: this.effects});
					var totalEnd = end+this.height;
				
 					myEffect.start({
   					 'top': [totalEnd, end]
					});
					this.isOpen = 1;
				
				}else{
					
				var myEffect = new Fx.Morph(this.content, {duration: this.slideSpeed, transition: this.effects});
				var totalEnd = end+this.height;
				
 				myEffect.start({
   				 'top': [end, totalEnd]
				});
					
					this.isOpen = 1;
				}
			
			}else{
			var myEffects = new Fx.Morph(this.content, {duration: this.fadeSpeed, transition: Fx.Transitions.linear});
			myEffects.start({
   				 'opacity': [1, 0]
			});
				
				this.isOpen = 0;
				var p = new Function(this.executeFunction);
				p();
			}
		}
	})

mooSlide2.implement(new Options);
mooSlide2.implement(new Events);