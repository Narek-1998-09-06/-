(function( $ ) {
	
	$(".importer-wrap .exportToggler").click(function(){
		$(".importer-wrap #exportProductsForm").slideToggle();
		$(".importer-wrap .exportTableWrapper").slideToggle();
		$(".importer-wrap .downloadToExcel").slideToggle();
	});
				
	
	$('.importer-wrap #upload').attr('disabled','disabled');
    $(".importer-wrap #file").change(function () {
        var fileExtension = ['xls', 'xlsx'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only format allowed: "+fileExtension.join(', '));	
			$('.importer-wrap #upload').attr('disabled','disabled');
        }else{
			$('.importer-wrap #upload').removeAttr('disabled');
			$("#woo_importer").submit();
		}
    });

	$('.importer-wrap .nav-tab-wrapper a').click(function(e){
		if($(this).hasClass("premium") ){
			$(".premium_msg").slideDown('slow');
			$('.importer-wrap').removeClass('loading');
		}		
	});	

	
	$(".importer-wrap #woo_importer").on("submit", function (e) {		
		e.preventDefault();	
				var data = new FormData();
				$.each($('#file')[0].files, function(i, file) {
					data.append('file', file);
					
				});	
				data.append('_wpnonce',$("#_wpnonce").val());
								
				$.ajax({
					url: $(this).attr('action'),
					data: data,
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					beforeSend: function() {
						$("html, body").animate({ scrollTop: 0 }, "slow");
						$('.importer-wrap').addClass('loading');	
					},					
					success: function(data){
						$('body').html(data);
						$("#woo_importer").fadeOut();	
												
					}
				});			
			});	
			//drag and drop
			$('.importer-wrap .draggable').draggable({cancel:false});
			$( ".importer-wrap .droppable" ).droppable({
			  drop: function( event, ui ) {
				$( this ).addClass( "ui-state-highlight" ).val( $( ".ui-draggable-dragging" ).val() );
				$( this ).attr('value',$( ".ui-draggable-dragging" ).attr('key')); //ADDITION VALUE INSTEAD OF KEY	
				$( this ).attr('placeholder',$( ".ui-draggable-dragging" ).attr('value')); 				
				$( ".ui-draggable-dragging" ).css('visibility','hidden'); //ADDITION + LINE
				$( this ).css('visibility','hidden'); //ADDITION + LINE
				// alert($(this).attr('key'));
				$( this ).parent().css('background','#90EE90');
			  }
			 
			});	
		
		
			$(".importer-wrap #woo_process").submit(function(e) {
				e.preventDefault();
				if($("input[name='post_title']").val() !='' ){
					$.ajax({
						url: window.location.href,
						data:  $(this).serialize(),
						type: 'POST',
						beforeSend: function() {	
							$("html, body").animate({ scrollTop: 0 }, "slow");
							$('.importer-wrap').addClass('loading');	
						},						
						success: function(data){
							$('body').html(data);
							$(".importer-wrap #woo_importer").hide().delay(5000).fadeIn();
							$(".importer-wrap .rating").delay(5000).fadeIn();
						}
					});
				}else alert('Title Selection is Mandatory.');
	
			});	
		

			$(".importer-wrap #exportProductsForm").on('submit',function(e) {
				e.preventDefault();
				//if checkbox is checked
				$(".importer-wrap .fieldsToShow").each(function(){
					if($(this).is(':checked')){
					}else localStorage.setItem($(this).attr('name') ,$(this).attr('name') );
				});	
				
				$.ajax({
					url: $(this).attr('action'),
					data:  $(this).serialize(),
					type: 'POST',
					beforeSend: function() {									
						$('.importer-wrap').addClass('loading');		
					},						
					success: function(response){
						
						$(".importer-wrap #exportProductsForm").hide();
						$(".importer-wrap #selectTaxonomy").hide();	
						
						$(".resultExport").slideDown().html($(response).find(".resultExport").html());
							
								//if checkbox is checked
								$(".importer-wrap .fieldsToShow").each(function(){									
									if (localStorage.getItem($(this).attr('name')) ) {
										$(this).attr('checked', false);
									}//else $(this).attr('checked', false);							
									localStorage.removeItem($(this).attr('name'));	
								});	
									
									var i=0;
									$(".importer-wrap input[name='total']").val($(".importer-wrap .totalPosts").html());
									$(".importer-wrap input[name='start']").val($(".importer-wrap .startPosts").html());							
									total = $(".importer-wrap input[name='total']").val();	
									start = $(".importer-wrap input[name='start']").val();
									rowcount = $('#toExport >tbody >tr').length;									
									progressBar(start,total) ;

								function woopeiExportProducts() {
									var total = $(".importer-wrap input[name='total']").val();
									var start = $(".importer-wrap input[name='start']").val() * i;
									
									if(parseInt($(".importer-wrap .totalPosts").html() , 10) <=500){
											$(".importer-wrap input[name='posts_per_page']").val($(".importer-wrap .totalPosts").html());
									}else $(".importer-wrap input[name='posts_per_page']").val($(".importer-wrap .startPosts").html());
									
									dif = total- start;
									
									if( $('#toExport >tbody >tr').length >= total ){
										
										$('.importer-wrap #myProgress').delay(10000).hide('loading');
										
										$.getScript(woopei.exportfile, function() {
											$("#toExport").tableExport();
											$('.xlsx').trigger('click');										  
										});	
										
										$("body").find('#exportProductsForm').find("input[type='number'],input[type='text'], select, textarea").val('');
										$('.importer-wrap .message').html('Job Done!');
										$('.importer-wrap .message').addClass('success');
										$('.importer-wrap .message').removeClass('error');
										
										$(".importer-wrap .rating").delay(5000).fadeIn();
										
									}else{	
									
										var dif = total - start;
										if(parseInt(total,10)> 500 && parseInt(dif,10) <=500 ){
											$(".importer-wrap  input[name='posts_per_page']").val(dif);
										} 
										
										$.ajax({
											url: woopei.ajaxUrl,
											data: $(".importer-wrap #exportProductsForm").serialize(),
											type: 'POST',
											beforeSend: function() {
												$("html, body").animate({ scrollTop: 0 }, "slow");	
												$('.importer-wrap').removeClass('loading');
											},						
											success: function(response){	
												
												$(".importer-wrap .tableExportAjax").append(response);
												i++;
												start = $(".importer-wrap input[name='start']").val() * i;
												
												$(".importer-wrap  input[name='offset']").val(start);
												
												var offset = $(".importer-wrap  input[name='offset']").val();													
												console.log("dif "+ dif+" i: "+ i + " offset: " + offset + " start: " + start+ " total: " + total);
												
												progressBar(start,total) ;	
											},complete: function(response){											
													woopeiExportProducts();	
											}
										});
									}
								}
								woopeiExportProducts();								
					}
					});	
			});	


			
			function progressBar(start,total) {
				var width = (start/total) * 100;
				var elem = document.getElementById("myBar");   
				if (start >= total-1) {
				  //clearInterval(id);
				  elem.style.width = '100%'; 
				} else {
				  start++; 
				  elem.style.width = width + '%'; 
				}
			}
			
		$(".importer-wrap .premium").click(function(e){
			e.preventDefault();
			$("#woopeiPopup").slideDown();
		});

		$("#woopeiPopup .close").click(function(e){
			e.preventDefault();
			$("#woopeiPopup").fadeOut();
		});		

		var modal = document.getElementById('woopeiPopup');

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}			
			
		
})( jQuery );