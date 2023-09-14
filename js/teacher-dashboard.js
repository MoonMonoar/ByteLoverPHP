const body = $("#dash_body"),
      error = '<div class="loading"><i class="fa fa-times"></i>Network error! Try again.</div>',
      load = '<div class="loading"><i class="fa fa-spin fa-circle-notch"></i>Loading...</div>';
const ins_c = ()=>{
    $("#qz_q").val($("#qz_q").val()+'<pre><code class="language-c">#inlcude &lt;stdio.h&gt;\r\nint main(){\r\n    \r\n    return 0;\r\n}</code></pre>');
};
const rec = (b, i)=>{
    let o =  $("#e_"+i), c = "rt-col";
    if(!o.hasClass("taken")){
        c = "rt-col-n";
    }
    if(o.hasClass(c)){
        o.removeClass(c);
        $(b).text("Collapse");
    }
    else {
        o.addClass(c);
        $(b).text("Expand");
    }
};
const fc = (o)=>{
    if(!o || o.length === 0){
        return true;
    }
    return false;
};
const set_clink = (b)=>{
    if(window.SCL_BUSY){
        iziToast.info({
            title: 'Wait',
            message: 'An operation is in progress!',
        });
        return;
    }
    window.SCL_BUSY = 1;
    $(b).html('<i class="fa-solid fa-podcast"></i> <span class="b">Saving...</span>');
    let o = $(b),
        c = o.attr("data-cid"),
        i = o.attr("data-fi"),
        f = $("#cl_"+i),
        l = f.val();
    if(!c || c.length == 0 || !l || l.length == 0){
        iziToast.error({
            title: 'Failed',
            message: 'Link can\'t be empty!',
        }); 
        $(b).html('<i class="fa-solid fa-podcast"></i> <span class="b">Invite Now</span>');
        window.SCL_BUSY = 0;
        return;
    }
    $.post("/ajax/pages/teacher/add_clink.php", {
        l: l,
        c: c
    }).done((d)=>{
        if(d == "DONE"){
            iziToast.success({
                title: 'Done',
                message: 'Class is live!',
            });
        }
        else {
            iziToast.error({
                title: 'Error',
                message: 'Unknown error! Try again.',
            });
        }
        $(b).html('<i class="fa-solid fa-podcast"></i> <span class="b">Invite Now</span>');
        window.SCL_BUSY = 0;
    })
    .fail(()=>{
            iziToast.error({
                title: 'Error',
                message: 'Communication error! Try again.',
            }); 
        $(b).html('<i class="fa-solid fa-podcast"></i> <span class="b">Invite Now</span>');
        window.SCL_BUSY = 0;
    })
};
const add_eq = (o)=>{
    if(window.EADR_BUSY){
        iziToast.info({
            title: 'Wait',
            message: 'An operation is in progress!',
        });
        return;
    }
    $(o).html('<i class="fa fa-spin fa-circle-notch"></i> Saving...');
    window.EADR_BUSY = 1;
    let b = $(o),
        u = b.attr("data-ui"),
        q = $("#eq_"+u).val(),
        c = b.attr("data-cid"),
        d = b.attr("data-d");
        if(fc(q) || fc(c) || fc(d)){
            iziToast.error({
                title: 'Failed',
                message: 'Make sure every field is filled!',
            }); 
            window.EADR_BUSY = 0;
            $(b).html('Add question');
            return
        }
    $.post("/ajax/pages/teacher/add_exam.php", {
        c: c,
        d: d,
        q: q
    })
    .done((d)=>{
        switch(d){
            case "MAXED":
                iziToast.info({
                    title: 'Maxed',
                    message: 'Maximum question count. Can\'t add more!',
                }); 
                break;
            case "ERROR":
                iziToast.error({
                    title: 'Failed',
                    message: 'Unknown issue! Try again.',
                }); 
                break;
            case "DONE":
                iziToast.success({
                    title: 'Saved',
                    message: 'Question saved successfully!',
                }); 
                $("#eq_"+u).val('');
                break;
            default:
                iziToast.error({
                    title: 'Failed',
                    message: 'Unknown issue! Try again.',
                }); 
        }
        window.EADR_BUSY = 0;
        $(b).html('Add question');
    })
    .fail(()=>{
        iziToast.error({
            title: 'Failed',
            message: 'Connection problem, try again!',
        }); 
        window.EADR_BUSY = 0;
        $(b).html('Add question');
    })

};
const add_qq = (b)=>{
    if(window.QADR_BUSY){
        iziToast.info({
            title: 'Wait',
            message: 'An operation is in progress!',
        });
        return;
    }
    window.QADR_BUSY = 1;
    $(b).html('<i class="fa fa-spin fa-circle-notch"></i> Saving...');
    //Data set
    let cid = $(b).attr("data-cid"),
        ques = $("#qz_q").val(),
        mark = $("#qz_m").val(),
        o1 = $("#qz_o1").val(),
        o2 = $("#qz_o2").val(),
        o3 = $("#qz_o3").val(),
        o4 = $("#qz_o4").val(),
        ans = $("#qz_ans").val(),
        exp = $("#qz_exp").val();
    if(fc(cid) || fc(ques) || fc(mark) || fc(o1) || fc(o2) || fc(o3) || fc(o4) || fc(ans) || fc(exp)){
        iziToast.error({
            title: 'Failed',
            message: 'Make sure every field is filled!',
        }); 
        window.QADR_BUSY = 0;
        $(b).html('Add question');
        return
    }
    $.post("/ajax/pages/teacher/add_quiz.php", {
        c: cid,
        q: ques,
        m: mark,
        o1: o1,
        o2: o2,
        o3: o3,
        o4: o4,
        a: ans,
        e: exp
    })
    .done((d)=>{
        switch(d){
            case "MAXED":
                iziToast.info({
                    title: 'Maxed',
                    message: 'Maximum question count. Can\'t add more!',
                }); 
                break;
            case "ERROR":
                iziToast.error({
                    title: 'Failed',
                    message: 'Unknown issue! Try again.',
                }); 
                break;
            case "DONE":
                iziToast.success({
                    title: 'Saved',
                    message: 'Question saved successfully!',
                }); 
                $("#qz_q, #qz_m, #qz_o1, #qz_o2, #qz_o3, #qz_o4, #qz_ans, #qz_exp").val('');
                if(window.CURRENT_QUIZ_BUTTON){
                    $(window.CURRENT_QUIZ_BUTTON).click();
                }
                break;
            default:
                iziToast.error({
                    title: 'Failed',
                    message: 'Unknown issue! Try again.',
                }); 
        }
        window.QADR_BUSY = 0;
        $(b).html('Add question');
    })
    .fail(()=>{
        iziToast.error({
            title: 'Failed',
            message: 'Connection problem, try again!',
        }); 
        window.QADR_BUSY = 0;
        $(b).html('Add question');
    })
};
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
const quiz_data = (b)=>{
    if(window.QD_BUSY){
        iziToast.info({
            title: 'Wait',
            message: 'A fetch is in progress!',
        });
        return;
    }
    let o = $(b),
        i = $(b).attr("data-cid");
    if(!i){
        iziToast.error({
            title: 'Failed',
            message: 'Unknown issue! Try again.',
        }); 
        return;
    }
    $(".q_date").removeClass("b");
    o.addClass("b");
    window.QD_BUSY = 1;
    $("#quizes_data").html('<div class="em">Loading...</div>');
    $.post("/ajax/pages/teacher/quiz_data.php", {cid: i})
    .done((d)=>{
        $("#quizes_data").html(d);
        hljs.highlightAll();
        window.CURRENT_QUIZ_BUTTON = b;
        window.QD_BUSY = 0;
    })
    .fail(()=>{
        $("#quizes_data").html('<div class="em">Fetch failed! Try again.</div>');
        iziToast.error({
            title: 'Failed',
            message: 'Connection issue! Try again.',
        }); 
        window.QD_BUSY = 0;
    })
};
const met_upload = (file, button)=>{
    if(window.UPLOADER_BUSY){
        iziToast.info({
            title: 'Wait',
            message: 'An upload is in progress!',
        });
        return;
    }
    window.UPLOADER_BUSY = 1;
    let o = $('#'+file),
        p = o.attr("data-pin"),
        t = o.attr("data-tf"),
        f = o[0].files;
    if(o.length > 0 && p && p.length > 0 && f && f.length > 0){
        let fd = new FormData();
        fd.append("file", f[0]);
        fd.append("pin", p);
        fd.append("fn", t);
        $(button).html('<i class="fa fa-spin fa-circle-notch"></i> Uploading...');
        $.ajax({
            url: '/files/upload.php',
            type: 'post',
            data: fd,
            dataType: 'text',
            contentType: false,
            processData: false,
            success: function(response){
                 if(response == "DONE"){
                    iziToast.success({
                        title: 'Uploaded',
                        message: 'File successfully uploaded!',
                    });
                    //Refresh list
                    $.post("/ajax/pages/teacher/class_files.php", {
                        cid: t
                    }).done((e)=>{
                        $('#mtrs_'+t).html(e);
                    });
                    window.UPLOADER_BUSY = 0;
                    $(button).html('<i class="fa fa-arrow-up"></i> Upload');
                 }
                 else{
                    iziToast.error({
                        title: 'Failed',
                        message: 'File upload failed! Try again.',
                    });
                    window.UPLOADER_BUSY = 0;
                    $(button).html('<i class="fa fa-arrow-up"></i> Upload');
                 }
            },
            error: function() {
                iziToast.error({
                    title: 'Failed',
                    message: 'Connection issue! Try again.',
                }); 
                window.UPLOADER_BUSY = 0;
                $(button).html('<i class="fa fa-arrow-up"></i> Upload');
            }
       })
       .fail((d)=>{
        iziToast.error({
            title: 'Failed',
            message: 'Unknown issue! Try again.',
        }); 
        window.UPLOADER_BUSY = 0;
        $(button).html('<i class="fa fa-arrow-up"></i> Upload');
       });
    }
    else {
        iziToast.error({
            title: 'Error',
            message: 'No file is selected!',
        });
        window.UPLOADER_BUSY = 0;
        $(button).html('<i class="fa fa-arrow-up"></i> Upload');
    }
};
$(".card .t").click((e)=>{
    let o = $(e.target).text(), link, t = o.trim();
    card_selection(t);
    switch(t){
        case 'Dashboard':
            link = "/ajax/pages/teacher/teacher_dashboard.php";
            break;
        case 'Responses':
            link = "/ajax/pages/teacher/teacher_responses.php";
            break;
        case 'Routine':
            link = "/ajax/pages/teacher/teacher_routine.php";
            break;
        case 'Settings':
            link = "/ajax/pages/settings.php";
            break;
        case 'Quizes':
            link = "/ajax/pages/teacher/teacher_quizes.php";
            break;
        case 'Assignments':
            link = "/ajax/pages/teacher/teacher_assignments.php";
            break;
        default:
            link = "/ajax/pages/teacher/teacher_dashboard.php";
            break;
    }
    body.html(load);
    $.post(link)
    .done((e)=>{
        body.html(e)
    })
    .fail(()=>{
        body.html(error)
    });
    if(window.MOBI_MENU_OPENED){
        $(".top_menu2").click();
        window.MOBI_MENU_OPENED = 0;
    }
});
//Initialiser
$(".def").click();
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
    $.post("/ajax/pages/teacher/main_routine.php", {cid: id})
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
const quize_course = (object)=>{
    let id = $(object).val();
    if(id == "def") {
        return;
    }
    $("#course_routine").html(load);
    iziToast.info({
        title: 'Fetching',
        message: 'Wait... Getting the routine data',
    });
    $.post("/ajax/pages/teacher/main_quizes.php", {cid: id})
    .done((d)=>{
        $("#course_quizes").html(d);
    })
    .fail(()=>{
        $("#course_quizes").html(error);
        iziToast.warning({
            title: 'Failed',
            message: 'Couldn\'t communicate to the server!',
        });
    });
};
const exam_course = (object)=>{
    let id = $(object).val();
    if(id == "def") {
        return;
    }
    $("#course_routine").html(load);
    iziToast.info({
        title: 'Fetching',
        message: 'Wait... Getting the routine data',
    });
    $.post("/ajax/pages/teacher/main_assignments.php", {cid: id})
    .done((d)=>{
        $("#course_exam").html(d);
    })
    .fail(()=>{
        iziToast.warning({
            title: 'Failed',
            message: 'Couldn\'t communicate to the server!',
        });
    });
};
const update_class = (cid, date, obj, vob, button)=>{
    if(window.CUPDATER_BUSY){
        iziToast.info({
            title: 'Wait',
            message: 'An operation is still in progress!',
        });
        return;
    }
    let o = $('#'+obj), v = $('#'+vob), f = $('#f'+vob), vl = 'None', vf = 'None';
    if(v.length > 0){
        let vv = v.val();
        if(vv && vv.length > 0){
            vl = vv;
        }
    }
    if(f.length > 0){
        let ff = f.val();
        if(ff && ff.length > 0){
            vf = ff;
        }
    }
    if(o.length > 0){
        let title = o.val();
        if(title.length > 0){
            window.CUPDATER_BUSY = 1;
            $(button).text("Please wait...");
            //Delay date checker
            let date_delay = $("#c_d"+vob).val();
            if(!date_delay || date_delay.length == 0){
                date_delay = false;
            }
            //Time
            let time = $("#c_t"+vob).val();
            if(!time || date_delay.length == 0){
                time = 'DEFAULT'
            }
            $.post("/ajax/pages/teacher/update_class.php", {
                cid: cid,
                title: title,
                date: date,
                time: time,
                delay: date_delay,
                vl: vl,
                vf: vf
            })
            .done((d)=>{
                if(d == "DONE"){
                    window.CUPDATER_BUSY = 0;
                    $(button).text("Update");
                    iziToast.success({
                        title: 'Saved',
                        message: 'Class updated successfully!',
                    });
                }
                else {
                    window.CUPDATER_BUSY = 0;
                    $(button).text("Update");
                    iziToast.warning({
                        title: 'Failed',
                        message: 'Invalid response, try again!',
                    });
                }
            })
            .fail(()=>{
                window.CUPDATER_BUSY = 0;
                $(button).text("Update");
                iziToast.warning({
                    title: 'Failed',
                    message: 'Couldn\'t communicate to the server!',
                });
            })
        }
        else {
            iziToast.warning({
                title: 'Empty',
                message: 'Title is empty!',
            });
        }
    }
};