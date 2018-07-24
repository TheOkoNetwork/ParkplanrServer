{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Digipasses</title>
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
        Digipasses
        <small>Manage your Digipasses</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/picsolve/"><i class="fa fa-camera"></i> Picsolve</a></li>
        <li class="active"><a href="/picsolve/digipass/"><i class="fa fa-ticket"></i> Digipass</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
		<button class="btn btn-success" onclick="show_add_dialog();"><span class="fa fa-plus"></span> Add Digipass</button>

		<p>By adding a Digipass to ParkPlanr, ParkPlanr will automagically load any photos on the pass to your picsolve account</p>
		<p>So no more having to type in the barcode after each trip, photos will just appear!</p>
        </div>
            <div class="box-body">
              <table id="digipass_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Barcode</th>
                  <th>Family mode</th>
                  <th></th>
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                <tr>
                  <th>Name</th>
                  <th>Barcode</th>
                  <th>Family mode</th>
                </tr>
                </tfoot>
              </table>
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
<script src="/js/handlebars.js"></script>
<script>
function digipass_table_datatable_init() {
	digipass_table_datatable=$('#digipass_table').DataTable( {
	    "language": {
	      "emptyTable": "No Digipasses in the database",
	      "zeroRecords": "No Digipasses match your filter"
	    }
	} );
};
digipass_table_datatable_init();
</script>

<script>
	function show_add_dialog() {
		$('#add_name').val('');
		$('#add_barcode').val('');
		$('#add_familymode-0').prop("checked",true);
		$('#add_modal').modal();
	};

	function show_delete_dialog(digipass_id,digipass_barcode) {
		$('#delete_id').val(digipass_id);
		$('#delete_barcode').val(digipass_barcode);
		$('#delete_modal').modal();
	};

	function show_edit_dialog(digipass_id) {
		console.log("Loading digipass info for digipass id:"+digipass_id);
		$.get( "/picsolve/digipass/"+digipass_id+"/getjson", function(data) {
			if (data.status) {
				console.log(data);
				$('#edit_id').val(data.digipass.id);
				$('#edit_name').val(data.digipass.name);
				$('#edit_barcode').val(data.digipass.barcode);
				if (Boolean(Number(data.digipass.familymode))) {
					$('#edit_familymode-1').prop("checked",true);
				} else {
					$('#edit_familymode-0').prop("checked",true);
				};
				$('#edit_modal').modal();
			} else {
				console.log(data);
				alert("Sorry failed to edit Digipass, "+data.status_user);
			};
		}).fail(function(data) {
			alert("Sorry, a server error occured. Please try again.");
		});
	
	};

	function add_digipass() {
		barcode=$('#add_barcode').val();
		name=$('#add_name').val();
		familymode=$('#add_familymode-1').prop("checked");
		if (!barcode) {
			alert("Digipass barcode cannot be empty.");
			return;
		} else {
			console.log("Adding:"+barcode);
			$.post( "/picsolve/digipass/addjson", { barcode: barcode, name: name,familymode: familymode}, function(data) {
				console.log(data);
				if (data.status) {
					$('#add_modal').modal('hide');
					alert("Successfully added Digipass");
					refresh_digipass_list();
				} else {
					alert("Sorry failed to add Digipass, "+data.status_user);
				};
			}).fail(function(data) {
				alert("Sorry, a server error occured. Please try again.");
			});
		};
	};


	function edit_digipass() {
		id=$('#edit_id').val();
		name=$('#edit_name').val();
		familymode=$('#edit_familymode-1').prop("checked");
		console.log("Editing digipass:"+id);
		alert("Editing digipass:"+id);
		$.post( "/picsolve/digipass/editjson", { id: id, name: name, familymode: familymode}, function(data) {
			console.log(data);
			if (data.status) {
				$('#edit_modal').modal('hide');
				alert("Successfully edited Digipass");
				refresh_digipass_list();
			} else {
				alert("Sorry failed to edit Digipass, "+data.status_user);
			};
		}).fail(function(data) {
			alert("Sorry, a server error occured. Please try again.");
		});
	};

	function delete_digipass() {
		digipass_id=$('#delete_id').val();
		if (!digipass_id) {
			alert("Digipass ID cannot be empty.");
			return;
		} else {
			console.log("Deleting:"+digipass_id);
			$.post( "/picsolve/digipass/deletejson", { digipass_id: digipass_id}, function(data) {
				console.log(data);
				if (data.status) {
					$('#delete_modal').modal('hide');
					alert("Successfully deleted Digipass");
					refresh_digipass_list();
				} else {
					alert("Sorry failed to delete Digipass");
				};
			}).fail(function(data) {
				alert("Sorry, a server error occured. Please try again.");
			});
		};
	};

	function forceInputUppercase(e) {
		var start = e.target.selectionStart;
		var end = e.target.selectionEnd;
		e.target.value = e.target.value.toUpperCase();
		e.target.setSelectionRange(start, end);
	};
	$('#barcode').on('keyup',forceInputUppercase);

	function refresh_digipass_list() {

		$.get( "/picsolve/digipass/listjson", function(data) {
			console.log(data);

			var source   = document.getElementById("row_template").innerHTML;
			var template = Handlebars.compile(source);

			digipass_table_datatable.destroy();
			$('#digipass_table').find('tbody:first').empty();
			$.each(data,function(key, digipass) {
				digipass.familymode=Boolean(Number(digipass.familymode));
				console.log(digipass);
				var html    = template(digipass);
				$('#digipass_table').find('tbody:first').append(html);
			});
			digipass_table_datatable_init();
		}).fail(function(data) {
			alert("Sorry, a server error occured. Please try again.");
		});
	};
	refresh_digipass_list();
</script>

{literal}
	<script id="row_template" type="text/x-handlebars-template">
		<tr id="digipass_{{id}}">
			<td> {{name}} <span class="fa fa-pencil" onclick="show_edit_dialog({{id}});"></span></td>
			<td>{{barcode}}</td>
			{{#if familymode}}
				<td>Yes</td>
			{{else}}
				<td>No</td>
			{{/if}}
			<td>
				<button class="btn btn-danger" onclick="show_delete_dialog('{{id}}','{{barcode}}');">DELETE</button>
			</td>
		</tr>
	</script>
{/literal}


			<div class="modal fade" id="add_modal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Add digipass</h4>
						</div>
						<div class="modal-body">
							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="add_name">Name</label>
								<div class="col-md-4">
									<input id="add_name" name="add_name" type="text" placeholder="" class="form-control input-md">
								</div>
							</div><br />
							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="add_barcode">Barcode</label>
								<div class="col-md-4">
									<input id="add_barcode" name="add_barcode" type="text" placeholder="" class="form-control input-md" required>
								</div>
							</div><br />
							<!-- Multiple Radios (inline) -->
							<div class="form-group">
							  <label class="col-md-4 control-label" for="add_familymode">Family mode?</label>
							  <div class="col-md-4"> 
							    <label class="radio-inline" for="add_familymode-0">
							      <input type="radio" name="add_familymode" id="add_familymode-0" value="0" checked>
							      No
							    </label> 
							    <label class="radio-inline" for="add_familymode-1">
							      <input type="radio" name="add_familymode" id="add_familymode-1" value="1">
							      Yes
							    </label>
							  </div>
							</div>


						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success" onclick="add_digipass();">Add Digipass</button>
						</div>
					</div>
				</div>
			</div>



			<div class="modal fade" id="edit_modal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Edit digipass</h4>
						</div>
						<div class="modal-body">
							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="edit_name">Name</label>
								<div class="col-md-4">
									<input id="edit_name" name="edit_name" type="text" placeholder="" class="form-control input-md">
								</div>
							</div><br />
							<!-- Text input-->
							<div class="form-group">
								<label class="col-md-4 control-label" for="edit_barcode">Barcode</label>
								<div class="col-md-4">
									<input id="edit_barcode" name="edit_barcode" type="text" placeholder="" class="form-control input-md" disabled>
								</div>
							</div>

							<input id="edit_id" name="edit_id" type="hidden" placeholder="" class="form-control input-md" disabled>
							
							<!-- Multiple Radios (inline) -->
							<div class="form-group">
							  <label class="col-md-4 control-label" for="edit_familymode">Family mode?</label>
							  <div class="col-md-4"> 
							    <label class="radio-inline" for="edit_familymode-0">
							      <input type="radio" name="edit_familymode" id="edit_familymode-0" value="0">
							      No
							    </label> 
							    <label class="radio-inline" for="edit_familymode-1">
							      <input type="radio" name="edit_familymode" id="edit_familymode-1" value="1">
							      Yes
							    </label>
							  </div>
							</div>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success" onclick="edit_digipass();">Edit Digipass</button>
						</div>
					</div>
				</div>
			</div>



			<div class="modal fade" id="delete_modal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Delete digipass</h4>
						</div>
						<div class="modal-body">
							<input id="delete_id" name="delete_id" type="hidden">
							<input id="delete_barcode" name="delete_barcode" type="text" disabled>
							<p>Are you sure you want to delete this Digipass?</p>
							<p>{#app_full_name#} will no longer automagically add photos from this digipass</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-danger" onclick="delete_digipass();">Delete Digipass</button>
						</div>
					</div>
				</div>
			</div>
</body>
</html>
