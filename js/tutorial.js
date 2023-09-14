$(document).ready(()=>{
    let old_read = localStorage.getItem(window.CURRENT_TUT+"_LAST_READ");
    let old_title = localStorage.getItem(window.CURRENT_TUT+"_LAST_READ_TITLE");
    let key = "SP_"+window.CURRENT_ARTICLE;
    let lr = window.CURRENT_ARTICLE.split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    localStorage.setItem(window.CURRENT_TUT+"_LAST_READ", location.pathname+"?ref=rem");
    localStorage.setItem(window.CURRENT_TUT+"_LAST_READ_TITLE", lr);
    const old_sp = localStorage.getItem(key);
    let k = ()=> {
        $(window).on("scroll", ()=>{
            const nsp = $(window).scrollTop();
            localStorage.setItem(key, nsp.toString());
        });
    }
    if (old_sp !== null) {
        window.scrollTo({
            top: parseInt(old_sp),
            behavior: 'smooth'
        });
        k();
    }
    else {
        k();
    }
    if(null != old_read && null != old_title){
        if(lr === old_title){
            return
        }
        $("#last").html('Last Read:<span class="notranslate"> '+old_title+"</span>. <a href="+old_read+">Go there</a>.");
    }
});