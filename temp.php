<script type="text/javascript">
(function ($) {
    $.braviPopUp = function (title, src, width, height) {
        //Destroy if exist
        $('#dv_move').remove();
        //create hte popup html
        var html = '<div class="main" id="dv_move" style="width:' + width + 'px; height:' + height + 'px;">';
        html += '  <div class="title">';
        html += '    <span id="title_left">' + title + '</span> <span class="close">';
        html += ' <img id="img_close" src="images/close.png" width="25" height="23" onclick="CloseDialog();"></span></div>';
        html += ' <div id="dv_no_move">';
        html += '<div id="dv_load"><img src="images/circular.gif"/></div>';
        html += ' <iframe id="url" scrolling="auto" src="' + src + '"  style="border:none;" width="100%" height="100%"></iframe>';
        html += ' </div>';
        html += ' </div>';

        //add to body
   $('<div></div>').prependTo('body').attr('id', 'overlay');// add overlay div to disable the parent page
        $('body').append(html);
        //enable dragable
        $('#dv_move').draggable();
        //enable resizeable
        $("#dv_move").resizable({
            minWidth: 300,
            minHeight: 100,
            maxHeight: 768,
            maxWidth: 1024
        });

        $("#dv_no_move").mousedown(function () {
            return false;
        });
        $("#title_left").mousedown(function () {
            return false;
        });
        $("#img_close").mousedown(function () {
            return false;
        });
        //change close icon image on hover
        $("#img_close").mouseover(function () {
            $(this).attr("src", 'images/close2.png');
        });
        $("#img_close").mouseout(function () {
            $(this).attr("src", 'images/close.png');
        });

        setTimeout("$('#dv_load').hide();", 1500);
    };
})(jQuery); 
</script>

<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Default.aspx.cs" Inherits="_Default" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css" />
    <!--><script src="jquery/braviPopup.js" type="text/javascript"></script> -->
    <link href="css/braviStyle.css" rel="stylesheet" type="text/css" />
    <title>braviPopUp</title>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#btnTest').click(function () {
                $.braviPopUp('testing title!', 'popup.aspx', 600, 400);
            });
        });  
 //if you want to refresh parent page on closing of a popup window then remove comment to the below function
        //and also call this function from the js file 
        //        function Refresh() {
        //            window.location.reload();
        //        }     
    </script>
</head>
<body>
    <form id="form1" runat="server">
    <input type="button" id="btnTest" value="Click Me!" />
    </form>
</body>
</html>