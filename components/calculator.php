<style>
    .btn.btn-calc {
        background-color: #EC9841;
        border-radius: 20px;
        padding: 9px 9px;
        margin-left: 8px;
        transition: background-color 0.3s ease, transform 0.15s ease;
        cursor: pointer;
    }

    .btn.btn-calc:hover {
        background-color: #f0ad4e;
    }

    .btn.btn-calc i {
        color: #fff;
        font-size: 15px;
    }

    /* Modal draggable */
    .modal.draggable .modal-dialog {
        position: fixed;
        margin: 0;
    }

    .modal.draggable .modal-header {
        cursor: move;
        user-select: none;
    }
</style>


<!-- ICON CALCULATOR (NAVBAR / SIDEBAR) -->
<div class="btn btn-calc"
     data-toggle="modal"
     data-target="#calculatorModal"
     title="Kalkulator">
    <i class="fa fa-calculator"></i>
</div>

<!-- MODAL CALCULATOR -->
<div class="modal fade draggable" id="calculatorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;">

            <div class="modal-header bg-primary text-white"
                 style="border-bottom:none;">
                <h4 class="modal-title">
                    <i class="fa fa-calculator"></i> Kalkulator
                </h4>
                <button type="button" style="margin-top: -25px;" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" style="padding:15px;">
                <input type="text"
                       id="calcDisplay"
                       class="form-control text-right"
                       style="font-size:18px;height:45px;margin-bottom:12px;"
                       placeholder="0"
                       autocomplete="off"
                       autofocus
                       >

                <div class="row" style="margin-bottom:6px;">
                    <div class="col-xs-3"><button class="btn btn-default btn-block" onclick="calcClear()">C</button></div>
                    <div class="col-xs-3"><button class="btn btn-default btn-block" onclick="calcInput('%')">%</button></div>
                    <div class="col-xs-3"><button class="btn btn-default btn-block" onclick="calcInput('/')">÷</button></div>
                    <div class="col-xs-3"><button class="btn btn-danger btn-block" onclick="calcBack()">⌫</button></div>
                </div>

                <div class="row" style="margin-bottom:6px;">
                    <div class="col-xs-3"><button class="btn btn-info btn-block" onclick="calcFunc('sqrt')">√</button></div>
                    <div class="col-xs-3"><button class="btn btn-info btn-block" onclick="calcFunc('square')">x²</button></div>
                    <div class="col-xs-3"><button class="btn btn-info btn-block" onclick="calcFunc('inv')">1/x</button></div>
                    <div class="col-xs-3"><button class="btn btn-info btn-block" onclick="calcToggleSign()">±</button></div>
                </div>

                <div class="row" style="margin-bottom:6px;">
                    <div class="col-xs-3"><button class="btn btn-secondary btn-block" onclick="calcFunc('sin')">sin</button></div>
                    <div class="col-xs-3"><button class="btn btn-secondary btn-block" onclick="calcFunc('cos')">cos</button></div>
                    <div class="col-xs-3"><button class="btn btn-secondary btn-block" onclick="calcFunc('tan')">tan</button></div>
                    <div class="col-xs-3"><button class="btn btn-secondary btn-block" onclick="calcFunc('pi')">π</button></div>
                </div>

                <div class="row" style="margin-bottom:6px;">
                    <div class="col-xs-3"><button class="btn btn-dark btn-block" onclick="calcFunc('log')">log</button></div>
                    <div class="col-xs-3"><button class="btn btn-dark btn-block" onclick="calcFunc('ln')">ln</button></div>
                    <div class="col-xs-3"><button class="btn btn-dark btn-block" onclick="calcMemory('MC')">MC</button></div>
                    <div class="col-xs-3"><button class="btn btn-dark btn-block" onclick="calcMemory('MR')">MR</button></div>
                </div>

                <div class="row" style="margin-bottom:6px;">
                    <div class="col-xs-6"><button class="btn btn-dark btn-block" onclick="calcMemory('M+')">M+</button></div>
                    <div class="col-xs-6"><button class="btn btn-dark btn-block" onclick="calcMemory('M-')">M-</button></div>
                </div>

                <div class="row" style="margin-top:6px;">
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('7')">7</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('8')">8</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('9')">9</button></div>
                    <div class="col-xs-3"><button class="btn btn-warning btn-block" onclick="calcInput('*')">×</button></div>
                </div>

                <div class="row" style="margin-top:6px;">
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('4')">4</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('5')">5</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('6')">6</button></div>
                    <div class="col-xs-3"><button class="btn btn-warning btn-block" onclick="calcInput('-')">−</button></div>
                </div>

                <div class="row" style="margin-top:6px;">
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('1')">1</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('2')">2</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('3')">3</button></div>
                    <div class="col-xs-3"><button class="btn btn-warning btn-block" onclick="calcInput('+')">+</button></div>
                </div>

                <div class="row" style="margin-top:6px;">
                    <div class="col-xs-6"><button class="btn btn-light btn-block" onclick="calcInput('0')">0</button></div>
                    <div class="col-xs-3"><button class="btn btn-light btn-block" onclick="calcInput('.')">.</button></div>
                    <div class="col-xs-3"><button class="btn btn-success btn-block" onclick="calcResult()">=</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let memory = 0;

    function getDisplay(){
        return document.getElementById('calcDisplay');
    }

    function calcInput(val){
        getDisplay().value += val;
    }

    function calcClear(){
        getDisplay().value = '';
    }

    function calcBack(){
        getDisplay().value = getDisplay().value.slice(0, -1);
    }

    function calcResult(){
        try{
            getDisplay().value = eval(getDisplay().value);
        }catch{
            getDisplay().value = 'Error';
        }
    }

    function calcToggleSign(){
        let v = getDisplay().value;
        if(!v) return;
        getDisplay().value = parseFloat(v) * -1;
    }

    function calcFunc(type){
        let v = parseFloat(getDisplay().value);
        if(isNaN(v) && type !== 'pi') return;

        switch(type){
            case 'sqrt': getDisplay().value = Math.sqrt(v); break;
            case 'square': getDisplay().value = Math.pow(v,2); break;
            case 'inv': getDisplay().value = 1/v; break;
            case 'sin': getDisplay().value = Math.sin(v * Math.PI / 180); break;
            case 'cos': getDisplay().value = Math.cos(v * Math.PI / 180); break;
            case 'tan': getDisplay().value = Math.tan(v * Math.PI / 180); break;
            case 'log': getDisplay().value = Math.log10(v); break;
            case 'ln': getDisplay().value = Math.log(v); break;
            case 'pi': getDisplay().value += Math.PI.toFixed(8); break;
        }
    }

    function calcMemory(type){
        let v = parseFloat(getDisplay().value) || 0;

        switch(type){
            case 'MC': memory = 0; break;
            case 'MR': getDisplay().value = memory; break;
            case 'M+': memory += v; break;
            case 'M-': memory -= v; break;
        }
    }
</script>

<!-- Input By Keyboard -->
<script>
    document.addEventListener('keydown', function(e){
        // hanya aktif kalau modal kalkulator terbuka
        if(!$('#calculatorModal').hasClass('in')) return;

        const display = document.getElementById('calcDisplay');
        if(!display) return;

        // angka & operator
        if(/[0-9+\-*/.]/.test(e.key)){
            display.value += e.key;
            e.preventDefault();
        }

        // enter = hasil
        if(e.key === 'Enter'){
            calcResult();
            e.preventDefault();
        }

        // backspace
        if(e.key === 'Backspace'){
            display.value = display.value.slice(0, -1);
            e.preventDefault();
        }

        // escape = clear
        if(e.key === 'Escape'){
            calcClear();
            e.preventDefault();
        }
    });

    $('#calculatorModal').on('shown.bs.modal', function () {
        $('#calcDisplay').focus();
    });

    // OPEN MODAL VIA ALT + SPACE
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.code === 'Space') {
            e.preventDefault();
            $('#calculatorModal').modal('show');
        }
    });
</script>

<!-- Draggable Modal -->
<script>
$(function () {
    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;

    const modal = $('#calculatorModal');
    const dialog = modal.find('.modal-dialog');

    // DRAG HEADER
    modal.find('.modal-header').on('mousedown', function (e) {
        isDragging = true;

        const pos = dialog.offset();
        offsetX = e.pageX - pos.left;
        offsetY = e.pageY - pos.top;

        e.preventDefault();
    });

    $(document).on('mousemove', function (e) {
        if (!isDragging) return;

        dialog.css({
            top: e.pageY - offsetY,
            left: e.pageX - offsetX
        });
    });

    $(document).on('mouseup', function () {
        isDragging = false;
    });

    // FIX POSISI AWAL (ANTI MUNCUL DI KIRI)
    modal.on('shown.bs.modal', function () {
        const winW = $(window).width();
        const winH = $(window).height();

        dialog.css({
            top: (winH - dialog.outerHeight()) / 2,
            left: (winW - dialog.outerWidth()) / 2
        });
    });
});
</script>






