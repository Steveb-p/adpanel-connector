<?php

namespace Enzode\AdPanelConnector\Tests\Client;

use Enzode\AdPanelConnector\Client\AdTredoClient;
use PHPUnit\Framework\TestCase;

class AdTredoClientTest extends TestCase
{
    private const KNOWN_ADVERT_HASH = 'EmrWco80Ku';

    private const KNOWN_PAGE_URL = 'https://pl.medic-reporters.com/article/sliminazer_pl/odkrycie-mlodego-studenta-af/';

    private const KNOWN_PAGE_HASH = 'JtP6pIK5BW';

    private const KNOWN_PAGE_TYPES = [
        'pre-sale' => 1,
        'sale' => 2,
    ];

    /**
     * @var AdTredoClient
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = AdTredoClient::createDefault();
    }

    /**
     * @group integration
     * @covers \Enzode\AdPanelConnector\Client\AdTredoClient::getRefData
     */
    public function testGetRefData(): void
    {
        $data = $this->client->getRefData(self::KNOWN_ADVERT_HASH);

        self::assertIsArray($data);
        self::assertArrayHasKey('paths', $data);
        self::assertGreaterThan(0, count($data['paths']));
        self::assertSame('ad', $data['type']);
        self::assertIsArray($data['paths']);

        foreach ($data['paths'] as $key => $path) {
            self::assertIsInt($key);
            self::assertIsInt($path['id']);
            self::assertIsInt($path['priority']);
            self::assertIsArray($path['urls']);

            foreach ($path['urls'] as $page) {
                self::assertIsInt($page['id']);
                self::assertIsString($page['url']);
                self::assertIsString($page['domain']);
                self::assertContains($page['type'], self::KNOWN_PAGE_TYPES);
            }
        }

        $data = $this->client->getRefData(self::KNOWN_PAGE_HASH);

        self::assertIsArray($data);
        self::assertArrayHasKey('paths', $data);
        self::assertGreaterThan(0, count($data['paths']));
        self::assertSame('page', $data['type']);
        self::assertIsArray($data['paths']);

        foreach ($data['paths'] as $key => $path) {
            self::assertIsInt($key);
            self::assertIsInt($path['id']);
            self::assertIsInt($path['priority']);
            self::assertIsArray($path['urls']);

            foreach ($path['urls'] as $page) {
                self::assertIsInt($page['id']);
                self::assertIsString($page['url']);
                self::assertIsString($page['domain']);
                self::assertContains($page['type'], self::KNOWN_PAGE_TYPES);
            }
        }

        $data = $this->client->getRefData('foo');
        self::assertNull($data);
    }

    /**
     * @group integration
     * @covers \Enzode\AdPanelConnector\Client\AdTredoClient::getPath
     */
    public function testGetPath(): void
    {
        $data = $this->client->getPath(52);

        self::assertIsArray($data);
        self::assertIsInt($data['id']);
        self::assertIsString($data['name']);
        self::assertIsArray($data['language']);
        self::assertIsArray($data['country']);
        self::assertIsArray($data['path_items']);

        foreach ($data['path_items'] as $pathItem) {
            self::assertIsInt($pathItem['id']);
            self::assertIsInt($pathItem['ord']);
            self::assertIsArray($pathItem['page']);
        }

        self::assertIsString($data['state']);

        $data = $this->client->getPath(-2);
        self::assertNull($data);
    }

    /**
     * @group integration
     * @covers \Enzode\AdPanelConnector\Client\AdTredoClient::getSiteRef
     */
    public function testGetSiteRefForUnknownPageUrl(): void
    {
        $data = $this->client->getSiteRef('foo');

        self::assertSame([
            'default_ref' => null,
        ], $data);
    }

    /**
     * @group integration
     * @covers \Enzode\AdPanelConnector\Client\AdTredoClient::getSiteRef
     */
    public function testGetSiteRefForKnownPageUrl(): void
    {
        $data = $this->client->getSiteRef(self::KNOWN_PAGE_URL);

        self::assertSame([
            'default_ref' => self::KNOWN_PAGE_HASH,
        ], $data);
    }
}
