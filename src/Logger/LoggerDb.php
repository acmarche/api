<?php


namespace AcMarche\Api\Logger;


use AcMarche\Api\Entity\CapSearch;
use AcMarche\Api\Repository\CapSearchRepository;

class LoggerDb
{
    /**
     * @var CapSearchRepository
     */
    private $capSearchRepository;

    public function __construct(CapSearchRepository $capSearchRepository)
    {
        $this->capSearchRepository = $capSearchRepository;
    }

    public function logSearch(string $keyword)
    {
        if (strlen($keyword) > 4) {
            $search = new CapSearch($keyword);
            $this->capSearchRepository->persist($search);
            $this->capSearchRepository->flush();
        }
    }

}
