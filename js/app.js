//Service worker
const script_var = "1.0.4-all";
if(navigator.serviceWorker){
    navigator.serviceWorker.register("/sw.js?v="+script_var);
    navigator.serviceWorker.register("/firebase-messaging-sw.js?v="+script_var);
}
//Markups
const notif_err_d = '<section style="padding: 20px;"><div style="display: flex; justify-content: center; align-items: center;"> <img alt="Notification Problem" src="/img/illustrations/NotificationProblem.png" style="height: auto; width: 40%; max-width: 150px;"> </div><div style="color: var(--black); font-size: 1.2em; font-weight: bold; text-align: center;">Notifications disabled!</div><div style=" margin-top: 15px; color: var(--grey-text); text-align: center;">It seems you have blocked or deined notification permission! Please do as follows...</div><ul style="line-height: 22px; padding-inline-start: 25px;color: var(--black);"><li>Click on the <i class="fa fa-lock"></i> (lock icon) near the browser address bar. A menu should appear. From there find \'Permissions\' and enable \'Notifications\'.</li><li style=" margin-top: 10px;"> If you can\'t enbale it the first way, go to your device\'s settings and find Notification permission and enable for \'bytelover.com\'.</li></ul> <div style="display: flex; align-items: center; justify-content: center;"> <button onclick="history.back()" style="padding: 7px; width: 100px; background: #e6c04d; border: none; font-family: \'Ubuntu\'; color: var(--black); border-radius: 5px; cursor: pointer;">Got it</button> </div></section>',
    logout_prompt = '<section style="width:280px;padding:10px 0 13px 0"><div style="text-align:center;font-size:70px;padding:10px;color:var(--main)"><i class="fa fa-question-circle"></i></div><div style="color:var(--black);font-size:1.2em;font-weight:700;text-align:center">Sure Logout?</div><div style="margin-top:10px;color:var(--grey-text);text-align:center">Do you really want to logout?</div><div style="display:grid;grid-template-columns:50% 50%;margin:20px 0 10px 0"><div class="cflex"><a href="/logout/?ref=student"><button style="padding:10px 20px 10px 20px;background:var(--alert);border:none;color:var(--white)">Logout</button></a></div><div class="cflex"><button onclick="history.back()" style="padding:10px 20px 10px 20px;background:var(--ok-green);border:none;color:var(--white)">Cancel</button></div></div></section>',
    notif_ph = '<section style="padding:0 10px 0 15px;box-shadow:0 0px 5px 0 rgba(0,0,0,0.12);"><div style="color:var(--black);font-size:1.4em;font-weight:700;height:50px;display:grid;grid-template-columns:calc(100% - 40px) 40px"><div class="cflex" style="justify-content:left"><i class="fa fa-bell" style="margin-right:10px"></i>Notifications</div><div onclick="history.back()"><button style="height:100%;width:100%;background:0 0;border:none;color:var(--black);font-size:1.2em"><i class="fa fa-times"></i></button></div></div><div style="width:100vh"></div><div id="notif_catch" style="position:absolute;bottom:0;top:50px;left:0;right:0"><div class="cflex" style="height:100%;font-size:1.2em;color:var(--black)"><i class="fa fa-circle-notch fa-spin" style="margin-right:10px"></i>Loading...</div></div></section>',
    course_convert = '<section style="padding: 10px;max-width:420px"><div style="display: flex; justify-content: center; align-items: center;"></div><div style="color: var(--black);font-size: 1.3em;padding: 10px;">Course conversions</div><div id="ccnv"><div style="color: var(--grey-text);padding: 0px 10px 10px 10px;font-size: small;">Are you sure? You <u>can not convert back</u> to Live mode once you convert to Recorded package. Also, all your students will receive notifications.</div><div class="cflex" style="margin-bottom:5px"><button class="b ce nom wauto" onclick="start_c_cnv()"><i class="fa-solid fa-arrow-right-arrow-left im"></i> Convert</button></div></div></section>',
    cnving = '<div style="color: var(--grey-text);padding: 0px 10px 10px 10px;font-size: small;">Converting the course status. This may take some time, be patient do not refresh the page.</div><div style="margin-left: 10px;color:var(--black)"><i id="conv_upd" class="fa fa-check-circle"></i> Updating status</div><div style="margin: 5px 0 0 10px;color:var(--black)"><i id="cnv_pkg" class="fa fa-check-circle"></i> Setting expiery</div><div style="margin: 5px 0 10px 10px;color:var(--black)"><i id="cnv_inform" class="fa fa-check-circle"></i> Informing students</div>';
//Notification
firebase.initializeApp({
  apiKey: "AIzaSyCuB53gY090zhFPT48Dd3AjcJ90WhfclHM",
  authDomain: "bytelover-android.firebaseapp.com",
  projectId: "bytelover-android",
  storageBucket: "bytelover-android.appspot.com",
  messagingSenderId: "228849322669",
  appId: "1:228849322669:web:076f1f15909d03824dd424",
});
const messaging = firebase.messaging();
messaging.requestPermission()
   .then(function () {
     // get the token in the form of promise
     return messaging.getToken()
   })
   .then(function(token) {
    // print the token on the HTML page
    let old_token = getCookie("PushToken");
    if(old_token === null || old_token !== token){
        document.cookie = "PushToken="+token+"; path=/";
        $.post('/ajax/dynamic/WebPushToken.php', {
            token_fcm_notification: token
        }).fail(()=>{
            console.warn("Failed to save push token.");
        });
    }
   })
   .catch(function (err) {prompt(notif_err_d)});
// Handle foreground messages
messaging.onMessage(function(payload) {
  try{
      const notification = new Notification(payload.notification.title,
      {
        body: payload.notification.body,
        icon: payload.notification.icon,
        data: payload.data
      });
      notification.addEventListener('click', function(event) {
        event.preventDefault();
        const d = notification.data;
        if(d && d.url){
          window.open(d.url);
        }
        notification.close();
      });
  }
  catch(e){
      return e;
  }
});
const conf_refresh = (event)=>{
  // Display a confirmation message
  const confirmationMessage = 'Are you sure you want to refresh? Your changes may be lost or operations may get interrupted!';
  event.returnValue = confirmationMessage; // For older browsers
  return confirmationMessage;
};
//Buttons
if($(".top_menu").length > 0){
$(".top_menu").click(()=>{
    let e = $(".top_links"), c = "top_links_opened", i = ".top_menu i";
    if(e.hasClass(c)){
        e.removeClass(c);
        e.removeAttr("style");
        $(i).attr("class", "fa fa-list mb");
        $("html, #main_body, footer").removeAttr("style");
        $("#main_body, footer").click(()=>{return});
    }
    else {
        e.fadeIn(3e2);
        e.addClass(c);
        $(i).attr("class", "fa fa-times mb cb");
        $("html").css("overflow", "hidden");
        let o = $("#main_body, footer");
        o.css("opacity", 0.1);
        o.css("pointer-events", "none");
        o.click(()=>{
            e.removeClass(c);
            e.removeAttr("style");
            $(i).attr("class", "fa fa-list mb");
            $("html, #main_body, footer").removeAttr("style");
            o.click(()=>{return});
        });
    }
});
};
if($(".top_menu2").length > 0){
//Custom top menu
$(".top_menu2").click(()=>{
    let e = $("#main_body .menu"), c = "dm_open", i = ".top_menu2 i";
    if(e.hasClass(c)){
        e.removeClass(c);
        $(i).attr("class", "fa fa-list mb");
        $("html, #dash_body, footer").removeAttr("style");
        $("#dash_body, footer").click(()=>{return});
        window.MOBI_MENU_OPENED = 0;
    }
    else {
        window.MOBI_MENU_OPENED = 1;
        e.addClass(c);
        $(i).attr("class", "fa fa-times mb cb");
        $("html").css("overflow", "hidden");
        let o = $("#dash_body, footer");
        o.css("opacity", 0.1);
        o.css("pointer-events", "none");
        o.click(()=>{
            e.removeClass(c);
            e.removeAttr("style");
            $(i).attr("class", "fa fa-list mb");
            $("html, #dash_body, footer").removeAttr("style");
            o.click(()=>{return});
            window.MOBI_MENU_OPENED = 0;
        });
    }
});
};
//Legacy code
function compressImage(file, compressionFactor, squareSize) {
  return new Promise((resolve, reject) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.onload = () => {
      canvas.width = squareSize;
      canvas.height = squareSize;
      ctx.drawImage(img, 0, 0, squareSize, squareSize);
      canvas.toBlob((blob) => {
        resolve(new File([blob], `${file.name}-${compressionFactor}-${squareSize}.jpg`, { type: 'image/jpeg' }));
      }, 'image/jpeg', 1);
    };
    img.onerror = reject;
    img.src = URL.createObjectURL(file);
  });
};
const getCookie = (cookieName)=>{
  var cookies = document.cookie.split(';');
  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i].trim();
    if (cookie.startsWith(cookieName + '=')) {
      return cookie.substring(cookieName.length + 1);
    }
  }
  return null;
};
const logout_app = ()=>{
  prompt(logout_prompt)
};
const course_cnv = (cid)=>{
    if(!cid){
        return;
    }
    window.ccnv_id = cid;
    prompt(course_convert);
};
const start_c_cnv = ()=>{
    if(!window.ccnv_id){
        return;
    }
    window.addEventListener('beforeunload', conf_refresh);
    $("#ccnv").html(cnving);
    $("#conv_upd").attr('class', 'fa fa-circle-notch fa-spin');
    $.post('//api.bytelover.com/teacher/convert_course_to_live.php', {
        cid: window.ccnv_id
    }).done((e)=> {
        if(e === 'Done'){
            $("#conv_upd").attr('class', 'fa fa-check-circle');
            $("#conv_upd").attr('style', 'color:var(--ok-green)');
            
            $("#cnv_pkg").attr('class', 'fa fa-circle-notch fa-spin');
            $.post('//api.bytelover.com/teacher/convert_students_to_live_package.php', {
                    cid: window.ccnv_id
                }).done((e)=> {
                    if(e === 'Done'){
                        $("#cnv_pkg").attr('class', 'fa fa-check-circle');
                        $("#cnv_pkg").attr('style', 'color:var(--ok-green)');
                        
                            $("#cnv_inform").attr('class', 'fa fa-circle-notch fa-spin');
                            $.post('//api.bytelover.com/teacher/notify_students_about_live_course.php', {
                                    cid: window.ccnv_id
                                }).done((e)=> {
                                    if(e === 'Done'){
                                        $("#cnv_inform").attr('class', 'fa fa-check-circle');
                                        $("#cnv_inform").attr('style', 'color:var(--ok-green)');
                                        window.removeEventListener('beforeunload', conf_refresh);
                                    }
                                    else {
                                        //Error
                                        $("#cnv_inform").attr('class', 'fa fa-times-circle');
                                        $("#cnv_inform").attr('style', 'color:var(--alert)');
                                        iziToast.error({
                                            title: 'Failed',
                                            message: 'Couldn\'t complete! Try again.'
                                        });
                                        window.removeEventListener('beforeunload', conf_refresh);
                                    }
                                }).fail(()=>{
                                        //Error
                                        $("#cnv_inform").attr('class', 'fa fa-times-circle');
                                        $("#cnv_inform").attr('style', 'color:var(--alert)');
                                        iziToast.error({
                                            title: 'Failed',
                                            message: 'Couldn\'t complete! Try again.'
                                        });
                                        window.removeEventListener('beforeunload', conf_refresh);
                                });
                        
                    }
                    else {
                        //Error
                        $("#cnv_pkg").attr('class', 'fa fa-times-circle');
                        $("#cnv_pkg").attr('style', 'color:var(--alert)');
                        iziToast.error({
                            title: 'Failed',
                            message: 'Couldn\'t complete! Try again.'
                        });
                        window.removeEventListener('beforeunload', conf_refresh);
                    }
                }).fail(()=>{
                        //Error
                        $("#cnv_pkg").attr('class', 'fa fa-times-circle');
                        $("#cnv_pkg").attr('style', 'color:var(--alert)');
                        iziToast.error({
                            title: 'Failed',
                            message: 'Couldn\'t complete! Try again.'
                        });
                        window.removeEventListener('beforeunload', conf_refresh);
                });
        }
        else {
            //Error
            $("#conv_upd").attr('class', 'fa fa-times-circle');
            $("#conv_upd").attr('style', 'color:var(--alert)');
            iziToast.error({
                title: 'Failed',
                message: 'Couldn\'t complete! Try again.'
            });
            window.removeEventListener('beforeunload', conf_refresh);
        }
    }).fail(()=>{
            //Error
            $("#conv_upd").attr('class', 'fa fa-times-circle');
            $("#conv_upd").attr('style', 'color:var(--alert)');
            iziToast.error({
                title: 'Failed',
                message: 'Couldn\'t complete! Try again.'
            });
            window.removeEventListener('beforeunload', conf_refresh);
    });
};
const notifs = ()=>{
  prompt(notif_ph, true);
  let init = ()=>{
      $.post("/ajax/pages/notifications.php", {
          r: 1
      }).done((d)=>{
          $("#notif_catch").html(d);
      }).fail(()=>{
          console.warn("Could not load notifcations! Trying to load again in 1 second.");
          setTimeout(init, 1e3);
      });
  };
  init();
};
//Legacy ends
const ne = (id)=>{
    let b = $("#b_"+id.getAttribute('nid'));
    if(b.attr("e")){
        //Remove
        b.removeClass('nbe');
        b.attr('e', '');
        $(id).html('<i class="fa-solid fa-expand"></i> Expand');
        iziToast.success({
            title: 'Collapsed',
            message: 'Showing minimum message',
            timeout: 1500
        });
    }
    else {
        //
        b.addClass('nbe');
        b.attr('e', 1);
        $(id).html('<i class="fa-solid fa-compress"></i> Collapse');
        iziToast.success({
            title: 'Expanded',
            message: 'Showing full message',
            timeout: 1500
        });
    }
};
const loadDp = async function (event) {
      let image = document.getElementById("output"),
          file = event.target.files[0];
      image.src = URL.createObjectURL(file);
      const cf = await compressImage(file, 0.8, 250);
      let fd = new FormData();
      fd.append("file", cf);
      iziToast.info({
          title: 'Saving',
          message: 'Updating profile picture...'
      });
      $.ajax({
            url: '/img/users/upload.php',
            type: 'post',
            data: fd,
            dataType: 'text',
            contentType: false,
            processData: false,
            success: function(response){
                 if(response == "DONE"){
                    iziToast.success({
                        title: 'Saved',
                        message: 'Profile picture updated!'
                    });
                 }
                 else{
                    iziToast.error({
                        title: 'Failed',
                        message: 'Couldn\'t update! Try again.'
                    });
                 }
            },
            error: function() {
                iziToast.error({
                    title: 'Failed',
                    message: 'Connection issue! Try again.'
                }); 
            }
       })
       .fail((d)=>{
        iziToast.error({
            title: 'Failed',
            message: 'Unknown issue! Try again.'
        }); 
       });
};
const prompt_dismiss = ()=>{
    $("#prompt").addClass("hide");
    setTimeout(()=>{
        $("#mask, #prompt").remove();
        $("html").css("overflow", "");
    }, 30);
};
const stop_prop = (e)=>{
    e.stopPropagation();
};
const prompt = (dialogue, full = false)=>{
    if(!dialogue){
        console.warn("Empty prompt attempted!");
        return;
    }
    history.pushState({ function: 'prompt' }, null, '');
    window.POP = 'prompt_dismiss()';
    $("#mask, #prompt").remove();
    $("html").css("overflow", "hidden");
    //HTML SETS
    let prompt = '<div id="mask" onclick="history.back()"><div id="prompt" onclick="stop_prop(event)" class="full">'+dialogue+'</div></div>';
    if(!full){
        prompt = '<div id="mask" onclick="history.back()"><div id="prompt" onclick="stop_prop(event)">'+dialogue+'</div></div>';
    }
    $("body").prepend(prompt);
};
window.addEventListener('popstate', function(event) {
  eval(window.POP);
  window.POP = '';
});