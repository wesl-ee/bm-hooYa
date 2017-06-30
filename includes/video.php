<?php
function video_muxvideo($file, $videotrack, $out)
{
	if (is_file($out)) return;
	$cmd = 'ffprobe -v quiet -print_format json -show_streams '.escapeshellarg($file).' 2>&1';
	$output = shell_exec($cmd);
	$streams = json_decode($output, true)['streams'];
	$cmd = 'ffmpeg -y -i '.escapeshellarg($file).' -c:v copy -c:a copy -strict -2 ';
	// Keep track of the audio files we should mux in
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'audio')
			$cmd .= '-map 0:'.$stream['index'].' ';
	}
	// Select a web-friendly container given the codec we are encoding from
	if ($streams[$videotrack]['codec_name'] == 'vp8') {
		$cmd .= '-map 0:'.$videotrack.' -f webm ';
	}
	else {
		$cmd .= '-map 0:'.$videotrack.' -f mp4 ';
	}
	$cmd .= $out.' 2>&1';
	exec($cmd);
}
function video_getstream($file, $stream, $out)
{
	if (is_file($out)) return;
	$cmd = 'ffmpeg -y -i '.escapeshellarg($file).' -f webvtt -map 0:'.$stream.' '.$out.' 2>&1';
	// SHOULD SOON execute async because of the redirection and fork
	exec($cmd);
}
function video_getstreaminfo($file)
{
	$cmd = 'ffprobe -v quiet -print_format json -show_streams '.escapeshellarg($file).' 2>&1';
	$output = shell_exec($cmd);
	return json_decode($output, true)['streams'];
}
function video_print($key)
{
	$file = bmfft_getattr($key, 'path');
	$streams = video_getstreaminfo($file);
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'audio') {
			$audiostreams[] = $stream;
		}
	}
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'video') {
			$src = CONFIG_DOCUMENT_ROOT_PATH.'/bmfft/download.php?key='.rawurlencode($key).'&track='.$stream['index'];
			if ($stream['codec_name'] == 'vp8')
				$type = 'video/webm';
			else
				$type = 'video/mp4';
			print('<source src="'.$src.'" type="'.$type.'">');
		}
		if ($stream['codec_type'] == 'subtitle') {
			$src = CONFIG_DOCUMENT_ROOT_PATH.'/bmfft/download.php?key='.rawurlencode($key).'&track='.$stream['index'];
			$srclang = $stream['tags']['language'];
			$label = $stream['tags']['title'];
			print('<track kind="subtitles" src="'.$src.'" label="'.$label.' srclang="'.$srclang.'"');
			if ($srclang == 'en') print (' default');
			print('">');
		}
	}
}
?>
