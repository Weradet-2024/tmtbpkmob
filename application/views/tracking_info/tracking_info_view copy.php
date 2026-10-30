<style>
    body {
        background: linear-gradient(#62cff4, #2c67f2);
    }

    .presentation-title {
        font-size: 70px !important;
    }

    .shadow-5 {
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12),
            0 2px 2px rgba(0, 0, 0, 0.12),
            0 4px 4px rgba(0, 0, 0, 0.12),
            0 8px 8px rgba(0, 0, 0, 0.12),
            0 16px 16px rgba(0, 0, 0, 0.12);
    }

    /* CSS */
    .button-17 {
        align-items: center;
        appearance: none;
        background-color: #fff;
        border-radius: 24px;
        border-style: none;
        box-shadow: rgba(0, 0, 0, .2) 0 3px 5px -1px, rgba(0, 0, 0, .14) 0 6px 10px 0, rgba(0, 0, 0, .12) 0 1px 18px 0;
        box-sizing: border-box;
        color: #3c4043;
        cursor: pointer;
        display: inline-flex;
        fill: currentcolor;
        font-family: "Google Sans", Roboto, Arial, sans-serif;
        font-size: 14px;
        font-weight: 500;
        height: 48px;
        justify-content: center;
        letter-spacing: .25px;
        line-height: normal;
        max-width: 100%;
        overflow: visible;
        padding: 13px 17px;
        position: relative;
        text-align: center;
        text-transform: none;
        transition: box-shadow 280ms cubic-bezier(.4, 0, .2, 1), opacity 15ms linear 30ms, transform 270ms cubic-bezier(0, 0, .2, 1) 0ms;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        width: auto;
        will-change: transform, opacity;
        z-index: 0;
        position: absolute;
        bottom: 40px;
        left: 15px;
    }

    .button-17:hover {
        background: #F6F9FE;
        color: #174ea6;
    }

    .button-17:active {
        box-shadow: 0 4px 4px 0 rgb(60 64 67 / 30%), 0 8px 12px 6px rgb(60 64 67 / 15%);
        outline: none;
    }

    .button-17:focus {
        outline: none;
        border: 2px solid #4285f4;
    }

    .button-17:not(:disabled) {
        box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
    }

    .button-17:not(:disabled):hover {
        box-shadow: rgba(60, 64, 67, .3) 0 2px 3px 0, rgba(60, 64, 67, .15) 0 6px 10px 4px;
    }

    .button-17:not(:disabled):focus {
        box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
    }

    .button-17:not(:disabled):active {
        box-shadow: rgba(60, 64, 67, .3) 0 4px 4px 0, rgba(60, 64, 67, .15) 0 8px 12px 6px;
    }

    .button-17:disabled {
        box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
    }

    .hidden {
        display: none;
    }
</style>

<br>
<div class="container" style="margin-top:50px;">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row">
                <div class="col-12 position-relative">
                    <div id="map" style="height: 60vh; width: 100% position: relative;">
                    </div>
                    <button class="button-17" role="button" id="current_location" onclick="current_location();"><i
                            class="bi bi-cursor" style="color: #0d6efd"></i></button>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>

<div class="container" style="margin-bottom:50px; margin-top: -4rem; !important;">
    <div class="card" style="border-radius: 10% !important;">
        <div class="card-body">
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-geo me-3"></i>
                                    <small id="lbl_route_start"
                                        style="margin-left: 10px; font-size: 0.9em; color: #333;"></small>
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill me-3"></i>
                                    <small id="lbl_end"
                                        style="margin-left: 10px; font-size: 0.9em; color: #333;"></small>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="card bg-primary" style="max-height: 6rem !important;">
                                <div class="card-body">
                                    <span style="font-size:14px;" class="card-title text-white"><?php echo $this->lang->line("distance");?></span>
                                    <br>
                                    <p class="card-text text-white">
                                        <span id="get_distance_km"></span><br>
                                        <?= $this->lang->line('km'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card bg-primary" style="max-height: 6rem !important;">
                                <div class="card-body">
                                    <span style="font-size:14px;" class="card-title text-white"><?php echo $this->lang->line("time");?></span>
                                    <br>
                                    <p class="card-text text-white">
                                        <span id="get_distance_time"></span><br>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table" style="font-size:14px;">

                                        <tr>
                                            <td style='width:20%;'><i class="fa fa-address-book"></i></td>
                                            <td style='width:80%;'>: <span id="lbl_drv_name"></span></td>
                                        </tr>
                                        <tr>
                                            <td style='width:20%;'><i class="fa fa-map"></i></td>
                                            <td style='width:80%;'>: <span id="lbl_route_name"></span></td>
                                        </tr>
                                        <tr>
                                            <td style='width:20%;'><i class="fa fa-car"></i></td>
                                            <td style='width:80%;'>: <span id="lbl_license_plate"></span></td>
                                        </tr>
                                        <tr>
                                            <td style='width:20%;'><i class="fa fa-mobile-phone"></i></td>
                                            <td style='width:80%;'>: <a id="dial" href="#"><span
                                                        id="lbl_drv_tel"></span></a></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input id="get_cookie" class="get_cookie" type="hidden">

<!-- <div id="result" class="result"></div> -->

<script>
    var map;
    map = new longdo.Map({
        placeholder: document.getElementById('map')
    });
</script>