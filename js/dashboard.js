const body = $("#dash_body"),
      error = '<div style="margin-top:50px" class="loading"><i class="fa fa-times"></i>Network error! Try again.</div>',
      load = '<div style="margin-top:50px" class="loading"><i class="fa fa-spin fa-circle-notch"></i>Loading...</div>';
const card_selection = (n)=>{
    $(".card").each((c, e)=>{
        let m = $(e).text().trim();
        if(m == n){
            $(e).addClass("active")
        }
        else {
            $(e).removeClass("active")
        }
    })
};
$(".card .t").click((e)=>{
    let o = $(e.target).text(), link, t = o.trim();
    card_selection(t);
    switch(t){
        case 'Dashboard':
            link = "/ajax/pages/student_dashboard.php";
            break;
        case 'Courses':
            link = "/ajax/pages/student_courses.php";
            break;
        case 'Routine':
            link = "/ajax/pages/student_routine.php";
            break;
        case 'Quizes':
            link = "/ajax/pages/student_quizes.php";
            break;
        case 'Settings':
            link = "/ajax/pages/settings.php";
            break;
        case 'Assignments':
            link = "/ajax/pages/student_assignments.php";
            break;
        case 'Marks':
            link = "/ajax/pages/student_marks.php";
            break;
        default:
            link = "/ajax/pages/student_dashboard.php";
            break;
    }
    body.html(load);
    $.post(link)
    .done((e)=>{
        body.html(e);
    })
    .fail(()=>{
        body.html(error);
    });
    if(window.MOBI_MENU_OPENED){
        $(".top_menu2").click();
        window.MOBI_MENU_OPENED = 0;
    }
});
//Initialiser
$(".def").click();
//Main functions
const select_course = (object)=>{
    let id = $(object).val();
    if(id == "def") {
        return;
    }
    $("#course_routine").html(load);
    iziToast.info({
        title: 'Fetching',
        message: 'Wait... Getting the routine data',
    });
    $.post("/ajax/pages/main_routine.php", {cid: id})
    .done((d)=>{
        $("#course_routine").html(d);
    })
    .fail(()=>{
        $("#course_routine").html(error);
        iziToast.warning({
            title: 'Failed',
            message: 'Couldn\'t communicate to the server!',
        });
    });
};
const course_marks = (object)=>{
    let id = $(object).val();
    if(id == "def") {
        return;
    }
    $("#cmarks_main").html(load);
    iziToast.info({
        title: 'Fetching',
        message: 'Wait... Getting the routine data',
    });
    $.post("/ajax/pages/marks_main.php", {cid: id})
    .done((d)=>{
        $("#cmarks_main").html(d);
        $("#qd_last").click();
    })
    .fail(()=>{
        $("#cmarks_main").html(error);
        iziToast.warning({
            title: 'Failed',
            message: 'Couldn\'t communicate to the server!',
        });
    });
};
const marks_data = (object)=>{
    let cid = $(object).attr('data-cid');
    if(!cid){
        iziToast.error({
            title: 'Error',
            message: 'Try again!',
        });
        return;
    }
    $(object).addClass('b');
    $(object).addClass('u');
    $(window.LAST_MO).removeClass('b');
    $(window.LAST_MO).removeClass('u');
    window.LAST_MO = object;
    iziToast.info({
        title: 'Fetching',
        message: 'Wait... Getting quiz result',
    });
    $("#marks_data").html(load);
    $.post("/ajax/pages/quiz_result.php", {cid: cid})
    .done((d)=>{
        $("#marks_data").html(d);
    })
    .fail(()=>{
        $("#marks_data").html(error);
        iziToast.warning({
            title: 'Failed',
            message: 'Couldn\'t communicate to the server!',
        });
    });
};