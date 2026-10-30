$(document).ready(function () {

    const queryString = sessionStorage.getItem('qr_message');

    key = queryString;

    if (key == null) {

        window.location.href = ('Home');

    } else {

        getData(key);

    }

});

function getData(key) {
    $.ajax({
        type: "POST",
        url: "Attendance_result/get_data",
        data: { key: key },
        success: function (response) {

            console.log(response);

            if (response == "bus_service") {

                document.getElementById('bus_service').style.display = 'block';
            } else if (response == "Wrong_QRcode") {

                document.getElementById('wrong_qr_code').style.display = 'block';
            } else {

                //set data in layout
                json_data = JSON.parse(response);
                set_data_in_layout(json_data);

            }

        },
        error: function(error){

            console.error(error);
        }
    });
};

function set_data_in_layout(data) {

    console.log(data.status);

    document.getElementById('scan_qr_code').style.display = 'block';

    if (data.status == "NO") {
        document.getElementById('notpass').style.display = 'block';
    } else if (data.status == "YES") {
        document.getElementById('pass').style.display = 'block';
    }

    document.getElementById('lbl_result_status').innerHTML = "<h6 class=\"text-success\">ตรวจสอบการขึ้นรถสำเร็จ</h6>";
    document.getElementById('lbl_result_name').innerHTML = "<h6>" + data.name + "<h6>";
    document.getElementById('lbl_result_date').innerHTML = "<h6>" + data.date_time + "<h6>";
    document.getElementById('lbl_result_route').innerHTML = "<h6>" + data.route + "<h6>";
    document.getElementById('lbl_result_trip').innerHTML = "<h6>" + data.trip + "<h6>";
    document.getElementById('lbl_result_veh_type').innerHTML = "<h6>" + data.veh_type + "<h6>";
    document.getElementById('lbl_result_veh_license').innerHTML = "<h6>" + data.veh_license + "<h6>";
    document.getElementById('lbl_result_veh_code').innerHTML = "<h6>" + data.veh_code + "<h6>";
    document.getElementById('lbl_result_driver').innerHTML = "<h6>" + data.driver + "<h6>";
    document.getElementById('lbl_result_driver_tel').innerHTML = "<h6>" + data.driver_tel + "<h6>";

    sessionStorage.removeItem("qr_message");

}
