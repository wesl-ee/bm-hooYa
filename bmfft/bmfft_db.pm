#!/usr/bin/perl -w

use strict;
use Exporter;
use vars qw($VERSION @ISA @EXPORT @EXPORT_OK %EXPORT_TAGS);
use BerkeleyDB;
use Data::Dumper;
# SQLite soon. . .
#use DBI;
use JSON::XS;
use File::Find;
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
	my $dbh = new BerkeleyDB::Hash(
		-Filename => $dbfile,
		-Flags => DB_CREATE
	) or die;
	find ( sub {
		if (-f) {
			my $key = file_md5_base64($File::Find::name);
			if ($dbh->db_exists($key) != DB_NOTFOUND) {
				my $json;
				$dbh->db_get($key, $json);
				my $path = decode_json($json)->{'path'};
				if ($File::Find::name ne $path) {
#					print "Not adding ".$File::Find::name." as it's a duplicate of $path,\n";
					return;
				}
				else { return }
			}
			my %value = (
				name => $_,
				path => $File::Find::name,
				size => -s $File::Find::name
			);
			my $json = encode_json(\%value);
			$dbh->db_put($key, encode_json(\%value));
			}
		},$directory);
}
# IN
#	@_ = (TAG_TO_SEARCH_FOR)
# OUT
#	A list of MD5 sums which match your query
sub bmfft_searchtag
{
	my %matches;
	my $tagfile = shift or die;
	my $query = shift or die;
	my $dbh = new BerkeleyDB::Hash(
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
	my $dbh = new BerkeleyDB::Hash(
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
	my $hash = shift or die;
	my $newtags = shift or die;

	my %tags;
	my $dbh = new BerkeleyDB::Hash(
		-Filename => $tagfile,
		-Flags => DB_CREATE
	) or die;
	my $val;
	$dbh->db_get($hash, $val);
	die if !defined $val;
	$val = decode_json($val);
	$a = $val->{'tags'};
	%tags = %$newtags;
	if ($a) {
		%tags = (%$newtags, %$a);
	}
	$val->{'tags'} = \%tags;
	
	$val = encode_json($val);
	$dbh->db_put($hash, $val);
}
1;