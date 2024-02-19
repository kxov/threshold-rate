<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CheckCurrencyRateCommandTest extends DbWebTestCase
{
    /**
     * @dataProvider getRates
     */
    public function testExecuteSuccess(
        string $input,
        array $clientResponse,
        int $statusCode,
        string $expectedResponse
    ): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $httpClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClientMock->method('request')->willReturn(new Response($statusCode, [], (string) json_encode($clientResponse)));

        $container->set('mono.client', $httpClientMock);

        $command = $container->get('App\Command\CheckCurrencyRateCommand');
        $application = new Application();
        $application->add($command);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $commandTester = new CommandTester($application->find('currency:check-rate'));

        $commandTester->execute(['currencyPair' => $input]);

        $this->assertSame(
            $expectedResponse, preg_replace('/\n|\r/', '', $commandTester->getDisplay())
        );
    }

    /**
     * @return mixed[]
     */
    public static function getRates(): array
    {
        return [
            [
                'USD/UAH',
                [
                    ['currencyCodeA' => 840, 'currencyCodeB' => 980, 'rateBuy' => 37.9, 'rateSell' => 38.2995],
                ],
                200,
                'The rate increased by 5.7',
            ],
            [
                'USD/UAH',
                [
                    ['currencyCodeA' => 840, 'currencyCodeB' => 980, 'rateBuy' => 17.9, 'rateSell' => 35.2995],
                ],
                200,
                'The rate decreased by 14.3',
            ],
        ];
    }
}
