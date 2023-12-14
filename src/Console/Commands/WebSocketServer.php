<?php

namespace Mr4Lc\RouteViewing\Console\Commands;

use Illuminate\Console\Command;
use Mr4Lc\RouteViewing\Http\Controllers\SocketController;
use Mr4Lc\RouteViewing\Models\RouteViewing;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        RouteViewing::whereNotNull('status')->delete();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SocketController()
                )
            ),
            config('mr4lc-route-viewing.port', 8090)
        );
        $server->run();
    }
}
