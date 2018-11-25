var selectedticket=null;
$(function() 
{
	"use strict";
	console.log("Here");
	google.charts.load('current', {
		callback: drawChart,
		packages: ['table']
	});	
});
function drawChart() 
{
	console.log("Drawing Chart");
	LoadTicketDataTable();
}
function OnDeleteTicket()
{
	if(selectedticket != null)
		DeleteTicket(selectedticket);
	selectedticket = null;
}
function HandleData(jsonData) 
{
/*	var title = jsonData.title;
	jsonData = jsonData.data;
	console.log(jsonData);
	$("#title").html(title);
	PreprocessData(jsonData);*/
	datatable = new google.visualization.DataTable(jsonData);
	var view = new google.visualization.DataView(datatable);

	for(var i=0;i<jsonData.rows.length;i++)
	{
		var old = jsonData.rows[i].c[6].v;
		var deadline = jsonData.rows[i].c[7].v;
		var path = jsonData.rows[i].c[10].v;
		var popup='deletepopup';
		var link ='<a href="#'+popup+'" data-rel="popup" data-transition="pop" onclick="selectedticket='
		 link =  link+"'"+path+"'"+'">Delete</a>';
		jsonData.rows[i].c[9].v = link;
		if(deadline.length > 0)
		{
			if(old > deadline)
			{
				datatable.setProperty(i, 0, 'style', 'color: red;');
				datatable.setProperty(i, 1, 'style', 'color: red;');
				
				datatable.setProperty(i, 7, 'style', 'color: red;');
				datatable.setProperty(i, 2, 'style', 'color: red;');
				datatable.setProperty(i, 3, 'style', 'color: red;');
				datatable.setProperty(i, 4, 'style', 'color: red;');
				
				datatable.setProperty(i, 5, 'style', 'color: red;');
				datatable.setProperty(i, 6, 'style', 'color: red;');
				datatable.setProperty(i, 7, 'style', 'color: red;');
				datatable.setProperty(i, 8, 'style', 'color: red;');
			}
			if(old < 0)
			{
				datatable.setProperty(i, 0, 'style', 'color: silver;');
				datatable.setProperty(i, 1, 'style', 'color: silver;');
				
				datatable.setProperty(i, 7, 'style', 'color: silver;');
				datatable.setProperty(i, 2, 'style', 'color: silver;');
				datatable.setProperty(i, 3, 'style', 'color: silver;');
				datatable.setProperty(i, 4, 'style', 'color: silver;');
				
				datatable.setProperty(i, 5, 'style', 'color: silver;');
				datatable.setProperty(i, 6, 'style', 'color: silver;');
				datatable.setProperty(i, 7, 'style', 'color: silver;');
				datatable.setProperty(i, 8, 'style', 'color: silver;');
			}
		}
		
	}
	/*
		var color = '';
		if(error != null)
			color = 'color:red;';
		
		datatable.setProperty(i, 0, 'style', 'width:15%;');
		datatable.setProperty(i, 1, 'style', 'text-align: left;width:35%;');	
		datatable.setProperty(i, 2, 'style', 'text-align: left;width:10%;');	
		datatable.setProperty(i, 4, 'style', 'width:5%;');	
		datatable.setProperty(i, 5, 'style', 'text-align: left;width:25%;'+color);	
		datatable.setProperty(i, 6, 'style', 'width:10%;');			
	}*/
	
	//<a href="#" onclick="OnDeleteTicket('.$ticket->path.');" >Delete</a>
	var showRowNumber = true;
	if(details==1)
	{
		view.setColumns([0,1,2,3,4,5,6,7,8,9]);
		showRowNumber = true;
	}
	else
	{
		view.setColumns([0,2,3,4,8,9]);
		showRowNumber = false;
	}

	var options = {
		showRowNumber: showRowNumber,
		width: '100%', 
		height: '100%',
		allowHtml:true,
		sortColumn: 5,
		sortAscending: false,
		sort: 'enable'
	};
	// Instantiate and draw our chart, passing in some options.
	var chart = new google.visualization.Table(document.getElementById('chart_div'));
	//chart.draw(datatable, options);
	chart.draw(view, options);
}
function DeleteTicket(path)
{
	$.ajax(
	{	url: 'src/data_delete.php',
			data: {path:path},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() 
			{
				
			},
			complete: function() 
			{
				
			},
			success: function (result) 
			{
				console.log(result);
				ShowDeletedSuccess();
			},
			error: function (request,error) 
			{
				 // This callback function will trigger on unsuccessful action               
				 ShowNetworkError();
			}
	}); 
}

function LoadTicketDataTable()
{
	$.ajax(
	{	url: 'src/data_admin.php',
			data: {},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() 
			{
				
			},
			complete: function() 
			{
				
			},
			success: function (result) 
			{
				HandleData(result);
				
			},
			error: function (request,error) 
			{
				 // This callback function will trigger on unsuccessful action               
				 alert('Network error has occurred please try again!');
			}
	}); 
}
function ShowDeletedSuccess()
{
	setTimeout( function(){ 
		$( "#deletsuccess_popup" ).popup( );
		$( "#deletsuccess_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#deletsuccess_popup" ).popup( "close" );window.location = "admin.php";}, 2000 );
	}, 500 );
}
function ShowNetworkError()
{
	setTimeout( function(){ 
		$( "#error_popup" ).popup( );
		$( "#error_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#error_popup" ).popup( "close" );}, 2000 );
	}, 500 );
}
