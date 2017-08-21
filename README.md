# bot-translation
[ngrok](https://ngrok.com/) will publish your local to internet -- `./ngrok http 8888`

```php
<?php

require __DIR__.'/vendor/autoload.php';

use LineMob\Bot\Translation\Setup;
use React\EventLoop\Factory;
use React\Http\Request;
use React\Http\Response;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;

$port = '8888';
$config = [
    'google_project_id' => 'theta-signal-826',
    'google_api_key' => 'AIzaSyDWe9Vymr-VfqNpV_I0TUIbDjpJuCJQPNw',
    'google_default_locale' => 'th',
    'google_fallback_locale' => 'en',
    'line_channel_token' => 'your_own_bot_token',
    'line_channel_secret' => 'your_own_bot_secret',
    'isDebug' => true,
];

$app = function (Request $request, Response $response) use ($config) {
    $request->on(
        'data',
        function ($data) use ($request, $config) {
            $receiver = Setup::demo($config);
            $signature = $request->getHeaderLine('X-Line-Signature');

            var_dump($data);

            if ($receiver->validate($data, $signature)) {
                var_dump($receiver->handle($data));
            } else {
                throw new \RuntimeException("Invalid signature: ".$signature);
            }
        }
    );

    $response->writeHead(200, array('Content-Type' => 'text/plain'));
    $response->end("Hello World hot\n");
};

$loop = Factory::create();
$socket = new SocketServer($loop);
$http = new HttpServer($socket, $loop);

$http->on('request', $app);

echo("Server running at http://127.0.0.1:".$port);

$socket->listen($port);
$loop->run();

```
