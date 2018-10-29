function graphTask(arr, arr1) {
    let ctx = document.getElementById('myChart').getContext('2d');
    let chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'bar',

        // The data for our dataset
        data: {
            labels: arr1,
            datasets: [{
                label: "Work done on this task",
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: loadTimes(),
                // data: [0, 10, 5, 2, 20, 30, 45],
            }]
        },

        // Configuration options go here
        options: {}
    });
}

function loadTimes() {
    var data;
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            data =  this.responseText;
        }
    };
    console.log(window.location.href);
    console.log(this.responseText);
    xhttp.open("GET", window.location.href + "/times", true);
    xhttp.send();
    console.log(data);
    console.log(this.readyState + " " + this.status);
}

