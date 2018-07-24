<?php
//I contain all the routes/functions used by administrators to manage parks

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use Aws\S3\S3Client;

$app->get('/picsolve/ocr/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_ocr']) {
			redirect('/app');
	        };

		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		$smarty->display('picsolve/ocr/add.tpl');
	};
});

$app->post('/picsolve/ocr/', function (Request $request, Response $response, array $args) {
	global $smarty,$config,$s3client;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_ocr']) {
			redirect('/app');
	        };
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		$total = count($_FILES['images']['name']);
		if ($total==0) {
			redirect('/picsolve/ocr');
		};

		DB::insert('ocr_jobs', array(
			'user' => $_SESSION['parkplanr']['user']['id'],
			'submitted' => time(),
			'state' => 1
		));
		$ocr_job = DB::queryFirstRow("SELECT * FROM ocr_jobs WHERE id=%i", DB::insertId());

		$job_file_count=0;
		for( $i=0 ; $i < $total ; $i++ ) {
			$tmpFilePath = $_FILES['images']['tmp_name'][$i];
			if ($tmpFilePath != ""){
		                $image_content=file_get_contents($tmpFilePath);
				$file_info = new finfo(FILEINFO_MIME_TYPE);
		                $mime_type = $file_info->buffer($image_content);

				DB::insert('ocr_job_files', array(
					'job' => $ocr_job['id'],
					'user' => $_SESSION['parkplanr']['user']['id'],
					'submitted' => time(),
					'state' => 1
				));
				$job_file_id=DB::insertId();
				$filename=$job_file_id.".".explode('/', $mime_type)[1];
				DB::update('ocr_job_files', array(
					'filename' => $filename
				), "id=%i",$job_file_id);

        		        $s3client->putObject(array(
        		                'Bucket' => $config['aws_bucket_ocr'],
        		                'Key'    => $filename,
        		                'Body'   => $image_content,
//        		                'ACL'        => 'public-read',
        		                'ContentType'        => $mime_type
	        	        ));
				$job_file_count=$job_file_count+1;
			};
		};

		$smarty->assign('ocr_job',$ocr_job);
		$smarty->assign('job_file_count',$job_file_count);
		$smarty->display('picsolve/ocr/processing.tpl');
	};
});
