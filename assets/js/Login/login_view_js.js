$(document).ready(function () {
    $('#btn_login').click(function () {
        login();
    });
});

function runScript(e) {
    if (e.keyCode == 13) {
        login();
    }
};

function login()
{
    var emp_email = $('#username').val();
    var emp_pname = $('#password').val();
    var remember = $('#remember').is(":checked");
    if (emp_email == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Please specify username',
            text:'โปรดระบุผู้ใช้'
        })
        return;
    } 
    if (emp_pname == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Please specify password',
            text:'โปรดระบุรหัสผ่าน'
        })
        return;
    }
    $.ajax({
        type: "POST",
        url: "Login/get_login",
        data: { emp_email: emp_email, emp_pname: emp_pname ,remember:remember},
        success: function (response) {
            console.log(response);
            if (response == "ng") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incorrect username or password',
                    text:'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
                })
                return;
            }
            if (response == "ng_dup") {
                Swal.fire({
                    icon: 'warning',
                    title: 'User has aready login',
                    text:'ผู้ใช้นี้ถูกใช้งานในอุปกรณ์อื่น'
                })
                return;
            }
            if (response == "ok") {
                window.location.href = ('Main');
            }else if(response == "ok_reserve"){
                window.location.href = ('Reserve_histrory');
            }
        },
        error: function (error) {
            console.error(error);
        }
   })
};


function forgotPassword(){
    window.location.href = ('Login/forgotPass');
}
