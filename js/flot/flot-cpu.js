<!-- Javascript -->
<script>
var data = [];
var dataset;
var totalPoints = 50;
var updateInterval = 1000;
var now = new Date().getTime();


function GetData() {
    data.shift();

    while (data.length < totalPoints) {     
        var y = Math.random() * 100;
        var temp = [now += updateInterval, y];

        data.push(temp);
    }
}

var options = {
    series: {
        lines: {
            show: true,
            lineWidth: 1.2,
            fill: true
        }
    },
    xaxis: {
        mode: "time",
        tickSize: [2, "second"],
        tickFormatter: function (v, axis) {
            var date = new Date(v);

            if (date.getSeconds() % 20 == 0) {
                var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
                var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

                return hours + ":" + minutes + ":" + seconds;
            } else {
                return "";
            }
        },
        axisLabel: "Time",
        axisLabelUseCanvas: true,
        axisLabelFontSizePixels: 12,
        axisLabelFontFamily: 'Verdana, Arial',
        axisLabelPadding: 10
    },
    yaxis: {
        min: 0,
        max: 100,        
        tickSize: 5,
        tickFormatter: function (v, axis) {
            if (v % 10 == 0) {
                return v + "%";
            } else {
                return "";
            }
        },
        axisLabel: "CPU loading",
        axisLabelUseCanvas: true,
        axisLabelFontSizePixels: 12,
        axisLabelFontFamily: 'Verdana, Arial',
        axisLabelPadding: 6
    },
    legend: {        
        labelBoxBorderColor: "#fff"
    }
};

$(document).ready(function () {
    GetData();

    dataset = [
        { label: "CPU", data: data }
    ];

    $.plot($("#flotcontainer"), dataset, options);

    function update() {
        GetData();

        $.plot($("#flotcontainer"), dataset, options)
        setTimeout(update, updateInterval);
    }

    update();
});



</script>
