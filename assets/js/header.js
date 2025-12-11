$(document).ready(function () {
    getMenu();
});

function getCookie(cookieName) {
    let cookie = {};
    document.cookie.split(';').forEach(function (el) {
        let [key, value] = el.split('=');
        cookie[key.trim()] = value;
    })
    return cookie[cookieName];
};

function getMenu() {
    var lang = getCookie("lang");
    $.ajax({
        type: "POST",
        url: "Login/getMenu",
        data: {},
        success: function (response) {
            var obj = JSON.parse(response);
            for (var i = 0; i < obj.length; i++) {
                (function(i) {
                    var module_name_en = obj[i]['module_name_en'].toLowerCase();
                    var module_name_th = obj[i]['module_name_th'];
                    var module_icon = obj[i]['module_icon'];
                    var module_link = obj[i]['module_link'];
            
                    var module_item_id = "mn_item_" + module_link;
                    var module_icon_id = "mn_icon_" + module_link;
                    var module_lbl_id = "mn_lbl_" + module_link;
                    var module_link_id = "mn_link_" + module_link;
            
                    $('#' + module_item_id).show();
                    $('#' + module_icon_id).attr('class', 'menu-item tf-icons ' + module_icon);
                    $('#' + module_lbl_id).text(module_name_th);
                    $('#' + module_link_id).click(function () {
                        window.location.href = module_link;
                    });
                })(i);
            }
            
        }
    })
};