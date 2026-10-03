import ws from 'k6/ws';
import { check, sleep } from 'k6';

// k6 Options: Simulate 100 concurrent testers active over 2 minutes
export const options = {
    stages: [
        { duration: '30s', target: 100 }, // Ramp up to 100 virtual users (testers)
        { duration: '1m', target: 100 },  // Maintain 100 active testers
        { duration: '30s', target: 0 },   // Ramp down to 0
    ],
    thresholds: {
        ws_connecting: ['p(95)<200'], // 95% of WebSocket handshakes should take < 200ms
        ws_session_duration: ['p(95)>1000'], // Verify active websocket sessions stay open
    }
};

export default function () {
    // The target Blazor Interactive Server WebSocket URL
    const url = 'ws://localhost:5000/_blazor';

    const params = {
        headers: {
            'User-Agent': 'k6-load-tester',
            'Sec-WebSocket-Protocol': 'blazor-server-signalr'
        }
    };

    const res = ws.connect(url, params, function (socket) {
        socket.on('open', function () {
            // 1. Send SignalR initial handshake JSON protocol signature (must end with record separator char \u001e)
            socket.send('{"protocol":"json","version":1}\u001e');
            
            // 2. Schedule regular ping/heartbeat message to maintain Blazor active circuit
            socket.setInterval(function () {
                socket.send('{"type":6}\u001e'); // Standard SignalR KeepAlive message type
            }, 5000);
        });

        socket.on('message', function (message) {
            // Verify we receive messages back from Kestrel
            check(message, {
                'received signalr frame': (msg) => msg.includes('protocol') || msg.includes('type') || msg.length > 0
            });
        });

        socket.on('close', function () {
            // SignalR circuit closed safely
        });

        socket.on('error', function (err) {
            console.error('Socket error encountered: ' + err.error());
        });

        // Keep active circuit alive for 30 seconds before disconnecting
        sleep(30);
        socket.close();
    });

    check(res, {
        'websocket connection successful': (r) => r && r.status === 101,
    });
}
