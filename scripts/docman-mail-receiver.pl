#!/usr/bin/perl

use Email::MIME;
use LWP::UserAgent;
use HTTP::Request::Common;
use MIME::Base64;

# config
my $url = "https://docs.your-server.de/webHooks/handleMail";
my $host = "docs.your-server.de:443";
my $realm = "Dokumente";
my $user = "...";
my $pass = "...";

# enable slurp mode
local $/;

my $msg = Email::MIME->new(<STDIN>);

my $from = $msg->header_str('From');
my $subject = $msg->header_str('Subject');
my @parts = $msg->parts;
my $description = @parts[0]->body;

my $ua = LWP::UserAgent->new();
$ua->credentials($host, $realm, $user, $pass);

foreach (@parts[1 .. $#parts]) {
    if (index($_->content_type, "application/pdf") == -1) { next; }
    my $request = POST $url,
        Content_Type => 'form-data',
        Content => [
            "attachment-1" => [
                undef,
                $_->filename,
                Content_Type => "application/pdf",
                Content => decode_base64($_->body_raw)
            ],
            from => $from,
            subject => $subject,
            "body-plain" => $description
        ];
    my $response = $ua->request($request);
}
