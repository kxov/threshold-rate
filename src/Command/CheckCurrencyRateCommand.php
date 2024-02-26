<?php

declare(strict_types=1);

namespace App\Command;

use App\Client\CurrencyRateApiClientException;
use App\Service\CurrencyRateChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCurrencyRateCommand extends Command
{
    public function __construct(
        private readonly CurrencyRateChecker $currencyRateChecker,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('currency:check-rate')
            ->setDescription('Check currency rate command')
            ->addArgument('currencyPair', InputArgument::REQUIRED, 'Currency pair');
    }

    /**
     * @throws CurrencyRateApiClientException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currencyPair = $input->getArgument('currencyPair');

        $currencyCheckerResponse = $this->currencyRateChecker->checkExchangeRate($currencyPair);

        if ($currencyCheckerResponse->getMessage() !== null) {
            $output->writeln($currencyCheckerResponse->getMessage());
        }

        return Command::SUCCESS;
    }
}
