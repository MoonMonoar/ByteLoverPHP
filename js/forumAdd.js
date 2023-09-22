const f_title = $("#problemTitle"),
      f_body = $("#problemContent"),
      b_insert_code = $("#insC"),
      b_remove_code = $("#rmvC"),
      d_save = $(".prob_draft");
window.pendingDraft = false;
const draft = ()=> {
    if(window.pendingDraft){
        return;
    }
    d_save.html(' <i class="fa fa-clock mr2"></i> Saving...');
    window.pendingDraft = true;
    setTimeout(()=> {
        localStorage.setItem("NEW_PROB_TITLE", f_title.val());
        localStorage.setItem("NEW_PROB_BODY", f_body.val());
        d_save.html('<i class="fa fa-check-circle mr2"></i> Drafted');
        window.pendingDraft = false;
    }, 1000);
}
let code_cache = [];
let cache_editor_code, current_code, current_lang;
const openCoder = (e)=> {
    const cd = e.clipboardData || window.clipboardData;
    cache_editor_code = cd.getData('text');
    e.preventDefault();
    b_insert_code.click();
    return null;
}
b_remove_code.click(()=> {
    if(code_cache.length > 0){
        f_body.val(f_body.val().replace(code_cache[code_cache.length-1], ""));
        code_cache.pop();
        draft();
    }
    else {
        iziToast.info({
            title: 'No code',
            message: 'You did not paste anymore code!'
        });
    }
});
b_insert_code.click(()=> {
    const o = $(".code_editor");
    const markup = o.html();
    o.html("");
    prompt(markup, false, false);

    $("#editor").click(()=> {
        $("#mask").click();
    });

    const editorElement = document.getElementById('editor');
    const editor = ace.edit(editorElement);

    let t;
    editor.setTheme(t = (localStorage.getItem("ce_t")?localStorage.getItem("ce_t"):'ace/theme/monokai'));
    $('#editor-themes option[value="'+t+'"]').prop('selected', true);
    $("#editor-themes").change(function() {
        const theme = $("#editor-themes").val();
        localStorage.setItem("ce_t", theme);
        editor.setTheme(theme);
    });

    let s = (localStorage.getItem("ce_s")?localStorage.getItem("ce_s"):14);
    editor.setFontSize(s+"px");
    $('#editor-fontsize option[value="'+s+'"]').prop('selected', true);
    $("#editor-fontsize").change(function() {
        const size = $("#editor-fontsize").val();
        localStorage.setItem("ce_s", size);
        editor.setFontSize(size+"px");
    });

    let l;
    editor.session.setMode(l = (localStorage.getItem("ce_l")?localStorage.getItem("ce_l"):'ace/mode/c_cpp'));
    $('#editor-lang option[value="'+l+'"]').prop('selected', true);
    current_lang = l;
    $("#editor-lang").change(function() {
        const mode = $("#editor-lang").val();
        localStorage.setItem("ce_l", mode);
        editor.session.setMode(mode);
        current_lang = mode;
    });

    editor.session.on('change', function () {
        current_code = editor.getValue();
    });

    editor.setValue((cache_editor_code && cache_editor_code.length > 0)?cache_editor_code:"");

    $("#closeCoder").click(()=> {
        prompt_dismiss();
        o.html(markup)
    });
    $("#insertCode").click(()=> {
        if(current_code && current_code.length > 0){
            current_lang = current_lang.replace("ace/mode/", "");
            current_code = '[CODE-'+current_lang+']'+current_code+'[/CODE-'+current_lang+']';
            code_cache[code_cache.length] = current_code;
            document.getElementById('problemContent').value += current_code;
            draft();
        }
        prompt_dismiss();
        o.html(markup);
    });
});

$(window).ready(()=>{
    const l = localStorage.getItem("NEW_PROB_TITLE"),
        b = localStorage.getItem("NEW_PROB_BODY");
    if(l && l.length > 0){
        f_title.val(l)
    }
    if(b && b.length > 0){
        f_body.val(b)
    }
});