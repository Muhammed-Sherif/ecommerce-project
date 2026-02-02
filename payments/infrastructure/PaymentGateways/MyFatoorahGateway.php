<?php
namespace payments\infrastructure\PaymentGateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use payments\domains\contracts\PaymentGatewayStrategy;

class MyFatoorahGateway implements PaymentGatewayStrategy
{
    private $apiKey;
    private $baseUrl;
    private $paymentMethod;
    private $redirectUrl;
    private $httpClient;

    public function __construct()
    {
        $this->apiKey = env('MYFATOORAH_API_KEY', 'SK_KWT_vVZlnnAqu8jRByOWaRPNId4ShzEDNt256dvnjebuyzo52dXjAfRx2ixW5umjWSUx');
        $this->baseUrl = env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com');
        $this->paymentMethod = strtoupper(env('MYFATOORAH_PAYMENT_METHOD', 'CARD'));
        $this->redirectUrl = env('MYFATOORAH_REDIRECT_URL', env('APP_URL') . '/orders');

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl . '/v3/',
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function createPaymentSession($amount, $currency, $orderId): array
    {
        if (empty($this->apiKey)) {
            return [
                'status' => 'failed',
                'message' => 'Missing MyFatoorah API key',
            ];
        }

        $payload = [
            'PaymentMethod' => $this->paymentMethod,
            'Order' => [
                'Amount' => (float) $amount,
            ],
            'IntegrationUrls' => [
                'Redirection' => $this->redirectUrl,
            ],
        ];

        if (!empty($currency)) {
            $payload['Order']['Currency'] = strtoupper($currency);
        }

        $response = $this->makeApiCall('POST', 'payments', $payload);
        \Log::info('MyFatoorah Response: ' . json_encode($response));
        if (!$response || empty($response['IsSuccess'])) {
            return [
                'status' => 'failed',
                'message' => 'Failed to create MyFatoorah payment',
                'error' => $response['Message'] ?? $response['error'] ?? 'Unknown error',
                'raw_response' => $response,
            ];
        }   
        $data = $response['Data'] ?? [];
        $paymentUrl = $data['PaymentURL'] ?? $data['PaymentUrl'] ?? $data['paymentUrl'] ?? null;
        if (empty($paymentUrl)) {
            return [
                'status' => 'failed',
                'message' => 'MyFatoorah response missing PaymentURL',
                'raw_response' => $response,
            ];
        }

        return [
            'status' => 'pending',
            'link' => $paymentUrl,
            'payment_id' => $data['PaymentId'] ?? null,
            'invoice_id' => $data['InvoiceId'] ?? null,
            'message' => 'Redirect user to PaymentURL to complete payment',
        ];
    }

    public function handleWebhook(array $data): array
    {
        $paymentId = $data['paymentId']
            ?? $data['payment_id']
            ?? $data['PaymentId']
            ?? (function () {
                try {
                    return request()->query('paymentId');
                } catch (\Throwable $e) {
                    return null;
                }
            })();

        if (empty($paymentId)) {
            return [
                'status' => 'ignored',
                'message' => 'paymentId not provided',
            ];
        }

        $response = $this->makeApiCall('GET', 'payments/' . $paymentId);
        if (!$response || empty($response['IsSuccess'])) {
            return [
                'status' => 'failed',
                'message' => 'Failed to get payment details',
                'error' => $response['Message'] ?? $response['error'] ?? 'Unknown error',
                'raw_response' => $response,
            ];
        }

        $details = $response['Data'] ?? [];
        $invoice = $details['Invoice'] ?? [];
        $transaction = $details['Transaction'] ?? [];
        $customer = $details['Customer'] ?? [];
        $amountData = $details['Amount'] ?? [];

        $invoiceStatus = strtoupper((string) ($invoice['Status'] ?? ''));
        $transactionStatus = strtoupper((string) ($transaction['Status'] ?? ''));

        if ($invoiceStatus === 'PAID' || $transactionStatus === 'SUCCESS') {
            $status = 'success';
        } elseif ($invoiceStatus === 'PENDING' || in_array($transactionStatus, ['INPROGRESS', 'AUTHORIZE'])) {
            $status = 'pending';
        } else {
            $status = 'failed';
        }

        return [
            'status' => $status,
            'order_id' => $invoice['ExternalIdentifier'] ?? $invoice['Reference'] ?? $customer['Reference'] ?? null,
            'transaction_id' => $transaction['Id'] ?? null,
            'payment_id' => $transaction['PaymentId'] ?? $paymentId,
            'amount' => $amountData['ValueInDisplayCurrency'] ?? $amountData['ValueInBaseCurrency'] ?? null,
            'raw_status' => [
                'invoice' => $invoiceStatus,
                'transaction' => $transactionStatus,
            ],
        ];
    }

    private function makeApiCall(string $method, string $endpoint, array $payload = null): ?array
    {
        try {
            $options = [];
            if (!empty($payload)) {
                $options['json'] = $payload;
            }

            $response = $this->httpClient->request($method, $endpoint, $options);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            return is_array($data) ? $data : ['error' => 'Invalid JSON response', 'raw_response' => $body];
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
