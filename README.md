# bot-translation

```php
require __DIR__.'/vendor/autoload.php';

use LineMob\Bot\Translation\Setup;
use LineMob\Core\Command\FallbackCommand;
use React\EventLoop\Factory;
use React\Http\Request;
use React\Http\Response;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;

$config = [
            'google_project_id' => 'XXXX',
            'google_api_key' => 'XXXX',
            'google_default_locale' => 'th',
            'google_fallback_locale' => 'en',
            'line_channel_token' => 'XXXX',
            'line_channel_secret' => 'XXXX',
            'isDebug' => true
        ];

        $app = function (Request $request, Response $response) use ($output, $config) {
            $request->on('data', function ($data) use ($output, $request, $config) {
                $receiver = Setup::demo($config);
                $signature = $request->getHeaderLine('X-Line-Signature');

                dump($data);

                if ($receiver->validate($data, $signature)) {
                    dump($receiver->handle($data));
                } else {
                    throw new \RuntimeException("Invalid signature: " . $signature);
                }
            });

            $response->writeHead(200, array('Content-Type' => 'text/plain'));
            $response->end("Hello World hot\n");
        };

        $loop = Factory::create();
        $socket = new SocketServer($loop);
        $http = new HttpServer($socket, $loop);

        $http->on('request', $app);

        echo("Server running at http://127.0.0.1:" . $port);

        $socket->listen($port);
        $loop->run();
```
