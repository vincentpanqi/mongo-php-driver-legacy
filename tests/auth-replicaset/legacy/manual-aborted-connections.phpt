--TEST--
Manual test for memory leaks and aborted connections.
--SKIPIF--
<?php require_once "tests/utils/auth-replicaset.inc" ?>
--FILE--
<?php
require_once "tests/utils/server.inc";

$s = new MongoShellServer;
$cfg = $s->getReplicaSetConfig(true);
$creds = $s->getCredentials();

$opts = array(
    "db" => "admin",
    "username" => $creds["admin"]->username,
    "password" => $creds["admin"]->password,
    "replicaSet" => $cfg["rsname"],
);
$m = new MongoClient($cfg["dsn"], $opts);
$c = $m->selectCollection("test", "test-ping");

$c->drop();
$c->insert( array( 'test' => 'helium' ) );

for ($i = 0; $i < 20; $i++) {
	$c->insert( array( 'test' => "He$i", 'nr' => $i * M_PI ) );
	try {
		$r = $c->findOne( array( 'test' => "He$i" ) );
	} catch (Exception $e) {
		exit($e->getMessage());
	}
	echo $r['nr'], "\n";
}

?>
--EXPECTF--
0
3.1415926535898
6.2831853071796
9.4247779607694
12.566370614359
15.707963267949
18.849555921539
21.991148575129
25.132741228718
28.274333882308
31.415926535898
34.557519189488
37.699111843078
40.840704496667
43.982297150257
47.123889803847
50.265482457437
53.407075111026
56.548667764616
59.690260418206
