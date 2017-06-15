#!/usr/bin/perl -w

use strict;
use Exporter;
use vars qw($VERSION @ISA @EXPORT @EXPORT_OK %EXPORT_TAGS);
use BerkeleyDB;
use GDBM_File;
use Data::Dumper;
# SQLite soon. . .
#use DBI;
use JSON::XS;
use File::Find;
use File::MimeInfo 'mimetype';
#use Getopt::Std;
use Digest::MD5::File qw /file_md5_base64/;
$VERSION     = 1.00;
@ISA         = qw(Exporter);
@EXPORT      = ();
@EXPORT_OK   = qw(bmfft_update_db bmfft_gettags bmfft_searchtag bmfft_addtags);

our $directory;

sub bmfft_update_db
{
	my $directory = shift or die;
	my $dbfile = shift or die;
	my %hash;

	find ( sub {
		if (-f) {
			tie %hash, 'GDBM_File', $dbfile, &GDBM_WRCREAT, 0640;
			my $key = file_md5_base64($File::Find::name);
			my %value;
			if ($hash{$key}) {
				%value = %{decode_json($hash{$key})};
			}
			$value{'name'} = $_;
			$value{'path'} = $File::Find::name;
			$value{'size'} = -s $File::Find::name;
			$value{'mimetype'} = mimetype($File::Find::name);

#			if ($hash{$key}) {
#				my $json = $hash{$key};
#				my $path = decode_json($json)->{'path'};
#				if (decode_json($hash{$key})->{'tags'}) {
#					%value = ( tags => decode_json($hash{$key})->{'tags'} );
#				}
#				if ($File::Find::name ne $path) {
				# If you want to delete double-files, do that
				# here
#					return;
#				}
#				else { return }
#			}



			my $json = encode_json(\%value);
			$hash{$key} = encode_json(\%value);
			untie(%hash);
			# Quick way to tag all my pictures on the initial run, left for legacy
			if (index($File::Find::name, 'Amaama to Inazuma') != -1) {
				bmfft_addtags($dbfile, $key, {'series:amaama_to_inazuma' => 1});
			}
			if (index($File::Find::name, 'Amanchi') != -1) {
				bmfft_addtags($dbfile, $key, {'series:amanchu' => 1});
			}
			if (index($File::Find::name, 'Angel Beats') != -1) {
				bmfft_addtags($dbfile, $key, {'series:angel_beats' => 1});
			}
			if (index($File::Find::name, 'Anitore') != -1) {
				bmfft_addtags($dbfile, $key, {'series:anitore' => 1});
			}
			if (index($File::Find::name, 'Ano Natsu de Matteru') != -1) {
				bmfft_addtags($dbfile, $key, {'series:ano_natsu_de_matteru' => 1});
			}
			if (index($File::Find::name, 'Aria') != -1) {
				bmfft_addtags($dbfile, $key, {'series:aria' => 1});
			}
			if (index($File::Find::name, 'Bakemonogatari') != -1) {
				bmfft_addtags($dbfile, $key, {'series:monogatari' => 1});
			}
			if (index($File::Find::name, 'Bakuon!!') != -1) {
				bmfft_addtags($dbfile, $key, {'series:bakuon' => 1});
			}
			if (index($File::Find::name, 'Dagashi Kashi') != -1) {
				bmfft_addtags($dbfile, $key, {'series:dagashi_kashi' => 1});
			}
			if (index($File::Find::name, 'Evangelion') != -1) {
				bmfft_addtags($dbfile, $key, {'series:neon_genesis_evangelion' => 1});
			}
			if (index($File::Find::name, 'Fate') != -1) {
				bmfft_addtags($dbfile, $key, {'series:fate' => 1});
			}
			if (index($File::Find::name, 'Girlish Number') != -1) {
				bmfft_addtags($dbfile, $key, {'series:girlish_number' => 1});
			}
			if (index($File::Find::name, 'GochiUsa') != -1) {
				bmfft_addtags($dbfile, $key, {'series:gochuumon_wa_usagi_desu_ka' => 1});
			}
			if (index($File::Find::name, 'Haifuri') != -1) {
				bmfft_addtags($dbfile, $key, {'series:high_school_fleet' => 1});
			}
			if (index($File::Find::name, 'Haruhi') != -1) {
				bmfft_addtags($dbfile, $key, {'series:suzumiya_haruhi_no_yuuutsu' => 1});
			}
			if (index($File::Find::name, 'Hibike') != -1) {
				bmfft_addtags($dbfile, $key, {'series:hibike_euphonium' => 1});
			}
			if (index($File::Find::name, 'Hidamari Sketch') != -1) {
				bmfft_addtags($dbfile, $key, {'series:hidamari_sketch' => 1});
			}
			if (index($File::Find::name, 'Ika Musume') != -1) {
				bmfft_addtags($dbfile, $key, {'series:ika_musume' => 1});
			}
			if (index($File::Find::name, 'im@s') != -1) {
				bmfft_addtags($dbfile, $key, {'series:idolmaster' => 1});
			}
			if (index($File::Find::name, 'Initial D') != -1) {
				bmfft_addtags($dbfile, $key, {'series:initial_d' => 1});
			}
			if (index($File::Find::name, 'Kantai Collection') != -1) {
				bmfft_addtags($dbfile, $key, {'series:kantai_collection' => 1});
			}
			if (index($File::Find::name, 'Katawa Shoujo') != -1) {
				bmfft_addtags($dbfile, $key, {'series:katawa_shoujo' => 1});
			}
			if (index($File::Find::name, 'Kill la Kill') != -1) {
				bmfft_addtags($dbfile, $key, {'series:kill_la_kill' => 1});
			}
			if (index($File::Find::name, 'K-On') != -1) {
				bmfft_addtags($dbfile, $key, {'series:k-on' => 1});
			}
			if (index($File::Find::name, 'Konosuba') != -1) {
				bmfft_addtags($dbfile, $key, {'series:kono_subarashii_sekai_ni_shukufuku_wo' => 1});
			}
			if (index($File::Find::name, 'Kuma Miko') != -1) {
				bmfft_addtags($dbfile, $key, {'series:kuma_miko' => 1});
			}
			if (index($File::Find::name, 'Lain') != -1) {
				bmfft_addtags($dbfile, $key, {'series:serial_experiments_lain' => 1});
			}
			if (index($File::Find::name, 'Love Live!') != -1) {
				bmfft_addtags($dbfile, $key, {'series:love_live' => 1});
			}
			if (index($File::Find::name, 'Magica') != -1) {
				bmfft_addtags($dbfile, $key, {'series:puella_magi_madoka_magica' => 1});
			}
			if (index($File::Find::name, 'ME') != -1) {
				bmfft_addtags($dbfile, $key, {'windows_me' => 1});
			}
			if (index($File::Find::name, 'Musigen no Phantom World') != -1) {
				bmfft_addtags($dbfile, $key, {'series:musaigen_no_phantom_world' => 1});
			}
			if (index($File::Find::name, 'New Game') != -1) {
				bmfft_addtags($dbfile, $key, {'series:new_game' => 1});
			}
			if (index($File::Find::name, 'Nichijou') != -1) {
				bmfft_addtags($dbfile, $key, {'series:nichijou' => 1});
			}
			if (index($File::Find::name, 'Nonon') != -1) {
				bmfft_addtags($dbfile, $key, {'series:non_non_biyori' => 1});
			}
			if (index($File::Find::name, 'Ojisan') != -1) {
				bmfft_addtags($dbfile, $key, {'series:ojisan_to_marshmallow' => 1});
			}
			if (index($File::Find::name, 'Pan de Peace') != -1) {
				bmfft_addtags($dbfile, $key, {'series:pan_de_peace' => 1});
			}
			if (index($File::Find::name, 'Panzer') != -1) {
				bmfft_addtags($dbfile, $key, {'series:girls_und_panzer' => 1});
			}
			if (index($File::Find::name, 'rezero') != -1) {
				bmfft_addtags($dbfile, $key, {'series:re:zero' => 1});
			}
			if (index($File::Find::name, 'Rolling Girls') != -1) {
				bmfft_addtags($dbfile, $key, {'series:rolling_girls' => 1});
			}
			if (index($File::Find::name, 'Sailor Moon') != -1) {
				bmfft_addtags($dbfile, $key, {'series:sailor_moon' => 1});
			}
			if (index($File::Find::name, 'Sakamoto') != -1) {
				bmfft_addtags($dbfile, $key, {'series:sakamoto_desu_ga' => 1});
			}
			if (index($File::Find::name, 'Shelter') != -1) {
				bmfft_addtags($dbfile, $key, {'porter_robinson' => 1});
			}
			if (index($File::Find::name, 'Tamako') != -1) {
				bmfft_addtags($dbfile, $key, {'series:tamako_market' => 1});
			}
			if (index($File::Find::name, 'Tanaka') != -1) {
				bmfft_addtags($dbfile, $key, {'series:tanaka-kun_wa_itsumo_kedaruge' => 1});
			}
			if (index($File::Find::name, 'touhou') != -1) {
				bmfft_addtags($dbfile, $key, {'series:touhou' => 1});
			}
			if (index($File::Find::name, 'Umaru') != -1) {
				bmfft_addtags($dbfile, $key, {'series:himouto!_umaru_chan' => 1});
			}
			if (index($File::Find::name, 'Vocaloid') != -1) {
				bmfft_addtags($dbfile, $key, {'series:vocaloid' => 1});
			}
			if (index($File::Find::name, 'Yama no Susume') != -1) {
				bmfft_addtags($dbfile, $key, {'series:yama_no_susume' => 1});
			}
			if (index($File::Find::name, 'Yuyushiki') != -1) {
				bmfft_addtags($dbfile, $key, {'series:yuyushiki' => 1});
			}
			if (index($File::Find::name, 'Yuyuyu') != -1) {
				bmfft_addtags($dbfile, $key, {'series:yuuki_yuuna_wa_yuusha_de_aru' => 1});
			}
		}
	},$directory);

}
# THESE FUNCTIONS MAY OR MAY NOT WORK AS IS, USE AT OWN PERIL
# IN
#	@_ = (TAG_TO_SEARCH_FOR)
# OUT
#	A list of MD5 sums which match your query
sub bmfft_searchtag
{
	my %matches;
	my $tagfile = shift or die;
	my $query = shift or die;
	my $dbh = new BerkeleyDB::Btree(
		-Filename => $tagfile,
		-Flags => DB_CREATE
	) or die;
	my $cursor = $dbh->db_cursor;
	my $hash = '';
	my $val = '';
	while ($cursor->c_get($hash, $val, DB_NEXT) == 0) {
		$matches{$hash} = 1 if (decode_json($val)->{'tags'}{$query});
	}
	%matches;
}
sub bmfft_gettags
{
	my $tagfile = shift or die;
	my $hash = shift or die;
	my $dbh = new BerkeleyDB::Btree(
		-Filename => $tagfile,
		-Flags => DB_CREATE
	) or die;
	my $val;
	$dbh->db_get($hash, $val);
	return if !defined $val;
	decode_json($val)->{'tags'};
}
sub bmfft_addtags
{
	my $tagfile = shift or die;
	my $key = shift or die;
	my $newtags = shift or die;

	my %tags;
	my %hash;
	tie %hash, 'GDBM_File', $tagfile, &GDBM_WRCREAT, 0640;

	my $value = $hash{$key};
	die if !defined $value;
	$value = decode_json($value);
	$a = $value->{'tags'};
	%tags = %$newtags;
	if ($a) {
		%tags = (%$newtags, %$a);
	}
	$value->{'tags'} = \%tags;
	
	$value = encode_json($value);
	$hash{$key} = $value;
	untie(%hash);
}
1;