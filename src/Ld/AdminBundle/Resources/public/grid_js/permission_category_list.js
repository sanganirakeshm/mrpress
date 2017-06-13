$(document).ready(function () {
	dTable = $('#permission_category_grid').dataTable({
				"oLanguage": {
                    "sLengthMenu": "Display _MENU_ Records",
                    "sZeroRecords": "<center>No Record Found!</center>",
                    "sInfo": "Showing _START_ to _END_ of _TOTAL_ Records",
                    "sInfoEmpty": "Showing 0 to 0 of 0 records",
                    "sInfoFiltered": "(filtered from _MAX_ total records)"
                },
                responsive: true,
                bJQueryUI: false,
                bProcessing: true,
                bServerSide: true,
                bFilter: true,
                //multipleSelection: true,
                iDisplayLength: recordPerPage,
                sAjaxSource: dataTableJsonPath,
                aoColumns: [
                    {"sName": "chkid","sTitle": "<input type='checkbox' id='checkallGrid'></input>","mDataProp": null, "sWidth": "20px", "sDefaultContent": "<input type='checkbox' ></input>", "bSortable": false, "bSearchable": false},        	
                    {"sName": "id", "bSearchable": false, "bSortable": true},
                    {"sName": "name","bSearchable": true,"bSortable": true},
                    {"sName": "status", "bSearchable": true, "bSortable": true},
                    {"sName": "Action", "bSearchable": false, "bSortable": false},
                ],
                aoColumnDefs: [
							{
								"mRender": function ( data, type, full ) {
							    	return '<input type="checkbox" value="'+ data[1] +'" id="chk_'+ data[1] +'"> ';
								},
							    "aTargets": [ 0 ]
							},
                            {
		                        "mRender": function(data, type, row) {
		                        	
		                            var pcId 	= row[1];
		                            var enabled = row[3];
		                            
		                            var returnData = '<div class="pull-md-left actionicon">';
		                            
		                            if(permissionCategoryEditPath) {
		                            	var editUrl = permissionCategoryEditPath.replace("pcid", pcId);                            
		                            	returnData +='<a class="btn btn-secondary btn-sm" href="'+editUrl+'"><i class="ti-pencil m-r-0-5"></i>Edit</a>';
		                            }
		                            
		                            var activeInactiveIconClass = ''; 
		                            var activeInactiveBtnClass = '';
		                            var activeInactiveBtnText = '';
		                            if (enabled == 'Active') {
		                            	
		                            	enabled = 0;
		                            	activeInactiveIconClass = 'ion-ios-minus-outline';
		                            	activeInactiveBtnClass	= 'btn-warning';
		                            	activeInactiveBtnText 	= 'In Active';
		                            } else {
		                            	
		                            	enabled = 1;                            	
		                            	activeInactiveIconClass = 'ion-ios-checkmark-outline';
		                            	activeInactiveBtnClass 	= 'btn-success';
		                            	activeInactiveBtnText 	= 'Active';
		                            }
		                            
		                            if(permissionCategoryActiveInactivePath) {
		                            	returnData +='<a class="btn '+activeInactiveBtnClass+' btn-sm" href="javascript:void(0)" onclick="ajaxActiveInactive(\''+permissionCategoryActiveInactivePath+'\','+pcId+','+enabled+')"><i class="'+activeInactiveIconClass+' m-r-0-5"></i>'+activeInactiveBtnText+'</a>';
		                            }
		                            if(permissionCategoryDeletePath) {
		                            	returnData +='<a class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="ajaxDeleteRecord(\''+permissionCategoryDeletePath+'\','+pcId+',\'\',\'\')"><i class="ti-trash m-r-0-5"></i>Delete</a>';
		                            }
		                                         
		                            returnData +='</div>';
		                            return returnData;
		                        },
		                        "aTargets": [4]
                            }
				],
                aaSorting: [[1, 'asc']]
		});  
	
	$('#permission_category_grid').dataTable().columnFilter({ 	
		//sPlaceHolder: "head:after",
		aoColumns: [ 	
		            	null,
		            	null,
		            	{ type: "text", sSelector: "#serName" },
		            	null,
		            	null
		            ]
	});
	
	$(".advsearchbar input").addClass("form-control");
    $(".advsearchbar select").addClass("form-control");
    $('#permission_category_grid').on( 'search.dt', function () { $( ".preloader" ).hide(); } ).DataTable();
});