const payer = (m, d)=>{
    $("#payer_body").html("Please wait...");
    $.post("/ajax/dynamic/payer.php", {
        m: m, d:d
    })
    .done((d)=>{
        $("#payer_body").html(d);
    })
    .fail(()=>{
        $("#payer_body").html("Network error! Try again.");
    });
}
$("#bKash").click(()=>{
    payer("bKash", $("#bKash").attr("data-all"));
    $("#bKash").addClass("curr");
    $("#nagad, #rocket").removeClass("curr");
});
$("#nagad").click(()=>{
    payer("nagad", $("#nagad").attr("data-all"));
    $("#nagad").addClass("curr");
    $("#bKash, #rocket").removeClass("curr");
});
$("#rocket").click(()=>{
    payer("rocket", $("#rocket").attr("data-all"));
    $("#rocket").addClass("curr");
    $("#bKash, #nagad").removeClass("curr");
});
//Default
$("#bKash").click();
//Paid response
$("#sent_money").click(()=>{
    if(window.SENT_LOCK){
        return;
    }
    let ref_c = $("#ref").attr("data-ref");
    if(!ref_c){
        return;
    }
    window.SENT_LOCK = 1;
    $("#sent_money i").attr("class", "fa fa-spin fa-circle-notch");
    $("#sent_money .b").text("Please wait...");
    $.post("/ajax/dynamic/proceed.php", {r: ref_c}).done((d)=>{
        if(d == "DONE"){
            $(".msent").html('<div class="b pdo">You have sent the money. Please check back in 1-2 hours. We will review and approve the order soon. You can call us anytime.</div>');
        }
        else {
            window.SENT_LOCK = 0; 
            $("#sent_money i").attr("class", "fas fa-check");
            $("#sent_money .b").text("I have sent the money");
            iziToast.error({
                title: 'Error',
                message: 'Invalid response from server!',
            });
        }
    }).fail(()=>{
        window.SENT_LOCK = 0; 
        $("#sent_money i").attr("class", "fas fa-check");
        $("#sent_money .b").text("I have sent the money");
        iziToast.warning({
            title: 'Failed',
            message: 'Couldn\'t communicate to the server!',
        });
    });
});