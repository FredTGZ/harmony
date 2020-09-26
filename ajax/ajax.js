			/*******************************************************************************
			 * HARMONY LIBRARY - AJAX SUPPORT SCRIPT
			 ******************************************************************************/ 

			// Ajax Class
			AjaxService = function(id, name, state)
			{
				this.id = id;
				this.name = name;
				this.state = state;
			}
			
			// Ajax Class
			Ajax = function()
			{
				var DebugMode = false;
				var services = new Array();
				
				//
				this.setServiceState = function(serviceID, serviceName, state)
				{
					var retID = serviceID;
			
					if (serviceID == null) {
						if (state == "UNINITIALIZED") {
							retID = services.length; 
							services[retID] = new AjaxService(serviceID, serviceName, state);
						}
						else {
						}
					}
					else {
						services[serviceID].state = state;
					}
					
					this.updateServiceInfos();
			
					return(retID);
				}
				
				//
				this.updateServiceInfos = function()
				{
					var element = document.getElementById('AjaxServiceState');
					if (element != null) {
						element.innerHTML = "";
			
						for (index in services) {
							if (services[index].state != "COMPLETED")
								element.innerHTML += '<img src="images/wait.gif" title="'+services[index].name+': '+services[index].state+'">';
						}
					}
				}
			
				//
				this.DebugMessage=function(Service, Message)
				{
					alert("Ajax Service\n----------------------------------------------------\nService: ["+Service+"]\n"+Message);
				}
				
				//
				this.createRequest = function()
				{
					var request;
					// Create the HTTP request object
					if (window.XMLHttpRequest) {
					    return new XMLHttpRequest();     //  Firefox, Safari, ...
					}
					else if (window.ActiveXObject) {
						try {
							request = new ActiveXObject("Msxml2.XMLHTTP");
							return request;
						} catch (e) {
							request = new ActiveXObject("Microsoft.XMLHTTP");
							return request;
						}
					}
					
				}
			
				//
				this.createXMLParser=function()
				{
					if (window.DOMParser)
					{
						return new DOMParser();
					}
					else // Internet Explorer
					{
						xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
						xmlDoc.async="false";
						return xmlDoc;
					} 	
				}
				
				// Call Service function
				this.CallService = function(Service				/* Service URL */,
											Data				/* Data Array */,
											CallbackFunction	/* Callback function */)
				{
					var request = this.createRequest();
					var serviceID = this.setServiceState(null, Service, "UNINITIALIZED");
			
					request.open("POST", Service, true); 	 
					request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					request.setRequestHeader("AjaxServiceName", Service);
					request.setRequestHeader("AjaxServiceID", serviceID);
					var InputData = '';
				    var debug = '';
				    
					if (Data == null) InputData = '';
					else if (isArray(Data)) {
						for (i in Data) {
							InputData += 'param'+i+"="+Data[i]+"&";
							debug +='param'+i+": ["+Data[i]+"]\n";
						}
					}
					else {
						InputData += 'param0='+Data+"&";
						debug +="param0: ["+Data+"]\n";
					}
				    
					if (this.DebugMode) {
						this.DebugMessage(Service, debug);
					}
					
					request.send(InputData); 
			
					request.onreadystatechange=function()
				    {
						var serviceName = request.getResponseHeader("AjaxServiceName");
						var serviceID = request.getResponseHeader("AjaxServiceID");
						
				        switch (request.readyState)
				        {
					        case 0:		// UNINITIALIZED
					        	break;
					        case 1:		// LOADING
				        		Ajax.setServiceState(serviceID, serviceName, "LOADING");
					        	break;
					        case 2:		// LOADED
				        		Ajax.setServiceState(serviceID, serviceName, "LOADED");
					        	break;
					        case 3:		// INTERACTIVE
				        		Ajax.setServiceState(serviceID, serviceName, "INTERACTIVE");
					        	break;
					        case 4:		// COMPLETED
				        		Ajax.setServiceState(serviceID, serviceName, "COMPLETED");
					        		
								switch (request.status)
								{
									case 404:
										alert(request.status + ": Unknown service ! " + request.statusText);
										break;
									case 200:	// Success
										var cType = request.getResponseHeader("Content-Type");
						                var response = "";
						                cType = cType.split(';')[0];
						                
						                var debug = "Response type: ["+cType+"]\n";
						                
										if (cType == 'text/xml') {
											if(!request.responseXML) alert("request.responseXML=null");
											response = request.responseXML;
											if(!response.length) response = request.responseText;
											var parser;
			
											if (window.DOMParser)
												parser =  new DOMParser();
											else // Internet Explorer
											{
												parser = new ActiveXObject("Microsoft.XMLDOM");
												parser.async = "false";
											} 	
											var docXML= request.responseXML.getElementsByTagName('root')[0];
											response = docXML;
										}
										else if (cType == 'text/html') response = request.responseText;
										debug += "Response length: "+response.length+" bytes";
										if (Ajax.DebugMode) Ajax.DebugMessage(Service, debug);
										
										CallbackFunction(response);
										break;
									case 0:
										break;
									default:
										alert("Error: returned status code " + request.status + " " + request.statusText);
										break;
								}
								break;
							default:
								break; 
						}
					};
				};
			}
			
			// Convert XML string to array
			AjaxResponse2Array = function(xmlDoc)
			{
				var AjaxArray = Array();
				
				if(xmlDoc.nodeName == "root") {
					//alert(xmlDoc);
					var provider = xmlDoc.childNodes[1];

					if (provider.textContent == "Harmony Library") {
						var tagName = null;
						var tagContent = null;
						var data = xmlDoc.childNodes[3];

						for(i=1; i<data.childNodes.length; i+=2) {
							tagName = data.childNodes[i].nodeName;
							tagContent = data.childNodes[i].textContent;
							AjaxArray[tagName] = tagContent;
						}
					}
				}
			
				return AjaxArray;
			}
			
			// Determine if object is an array
			isArray = function (obj) {
			    return obj.constructor == Array;
			}
			
			var Ajax = new Ajax();