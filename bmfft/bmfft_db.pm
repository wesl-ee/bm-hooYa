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
	my @matches;
	my $dbfile = shift or die;
	my $query = shift or die;
	tie my %hash, 'GDBM_File', $dbfile, &GDBM_WRCREAT, 0640;
	foreach my $key (keys %hash) {
		my %value = %{decode_json($hash{$key})};
		foreach my $tag (keys %{$value{'tags'}}) {
			push @matches, $key if $tag eq $query;
		}
	}
	untie %hash;
	@matches;
}
sub bmfft_gettags
{
	my $dbfile = shift or die;
	my $key = shift or die;
	tie my %hash, 'GDBM_File', $dbfile, &GDBM_WRCREAT, 0640;
	my %value = %{decode_json($hash{$key})};
	return $value{'tags'};
}
sub bmfft_getattr
{
	my $dbfile = shift or die;
	my $key = shift or die;
	my $attr = shift or die;
	tie my %hash, 'GDBM_File', $dbfile, &GDBM_WRCREAT, 0640;
	my %value = %{decode_json($hash{$key})};
	return $value{$attr};
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
sub bmfft_remove
{
	my $tagfile = shift or die;
	my $key = shift or die;

	my %hash;
	tie %hash, 'GDBM_File', $tagfile, &GDBM_WRCREAT, 0640;
	delete $hash{$key};
	untie %hash;
}
# Returns true if the key exists in the DB,
# false otherwise
sub bmfft_exists
{
	my $tagfile = shift or die;
	my $key = shift or die;

	my %hash;
	tie %hash, 'GDBM_File', $tagfile, &GDBM_WRCREAT, 0640;
	my $value = $hash{$key};
	untie %hash;
	return $value;
}
1;
