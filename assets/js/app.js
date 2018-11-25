var changepassword = 0;
var tickets;
var selectedticket=-1;
function OnChangePasswordClicked()
{
	console.log(changepassword);
	if(changepassword)
	{
		$('#lopassword').hide();
		$('#opassword').hide();
		$('#lnpassword').hide();
		$('#npassword').hide();
		$("#changepasswordlink").text("Change Password");
		changepassword = 0;
		$('#submit').attr('value', 'Login');
	}
	else
	{
		$('#opassword').val('');
		$('#lopassword').show();
		$('#opassword').show();
		
		$('#npassword').val('');
		$('#lnpassword').show();
		$('#npassword').show();
		$("#changepasswordlink").text("Hide");
		changepassword = 1;
		$('#submit').attr('value', 'Change Password');		
	}
	/*$("#password").val(''); 
	$("#fieldset").append('<div id="npass"></pass>');
	$("#fieldset").append('<div data-role="fieldcontain">'+   
                         '<label for="opassword">Old password:</label>'+
                         '<input  value="" name="opassword" autocomplete="new-password" id="opassword" data-mini="true"/>')+
						 '</div>';
					 
	$("#fieldset").append('<div data-role="fieldcontain">'+ 
                         '<div data-role="fieldcontain">'+	
                         '<label for="npassword">New password:</label>'+
                         '<input  value="" name="npassword" autocomplete="new-password" id="npassword" data-mini="true"/>')+
                         '<div>';						 
	changepassword = 1;*/
	//$("#changepasswordlink").remove();
}
function PopulateListView()
{
	$("#listview").empty();
	
	if(tickets.length == 0)
	
	$('#listview').append(
			'<li style="text-align: center;" class="bar" data-role="list-divider"> There are no tickets for you</li>');
			
	for(var i=0;i<tickets.length;i++)
	{
		var activatedby=tickets[i].activatedby;
		var activatedon=ConvertJsDateFormat(tickets[i].activated);
		var title=tickets[i].title;
		var start = moment(tickets[i].activated);
		var end = moment(new Date());
		var diff = end.diff(start, "days");
		var waitstate = tickets[i].waitstate;
		var statename=tickets[i].statename;
		var revertable=tickets[i].revertable;
		var number=tickets[i].number;
		var type=tickets[i].type.toLowerCase();
		
		var col='green';
		if(diff > tickets[i].days)
			col='red';
		if(tickets[i].days == -1)
		{
			col='black';
		}
		var msg = diff+" days old";
		if(diff == 0)
			msg = "Today";

			
		
		if(waitstate == false)
		{
			/*$('#listview').append(
			'<li class="bar" data-role="list-divider">From '+activatedby+'<span style="float:right;">'+msg+'</li>'+
			'<li style="font-size:5px;" data-role="list-divider"><span style="font-size:5px">'+activatedon+'</span></li>'+
			'<li><a href="#closepopup" data-rel="popup" data-transition="pop" onclick="selectedticket='+i+';">'+
			'<img src="css/images/planner.png" alt="France" class="ui-listview-item-icon ui-corner-none">'+title+
			'<span class="ui-listview-item-count-bubble"style="font-size:8px;margin-right:15px;color:'+col+'">'+statename+'</span>'+
			'</a>'+
			
			'</li>');*/
			var popup='closepopup';
			if(revertable)
				popup='revertpopup';
			
			$('#listview').append(
			'<li class="bar" data-role="list-divider">'+statename+'<span style="float:right;">'+activatedon+
			'</li>'+
			'<li style="font-size:5px;" data-role="list-divider"><span style="font-size:8px">From '+activatedby+'</span></li>'+
			'<li><a href="#'+popup+'" data-rel="popup" data-transition="pop" onclick="selectedticket='+i+';">'+
			'<img src="assets/css/images/'+type+'.png" alt="" class="ui-listview-item-icon ui-corner-none">'+title+" "+number+
			'<span class="ui-listview-item-count-bubble"style="font-size:8px;margin-right:15px;color:'+col+'">'+msg+'</span>'+
			'</a>'+
			'</li>');
			
		}
		
	}
	for(var i=0;i<tickets.length;i++)
	{
		var activatedby=tickets[i].activatedby;
		var activatedon=ConvertJsDateFormat(tickets[i].activated);
		var title=tickets[i].title;
		var statename=tickets[i].statename;
		var start = moment(tickets[i].activated);
		var end = moment(new Date());
		var diff = end.diff(start, "days");
		var waitstate = tickets[i].waitstate;
		var nextaction = tickets[i].nextaction;
		var col='black';
		if(diff > tickets[i].days)
			col='red';
		console.log(tickets[i]);
		if(waitstate != false)
		{
			/*$('#listview').append(
			'<li class="taskclosedbar" data-role="list-divider">Closed<span style="float:right;"></float></li>'+
			'<li><a href="#reopenpopup" data-rel="popup" data-transition="pop" onclick="selectedticket='+i+';">'+
			'<img src="css/images/planner.png" alt="France" class="ui-listview-item-icon ui-corner-none">'+title+
			'<span class=""style="font-size:8px;margin-right:15px;color:'+col+'">'+'</span>'+
			'</a>'+
			'</li>');*/
			if(nextaction == 'next')
			{
				$('#listview').append(
				'<li class="taskclosedbar" data-role="list-divider">'+title+' Closed'+
					'<a width="20px" data-role="button" data-mini="true"  style="float:right;color:white;"  href="#reopenpopup" data-rel="popup" data-transition="pop" onclick="selectedticket='+i+';"><span style="font-size:8px;">Undo</span></a>'+	
				'</li>');
			}
			else
			{
				$('#listview').append(
				'<li class="taskrevertbar" data-role="list-divider">'+title+' Reverted'+
					'<a width="20px" data-role="button" data-mini="true"  style="float:right;color:white;"  href="#reopenpopup" data-rel="popup" data-transition="pop" onclick="selectedticket='+i+';"><span style="font-size:8px;">Undo</span></a>'+	
				'</li>');
				
			}
		}
	}
	$("#listview").listview("refresh");
}
function ReadTickets(user)
{
	console.log("Reading Tickets");
	
	$.ajax({url: 'src/data_tickets.php',
			data: {
			},
			data: {cmd : 'gettickets', user : user},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				//window.location = "login.php";
				console.log(result.error);
				if(result.error.length == 0)
				{
					tickets = result.data;
					PopulateListView();
				}
				else
				{
					console.log("Response error");
					tickets = result.data;
					PopulateListView();
					ShowNetworkError();				
				}
					
			},
			error: function (request,error) {
				ShowNetworkError();	
				return ; 
			}
		}); 
	
}
$(document).on('click', '#logoutbtn', function()
{
	$.ajax({url: 'src/data_logout.php',
			data: {
			},
			//data: {action : 'authorization', formData : $('#check-user').serialize()},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				ShowLogoutSuccess();
				//window.location = "login.php";
			},
			error: function (request,error) {
				ShowNetworkError();	
			}
		}); 
})
function ConvertJsDateFormat(datestr)
{
	var d = new Date(datestr);
	if(d == 'Invalid Date')
		return '';
	
	dateString = d.toUTCString();
	dateString = dateString.split(' ').slice(0, 4).join(' ').substring(5);
	return dateString;
}
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
$(document).on('click', '#createverify', function() 
{
	var task=$('#taskname').val();
	if(task.length == 0)
		task = "-";
	
	var user=$("#assignee").find(":selected").text();
	
	var startdate = "";
	if($('#date').val().length == 0)
		startdate = 'Today';
	else
		startdate = $('#date').val();
	
	var endate = "";
	if($('#edate').val().length == 0)
		endate = '';
	else
		endate  = $('#edate').val();
	
	var text ="Creating task "+task+"<br> for <strong>"+capitalizeFirstLetter(user)+"</strong> starting <strong>"+ConvertJsDateFormat(startdate)+"</strong>";
	if(endate == '')
		text = text + " with no deadline";
	else
		text = text + " with a deadline of <strong>"+ConvertJsDateFormat(endate)+"</strong>";
	
	
	$('#ctask').html(text);
})

$(document).on('click', '#createtask', function() 
{
	if($('#taskname').val().length > 0 && $('#assignee').val().length > 0)
	{ 	 
		// Send data to server through the Ajax call
		// action is functionality we want to call and outputJSON is our data
		console.log( $("#assignee").find(":selected").text() );
		
		$.ajax({url: 'src/data_create.php',
			data: {taskname :  $('#taskname').val(),
				   assignee :  $("#assignee").find(":selected").text().toLowerCase(),
				   date :  $('#date').val(),
				   edate :  $('#edate').val()
			},
			//data: {action : 'authorization', formData : $('#check-user').serialize()},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				if(result.error.length == 0)
				{
					ShowTaskCreated();
				}
				else
				{
					console.log(result.error[0]);
					ShowTaskCreatError();
				}
			},
			error: function (request,error) {
				console.log("Network Error");
				ShowNetworkError();
			}
		});
	} 
	else 
	{
		ShowHalfFilledFormError();
		
	}          
	return true; // cancel original event to prevent form submitting
	
})

$(document).on('click', '#back', function() 
{
	window.location = "index.php";
});

$(document).on('click', '#submit', function() 
{ 
	// catch the form's submit event
	if($('#username').val().length > 0 && $('#password').val().length > 0)
	{ 
		console.log(changepassword);
		//userHandler.username = $('#username').val();
	 
		// Send data to server through the Ajax call
		// action is functionality we want to call and outputJSON is our data
		$.ajax({url: 'src/data_auth.php',
			data: {username :  $('#username').val(),
				   password :  $('#password').val(),
				   opassword :  $('#opassword').val(), 
                   npassword :  $('#npassword').val(),
				   changepassword : changepassword
			},
			//data: {action : 'authorization', formData : $('#check-user').serialize()},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				if(result.error.length == 0)
				{
					if (changepassword == 1)
					{
						ShowInvalidPassordError();
						OnChangePasswordClicked();
					}
					else
						window.location = "index.php";
				}
				else
				{
					ShowInvalidCredentialsError()
				}
			},
			error: function (request,error) {
				ShowNetworkError();
			}
		});                  
	} else {
		alert('Please fill all necessary fields');
	}          
	return false; // cancel original event to prevent form submitting
});  

function OnReopenTicket()
{
	console.log("Reopen Ticket");
	var ticket = tickets[selectedticket];
	
	$.ajax({url: 'src/data_tickets.php',
			data: {
			},
			data: {cmd : 'changestate_canceltimewait', user : user, path:ticket.path,id:ticket.id},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				//window.location = "login.php";
				tickets = result.data;
				if(result.error.length == 0)
				{
					PopulateListView();
				}
				else
				{
					console.log(result.error[0]);
					PopulateListView();
					ShowReopenError(result.error);
				}
					
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action               
				ShowNetworkError();
				
			}
		});
}

function OnRevertTicket()
{
	console.log("Revert Ticket");
	var ticket = tickets[selectedticket];
	
	$.ajax({url: 'src/data_tickets.php',
			data: {
			},
			data: {cmd : 'changestate_revert_timewait', user : user, path:ticket.path,id:ticket.id},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				//window.location = "login.php";
				tickets = result.data;
				if(result.error.length == 0)
				{
					PopulateListView();
				}
				else
				{
					PopulateListView();
					ShowRevertTaskError(result.error);
				}
					
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action               
				ShowNetworkError();
			}
		});
	
}
function OnCloseTicket()
{
	console.log("Closing Ticket");
	var ticket = tickets[selectedticket];
	
	$.ajax({url: 'src/data_tickets.php',
			data: {
			},
			data: {cmd : 'changestate_close_timewait', user : user, path:ticket.path,id:ticket.id},
			type: 'post',                  
			async: 'true',
			dataType: 'json',
			beforeSend: function() {
				// This callback function will trigger before data is sent
				$.mobile.loading('show'); // This will show Ajax spinner
			},
			complete: function() {
				// This callback function will trigger on data sent/received complete   
				$.mobile.loading('hide'); // This will hide Ajax spinner
			},
			success: function (result) {
				// Check if authorization process was successful
				//window.location = "login.php";
				tickets = result.data;
				if(result.error.length == 0)
				{
					PopulateListView();
				}
				else
				{
					PopulateListView();
					ShowCloseTaskError(result.error);
				}
					
			},
			error: function (request,error) {
				// This callback function will trigger on unsuccessful action               
				ShowNetworkError();
			}
		}); 
}

function ShowTaskCreated()
{
	setTimeout( function(){ 
		$( "#taskcreated_popup" ).popup( );
		$( "#taskcreated_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#taskcreated_popup" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowHalfFilledFormError()
{
	setTimeout( function(){ 
		$( "#taskhalffillederror_popup" ).popup( );
		$( "#taskhalffillederror_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#taskhalffillederror_popup" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowTaskCreatError()
{
	setTimeout( function(){ 
		$( "#taskcreaterror_popup" ).popup( );
		$( "#taskcreaterror_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#taskcreaterror_popup" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowInvalidPassordError()
{
	setTimeout( function(){ 
		$( "#pwdchange_popup" ).popup( );
		$( "#pwdchange_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#pwdchange_popup" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowInvalidCredentialsError()
{
	setTimeout( function(){ 
		$( "#invalidcred_popup" ).popup( );
		$( "#invalidcred_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#invalidcred_popup" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowReopenError(errors)
{
	setTimeout( function(){ 
		$( "#reopenerror" ).popup( );
		$( "#reopenerror" ).popup( "open" ); 
		setTimeout( function(){ $( "#reopenerror" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowCloseTaskError(errors)
{
	setTimeout( function(){ 
		$( "#error_popup" ).popup( );
		$( "#error_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#error_popup" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowRevertTaskError(errors)
{
	setTimeout( function(){ 
		$( "#reverterror" ).popup( );
		$( "#reverterror" ).popup( "open" ); 
		setTimeout( function(){ $( "#reverterror" ).popup( "close" );}, 2000 );
	}, 100 );
}
function ShowNetworkError()
{
	setTimeout( function(){ 
		$( "#error_popup" ).popup( );
		$( "#error_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#error_popup" ).popup( "close" );}, 2000 );
	}, 500 );
}
function ShowLogoutSuccess()
{
	setTimeout( function(){ 
		$( "#logout_popup" ).popup( );
		$( "#logout_popup" ).popup( "open" ); 
		setTimeout( function(){ $( "#logout_popup" ).popup( "close" );window.location = "login.php";}, 2000 );
	}, 500 );
}
function ConvertJsDateFormat(datestr)
{
	var d = new Date(datestr);
	if(d == 'Invalid Date')
		return '';
	
	dateString = d.toUTCString();
	dateString = dateString.split(' ').slice(0, 4).join(' ').substring(5);
	return dateString;
}
function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}
$( document ).ready(function() 
{

    if(page == 'tasklist')
	{
		ReadTickets(user);
		if(admin == 1)
		{
			$('#createbtn').show();
			$('#adminbtn').show();
		}
	}
	if(page == 'create')
	{

	}
});