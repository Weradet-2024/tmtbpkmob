<style>
    #reader {
        width: 640px;
    }

    @media(max-width: 600px) {
        #reader {
            width: 300px;
        }
    }

    .empty {
        display: block;
        width: 100%;
        height: 20px;
    }
</style>
<div class="container" style="margin-top:20px;">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row">

                <div class="col-12">
                    <div style="border-radius:5px;  padding:5px;" data-aos="fade-left">
                        <span class="row justify-content-center text-center mb-5" data-aos="fade-up"
                            style="font-size:24px !important; color:#FFF;"><?= $this->lang->line('scan_qrcode'); ?></span>

                        <div class="row">
                            <div class="col-md-12" style="text-align: center;margin-top:50px;">
                                <div id="reader" style="display: inline-block;"></div>
                                <div class="empty"></div>
                                <div id="scanned-result"></div>
                                <div>
                                    <select id="facingMode" style="display:none;">
                                        <option value="user">user</option>
                                        <option value="environment">environment</option>
                                    </select>
                                    <button id="start" style="display:none;">Start Scanning</button>
                                </div>
                            </div>
                            <div style='text-align:center;'>
                                <small style='color:#000;'><?= $this->lang->line('text_scan_1'); ?> <br>
                                    <?= $this->lang->line('text_scan_2'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="return_result"></div>
        </div>
    </div>
</div>

<script>
    var scanning = false;
    var scanningRequested = false;
    var html5qrcode = new Html5Qrcode("reader", true);


    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete" || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    function startScanning(facingMode) {
        console.log(facingMode)
        var results = document.getElementById('scanned-result');
        var lastMessage;
        var codesFound = 0;

        function onScanSuccess(qrCodeMessage) {

            if (lastMessage !== qrCodeMessage) {
                lastMessage = qrCodeMessage;
                ++codesFound;
                results.innerHTML += `<div hidden>[${codesFound}] - ${qrCodeMessage}</div>`;

                window.location.href = ('Attendance_result');

                sessionStorage.setItem('qr_message', qrCodeMessage);
            }

        }
        // }
        return html5qrcode.start({
            facingMode: facingMode
        }, {
            fps: 10,
            qrbox: 250
        },
            onScanSuccess);
    }

    function stopScanning() {
        return html5qrcode.stop();
    }
    docReady(function () {
        startScanning("environment");
        hljs.initHighlightingOnLoad();
        var button = document.getElementById('start');
        var facingModeSelect = document.getElementById('facingMode');
        button.addEventListener('click', function () {
            if (!scanning) {
                button.disabled = true;
                facingModeSelect.disabled = true;
                startScanning(facingModeSelect.value)
                    .then(_ => {
                        scanning = true;
                        button.disabled = false;
                        button.innerHTML = "Stop Scanning";
                    })
                    .catch(err => {
                        button.disabled = false;
                        facingModeSelect.disabled = false;
                        alert(err);
                    })
            } else {
                button.disabled = true;
                stopScanning()
                    .then(_ => {
                        scanning = false;
                        button.disabled = false;
                        facingModeSelect.disabled = false;
                        button.innerHTML = "Start Scanning";
                    })
                    .catch(err => {
                        button.disabled = false;
                        alert(err);
                    })
            }
        });
    });
</script>