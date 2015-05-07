//this function can remove a array element.
Array.remove = function(array, from, to) {
    var rest = array.slice((to || from) + 1 || array.length);
    array.length = from < 0 ? array.length + from : from;
    return array.push.apply(array, rest);
};

//this variable represents the total number of popups can be displayed according to the viewport width
var total_popups = 0;
            
//arrays of popups ids
var popups = [];

//this is used to close a popup
function close_popup(id)
{
    for(var iii = 0; iii < popups.length; iii++)
    {
        if(id == popups[iii])
        {
            Array.remove(popups, iii);
            var f = document.getElementById(id).getElementsByTagName("form");
            //document.getElementById(id).removeChild(f[0]);
            //document.getElementById(id).style.display = "none";   
            var div = document.getElementById(id);
            div.parentNode.removeChild(div);     

            calculate_popups();

            return;
        }
    }   
}

//displays the popups. Displays based on the maximum number of popups that can be displayed on the current viewport width
function display_popups()
{
    var right = 10;

    var iii = 0;
    for(iii; iii < total_popups; iii++)
    {
        if(popups[iii] != undefined)
        {
            var element = document.getElementById(popups[iii]);
            element.style.right = right + "px";
            right = right + 320;
            element.style.display = "block";
        }
    }

    for(var jjj = iii; jjj < popups.length; jjj++)
    {
        var element = document.getElementById(popups[jjj]);
        element.style.display = "none";
    }
}
            
//creates markup for a new popup. Adds the id to popups array.
function register_popup(id, name)
{

    for(var iii = 0; iii < popups.length; iii++)
    {   
        //already registered. Bring it to front.
        if(id == popups[iii])
        {
            Array.remove(popups, iii);

            popups.unshift(id);

            calculate_popups();


            return;
        }
    }               
                
    var element = '<div class="popup-box chat-popup" id="'+ id +'">';
    element = element + '<div class="popup-head">';
    element = element + '<div class="popup-head-left">'+ name +'</div>';
    element = element + '<div class="popup-head-right"><a href="javascript:close_popup(\''+ id +'\');">&#10005;</a></div>';
    element = element + '<div style="clear: both"></div></div><div class = "message-area">';
    element = element + '<div class="popup-messages"></div><div class ="form"></div></div></div>';   


    document.getElementById("chats").innerHTML = document.getElementById("chats").innerHTML + element;  

        var f = document.createElement("form");
        f.setAttribute('action',"");
        f.setAttribute('name',"message");
        f.setAttribute('id', 'form' + id);
        //f.setAttribute('onSubmit', "return false");

        var i = document.createElement("input"); //input element, text
        i.setAttribute('type',"text");
        i.setAttribute('name',"usermsg");
        i.setAttribute('class',"usermsg");
	i.setAttribute('id', 'input' + id);
        i.setAttribute('size',"63");

        var s = document.createElement("input"); //input element, Submit button
        s.setAttribute('type',"submit");
        s.setAttribute('value',"Send");
        s.setAttribute('class',"submitmsg");
        s.setAttribute('name',"submitmsg");

        f.appendChild(i);
        f.appendChild(s);

        document.getElementById(id).getElementsByClassName("form")[0].appendChild(f);

    //and some more input elements here
    //and dont forget to add a submit button   
    popups.unshift(id);

    calculate_popups();

}
            
//calculate the total number of popups suitable and then populate the toatal_popups variable.
function calculate_popups()
{
    var width = window.innerWidth;
    if(width < 540)
    {
        total_popups = 0;
    }
    else
    {
        // width = width - 200;
        //320 is width of a single popup box
        total_popups = parseInt(width/320);
    }
                
    display_popups();
}
            
/*//recalculate when window is loaded and also when window is resized.
window.addEventListener("resize", calculate_popups);
window.addEventListener("load", calculate_popups);*/

//jquery for submitting loading and logging message
$(document).ready(function(){
    ///If user submits the form, log the message in the chat_history table using chat_logmessage.php
    $(document).on("submit", "form", function(event) {
        event.preventDefault();
        //event.stopImmediatePropagation();
        
        var id = event.target.id.substring(4);  
        var clientmsg = $("#" + id + " .usermsg").val();
        $.post("php/chatLogmessage.php", {text: clientmsg, recipient: id});                
        $("#input" + id).val("");
        return false;
    });
    
    //Load the data containing the chat log by querying the chat_history table through chat_query.php
    function loadLog(element){
        for (var iii = 0; iii < popups.length; iii++){
            (function(i) {
                var oldscrollHeight = $("#" + popups[i] + " .popup-messages").prop("scrollHeight") - 20; //Scroll height before the request
		console.log($("#" + popups[i] + " .popup-messages").prop("scrollHeight"));
                $.ajax({
                    type: "GET",
                    url: "php/chatRetrieve.php?recipient=" + popups[i],
                    dataType: "html",
                    cache: false,
                    success: function(response){   
                        $("#" + popups[i] + ' .popup-messages').html(response); //Insert chat log into the #chatbox div

                        //Auto-scroll           
                        var newscrollHeight = $("#" + popups[i] + " .popup-messages").prop("scrollHeight") - 20; //Scroll height after the request
			console.log(newscrollHeight);                        
			if(newscrollHeight > oldscrollHeight){
                            $("#" + popups[i] + " .popup-messages").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
                        }               
                    },
                });
            })(iii);
        }
    }
    
    setInterval (loadLog, 2500);    //Reload file every 2500 ms or x ms if you w
});
