{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Firebase Migration | Graphs</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  {include file="header.tpl"}
  {include file="left_sidebar.tpl"}

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
	Firebase Migration
        <small>Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/firebasemigration/"><i class="fa fa-gear"></i> Firebase Migration</a></li>
        <li class="active"><a href="/admin/firebasemigration/graphs/"><i class="fa fa-pie-chart"></i> Graphs</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
		<p>Timestamp:<b><span id="last_updated"></span></b></p>
		<p>Legacy users:<b><span id="total_legacy"></span> <span id="percent_legacy"></span>%</b></p>
		<p>Firebase migrated users:<b><span id="total_migrated"></span> <span id="percent_migrated"></span>%</b></p>
        </div>
            <div class="box-body">
		<canvas id="users_chartcanvas"></canvas>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->








    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  {include file="footer.tpl"}
</div>
<!-- ./wrapper -->

{include file="prebodyend_includes.tpl"}

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js" integrity="sha256-JG6hsuMjFnQ2spWq0UiaDRJBaarzhFbUxiUTxQDA9Lk=" crossorigin="anonymous"></script>
<script>
	{literal}
		$('#last_updated').text("LOADING");

		function reload_graph_data() {
			$.get( "/admin/firebasemigration/stats_json", function(data) {
				console.log(data);
				$('#last_updated').text(data.timestamp);
				$('#total_legacy').text(data.legacy);
				$('#percent_legacy').text( Math.round((((data.legacy/data.total)*100)*100))/100 );

				$('#total_migrated').text(data.migrated);
				$('#percent_migrated').text( Math.round((((data.migrated/data.total)*100)*100))/100 );

				users_graph.data.datasets[0].data[0] = data.legacy;
				users_graph.data.datasets[0].data[1] = data.migrated;
				users_graph.update();
			}).fail(function(error) {
				console.log(error);
			});
		};
	{/literal}
</script>

<script>
var users_chart_ctx = document.getElementById("users_chartcanvas").getContext('2d');
var users_graph = new Chart(users_chart_ctx, {
    type: 'pie',
    data: {
        labels: ["Legacy", "Firebase migrated"],
        datasets: [{
            data: [0,0],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
            ],
            borderWidth: 1
        }]
    }
});


setTimeout(function() {
	setInterval(function() {
		reload_graph_data();
	}, 1000);
}, 2000);
reload_graph_data();
</script>


</script>
</body>
</html>
