$( document ).ready(function() {
    var images;
    var cancel = false;
    var i = 0;
    var data = {};
    var percent, start, remaining, seconds, minutes;
    
    function asyncConvert(path) {
        data["file"] = path;
        
        if (!start) start = event.timeStamp;
        
        remaining = ((event.timeStamp - start) / i) * (images.length - i) / 1000;
        minutes = Math.floor(remaining / 60);
        seconds = Math.floor(remaining % 60);
        percent = Math.floor(i / images.length * 100) + "%";
        
        $.post("ajax.php", data, function(data) {
            $("#status").html("<b>Converting image: </b>" + path + 
            "<br><b>Progress: </b>" + i + " of " + images.length +
            "<br><b>Time remaining: </b>" + minutes + " minutes " + seconds + " seconds");
            $(".progress-bar").css("width", percent);
            $(".progress-bar").text(percent); 
            
            if (data != "ok") $("#log").append(data + "<br>");
            i++;
            if (i < images.length && !cancel) asyncConvert(images[i]);
            
            if (cancel){
                $("#log").append("Cancelled by user.");
                cancel = false;
            } 
        });
    }
    
    $("#GetInformation").click(function() {
        data["task"] = "GetInformation";
        
        $("#info").text("Calculating...");
        
        $.post("ajax.php", data, function(data) {
            images = $.parseJSON(data);
            $("#info").html("<b>Number of files:</b> " + images.length);
        }), "json";
    });
    
    $("#StartConvertion").click(function() {
        data["task"] = "StartConvertion";
        asyncConvert(images[i]);

    });
    
    $("#CancelConvertion").click(function() {
        cancel = true;
    });
    
    $("#ClearLog").click(function() {
        $("#log").empty();
    });
});