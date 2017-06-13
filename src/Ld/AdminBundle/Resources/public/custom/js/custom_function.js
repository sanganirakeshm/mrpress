$(document).on('click', '#checkallGrid', function() { 
	var selected = new Array();
    $(dTable.fnGetNodes()).find(':checkbox').each(function () {
        $this = $(this);
        if($('#checkallGrid').is(':checked')){
        	$this.prop('checked', true);
        	selected.push($this.val());
        }else{
        	$this.prop('checked', false);
        	selected.pop($this.val());
		}
    });
    // convert to a string
    var mystring = selected.join();
   	//alert(mystring);
});
$(document).ready( function () {
	  
});

function ajaxActiveInactive(ajaxUrl,id,status) {
	
	$.ajax({
 		type: "POST",
  		url: ajaxUrl,
  		data: "id="+id+"&status="+status+"&mode=single",
  		success: function(data) {
  			
  			var obj = $.parseJSON(data);
  			
  			dTable.fnDraw(true);  				
			$('#checkall').prop('checked', false);
			showErrorSuccessMsg(getMessageHtml(obj['msgType'],obj['msg']));
  		}
	});
}

function ajaxDeleteRecord(ajaxUrl, id, confirmBxTitle, confirmBxMsg){
	
	if (confirmBxTitle == '') { confirmBxTitle = 'Confirm!'; }
	if (confirmBxMsg   == '') { confirmBxMsg = 'Are you sure you want to delete record?'; }
	
	$.confirm({
	    title: confirmBxTitle,
	    content: confirmBxMsg,
	    type: 'blue',
	    typeAnimated: true,
	    buttons: {
	    	yes: {
	            text: 'Yes',
	            action: function () {
	            	$.ajax({
		            	
			            type: "POST",
			            url: ajaxUrl,
			            data: "id="+id+"&mode=single",			            
			            success: function(data) {
			            
			            	var obj = $.parseJSON(data);
			      			
			      			dTable.fnDraw(true);  				
			    			$('#checkall').prop('checked', false);
			    			showErrorSuccessMsg(getMessageHtml(obj['msgType'],obj['msg']));			
			            }
		            });	            	
	            }
	        },
	        no: {
	            text: 'No',
	            close: function () {	        
	            }
	        }
	    }
	});
}

function ajaxActiveInactiveAll(ajaxUrl, status, confirmBxTitle, confirmBxMsg){
	
	if (confirmBxTitle == '') { confirmBxTitle = 'Confirm!'; }
	if (confirmBxMsg   == '') { confirmBxMsg = 'Are you sure you want to update status of selected records?'; }	
	
	var selected = new Array();
    $(dTable.fnGetNodes()).find(':checkbox').each(function () {
        $this = $(this);
        if($(this).is(':checked')){
        	selected.push($this.val());
        }
    });
    
    var ids = selected.join();
    if(ids != ''){
    	
    	$.confirm({
    	    title: confirmBxTitle,
    	    content: confirmBxMsg,
    	    type: 'blue',
    	    typeAnimated: true,
    	    buttons: {
    	    	yes: {
    	            text: 'Yes',
    	            action: function () {
    	            	$.ajax({
    	        	 		type: "POST",
    	        	  		url: ajaxUrl,
    	        	  		data: "id="+ids+"&status="+status+"&mode=all",
    	        	  		success: function(data) {        
    	        	  			
    	        	  			var obj = $.parseJSON(data);
    	        	  			
    	        	  			dTable.fnDraw(true);  				
    	        				$('#checkall').prop('checked', false);
    	        				showErrorSuccessMsg(getMessageHtml(obj['msgType'],obj['msg']));
    	        	  		}
    	        		});	            	
    	            }
    	        },
    	        no: {
    	            text: 'No',
    	            close: function () {	        
    	            }
    	        }
    	    }
    	});
    	    	    	
	}else{
		
		showAlert('Alert!','Please select at least one record.');		
	}
}

function ajaxDeleteAll(ajaxUrl, confirmBxTitle, confirmBxMsg){
	
	if (confirmBxTitle == '') { confirmBxTitle = 'Confirm!'; }
	if (confirmBxMsg   == '') { confirmBxMsg = 'Are you sure you want to delete selected records?'; }
	
	var selected = new Array();
    $(dTable.fnGetNodes()).find(':checkbox').each(function () {
        $this = $(this);
        if($(this).is(':checked')){
        	selected.push($this.val());
        }
    });
    var ids = selected.join();
    if(ids != ''){
    	
    	$.confirm({
    	    title: confirmBxTitle,
    	    content: confirmBxMsg,
    	    type: 'blue',
    	    typeAnimated: true,
    	    buttons: {
    	    	yes: {
    	            text: 'Yes',
    	            action: function () {
    	            	$.ajax({
    	    		 		type: "POST",
    	    		  		url: ajaxUrl,
    	    		  		data: "id="+ids+"&mode=all",
    	    		  		success: function(data) {
    	    		  			var obj = $.parseJSON(data);
    	        	  			
    	        	  			dTable.fnDraw(true);  				
    	        				$('#checkall').prop('checked', false);
    	        				showErrorSuccessMsg(getMessageHtml(obj['msgType'],obj['msg']));
    	    		  		}
    	    			});	            	
    	            }
    	        },
    	        no: {
    	            text: 'No',
    	            close: function () {	        
    	            }
    	        }
    	    }
    	});
    	
	}else{
		
		showAlert('Alert!','Please select at least one record.');
	}
}


function showAlert(title,content) {
	$.alert({
	    title: title,
	    content: content,
	});
}

function showErrorSuccessMsg(msgTxtHtml) {
	
	$('#alermeg').html('');
	$('#alermeg').html(msgTxtHtml);	
}

function getMessageHtml(msgType,msg) {
	
	var html = '';
	html += '<div class="alert alert-'+msgType+'-fill alert-dismissible fade in m-b-0" role="alert">';
		html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
			html += '<span aria-hidden="true">x</span>';
		html += '</button>';
		html += '<strong></strong> '+msg;
	html += '</div>';
	
	return html;
}

function hideErrorSuccessMsg() {
	
	$(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });	
}
