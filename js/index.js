$(document).ready(()=>{
    $(".card_expand").click((e)=>{
        let icon = $(e.target);
        let id = parseInt(icon.attr("data-id"));
        let card = $($(".card_image")[id]);
        if(icon.hasClass("fa-angle-down")){
            card.addClass("card_expanded");
            icon.attr("class", "card_expand fa fa-angle-up");
            return
        }
        card.removeClass("card_expanded");
        icon.attr("class", "card_expand fa fa-angle-down");
    });
    $("#feed").load("/ajax/index/feed.php");
});
$(".tmm").click(()=>{
    let o = $(".t_l_holder");
    o.addClass("tmm_show");
});
$(".mc").click(()=>{
    let o = $(".t_l_holder");
    o.removeClass("tmm_show");
});
let comingSoon = ()=>{
    iziToast.info({
        title: 'Coming soon!',
        message: 'Feature is on its way!',
    });
}