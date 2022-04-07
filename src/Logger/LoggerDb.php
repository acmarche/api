<?php


namespace AcMarche\Api\Logger;


use AcMarche\Api\Entity\CapSearch;
use AcMarche\Api\Repository\CapSearchRepository;

class LoggerDb
{
    public function __construct(private CapSearchRepository $capSearchRepository)
    {
    }

    public function logSearch(string $keyword): void
    {
        if (strlen($keyword) > 4) {
            $search = new CapSearch($keyword);
            $this->capSearchRepository->persist($search);
            $this->capSearchRepository->flush();
        }
    }

}
