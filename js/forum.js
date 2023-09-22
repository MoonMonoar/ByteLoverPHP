const root = $("#forum-root"),
    error = '<div class="loading load-margin"><i class="fa fa-times"></i>Network error! Try again.</div>',
    load = '<div class="loading load-margin"><i class="fa fa-spin fa-circle-notch"></i>Loading...</div>';
root.html(load);
$.post("/ajax/forum/home.php").done((d)=>{
    root.html(d);
    $(".fadb").click(()=> {

    });
});