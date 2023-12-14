<?php

namespace Mr4Lc\RouteViewing\Http\Controllers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Illuminate\Support\Facades\Log;
use Mr4Lc\RouteViewing\Models\RouteViewing;

/**
 * SocketController class
 */
class SocketController extends \Illuminate\Routing\Controller implements MessageComponentInterface
{
    protected $clients;
    protected $logger;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->logger = Log::channel('ws');
    }

    public function getQueryArray(ConnectionInterface $conn)
    {
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $queryarray);
        return $queryarray;
    }

    public function sendMessage($msg, ConnectionInterface $conn = null)
    {
        foreach ($this->clients as $client) {
            if (isset($conn)) {
                if ($client->resourceId === $conn->resourceId) {
                    $client->send(json_encode($msg));
                    return;
                } else {
                    continue;
                }
            }
            $client->send(json_encode($msg));
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        if (isset($msg)) {
            $data = json_decode($msg);
            $data->resource_id = $conn->resourceId;
            $viewing = null;
            if (isset($data) && property_exists($data, 'type') && $data->type === 'route_viewings') {
                $viewing = RouteViewing::UpdateOrCreate($data);
            }
            if (isset($viewing)) {
                $result = [];
                if ($viewing->status === RouteViewing::VIEWING || $viewing->status === RouteViewing::CLOSED) {
                    $result = [
                        'data' => [
                            'message' => __('mr4lc-route-viewing.single_edit.allow', ['user' => $viewing->getUser()->displayName]),
                        ],
                        'status_code' => '200',
                    ];
                } else if ($viewing->status === RouteViewing::PENDING) {
                    $result = [
                        'data' => [
                            'message' => __('mr4lc-route-viewing.single_edit.deny', ['user' => $viewing->getUser()->displayName]),
                        ],
                        'status_code' => '302',
                    ];
                }
                $this->sendMessage($result, $conn);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error($e);
        $conn->close();
    }
}
