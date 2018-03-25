			function getWindowSize()
			{
				var x,y;
				if (self.innerHeight) // all except Explorer
				{
					x = self.innerWidth;
					y = self.innerHeight;
				}
				else if (document.documentElement &&
				    document.documentElement.clientHeight)  // Explorer 6 Strict Mode
				{
					x = document.documentElement.clientWidth;
					y = document.documentElement.clientHeight;
				}
				else if (document.body) // other Explorers
				{
					x = document.body.clientWidth;
					y = document.body.clientHeight;
				}
				
				return Array(x,y);			
			}
			
			function getScrollValue()
			{
				var x,y;
				if (self.pageYOffset) // all except Explorer
				{
					x = self.pageXOffset;
					y = self.pageYOffset;
				}
				else if (document.documentElement && document.documentElement.scrollTop)
				// Explorer 6 Strict
				{
					x = document.documentElement.scrollLeft;
					y = document.documentElement.scrollTop;
				}
				else if (document.body) // all other Explorers
				{
					x = document.body.scrollLeft;
					y = document.body.scrollTop;
				}
			
				return Array(x,y);			
			}
			
			function loginToolbarOnResize()
			{
				var windowSize = getWindowSize();
				var scrollValue = getScrollValue();
				var obj = document.getElementById('logintoolbar');
				obj.style.position = "absolute";
				obj.style.left = 0;
				obj.style.top = windowSize[1] + scrollValue[1] - 32/*obj.style.height*/;
				obj.style.width = "100%";
			}