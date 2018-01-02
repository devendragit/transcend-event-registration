jQuery(document).ready(function($) {

	init_dataTable();

	$('.select-redirect').on('change', function(e) {
		e.preventDefault();
		var url = $('option:selected', this).attr('data-url');
		if(url){
			window.location.href = url;
		}
	});	

	$('.ts-tabs').tabs();
});

var init_dataTable = function() {
	var tables = jQuery('.ts-data-table');
	if(tables.length > 0) {
		tables.each(function() {
			var table_el    = jQuery(this);
			var table_id 	= table_el.attr('id');
			var dom 		= table_el.attr('data-dom') !=null ? table_el.attr('data-dom') : 'frt<"table-footer clearfix"p>';
			var orderby 	= table_el.attr('data-orderby') !=null ? table_el.attr('data-orderby') : null;
			var sort 		= table_el.attr('data-sort') !=null ? table_el.attr('data-sort').toString() : 'desc';
			var length 		= table_el.attr('data-length') !=null ? table_el.attr('data-length') : 25;
			var filter 		= table_el.attr('data-filter') !=null || table_el.attr('data-range') !=null ? true : false;
			var reorder 	= table_el.attr('data-reorder') !=null ? true : false;
			var colfilter 	= table_el.attr('data-colfilter') !=null ? table_el.attr('data-colfilter') : null;
			var exportcol 	= table_el.attr('data-exportcol') !=null ? table_el.attr('data-exportcol') : null;
			var exporttitle = table_el.attr('data-exporttitle') !=null ? table_el.attr('data-exporttitle') : '';
			var multiorder 	= table_el.attr('data-multiorder') !=null ? table_el.attr('data-multiorder') : null;
			var trimtrigger = table_el.attr('data-trimtrigger') !=null ? table_el.attr('data-trimtrigger') : null;
			var trimtarget 	= table_el.attr('data-trimtarget') !=null ? table_el.attr('data-trimtarget') : null;
			var titleswitch = table_el.attr('data-titleswitch') !=null ? table_el.attr('data-titleswitch') : null;
			var options = {
				aaSorting : [],
				bLengthChange : true,
				bFilter : filter,
				bInfo : true,
				iDisplayLength : parseInt(length),
				aLengthMenu : [[10, 25, 50, -1], [10, 25, 50, 'All']],
				dom : dom,
				language: {
					sSearch : '',
					sSearchPlaceholder : 'Search',
					lengthMenu: '_MENU_ Records per page',
				},
				rowReorder: reorder,
			};
			if(orderby!=null){
				order = {
					order : [[orderby, sort]]
				};
				jQuery.extend(options, order);
			}
			else if(multiorder!=null){
				order = {
					order : JSON.parse(multiorder)
				};
				jQuery.extend(options, order);
			}
			if(exportcol!=null) {
				buttons = {
					buttons : [
						{
							extend: 'print',
							title: exporttitle,
							exportOptions: {
								columns: [exportcol],
							},
							action: function (e, dt, node, config) {
								config.title = table_el.attr('data-exporttitle');
								jQuery.fn.dataTable.ext.buttons.print.action.call(this, e, dt, node, config);
							},
							customize: function(win) {
								jQuery(win.document.body).find('table').css({
									'font-size' : '9pt',
								});
								jQuery(win.document.body).find('h1').css({
									'text-align' : 'center',
									'font-size' : '18pt',
									'font-weight' : 'bold',
								});
							},
						},
						{
							extend: 'pdf',
							title: exporttitle,
							exportOptions: {
								columns: [exportcol]
							},
							customize: function(doc) {
								doc.content[1].table.widths =
									Array(doc.content[1].table.body[0].length + 1).join('*').split('');
								doc.styles.tableHeader.alignment = 'left';
							},
							action: function (e, dt, node, config) {
								config.title = table_el.attr('data-exporttitle');
								jQuery.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, node, config);
							},
						}
					],
				};
				jQuery.extend(options, buttons);
			}
			var table = table_el.DataTable(options);
			table_el.parent().prepend('<div id="'+table_id+'_column-filter" class="dataTables_column-filter"></div>');
			if(colfilter!=null) {
				jQuery.each(JSON.parse(colfilter), function( index, value ) {
					var column = table.column(value);
					if(jQuery(column.footer()).hasClass('hidden')==false) {
						var select = jQuery('<select id="filter-'+value+'"><option value="">'+jQuery(column.footer()).text()+'</option></select>')
							.appendTo(jQuery('#'+table_id+'_column-filter'))
							.on('change', function() {
								var val = jQuery.fn.dataTable.util.escapeRegex(
									jQuery(this).val()
								);
								column.search( val ? '^'+val+'$' : '', true, false ).draw();
								if(trimtrigger!=null && trimtarget!=null) {
									if(index==trimtrigger){
										trim_filter(trimtarget, table, table_el, val);
									}
								}
								if(index==titleswitch) {
									var titleswitch_val = jQuery(this).find('option:selected').val();
									jQuery('#'+table_id).attr('data-exporttitle', titleswitch_val+' Registrations');
									if(table_id=='entries-list') {
										if(titleswitch_val=='Studio') {
											table.order([[2, 'asc']]).draw();
										}
										else if(titleswitch_val=='Individual') {
											table.order([[3, 'asc']]).draw();
										}
										else {
											table.order([[orderby, sort]]).draw();
										}
									}
								}
							});
						if(jQuery(column.footer()).attr('data-sort')=='true'){
							column.data().unique().sort().each( function ( d, j ) {
								d = d.replace(/(<([^>]+)>)/ig,"");
								select.append( '<option value="'+d+'">'+d+'</option>' )
							});
						}
						else {
							column.data().unique().each( function ( d, j ) {
								d = d.replace(/(<([^>]+)>)/ig,"");
								select.append( '<option value="'+d+'">'+d+'</option>' )
							});
						}
					}
				});
			}
		    jQuery('#min, #max').keyup(function() {
		        table.draw();
		    });
			jQuery.fn.dataTable.ext.search.push(
			    function( settings, data, dataIndex ) {
			        var min = parseInt( jQuery('#min').val(), 10 );
			        var max = parseInt( jQuery('#max').val(), 10 );
			        var num = parseFloat( data[0] ) || 0;
			        if ( ( isNaN( min ) && isNaN( max ) ) ||
			             ( isNaN( min ) && num <= max ) ||
			             ( min <= num   && isNaN( max ) ) ||
			             ( min <= num   && num <= max ) )
			        {
			            return true;
			        }
			        return false; 
			    }
			);		
		});
	}
}