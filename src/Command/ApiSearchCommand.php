<?php

namespace AcMarche\Api\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class ApiSearchCommand extends Command
{
    protected static $defaultName = 'api:search';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $result = $this->searchOld('axa');
        var_dump($result);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }

    protected function searchOld(string $keyword)
    {
        $size = 10;
        $select = array('societe', 'telephone', 'classements');
        $sort = array(
            'societe' =>
                array(
                    'order' => 'desc',
                ),
        );
        $query = array('match_phrase' => array('societe' => $keyword));

        $default = array(
            'query' => $query,
            'size' => $size,
            'sort' => $sort,
            '_source' =>
                $select,
        );

        $querySring = json_encode($default);
        //$urlCurl = "localhost:9200/bottin/_search";
        $urlCurl = "https://api.marche.be/search/bottin/fiches/_search";
        $urlCurl = "http://api.local/search/bottin/fiches/_search";
        // $urlCurl = "localhost:9200/bottin/fiches/_search";
        $elastic = curl_init($urlCurl);
        curl_setopt($elastic, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($elastic, CURLOPT_HEADER, false);
        curl_setopt($elastic, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $elastic,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: '.strlen($querySring),
            )
        );
        curl_setopt($elastic, CURLOPT_POSTFIELDS, $querySring);
        $response = curl_exec($elastic);
        $error = curl_error($elastic);
        if ($error) {
            var_dump($error);
            exit();
        }
        curl_close($elastic);

        return json_decode($response);
    }
}
