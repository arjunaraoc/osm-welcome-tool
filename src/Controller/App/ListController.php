<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Service\RegionsProvider;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    public function __construct(
        private RegionsProvider $provider,
    ) {}

    #[Route('/{regionKey}/list', name: 'app_list')]
    public function index(string $regionKey): Response
    {
        $region = $this->provider->getRegion($regionKey);

        /** @var Mapper[] */
        $mappers = $this->getDoctrine()
            ->getRepository(Mapper::class)
            ->findBy(['region' => $regionKey]);

        $firstChangetsetCreatedAt = array_map(function (Mapper $mapper) { return $mapper->getChangesets()->first()->getCreatedAt(); }, $mappers);
        array_multisort($firstChangetsetCreatedAt, SORT_DESC, $mappers);

        return $this->render('app/list/index.html.twig', [
            'region' => $region,
            'mappers' => $mappers,
        ]);
    }
}
