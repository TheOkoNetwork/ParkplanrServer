{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Picsolve Downloader Bots | Graphs</title>
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
        Picsolve Downloader Bots
        <small>Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/picsolvedownloaderbots/"><i class="fa fa-gear"></i> Picsolve Downloader bots</a></li>
        <li class="active"><a href="/admin/picsolvedownloaderbots/graphs/"><i class="fa fa-pie-chart"></i> Graphs</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
		<p>Timestamp:<b><span id="last_updated"></span></b></p>
		<p>Total Queued:<b><span id="total_queued"></span></b></p>
		<p>Total Processing:<b><span id="total_processing"></span></b></p>
        </div>
            <div class="box-body">
		<div id="googledrive_div">
			<p>Google Drive Queued:<b><span id="googledrive_queued"></span></b> Google Drive Processing:<b><span id="googledrive_processing"></span></b> <button onclick="graph_hideshow('googledrive');" class="btn btn-primary">Hide/Show</button></p>
			<canvas id="googledrive_chartcanvas"></canvas>
			<hr />
		</div>

		<div id="dropbox_div">
			<p>Dropbox Queued:<b><span id="dropbox_queued"></span></b> Dropbox Processing:<b><span id="dropbox_processing"></span></b><button onclick="graph_hideshow('dropbox');" class="btn btn-primary">Hide/Show</button></p>
			<canvas id="dropbox_chartcanvas"></canvas>
			<hr />
	            </div>
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
			$.get( "/admin/picsolvedownloaderbots/stats_json", function(data) {
				console.log(data);
				$('#last_updated').text(data.timestamp);
				$('#total_queued').text(data.total_queued);
				$('#total_processing').text(data.total_processing);
				if (data.total_queued>=10) {
					$('#total_queued').css('background-color','red');
				} else {
					$('#total_queued').css('background-color','');
				};

				$('#googledrive_queued').text(data.googledrive_queued);
				$('#googledrive_processing').text(data.googledrive_processing);
				googledrive_graph.data.datasets[0].data[0] = data.googledrive_queued;
				googledrive_graph.data.datasets[0].data[1] = data.googledrive_processing;
				googledrive_graph.update();
				if (data.googledrive_queued>=5) {
					$('#googledrive_queued').css('background-color','red');
					$('#googledrive_div').css('outline-color','red');
					$('#googledrive_div').css('outline-style','dotted');
				} else {
					$('#googledrive_queued').css('background-color','');
					$('#googledrive_div').css('outline-color','');
					$('#googledrive_div').css('outline-style','');
				};

				$('#dropbox_queued').text(data.dropbox_queued);
				$('#dropbox_processing').text(data.dropbox_processing);
				dropbox_graph.data.datasets[0].data[0] = data.dropbox_queued;
				dropbox_graph.data.datasets[0].data[1] = data.dropbox_processing;
				dropbox_graph.update();
				if (data.dropbox_queued>=5) {
					$('#dropbox_queued').css('background-color','red');
					$('#dropbox_div').css('outline-color','red');
					$('#dropbox_div').css('outline-style','dotted');
				} else {
					$('#dropbox_queued').css('background-color','');
					$('#dropbox_div').css('outline-color','');
					$('#dropbox_div').css('outline-style','');
				};

			}).fail(function(error) {
				console.log(error);
			});
		};
	{/literal}
</script>

<script>
var googledrive_chart_ctx = document.getElementById("googledrive_chartcanvas").getContext('2d');
var googledrive_graph = new Chart(googledrive_chart_ctx, {
    type: 'pie',
    data: {
        labels: ["Google Drive Queued", "Google drive Processing"],
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

var dropbox_chart_ctx = document.getElementById("dropbox_chartcanvas").getContext('2d');
var dropbox_graph = new Chart(dropbox_chart_ctx, {
    type: 'pie',
    data: {
        labels: ["Dropbox Queued", "Dropbox Processing"],
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


<script>
	function graph_hideshow(provider) {
		visible=$('#' +provider+ '_chartcanvas').is(':visible');
		if (visible) {
			$('#' +provider+ '_chartcanvas').hide();
		} else {
			$('#' +provider+ '_chartcanvas').show();
		};
	};
</script>

</script>
</body>
</html>
