<?php namespace Spatie\Commands;

use Spatie\Scanner\Scanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends Command {

    public function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('Scan a https-enabled site for mixed content')
            ->addArgument('url', InputArgument::REQUIRED, 'The url of the site to scan');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        if (! $this->validateUrl($url))
        {
            $output->writeln('<error>' . $url . ' is not a valid url');
            return;
        }

        $scanner = new Scanner($output);

        $scannerResults = $scanner
            ->setRootUrl($url)
            ->scan();

        $this->presentResults($output, $scannerResults);
    }

    /**
     * Present the results of the scan
     *
     * @param OutputInterface $output
     * @param $scannerResults
     */
    protected function presentResults(OutputInterface $output, $scannerResults)
    {
        if (count($scannerResults)) {
            foreach ($scannerResults as $siteUrl => $mixedContentUrls) {
                $tableArray[] = [$siteUrl, implode(PHP_EOL, $mixedContentUrls)];
            }

            $table = $this->getHelper('table');
            $table
                ->setHeaders(['URL', 'Found Mixed Content'])
                ->setRows($tableArray);

            $table->render($output);
        }
        else
        {
            $output->writeln('No mixed content found! Hurray!');
        }
    }

    /**
     * Validate the given url
     *
     * @param $url
     * @return bool
     */
    private function validateUrl($url)
    {
        return parse_url($url);
    }
}