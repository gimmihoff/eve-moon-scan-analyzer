<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Fetches market prices from Eve ESI API.
 */
class EsiPricingService
{
    private const ESI_BASE_URL = 'https://esi.evetech.net/latest';
    private const MARKETS_ENDPOINT = '/markets/prices';
    private const BATCH_SIZE = 100;

    private Client $httpClient;
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->httpClient = new Client(['base_uri' => self::ESI_BASE_URL]);
        $this->logger = $logger;
    }

    /**
     * Fetch current market prices for a list of type IDs.
     *
     * @param array<int> $typeIds
     * @return array<int, float> Map of type ID to average price
     */
    public function getPrices(array $typeIds): array
    {
        if (empty($typeIds)) {
            return [];
        }

        $prices = [];

        try {
            // Fetch all available market prices from ESI
            $response = $this->httpClient->get(self::MARKETS_ENDPOINT, [
                'query' => ['datasource' => getenv('ESI_DATASOURCE') ?: 'tranquility'],
            ]);

            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

            // Build map of type ID -> average price
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['type_id'], $item['average_price'])) {
                        $prices[$item['type_id']] = (float) $item['average_price'];
                    }
                }
            }

            $this->logger?->info(
                'Fetched market prices from ESI',
                ['count' => count($prices), 'type_ids_requested' => count($typeIds)]
            );

            return $prices;
        } catch (GuzzleException $e) {
            $this->logger?->error(
                'Failed to fetch market prices from ESI',
                ['error' => $e->getMessage()]
            );
            return [];
        } catch (\Exception $e) {
            $this->logger?->error(
                'Unexpected error fetching market prices',
                ['error' => $e->getMessage()]
            );
            return [];
        }
    }

    /**
     * Get price for a single type ID.
     *
     * @param int $typeId
     * @return float|null
     */
    public function getPrice(int $typeId): ?float
    {
        $prices = $this->getPrices([$typeId]);
        return $prices[$typeId] ?? null;
    }

    /**
     * Get the last known update time from ESI.
     *
     * @return \DateTime|null
     */
    public function getLastUpdate(): ?\DateTime
    {
        try {
            $response = $this->httpClient->head(self::MARKETS_ENDPOINT, [
                'query' => ['datasource' => getenv('ESI_DATASOURCE') ?: 'tranquility'],
            ]);

            if ($response->hasHeader('expires')) {
                $expiresAt = $response->getHeaderLine('expires');
                return new \DateTime($expiresAt);
            }
        } catch (GuzzleException $e) {
            $this->logger?->warning('Failed to get ESI price update time', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
