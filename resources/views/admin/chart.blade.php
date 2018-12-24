<canvas id="myChart" ></canvas>

<script>
    $(function () {
    var ctx = document.getElementById("myChart").getContext('2d');
    @if (isset($height))
        ctx.height ={{$height}};
    @endif
    
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [@foreach ($data['days'] as $item) '{{$item}}', @endforeach],
            datasets: [{
                label: '#  销售数量',
                data: [{{$data['vals']}}],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            },{
                label: '#  销售金额',
                data: [10,20,30,40,50,60,70],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1,
                borderDash: [5, 5],
            }
            ]
        },
        options: {
            @if(isset($height))
                maintainAspectRatio: false,
            @endif
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
});

</script>