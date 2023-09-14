const start_quiz = (b)=>{
    let c = $(b).attr("data-cid");
    if(!c || c.length == 0){
        iziToast.error({
            title: 'Error',
            message: 'Something went wrong! Refresh page.',
        });
        return;
    }
    if(window.QUIZ_P){
        iziToast.info({
            title: 'Wait',
            message: 'Preparing quiz!',
        });
        return;
    }
    window.QUIZ_P = 1;
    window.QUIZ_B = $(b).html();
    $(b).html('<i class="fa fa-spin fa-circle-notch"></i> <span class="b">Starting...</span>');
    $.post("/ajax/pages/quiz.php", {
        class_id: c
    })
    .done((d)=>{
        $("#dash_body").html(d);
        hljs.highlightAll();
        //Start timer - per second
        const l = ()=>{
            setTimeout(()=>{
                $.post("/ajax/pages/quiz_timer.php", {
                    class_id: c
                }).done((d)=>{
                    if(d == "00:00"){
                        //Ended
                        $("#submit_final").click();
                    }
                    else {
                        //Update timer
                        if($("header #q_timer").length == 0){
                            $("header").append('<div class="tm qtmr b cflex" id="q_timer">15:00</div>');
                            $("#main_body").css("margin-top", "50px");
                            $("#dash_body").addClass("nom2");
                        }
                        $("#q_timer").text(d);
                        l();
                    }
                })
                .fail(()=>{
                    l();
                })
            }, 1e3);
        }
        l();
    })
    .fail(()=>{
        iziToast.error({
            title: 'Error',
            message: 'Network error!',
        });   
        $(b).html(window.QUIZ_B);
    })
}