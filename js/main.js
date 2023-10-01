// Set the date we're counting down to
if (document.getElementById("count-time")){
    var counttime = document.getElementById("count-time").textContent;    
}
var countDownDate = new Date(counttime).getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  console.log(now);
    
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"

  if (document.getElementById("au-d")) {
      document.getElementById("au-d").innerHTML = days;
      document.getElementById("au-h").innerHTML = hours;
      document.getElementById("au-m").innerHTML = minutes;
      document.getElementById("au-s").innerHTML = seconds;
  }

  // document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  // + minutes + "m " + seconds + "s ";
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    // document.getElementById("demo").innerHTML = "EXPIRED";
    location.reload(true);
  }
}, 1000);


jQuery(document).ready(function($) {
     

    $(".tr_content .im-info").hover(function() {
        $("#mem_content .goal_text").css('display', 'inline');
    }, function() {
        $("#mem_content .goal_text").css('display', 'none');
    });


    var cof = $(".curoff_content .bid").text().replace('$', '');
     
    $(".active_content #bid_amount").attr({ "min" : cof });

     $("#bid_form").submit(function(e){
        e.preventDefault();
        $("#offerbtn").text("Loading...");
        var pid = $("#pid").val();
        var uid = $("#uid").val();
        var bid = $("#bid_amount").val();
        
        $.ajax({
            url: ajax_object.ajax_url, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                'action': 'submit_auction_request',
                'pid' : pid,
                'uid' : uid,
                'bid' : bid,
            },
            dataType: 'json',
            type: "post",            
            success: function (data) {
                if (data['redirect']) {
                    location.reload();
                } else {
                    $(".curoff_content .bid").text("$"+data['offer']);
                    $(".active_content #bid_amount").val('');
                    $(".active_content #bid_amount").attr({ "min" : data['offer'] });
                    if (data['error']) {
                      $(".active_content .error #minbid").text(data['offer']);
                      $(".active_content .error").css('display', 'block');
                    } else {
                      $(".active_content .error").css('display', 'none');
                    }
                    $("#offerbtn").text("Submit Offer");
                }
           }
        });
    });
});
