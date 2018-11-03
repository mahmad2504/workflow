var userHandler = {
    username : '',
    status : ''
}
var alltickets = null;
var selected_ticket = null;
function OnDoneClicked()
{
	ChangeTicketState(selected_ticket,'done');
	//LoadTickets($('#username').val());
}
function OnRevertClicked()
{
	ChangeTicketState(selected_ticket,'revert');
	//LoadTickets($('#username').val());
}
function OnPasswordChange()
{

}
function OnChangePasswordClicked()
{
	$("#fieldset").append('<div id="opassword" data-role="fieldcontain">'+                                  
                         '<label for="opassword">Old password:</label>'+
                         '<input  value="" name="opassword" autocomplete="new-password" id="opassword"/>');
	$("#fieldset").append('<div id="npassword" data-role="fieldcontain">'+                                  
                         '<label for="npassword">New password:</label>'+
                         '<input  value="" name="npassword" autocomplete="new-password" id="npassword"/>');	
	
	$("#changepasswordlink").remove();
                  /*  </div>
					
					<div id="npassword" data-role="fieldcontain">                                     
                        <label for="npassword">New password:</label>
                        <input type="password" value="" name="npassword" id="npassword"/>
                    </div>*/
}
function ChangeTicketState(ticket,status)
{
	console.log("Marking ticket done");
	$.ajax(
	{	url: 'modify.php',
            data: {user : $('#username').val(), ticket: ticket.path, state:status},
            type: 'post',                  
            async: 'true',
            dataType: 'json',
            beforeSend: function() 
			{
                // This callback function will trigger before data is sent
               // $.mobile.loading('show'); // This will show Ajax spinner
            },
            complete: function() 
			{
                // This callback function will trigger on data sent/received complete   
               // $.mobile.loading('hide'); // This will hide Ajax spinner
            },
            success: function (ticket) 
			{
				if(ticket.state >= 0)
				{
					state = ticket.states[ticket.state];
					if(state.assignee != $('#username').val())
						$("#LI"+ticket.number).remove();
				}
				else
					$("#LI"+ticket.number).remove();
				
				
				//LoadTickets($('#username').val());
                   // userHandler.status = result.status;
				//PopulateList(result);
				//$.mobile.changePage("#second");  
            },
            error: function (request,error) 
			{
                 // This callback function will trigger on unsuccessful action               
                 alert('Network error has occurred please try again!');
            }
    }); 
}
function ShowDoneRevertDialog(number)
{
	selected_ticket = alltickets[number];
	state = selected_ticket.states[selected_ticket.state];

	if(typeof state.revert !== 'undefined') 
		$("#purchase").append('<a href="" data-rel="back" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-mini" onclick="OnRevertClicked();">Revert</a>');
	
    console.log(selected_ticket);	
}
function Dump()
{
	
$('#listview').append(
					'<li id="LI'+ticket.number+'"><a href="#">'+
					'<img "height="42" width="42" src="css/images/'+type+'.png">'+
					'<p><span style="font-weight: bold;">('+ticket.number+")  "+ticket.title+'</span>'+
					'<span style="float: right;">'+state.name+'</span></p>'+
					'<p><span>&nbsp&nbsp&nbsp&nbsp'+ConvertJsDateFormat(ticket.created)+'   ('+diff+') days</span>'+
					'<span style="float: right;">'+state.assignee+'</span></p></a>'+
					'<a href="#purchase" data-rel="popup" data-position-to="window" data-transition="pop" onclick="ShowDoneRevertDialog('+i+');"></a>'+
					'</li>');	
	
}

function PopulateList(tickets)
{
	for(var i=0;i<tickets.length;i++)
	{
		var ticket= tickets[i];
		
		var state = ticket.states[ticket.state];
		var start = moment(state.activated);
		var end = moment(new Date());
		var diff = end.diff(start, "days");
		var color = 'grey';
		//sconsole.log(state);
		if(typeof state.days !== 'undefined')
		{
			if(diff >= state.days)
				color = 'red';
		}

		var type =  ticket.type.toLowerCase();
		
		$('#listview').append(
					'<li id="LI'+ticket.number+'"><a href="#">'+
					'<img style="float: left;display:inline-block;"  src="css/images/'+type+'.png">'+
					'<span style="float: left;font-weight: bold; display:inline-block;">'+ticket.title+'</span>'+
					'<span style="float:left;font-weight:bold; display:inline-block; color:green">&nbsp&nbsp'+ticket.number+'</span><br>'+
					
					'<span style="float: right;color:green">'+state.name+'</span>'+
					'<span style="font-size:10px;float: left;color:'+color+'">'+diff+' days old&nbsp&nbsp</span>'+
					
					'<a href="#purchase" data-rel="popup" data-position-to="window" data-transition="pop" onclick="ShowDoneRevertDialog('+i+');"></a>'+
					'</li>');
	}
	
	//var start = moment(ticket.created);
	//var end = moment(new Date());
	//console.log(end.diff(start, "days"));
	//console.log(tickets);
	alltickets = tickets;
	
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
function LoadTickets(username)
{
	$.ajax(
	{	url: 'data.php',
            data: {user : username},
            type: 'post',                  
            async: 'true',
            dataType: 'json',
            beforeSend: function() 
			{
                // This callback function will trigger before data is sent
                $.mobile.loading('show'); // This will show Ajax spinner
            },
            complete: function() 
			{
                // This callback function will trigger on data sent/received complete   
                $.mobile.loading('hide'); // This will hide Ajax spinner
            },
            success: function (result) 
			{

                   // userHandler.status = result.stat
				PopulateList(result);
				$.mobile.changePage("#second"); 
				var activePage = $(':mobile-pagecontainer').pagecontainer('getActivePage');
				if(activePage.attr('id') !== 'second')
				{
					$.mobile.changePage("#second"); 
				}
				
            },
            error: function (request,error) 
			{
                 // This callback function will trigger on unsuccessful action               
                 alert('Network error has occurred please try again!');
            }
    }); 
}


$(document).on('pagecontainershow', function (e, ui) {
    var activePage = $(':mobile-pagecontainer').pagecontainer('getActivePage');
    if(activePage.attr('id') === 'login') {
        $(document).on('click', '#submit', function() { // catch the form's submit event
            if($('#username').val().length > 0 && $('#password').val().length > 0){
             
                userHandler.username = $('#username').val();
             
                // Send data to server through the Ajax call
                // action is functionality we want to call and outputJSON is our data
                $.ajax({url: 'auth.php',
                    data: {action : 'authorization', formData : $('#check-user').serialize()},
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
                        if(result.status == 'success') {
                            userHandler.status = result.status;
							//$('#page-2-title').text($('#username').val()+" Tasks");
                            //$.mobile.changePage("#second");  
							
                            LoadTickets($('#username').val());	
							 
                        } else {
                            alert('Logon unsuccessful!');
                        }
                    },
                    error: function (request,error) {
                        // This callback function will trigger on unsuccessful action               
                        alert('Network error has occurred please try again!');
                    }
                });                  
            } else {
                alert('Please fill all necessary fields');
            }          
            return false; // cancel original event to prevent form submitting
        });  
    } else if(activePage.attr('id') === 'second') {
       // activePage.find('.ui-content').text('Wellcome ' + userHandler.username);
    }
});
 
$(document).on('pagecontainerbeforechange', function (e, ui) {
    var activePage = $(':mobile-pagecontainer').pagecontainer('getActivePage');
    if(activePage.attr('id') === 'second') {
        var to = ui.toPage;
         
        if (typeof to  === 'string') {
            var u = $.mobile.path.parseUrl(to);
            to = u.hash || '#' + u.pathname.substring(1);
              
            if (to === '#login' && userHandler.status === 'success') {
                alert('You cant open a login page while youre still logged on!');
                e.preventDefault();
                e.stopPropagation();
                  
                // remove active status on a button if a transition was triggered with a button
                $('#back-btn').removeClass('ui-btn-active ui-shadow').css({'box-shadow':'0 0 0 #3388CC'});
            } 
        }
    }
});