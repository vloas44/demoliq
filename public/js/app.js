console.log("testounet");

function sendClap (){
    var url= $(this).attr("data-url");
    var clickedElement = this;

    $.ajax({
        url: url,
        method: 'post'
    }).done(function(response){
        var newClapNumber = response.data.claps;
        $(clickedElement).html(newClapNumber);
    });

}

// sur clic du bouton clap, on lance une requête Ajax
$(".clap").on("click", sendClap);