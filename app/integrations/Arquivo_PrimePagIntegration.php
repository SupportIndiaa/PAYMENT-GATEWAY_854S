<?php

class PrimePagIntegration
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = getenv('PRIMEPAG_URL'); // URL da API
        $this->clientId = getenv('PRIMEPAG_CLIENT_ID'); // Client ID
        $this->clientSecret = getenv('PRIMEPAG_CLIENT_SECRET'); // Client Secret
    }

    /**
     * Gera o token dand theutenticação.
     */
    public function authenticate()
    {
        $url = $this->baseUrl . "oauth/token";
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ];

        $response = $this->makeRequest($url, $data);

        if (isset($response['access_token'])) {
            return $response['access_token'];
        }

        throw new Exception("Error ao autenticar na PrimePag: " . json_encode($response));
    }

    /**
     * Faz uma requisição genérica para a API PrimePag.
     */
    public function makeRequest($url, $data = [], $method = 'POST', $token = null)
    {
        $headers = [
            'Content-Type: application/json',
        ];

        if ($token) {
            $headers[] = "Authorization: Bearer $token";
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new Exception("Error na requisição: $response");
    }

    /**
     * Exemplo de requisição para criar um pagamento.
     */
    public function createPayment($paymentData)
    {
        $token = $this->authenticate();
        $url = $this->baseUrl . "payments";

        return $this->makeRequest($url, $paymentData, 'POST', $token);
    }
}
