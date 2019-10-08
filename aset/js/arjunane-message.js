var msg_img         = "",
    msg_text        = "",
    forward_id      = new Array(),
    forward_name    = new Array(),
    forward_length  = 0;

function pushForward(id, name)
{
    forward_id.push(id);
    forward_length += 1;
    forward_name.push(name);
}
function removeForward(id, name)
{
    forward_length -= 1;
    forward_id.splice(forward_id.indexOf(id), 1);
    forward_name.splice(forward_name.indexOf(name), 1);
}
function setForward(reset)
{
    let data = 
    {
        id     : forward_id.join(","),
        name   : forward_name.join(", "),
        length : forward_length
    }
    if(typeof reset !== 'undefined')
    {
        data.id         = "";
        data.name       = "";
        forward_id      = [];
        forward_name    = [];
        forward_length  = 0;
    }
    return data;
}

function getDataMessage(msg, img)
{
    msg_img  = img;
    msg_text = msg;
}

function setDataMessage()
{
    let data = 
    {
        msg : msg_text,
        img : msg_img
    };
    return data;
}

//menampilkan pesan/chat yang sudah di tambahkan ke dalam database
function appendPesan(data, _friend)
{ 
    let reply = "";
    if(data.isReply)
    {
        reply = '<div class="reply-msg">' +
                    '<div class="img">' +
                        '<img src="' + data.reply_img + '" alt="">' +
                    '</div>' +
                    '<pre>' + data.reply_msg + '</pre>' +
                '</div>';
    }

    let photo   = data.photo, //photo user
        nama    = data.name,
        img     = (data.img !== null) ? '<div class="_img"><img class="view-photo" src="' + data.img + '" alt="' + nama + '"></div>' : "", //gambar insert user
        self    = _friend ? " right" : "", //menge-check apakah pesan berasal dari diri sendiri atau orang lain
        friend  = _friend ? " _friend" : " _self", //menge-check apakah pesan berasal dari diri sendiri atau orang lain
        msg     = data.msg,
        _msg  = "<div class=\"contact" + self + "\" data-id=\"" + data.id  + "\">" +
                    "<div class=\"d-100\">" + 
                        "<div class=\"photo\">" + 
                            "<img src=\"" + photo + "\" alt=\"alsdojkhf\">" +
                        "</div>" +
                        "<div class=\"msg" + friend + "\">" +
                            reply +
                            img +
                            "<div class=\"button-msg" + friend + "\"></div>" +
                            "<pre>" + msg + "</pre>"
                        "</div>" +
                    "</div>" +
                "</div>";
    return _msg;
}

function forwardContact()
{
    let container = $('.container-chat'),
        contact   = container.find(".show-msg"),
        forward   = $('.forward'),
        f_contact = forward.find('.wrap-inner');
    for(let i = 0; i < contact.length; i++)
    {
        let _contact = contact.eq(i),
            name     = _contact.find(".name span").text(),
            data_id  = _contact.attr('for'),
            child    = '<div class="f-contact" name-user="' + name + '"> <div class="check" data-id="' + data_id + '"></div>' + _contact.html() + "</div>";
        
        f_contact.append(child);
    }
}

//menampilkan pesan/chat yang sudah di edit ke dalam database
function editDeletePesan(data, status, friend)
{
    let ini            = $("body").find(".chat-wrap .contact"),
        action_message = $("body").find('.action-msg');
    for (let i = 0; i < ini.length; i++)
    { 
        let _ini    = ini.eq(i),
            btn_msg = _ini.find(".button-msg"),
            photo   = _ini.find("._img"),
            _id     = _ini.attr('data-id'),
            edit    = _ini.find(".msg > pre");
        if (data.id === _id && status) //jika status === true maka EDIT
        { 
            edit.html(data.msg);
        }
        else if (data.id === _id && !status) //jika status === false maka DELETE
        { 
            if (friend) //parameter ketiga ditujukan untuk memperlihatkan info socket ke chat lawan
            {
                photo.remove();
                edit.css("font-style", "italic");
                edit.html(data.msg);
            }
            else
            { 
                _ini.remove();
            }
            
        }
        action_message.removeClass("aktif");
    }
}

// send forward message
$(document).on("click", ".send-forward", function() {
    let ini         = $(this),
        forward     = $('body').find(".forward"),
        data_msg    = forward.attr('data-msg'),
        data_id     = ini.attr('data-user'),
        url         = ini.attr("url"),
        _chat       = $("body").find(".chat .chat-wrap"), //mencari body .chat-wrap
        data        = setForward();
        
    let http   = (window.location.protocol === 'http:') ? "http://" : "https://",
        socket = io( http + window.location.hostname + ":3000" );
    let final_url   = url + data_msg + "/" + data_id.split(",").join("-");
    if(data.length > 0)
    {
        $.ajax({
            url : final_url,
            type: "POST",
            success: function (data) 
            {
                let json = JSON.parse(data);
                for(let i = 0; i < json.id_user.length; i++)
                {
                    let id_user = json.id_user[i],
                        data    = {
                            name : json.name,
                            photo: json.photo,
                            img  : json.photo_msg,
                            msg  : json.msg,
                            from : json.from,
                            isReply: json.isReply,
                            to   : id_user
                        };
                    let user_chat = $('body').find('.show-msg.aktif');

                    if(user_chat.attr('for') === id_user && user_chat.attr('user') === data.from)
                    {
						_chat.append(appendPesan(data, false));
                    }

                    socket.emit("kirim_pesan", data);
                }

                let container   = forward.find(".wrap-send"),
                contact     = forward.find(".wrap-inner"),
                name_text   = container.find(".name-contact"),
                btn_forward = container.find(".send-forward");

                setForward(true);
                
                name_text.html(""); // remove all name if checked
                contact.html(""); // remove all contact
                forward.removeClass('aktif');
                container.removeClass('aktif');
                btn_forward.removeClass('aktif');
                forward.attr('data-msg',"");
                btn_forward.attr('data-user',"");
            }
        });
    }
});

// remove value input on forward search
$(document).on('click', '.forward .remove-search-forward', function () {

    let forward = $('body').find('.forward'),
        contact = forward.find('.wrap .wrap-inner .f-contact');

    $(this).prev().val("");
    $(this).prev().focus();

    contact.removeClass('hidden');
});

// search contacts on forward element
$(document).on('keyup', '.forward .search-forward input', function() {
    let ini = $(this),
        val = ini.val().toString().toLowerCase(),
        forward = $('body').find('.forward'),
        contact = forward.find('.wrap .wrap-inner .f-contact');
    if (val.length !== 0)
    { 
        for (let i = 0; i < contact.length; i++)
        {
            let u = contact.eq(i).attr('name-user').toString().toLowerCase();
            //jika val tidak sama dengan -1 atau elbih dari 0 maka akan menghapus class .hidden di dalam element a-href ".show-msg", jika tidak di temukan maka akan menambahkan class .hidden pada element a-href ".show-msg"
            (u.indexOf(val) !== -1) ? contact.eq(i).removeClass('hidden') : contact.eq(i).addClass('hidden');
        }
    }
    else
    {
        contact.removeClass('hidden');
    }
});

//menampilkan pesan error atau sukses, dll
function info(cls, msg)
{ 
    return "<div class=\"info-" + cls +"\">" + msg + "</div";
}

//menampilkan info bahwa user yang bersangkutan sedang ngetik
function is_ngetik(data)
{
    let ini = $("body").find(".show-msg");
    for (let i = 0; i < ini.length; i++)
    { 
        let chat = ini.eq(i),
            new_msg = chat.find(".msg-new span");
        //jika user yang di tuju sama dengan user yang disebelah kiri dengan attribute "for" (.show-msg)
        if(chat.attr('for') === data._from && data.status_ngetik && chat.attr('user') === data._for) 
        {
            new_msg.html("Is Ngetik...");
        }
        else if(chat.attr('for') === data._from && !data.status_ngetik && chat.attr('user') === data._for && data.replaceFor === chat.attr('for'))
        {
            new_msg.html("");
        }
    }
}

// close forward 
$(document).on('click', '.forward .close', function() {
    
    let forward     = $('body').find('.forward'),
        container   = forward.find(".wrap-send"),
        contact     = forward.find(".wrap-inner"),
        name_text   = container.find(".name-contact"),
        btn_forward = container.find(".send-forward");

    setForward(true);

    name_text.html(""); // remove all name if checked
    contact.html(""); // remove all contact
    forward.removeClass('aktif');
    container.removeClass('aktif');
    btn_forward.removeClass('aktif');
    forward.attr('data-msg',"");
    btn_forward.attr('data-user',"");
});

//check forward
$(document).on('click', '.check', function() {
    let ini         = $(this),
        data_id     = ini.attr('data-id'),
        forward     = $('body').find('.forward'),
        container   = forward.find(".wrap-send"),
        name_text   = container.find(".name-contact"),
        btn_forward = container.find(".send-forward"),
        name        = ini.siblings().find('.name span').text();
    if(ini.hasClass('aktif'))
    {
        ini.removeClass('aktif');
        removeForward(data_id, name);
    }
    else
    {
        ini.addClass('aktif');
        pushForward(data_id, name);
    }

    let df = setForward(); // data forward

    btn_forward.attr('data-user', df.id); // set id users
    console.log(df);
    if(df.length !== 0)
    {
        name_text.html(df.name);
        container.addClass("aktif");
        setTimeout(function () {
            btn_forward.addClass('aktif');
        }, 300);
    }
    else
    {
        name_text.html("");
        btn_forward.removeClass("aktif");
        setTimeout(function () {
            container.removeClass('aktif');
        }, 300);
    }
});

//menghapus element info
$(document).on("click", ".info-error, .info-success", function () { 
    $(this).remove();
});

//FORM INPUT TYPE 1
$(document).on("input", '.f-input.t-1', function () { 
    let ini   = $(this),
        input = ini.find("input");
    //menghapus pesan error setiap klik pada input'an
    ini.prev().html("");
    //jika panjang input'an tidak sama dengan 0 atau lebih dari 0
    (input.val().length !== 0) ? ini.addClass('aktif') : ini.removeClass('aktif');
});
$(window).on("load", function () { 
    let ini = $("body").find(".f-input.t-1");
    for (let i = 0; i < ini.length; i++)
    { 
        let input = ini.eq(i).find("input");
        if (input.val().length !== 0)
        { 
            ini.eq(i).addClass("aktif");
        }
    }
});

//menghapus semua input'an sesuai dengan element parent nya
function reset(cls)
{
    let input_form = $(cls).find("input"),
        f_input    = $(cls).find('.f-input');
    for (let i = 0; i < input_form.length; i++)
    {
        input_form[i].value = ""; //menghapus input atau value
        f_input.eq(i).removeClass("aktif"); //menghilangkan class aktif pada f-input, biar posisi placeholdernya berada di tengah
    }
}

//MENU TAB LOGIN & DAFTAR
$(document).on('click', '.menu-tab .btn-tab', function () { 
    let ini        = $(this),
        btn_tab    = $('.menu-tab .btn-tab'),
        for_form   = ini.attr('for'),
        f_input1   = $('.f-input.t-1'),
        form_user  = $('.form'),
        input_form = form_user.find("input"),
        msg_info   = $('.msg-info');

    //jika button tab tidak memiliki class aktif, dalam ini untuk menghindari hilangnya form login/daftar setelah beberapa kali ke klik
    if (!ini.hasClass('aktif'))
    { 
        //menghapus class aktif untuk element yang tidak bersangkutan
        btn_tab.removeClass('aktif');
        form_user.addClass('dis-none'); //menghilangkan form yang tidak bersangkutan, di sini menambahkan display none
        msg_info.html(""); //menghapus semua pesan yang ada di form login atau daftar
        
        //menghapus info error setelah ter-submit
        $("#msg-login").html("");
        $('#msg-daftar').html("");

        for (let i = 0; i < input_form.length; i++)    
        { 
            input_form[i].value = ""; //menghapus input atau value setiap menu tab atau button tab ke klik
            f_input1.eq(i).removeClass("aktif"); //menghilangkan class aktif pada f-input, biar posisi placeholdernya berada di tengah
        }

        ini.addClass('aktif');
        $('.' + for_form).removeClass('dis-none'); //menghapus class dis-none guna menampilkan form yang bersangkutan (form di sini :: form login atau daftar)
    }
});


//pencarian kontak
$(document).on("input", ".search-contact input", function () { 
    let ini   = $(this),
        input = ini.val().toString().toLowerCase(), //mendapatkan nilai dari input'an
        user = $('body').find('.show-msg');
    if (input.length !== 0)
    { 
        for (let i = 0; i < user.length; i++)
        {
            let u = user.eq(i).attr('name-user').toString().toLowerCase();
            //jika input tidak sama dengan -1 atau elbih dari 0 maka akan menghapus class .hidden di dalam element a-href ".show-msg", jika tidak di temukan maka akan menambahkan class .hidden pada element a-href ".show-msg"
            (u.indexOf(input) !== -1) ? user.eq(i).removeClass('hidden') : user.eq(i).addClass('hidden');
        }
    }
    else
    {
        user.removeClass('hidden');
    }
});


//menampilkan pesan chat sesuai dengan ID User yang terklik
$(document).on('click', 'a.show-msg', function (x) { 
    x.preventDefault();
    let ini     = $(this),
        url     = ini.attr('href'),
        new_msg = ini.find(".msg-new span"); //pesan baru sebelah kanan di kontak user
    $('.show-msg').removeClass('aktif'); //menghapus semua class "Aktif" pada contact USER sebelah kiri

    if (!ini.hasClass('aktif'))
    { 
        $.ajax({
            url  : url,
            type: "POST",
            success: function (data) { 
                let json = JSON.parse(data);
                $('.chat').html(json.menu);
                ini.addClass('aktif');
                new_msg.html("");
            }
        });
    }
});

//tool-tip di saat hover
$(document).on({
    mouseenter: function ()
    { 
        let ini           = $(this),
            tool          = $("body").find(".tool-tip"), //cari dahulu element tool-tip
            w_hover       = ini.outerWidth(), //mendapatkan panjang element yang di hover
            h_hover       = ini.outerHeight(), //mendapatkan tinggi element yang di hover
            y             = ini.offset().top, //mendapatkan posisi horizontal
            x             = ini.offset().left, //mendapatkan posisi vertical
            width_window  = window.innerWidth, //mendapatkan panjang halaman window
            height_window = window.innerHeight, //mendapatkan tinggi halaman window
            text          = ini.attr('data-tip'), //mendapatkan pesan tool-tip pada attribute "data-tip"
            style         = "left:" + x + ";top:" + y;
        let tool_tip      = "<div class=\"tool-tip\" style=\"" + style + "\">" + text + "<span></span></div>";
        
        //Jika element tool-tip tidak terdapat pada "body" maka akan append tool_tip, jika tidak maka akan menghapus element "tool-tip" dulu
        if (tool.length === 0) {
            $("body").append(tool_tip);
        }
        else
        { 
            tool.remove();
            $("body").append(tool_tip);
        }

        let newTool     = $("body").find(".tool-tip"),
            width_elem  = newTool.outerWidth(),//mendapatkan panjang tool-tip hover setelah di append
            height_elem = newTool.outerHeight(),//mendapatkan tinggi tool-tip hover setelah di append
            minus       = (w_hover > width_elem) ? (w_hover - w_elem) / 2 : (width_elem - w_hover) / 2,//panjang element "hover" dikurangi panjang element "tool-tip", jika panjang w_hover (panjang element yang di hover) lebih besar dari panjang element tool-tip maka : panjang element yang dihover akan dikurangi panjang element "tool-tip" terlebih dahulu kemudian di bagi 2, dan sebaliknya
            total_panjang = width_elem + width_window, //panjang element tool-tip di tambah panjang window 
            total_tinggi  = y + h_hover + (height_elem / 15) + 22; //tinggi element tool-tip + posisi Y di tambah (tinggi element "tool-tip" di bagi 15) + 22 (dimana 22 merupakan tinggi tambahan / untuk berjaga-jaga)
        
        let left       = 0,
            top        = 0;
        
        //membandingkan panjang window dengan panjang element tool-tip (element tool-tip di tambah panjang window)
        if (total_panjang > width_window) //jika panjang element tool-tip (tool-tip + panjang window) melebihi batas panjang window
        {
            left = ((x + width_elem) > width_window) ? x - width_elem + w_hover : x - minus;
            /*
            left: posisi "hover" X akan ditambah panjang element "tool-tip", jika panjang masih melebihi batas width_window maka X [posisi hover] - width_elem [panjang element "tool-tip"] + panjang element yang dihover, jika tidak maka posisi X [Hover] dikurangi $minus

            top : posisi Y (element yang dihover) ditambah dengan tinggi element yang dihover
            */
        }
        //jika total tinggi element melebihi batas tinggi window : posisi Y - tinggi element hover - (tinggi element "tool-tip" / 15) dan sebaliknya
        top = (total_tinggi > height_window) ? y - h_hover - (height_elem / 5) : y + h_hover + (height_elem / 15);
        
        newTool.addClass("aktif");
        newTool.css({ left: left + "px", top: top + "px" });
    },
    mouseleave: function()
    {
        let tool = $("body").find(".tool-tip");
        tool.removeClass("aktif");
        setTimeout(function () { 
            tool.remove();
        }, 200);
    }
}, '.hover');

//form untuk mengirimkan pesan ketika press ENTER
$(document).on('keyup', '.send-input', function (e) { 
    let event  = e || window.event,
        key    = event.which || event.keyCode,
        ini    = $(this),
        form   = ini.parents('form');
    if (key === 13 && !event.shiftKey && ini.val().trim().length !== 0) //sama dengan "ENTER" dan "SHIFT"
    { 
        e.preventDefault();
        $(form).trigger("submit"); //memanipulasi "SUBMIT" ketika textarea menekan "ENTER"
    }
});

//form untuk mengirimkan pesan ketika press ENTER (PREVIEW PHOTO)
$(document).on('keyup', '.preview-input', function (e) { 
    let event     = e || window.event,
        key       = event.which || event.keyCode,
        ini       = $(this),
        form      = $("body").find('form.form-send'),
        text_form = form.find('textarea'),
        preview   = $("body").find(".preview-photo");
    if (key === 13 && !event.shiftKey) //sama dengan "ENTER" dan "SHIFT"
    { 
        (ini.val().trim().length !== 0) ? text_form.val(ini.val()) : text_form.val(ini.val().trim());
        setTimeout(function () { //memberi jeda supaya value pada textarea yang ada di preview photo akan di apply ke textarea di form-send
            $(form).trigger("submit"); //memanipulasi "SUBMIT" ketika textarea menekan "ENTER"
            preview.removeClass('aktif preview-send');
        }, 500);
        e.preventDefault();
        return false;
    }
});

//button untuk mengirimkan pesan
$(document).on('click', '.button-send', function (e) { 
    let ini    = $(this),
        form   = ini.parents('form'),
        text   = form.find('.send-input');
    
    if(text.val().trim().length !== 0) $(form).trigger("submit"); //memanipulasi "SUBMIT" tombol kirim ke klik
    
});

//button untuk mengirimkan pesan (PREVIEW PHOTO)
$(document).on('click', '.preview-button', function (e) { 
    let form      = $("body").find("form.form-send"),
        text_form = form.find('textarea'),
        preview   = $("body").find(".preview-photo"),
        ini       = preview.find("textarea");
    
    (ini.val().trim().length !== 0) ? text_form.val(ini.val()) : text_form.val(ini.val().trim());
    
    setTimeout(function () { //memberi jeda supaya value pada textarea yang ada di preview photo akan di apply ke textarea di form-send
        $(form).trigger("submit"); //memanipulasi "SUBMIT" ketika textarea menekan "ENTER"
        preview.removeClass('aktif preview-send');
    }, 500);
    
});

//FORM LOGIN DAN DAFTAR
$(document).on('submit', 'form.form-log-daf', function (x) {
    x.preventDefault();
    let ini       = $(this),
        input     = $(".form-" + ini.attr('for')).find("input"),
        url       = ini.attr('action'),
        type_form = ini.attr('for'),
        submit    = true;
    for (let i = 0; i < input.length; i++)
    {
        if (input.eq(i).val().length === 0)
        {
            submit    = false;
            let pesan = input.eq(i).attr('msg'); //mengambil pesan pada attribute msg pada setiap input'an
            input.eq(i).parent().prev().html(info('error', 'Harap "' + pesan + '" di isi'));
        }
    }

    //jika inputan form login atau daftar tidak ada yang kosong, maka submit bernilai TRUE
    if (submit)
    { 
        $.ajax({ 
            type: "post",
            data: ini.serialize(),
            url : url,
            success: function (data)
            { 
                let json = JSON.parse(data);
                if(type_form === 'login')
                {
                    if (json.login) //jika login berhasil atau json.login bernilai true
                    { 
                        window.location.href = json.redirect;
                    }
                    else
                    {
                            
                        if(json.msg_error.length !== 0) //jika form validation berjalan lancar tapi terjadi error
                        {
                            $('#msg-login').html(info('error', json.msg_error));
                        }
                        else
                        {
                            $('#login-name').html(json.error.name);
                            $('#login-pass').html(json.error.pass);
                        }
                    }
                }
                else
                {
                    if (json.login) //jika daftar berhasil maka nilai JSON.LOGIN akan bernilai TRUE
                    { 
                        $('.form-login').removeClass("dis-none"); //menampilkan form login
                        $('.form-daftar').addClass("dis-none"); //menghilangkan tampilan form daftar
                        $("#msg-login").html(info('success', "Daftar berhasil, harap login!!!")); //mengirimkan pesan bahwa daftar berhasil
                    }
                    else
                    {
                        if(json.msg_error.length !== 0) //jika form validation berjalan lancar tapi terjadi error
                        {
                            $('#msg-daftar').html(info('error', json.msg_error));
                        }
                        else
                        {
                            $('#daftar-name').html(json.error.name);
                            $('#daftar-uname').html(json.error.uname);
                            $('#daftar-pass').html(json.error.pass);
                            $('#daftar-repass').html(json.error.repass);
                        }
                    }
                }
                
            }
        });
    }
});

//MENU SETTINGS
$(document).on('click', '.menu-settings', function () { 
    let ini = $("body").find(".menu-settings-account");
    //jika menu settings mempunyai class "aktif", maka class "aktif" yang ada di element "settings" akan di hapus, dan sebaliknya.
    (ini.hasClass('aktif')) ? ini.removeClass('aktif') : ini.addClass('aktif');
});

//MENU PROFIL USER
$(document).on('click', '.menu-profil', function () { 
    let ini  = $(this),
        url  = ini.attr('url'),
        menu = $("body").find(".menu-profil-user");
    if (!menu.hasClass('aktif'))
    { 
        let name     = menu.find('#profil-name'),
            name2    = menu.find('#profil-name2'),
            photo    = menu.find('#profil-photo'),
            username = menu.find('#profil-username'),
            status   = menu.find('#profil-status');
        $.ajax({
            url : url,
            type: "POST",
            success: function (data)
            { 
                let json = JSON.parse(data);
                name.html(json.name);
                name2.val(json.name);
                photo.attr('src', json.photo);
                username.val(json.username);
                status.val(json.status);
                menu.addClass('aktif');
            }
        });
    }
    else { menu.removeClass('aktif');}
});

//TAMPIL ALERT
$(document).on('click', '.show-alert', function () {
    let alert    = $("body").find(".alert"),
        ini      = $(this),
        title    = ini.attr('alert-title'), //mendapatkan judul alert
        msg      = ini.attr('alert-msg'), //mendapatkan pesan alert
        type     = ini.attr('alert-type'), //mendapatkan type class alert
        category = ini.attr('data-category'), //mendapatkan kategori aksi pada alert
        url      = ini.attr('url'); //mendapatkan attribute URL
    
    ini.removeClass('error warning success');
    
    alert.find(".body-alert span").html(msg); //pesan alert
    alert.find(".info span").html(title); //judul alert
    alert.find("button").attr("url", url);
    alert.find("button").attr("category", category);
    alert.addClass("aktif " + type);
});

//AKSI PADA ALERT
$(document).on('click', '.confirm-alert', function () { 
    let ini      = $(this),
        url      = ini.attr('url'),
        alert    = $("body").find(".alert"),
        category = ini.attr('category');
    $.ajax({
        url : url,
        type: "POST",
        beforeSend: function () { 
            ini.attr('disabled', true);
        },
        success: function (data)
        { 
            let json = JSON.parse(data);
            //menghapus semua pesan/chat
            if (category === 'del-all-msg' && json.msg === 'success')
            { 
                $('body').find(".chat-wrap").html("");
            }
            ini.attr('disabled', false);
            alert.removeClass("aktif");
        }
    });
});

//MENU ALERT
$(document).on('click', '.close-alert', function () { 
    $("body").find(".alert").removeClass('aktif');
});

$(document).on("click", '.form-send .close', function() {
    let ini   = $(this),
        form  = ini.parents('.form-send'),
        reply = form.find('.reply'),
        send  = form.find('.form-send-inner');
    reply.removeClass('aktif');
    form.attr('category','add');
    form.attr('data-id','');
    send.find('#_reply').val("");
});

//MENAMPILKAN OPSI HAPUS DAN EDIT PADA CHAT
$(document).on('click', '.button-msg', function () { 
    let ini        = $(this),
        Y          = ini.offset().top,
        X          = ini.offset().left,
        ini_height = ini.outerHeight(),
        // mendapatkan class button action chat
        _getButton = ini.hasClass('_friend') ? '.action-msg._friend' : '.action-msg._self',
        // mendapatkan class button action chat yang aktif selain button ini
        _removeBtn = ini.hasClass('_friend') ? '.action-msg._self' : '.action-msg._friend',
        contact    = ini.parents(".contact"),
        // get src photo instead of reply img
        img        = contact.find(".view-photo").attr('src'),
        // get message instead of reply messsage
        msg        = contact.find(".msg > pre").text(),
        data_id    = contact.attr('data-id'),
        action_msg = $("body").find(_getButton),
        removeBtn  = $("body").find(_removeBtn),
        msg_btn    = action_msg.find("._button"),
        width_msg  = action_msg.outerWidth() / 2;

    // set data message + img to function, and it will transfer when user click button "reply"
    getDataMessage(msg, img);

    removeBtn.removeClass('aktif');
    action_msg.css({ left: X - width_msg, top: Y + ini_height });
    msg_btn.attr("data-id", data_id); //memilih data-id yang terdapat pada pesan/chat yang dipilih, kemudian akan diterapkan pada tombol yang terdapat di "action-msg"
    action_msg.addClass('aktif');
});

//menghilangkan element edit pesan
$(document).on('click', "body", function (x) { 

    //jika target click body tidak sama dengan "action-msg" && isi dari action-msg ("._button") dan juga target tidak sama dengan "button-msg" yang dimana "button-msg" akan menampilkan pengaturan chat jika di klik
    if (!$(x.target).is(".action-msg, .action-msg ._button, .button-msg"))
    {
        let action_msg = $('body').find('.action-msg');
        action_msg.removeClass('aktif');
        action_msg.attr("data-id", "");//menghapus isi dari attribute data-id pada "action-msg"
    }
});

//manipulasi file upload pada chat/pesan
$(document).on("click", ".button-photo", function () {
    let ini  = $(this).parents('form.form-send');
        file = ini.find("#_photo");
    file.trigger('click');
});

//menampilkan setiap photo ke Preview Photo
$(document).on("click", '.view-photo', function () { 
    let ini = $(this),
        attr = ini.attr('src'),
        preview = $('body').find('.preview-photo'),
        preview_photo = preview.find("#prev-img");
    preview.addClass("aktif");
    preview_photo.attr('src', attr);
});

//menghilangkan tampilan Preview Photo
$(document).on("click", '.close-view-photo', function () { 
    let ini           = $(this),
        preview       = ini.parents('.preview-photo'),
        preview_photo = preview.find("#prev-img"),
        form          = $("body").find('.form-send'); //form pengiriman chat/pesan;
    
    form.find('#_photo').val("");
    preview.removeClass("aktif preview-send");
    preview_photo.attr('src', "");
});

$(document).on('click', '.reply-msg', function() {
    let ini         = $(this),
        data_reply  = ini.attr('data-reply'),
        chat        = $('.chat').find('.chat-wrap'),
        conct       = $('.chat').find('.chat-wrap .contact');
    for(let i = 0; i < conct.length; i++)
    {
        let contact = conct.eq(i);
        if(data_reply === contact.attr('data-id'))
        {
            chat.scrollTop(contact.offset().top);
        }
    }
});

//membaca gambar
function readGambar(input)
{

    if (input.files && input.files[0])
    {
        let reader = new FileReader();
  
        reader.onload = function (e)
        {
            let form      = $("body").find('.form-send'), //form pengiriman chat/pesan
                text      = form.find("textarea"), //input chat/pesan
                preview   = $("body").find(".preview-photo"),
                img       = preview.find('#prev-img'), //gambar pada preview photo
                prev_text = preview.find("textarea"); //input pada preview photo
            prev_text.val(text.val()); //apply text input chat/pesan ke input preview photo
            preview.addClass("aktif preview-send");
            img.attr('src', e.target.result); //apply gambar
        }
  
        reader.readAsDataURL(input.files[0]);
    }
}
//mendeteksi apakah input  gambar ada perubahan ketika photo sudah di klik OPEN atau OK
$(document).on("change", ".form-send #_photo", function() {
    readGambar(this);
});