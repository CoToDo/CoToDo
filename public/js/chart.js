var chart;
var timesSeconds = [];

function graphTask(data) {
    if (data.length === 0) {
        document.getElementById('sel').style.display = "none";
        return;
    }
    let count = 0;
    data.forEach(function (e) {
        if (e.sum == null) {
            count++;
        }
    });
    if (count === data.length) {
        document.getElementById('sel').style.display = "none";
        return;
    }
    document.getElementById('sel').style.display = "block";
    let users = [];
    let times = [];
    for (var i = 0; i < data.length; i++) {
        users.push(data[i].mail);
        times.push(data[i].sum);
        timesSeconds.push(data[i].sum);
    }

    let ctx = document.getElementById('myChart').getContext('2d');
    chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'bar',
        // The data for our dataset
        data: {
            labels: users,
            data: times,
            datasets: [{
                label: "Work done on this task",
                data: times,
                backgroundColor: palette('tol', users.length).map(function(hex) {
                    return '#' + hex;
                })
            }]
        },

        // Configuration options go here
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function(value) {
                            return value + " s";
                        }
                    }
                }]
            }

        }
    });

}

function loadGraph(location) {
    $.get( window.location.href + "/" + location , function( data ) {
        graphTask($.parseJSON(data));
    });
}

function updateData() {
    var OptionsEnum = {"s":1, "m":60, "h":3600, "d":86400};

    var x = document.getElementById("sel");
    var opt = x.options[x.selectedIndex].value;

    help = chart.data.datasets[0].data;

    for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
        chart.data.datasets[0].data[i] = timesSeconds[i] / OptionsEnum[opt];
    }

    chart.options.scales.yAxes[0].ticks.callback = function(value) {
        return value.toFixed(2) + opt;
    };

    chart.update();
}

