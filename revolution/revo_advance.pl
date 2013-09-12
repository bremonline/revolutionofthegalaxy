# Create a user agent object
use LWP::UserAgent;
$ua = LWP::UserAgent->new;
$ua->agent("MyApp/0.1 ");


advanceTick();

sub advanceTick {
	
# Create a request
	my $req = HTTP::Request->new(POST => 'http://revolutionofthegalaxy.com/revolution/basic_advance_tick.php5');
	$req->content_type('application/x-www-form-urlencoded');
	$req->content('query=libwww-perl&mode=dist');



	# Pass request to the user agent and get a response back
	my $res = $ua->request($req);
	
	# Check the outcome of the response
	if ($res->is_success) {
		print $res->content;
	}
	else {
	print $res->status_line, "\n";
	}
	select((select(STDOUT), $|=1)[0]);
}