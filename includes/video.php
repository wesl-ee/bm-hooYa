<?php
function video_genstream($key)
{
	$file = bmfft_getattr($key, 'path');
	$file = '/var/http/bmffd/bmfft/madoka.mkv';
	$cmd = 'ffprobe -v quiet -print_format json -show_entries stream=index,codec_name,codec_type:stream_tags=language '.escapeshellarg($file).' 2>&1';
	$output = shell_exec($cmd);
	$streams = json_decode($output, true)['streams'];
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'subtitle') $subtitles[] = $stream;
		if ($stream['codec_type'] == 'audio') $audios[] = $stream;
		if ($stream['codec_type'] == 'video') $video = $stream;
	}
	foreach ($audios as $a_track) {
		$audiofile = $a_track;
		if ($a_track['tags']['language'] == 'jpn') break;
	}
	foreach ($subtitles as $s_track) {
		$subfile = $_SERVER['DOCUMENT_ROOT'].'/'.CONFIG_TEMPORARY_DIRECTORY.'/'.$key.'_'.$s_track['index'].'.vtt';
		$cmd = 'ffmpeg -i '.escapeshellarg($file).' -map 0:'.$s_track['index'].' '.escapeshellarg($subfile);
		shell_exec($cmd);
		$subfiles[] = $subfile;
	}
	if ($video['codec_name'] == 'h264') {
		$file = $_SERVER['DOCUMENT_ROOT'].'/'.CONFIG_TEMPORARY_DIRECTORY.'/'.$key.'.mp4';
		var_dump($file);
		$cmd = 'ffmpeg -i '.escapeshellarg($file).' -map 0:'.$video['index'].' -map 0:'.$audiofile['index'].' -c:a copy -c:v copy '.escapeshellarg($file);
		shell_exec($cmd);
	}
	return [ 'file' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $file),
	'subtitles' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $subfiles)];
}
function video_gensubtitles($file, $outputdir, $basename)
{
	$cmd = 'ffprobe -v quiet -print_format json -show_streams '.escapeshellarg($file).' 2>&1';
	$output = shell_exec($cmd);
	$streams = json_decode($output, true)['streams'];
	$i = 0;
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'subtitle') {
			$subfile = $outputdir.'/'.$basename.'_'.$i.'.vtt';
			$cmd = 'ffmpeg -i '.escapeshellarg($file).' -map 0:'.$stream['index'].' '.escapeshellarg($subfile);
			shell_exec($cmd);
			$language = $stream['tags']['language'];
			$title = $stream['tags']['title'];
			$subfiles[$i] = [
				"file" => $subfile,
				"title" => $title,
				"language" => $language
			];
			$i++;
		}
	}
	return $subfiles;
}
function video_genvideo($file, $outputdir, $basename)
{
	$cmd = 'ffprobe -v quiet -print_format json -show_streams '.escapeshellarg($file).' 2>&1';
	$output = shell_exec($cmd);
	$streams = json_decode($output, true)['streams'];
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'audio') {
			$audiotracks[] = $stream;
		}
	}
	$i = 0;
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'video') {
			if ($stream['codec_name'] == 'h264') {
				$videofile = $outputdir.'/'.$basename.'_'.$stream['index'].'.mp4';
//				if (!file_exists($videofile)) {
					$cmd = 'ffmpeg -i '.escapeshellarg($file).' -c:v copy -c:a copy ';
					foreach ($audiotracks as $audiotrack) {
						$cmd .= '-map 0:'.$audiotrack['index'].' ';
					}
					$cmd .= '-map 0:'.$i.' '.escapeshellarg($videofile);
					shell_exec($cmd);
//				}
				$language = $stream['tags']['language'];
				$title = $stream['tags']['title'];
				$files[$i] = [
					"file" => $videofile,
					"type" => "video/mp4",
					"title" => $title,
					"language" => $language
				];
			}
			$i++;
		}
	}
	return $files;
}
// Not used; all audio tracks are pulled into the video in video_genvideo instead
function video_genaudio($file, $outputdir, $basename)
{
	$cmd = 'ffprobe -v quiet -print_format json -show_streams '.escapeshellarg($file).' 2>&1';
	$output = shell_exec($cmd);
	$streams = json_decode($output, true)['streams'];
	$i = 0;
	foreach ($streams as $stream) {
		if ($stream['codec_type'] == 'audio') {
			if ($stream['codec_name'] == 'aac') {
				$audiofile = $outputdir.'/'.$basename.'_'.$stream['index'].'.aac';
				if (!file_exists($audiofile)) {
					$cmd = 'ffmpeg -i '.escapeshellarg($file).' -c:v copy -map 0:'.$i.' '.escapeshellarg($audiofile);
					shell_exec($cmd);
				}
				$language = $stream['tags']['language'];
				$title = $stream['tags']['title'];
				$audiofiles[$i] = [
					"file" => $audiofile,
					"type" => "audio/aac",
					"title" => $title,
					"language" => $language
				];
			}
			$i++;
		}
	}
	return $audiofiles;
}
function video_printstream($file)
{
	// Try to get a unique, quick filename (this does NOT correspond
	// to the database $key)
	$shortname = base64_encode(hash('md5', $file, true));
	$videos = video_genvideo($file, $_SERVER['DOCUMENT_ROOT'].CONFIG_TEMPORARY_DIRECTORY, $shortname);
	foreach ($videos as $video) {
		$video["file"] = substr($video["file"], strlen($_SERVER['DOCUMENT_ROOT']));
		print("<source src=\"".$video["file"]."\" type=\"".$video["type"]."\">");
	}
	$subtitles = video_gensubtitles($file, $_SERVER['DOCUMENT_ROOT'].CONFIG_TEMPORARY_DIRECTORY, $shortname);
	foreach ($subtitles as $subtitle) {
		$subtitle["file"] = substr($subtitle["file"], strlen($_SERVER['DOCUMENT_ROOT']));
		print('<track src="'.$subtitle["file"].'" kind="subtitles" srclang="'.$subtitle["language"].'" label="'.$subtitle["title"].'" >');
	}
}
?>
