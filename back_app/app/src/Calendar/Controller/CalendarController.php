<?php

namespace App\Calendar\Controller;

use App\Calendar\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/days', name: 'calendar_days', methods: ['GET'])]
    public function getDays(Request $request, CalendarRepository $repository): JsonResponse
    {
        $year = $request->query->get('year', date('Y'));
        $month = $request->query->get('month', date('m'));

        $days = $repository->findByYearMonth($year, $month);

        return $this->json(array_map(fn($day) => [
            'date' => $day->getDate()->format('Y-m-d'),
            'type' => $day->getType(),
            'hours' => $day->getHours(),
        ], $days));
    }
}