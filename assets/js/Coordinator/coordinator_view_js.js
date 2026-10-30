$(document).ready(function () {
    $('#tb_reservation_controller').DataTable({
        'paging': false,           // Disable pagination
            'ordering': false,         // Disable column ordering
            'searching': false,        // Disable search/filtering
            'info': false,             // Disable table information display
            'bLengthChange': false,    // Disable the ability to change the number of rows displayed
    });
    $('#tb_vendor_controller').DataTable({
        'paging': false,           // Disable pagination
            'ordering': false,         // Disable column ordering
            'searching': false,        // Disable search/filtering
            'info': false,             // Disable table information display
            'bLengthChange': false,    // Disable the ability to change the number of rows displayed
    });
    $('#tb_system_controller').DataTable({
        'paging': false,           // Disable pagination
            'ordering': false,         // Disable column ordering
            'searching': false,        // Disable search/filtering
            'info': false,             // Disable table information display
            'bLengthChange': false,    // Disable the ability to change the number of rows displayed
    });
	getData();
});

function getData() {
	$.ajax({
		type: "POST",
		url: "Coordinator/get_data",
		success: function (response) {
            let obj = JSON.parse(response);
            //create table user reservation
            console.log(obj);
            var tb_reservation_controller = $('#tb_reservation_controller').DataTable();
            tb_reservation_controller.clear().draw();
    
            obj.data.forEach((row, index) => {
                if(row.coordinator_type === "1"){

                    let btn  = "<a type='button' class='btn btn-info no-loading-overlay' href='tel:" +(row.coordinator_tel) + "'" +
                                          "style='padding: 5px 10px !important;'>" +
                                            "<i class='bi bi-phone'></i>"
                                        + "</a>"
      
                    tb_reservation_controller.row.add([row.coordinator_name, row.coordinator_tel, btn]).draw();
        
                }
            });

            var tb_vendor_controller = $('#tb_vendor_controller').DataTable();
            tb_vendor_controller.clear().draw();
            obj.data.forEach((row, index) => {
                if(row.coordinator_type == "2"){

                    let btn  = "<a type='button' class='btn btn-info no-loading-overlay' href='tel:" +(row.coordinator_tel) + "'" +
                                          "style='padding: 5px 10px !important;'>" +
                                            "<i class='bi bi-phone'></i>"
                                        + "</a>"
      
                   tb_vendor_controller.row.add([row.coordinator_name, row.coordinator_tel, btn]).draw();
        
                }
            });

            var tb_system_controller = $('#tb_system_controller').DataTable();
            tb_system_controller.clear().draw();
            obj.data.forEach((row, index) => {
                if(row.coordinator_type == "3"){

                    let btn  = "<a type='button' class='btn btn-info no-loading-overlay' href='tel:" +(row.coordinator_tel) + "'" +
                                          "style='padding: 5px 10px !important;'>" +
                                            "<i class='bi bi-phone'></i>"
                                        + "</a>"
      
                    tb_system_controller.row.add([row.coordinator_name, row.coordinator_tel, btn]).draw();
        
                }
            });

		},error:function(error){
            console.error(error);
        }
	});
};



