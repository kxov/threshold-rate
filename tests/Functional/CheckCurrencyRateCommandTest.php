<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use kxov\ThresholdRateBundle\Client\Provider\MonoCurrencyRateApiClient;
use kxov\ThresholdRateBundle\Client\Provider\PrivatCurrencyRateApiClient;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CheckCurrencyRateCommandTest extends WebTestCase
{
    /**
     * @dataProvider getRates
     */
    public function testExecuteSuccess(
        string $input,
        array $privatResponse,
        array $monoResponse,
        int $statusCode,
        string $expectedResponse
    ): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $privatClient = $this->getMockBuilder(PrivatCurrencyRateApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $privatClient->method('getCurrencyRatesByNumbers')->willReturn($privatResponse);
        $container->set(PrivatCurrencyRateApiClient::class, $privatClient);

        $monoClient = $this->getMockBuilder(MonoCurrencyRateApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $monoClient->method('getCurrencyRatesByNumbers')->willReturn($monoResponse);
        $container->set(MonoCurrencyRateApiClient::class, $monoClient);

        $command = $container->get('kxov\ThresholdRateBundle\Command\CheckCurrencyRateCommand');
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
     * @return array
     */
    public static function getRates(): array
    {
        return [
            [
                'USD/UAH',
                [37.9, 38.2995],
                [37.9, 38.2995],
                200,
                'MONO# The rate increased by 5.7 ',
            ],
            [
                'USD/UAH',
                [17.9, 35.2995],
                [17.9, 35.2995],
                200,
                'MONO# The rate decreased by 20 PRIVAT# The rate decreased by 20 ',
            ],
        ];
    }
}
