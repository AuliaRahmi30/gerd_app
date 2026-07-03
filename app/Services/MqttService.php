<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    protected function makeClient(?string $clientId = null): MqttClient
    {
        return new MqttClient(
            $this->resolveMqttHost(),
            config('mqtt.port', 1883),
            $clientId ?? $this->resolveClientId(),
            MqttClient::MQTT_3_1_1
        );
    }

    protected function resolveClientId(): string
    {
        $clientId = trim((string) config('mqtt.client_id', ''));

        return $clientId !== ''
            ? $clientId
            : uniqid('gerd_app_', true);
    }

    protected function resolveMqttHost(): string
    {
        $host = config('mqtt.host', 'broker.hivemq.com');

        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $host;
        }

        if (config('mqtt.tls', false)) {
            // For TLS connections, preserve the hostname so certificate validation and SNI can work.
            return $host;
        }

        $records = dns_get_record($host, DNS_A);
        if ($records !== false && count($records) > 0 && isset($records[0]['ip'])) {
            return $records[0]['ip'];
        }

        return $host;
    }

    protected function makeConnectionSettings(): ConnectionSettings
    {
        $settings = new ConnectionSettings();

        $username = trim((string) config('mqtt.username', ''));
        $password = trim((string) config('mqtt.password', ''));

        if ($username !== '') {
            $settings = $settings->setUsername($username);
        }

        if ($password !== '') {
            $settings = $settings->setPassword($password);
        }

        $settings = $settings
            ->setKeepAliveInterval((int) config('mqtt.keepalive', 10))
            ->setSocketTimeout((int) config('mqtt.socket_timeout', 30))
            ->setResendTimeout((int) config('mqtt.resend_timeout', 10))
            ->setConnectTimeout((int) config('mqtt.connect_timeout', 10))
            ->setReconnectAutomatically((bool) config('mqtt.reconnect', false))
            ->setMaxReconnectAttempts((int) config('mqtt.max_reconnect_attempts', 3))
            ->setDelayBetweenReconnectAttempts((int) config('mqtt.reconnect_delay', 1000));

        if (config('mqtt.tls', false)) {
            $settings = $settings->setUseTls(true)
                ->setTlsVerifyPeer((bool) config('mqtt.tls_verify_peer', true));
        }

        return $settings;
    }

    public function testConnection(): bool
    {
        $mqtt = $this->makeClient(uniqid('gerd_test_', true));

        try {
            // Explicitly use a non-clean session when reconnect is enabled.
            $mqtt->connect($this->makeConnectionSettings(), false);
            $mqtt->disconnect();
            return true;
        } catch (\Throwable $exception) {
            Log::debug('MQTT test connection failed', [
                'error' => $exception->getMessage(),
            ]);
            return false;
        }
    }

    public function markDeviceSeen(): void
    {
        Cache::put($this->getLastSeenCacheKey(), now(), config('mqtt.device_status_timeout', 120));
    }

    public function getLastDeviceSeen(): ?Carbon
    {
        $timestamp = Cache::get($this->getLastSeenCacheKey());
        if (! $timestamp) {
            return null;
        }

        return Carbon::parse($timestamp);
    }

    protected function getLastSeenCacheKey(): string
    {
        return 'mqtt.device_last_seen';
    }

    public function isDeviceOnline(): bool
    {
        $lastSeen = $this->getLastDeviceSeen();

        if (! $lastSeen) {
            return false;
        }

        return $lastSeen->greaterThanOrEqualTo(now()->subSeconds(config('mqtt.device_status_timeout', 120)));
    }

    public function publish($topic, $message)
    {
        $mqtt = $this->makeClient(uniqid('gerd_pub_', true));

        try {
            $mqtt->connect($this->makeConnectionSettings(), false);
            $mqtt->publish($topic, $message, 0);
            $mqtt->disconnect();
        } catch (\Throwable $exception) {
            Log::error('MQTT publish failed', [
                'topic' => $topic,
                'message' => $message,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Create and connect an MQTT client using current settings.
     * Returns a connected `MqttClient` instance.
     */
    public function createClient(bool $cleanSession = false): MqttClient
    {
        $mqtt = $this->makeClient(uniqid('gerd_client_', true));
        $mqtt->connect($this->makeConnectionSettings(), false);
        return $mqtt;
    }
}