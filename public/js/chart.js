var ctx = document.getElementById('myChart').getContext('2d');
var user = {{ user|json_encode|raw }};
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: autocomplete(document.getElementById("works"), user),
        datasets: [{
            label: "Work done on this task",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [0, 10, 5, 2, 20, 30, 45],
        }]
    },

    // Configuration options go here
    options: {}
});
