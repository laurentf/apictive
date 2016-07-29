<?php

namespace Controller\Image;

class Image {
	function transform(\Base $f3, $params) {
		// response init
		$response = new \stdClass();

		$img = isset($_GET['img'])?$_GET['img']:'none';
		$format = isset($_GET['format'])?$_GET['format']:'raw';

		$operations = $params['operations'];

	    $pattern = '/(resize,(\d)*,(\d)*,(crop|d),(enlarge|d)|pixel,(\d)*|bright,(-|)(\d)*|contrast,(-|)(\d)*|rotate,(\d)*|smooth,(-|)(\d)*|vflip|hflip|invert|grey|sepia|emboss|sketch|blur,(selective|d))/im';

	    preg_match_all($pattern, $operations, $matches);

	    $operations = $matches[0];

		$mimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
		
		if($properties = @getimagesize($img)) {
			if(in_array($properties['mime'], $mimeTypes)) {
				// rename file and build cache dir
				$newName = sha1($params[0]);
				$fullPath = $f3->get('image')['root'] . '/' . $properties['mime'] . '/' . $newName;

				// response OK => build general info
				$response->status = '1';
				$response->message = $f3->get('success');
				$response->url = $params[0];
				$response->mime = $properties['mime'];

				// if the file already exist we directly use it (very simple cache system)
				if (!file_exists($fullPath)) {
					switch($properties['mime']) {
						case 'image/jpeg' : 
							$newImg = imagecreatefromjpeg($img);
							imagejpeg($newImg, $fullPath, 100);
						break;
						case 'image/png' : 
							$newImg = imagecreatefrompng($img);
							imagepng($newImg, 'cache/image/png/test.png', 0);
						break;
						case 'image/gif' : 
							$newImg = imagecreatefromgif($img);
							imagegif($newImg, $fullPath);
						break;
					}
					// process
					$tImg = $this->process($fullPath, $operations);
				}
				else {
					// test GIF
					$this->buildPixelGif($fullPath);
					$tImg = new \Image($fullPath);
				}
				
			}
			else {
				$response->status = '0';
				$response->message = $f3->get('error')['image_type'];
			}
		}
		else{
			$response->status = '0';
			$response->message = $f3->get('error')['image_load'];
		}

		// if error build error identicon image
		if($response->status == 0) {
			// build default identicon image for errors
			$tImg = new \Image();
			$tImg->identicon($response->message);
		}

		// what format do you want
		switch ($format) {
			case 'raw':
				$tImg->render();
			break;
			case 'json':
				header('Content-Type: application/json');
				$response->data = base64_encode($tImg->dump());
				// if error this is a png identicon
				if($response->status == 0) {
					$response->src = 'data:image/png;base64,' . base64_encode($tImg->dump());
				}
				// else we know the mime type from the image properties
				else {
					$response->src = 'data:' . $properties['mime'] . ';base64,' . base64_encode($tImg->dump());
				}
				die(json_encode($response));
			break;
			default:
				$tImg->render();
			break;
		}
	}

	private function process($fullPath, $operations) {
		// processing here
		$tImg = new \Image($fullPath);
		//$tImg->pixelate(30);
		$rotate = false;
		foreach($operations as $operation){
			$ope = explode(',', $operation);
			switch($ope[0]) {
				case 'resize':
					$x = $ope[1];
					$y = $ope[2];
					$crop = ($ope[3]=='crop')?true:false;
					$enlarge= ($ope[4]=='enlarge')?true:false;
					$tImg->resize($x, $y, $crop, $enlarge);
				break;
				case 'blur':
					$selective = ($ope[1]=='selective');
					$tImg->blur($selective);
				break;
				case 'pixel':
					$size = $ope[1];
					$tImg->pixelate($size);
				break;
				case 'bright':
					$level = $ope[1];
					$tImg->brightness($level);
				break;
				case 'contrast':
					$level = $ope[1];
					$tImg->contrast($level);
				break;
				case 'rotate':
					$rotate = true;
					$angle = $ope[1];
					$tImg->rotate($angle);
				break;
				case 'smooth':
					$level = $ope[1];
					$tImg->smooth($level);
				break;
				case 'grey':
					$tImg->grayscale();
				break;
				case 'invert':
					$tImg->invert();
				break;
				case 'sepia':
					$tImg->sepia();
				break;
				case 'emboss':
					$tImg->emboss();
				break;
				case 'sketch':
					$tImg->sketch();
				break;
				case 'hflip':
					$tImg->hflip();
				break;
				case 'vflip':
					$tImg->vflip();
				break;
			}
		}
		// save the processed image
		$newImg = imagecreatefromstring($tImg->dump());
		imagesavealpha ($newImg , true);
		imagealphablending($newImg, true); 
		imagepng($newImg, $fullPath, 0);
		return $tImg;
	}

	private function buildPixelGif($fullPath) {
		$oImg = new \Image($fullPath);
		$oImg->resize(300,300,true);

		$img100 = $oImg->pixelate(100);
		$img80 = $oImg->pixelate(80);
		$img50 = $oImg->pixelate(50);
		$img30 = $oImg->pixelate(30);
		$img20 = $oImg->pixelate(20);
		$img10 = $oImg->pixelate(10);
		$img5 = $oImg->pixelate(5);
		$img2 = $oImg->pixelate(2);
		$img1 = $oImg->pixelate(1);

		$frames = [
				imagecreatefromstring($img100->dump()),
				imagecreatefromstring($img80->dump()),
				imagecreatefromstring($img50->dump()),
				imagecreatefromstring($img30->dump()),
				imagecreatefromstring($img20->dump()),
				imagecreatefromstring($img10->dump()),
				imagecreatefromstring($img5->dump()),
				imagecreatefromstring($img2->dump()),
				imagecreatefromstring($img1->dump())
		];

		// Create an array containing the duration (in millisecond) of each frames (in order too)
		$durations = [20, 20, 20, 20, 20, 20, 20, 20, 20];

		// Initialize and create the GIF !
		$gc = new \GifCreator\GifCreator();
		$gc->create($frames, $durations, 10);

		$gifBinary = $gc->getGif();

		header('Content-type: image/gif');
		//header('Content-Disposition: filename="test.gif"');
		die($gifBinary);
	}
}