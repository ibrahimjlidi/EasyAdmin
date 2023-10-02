<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartController extends AbstractController
{
    
    /**
     * @Route("/api/chart/data", name="chart_data")
     */
    public function getData()
    {
        // Replace this with your actual chart data retrieval logic
        $chartData = [
            'labels' => ['Products', 'Users', 'Stock', 'Category'],
            'datasets' => [
                [
                    'label' => 'data',
                    'data' => [10, 20, 15, 30], // Replace with your dynamic data
                    'backgroundColor' => ['rgba(5, 200, 200, 0.2)', 'rgba(255, 20, 20, 0.5)', 'rgba(20, 20, 20, 0.5)', 'rgba(255, 206, 86, 0.2)'],
                    'borderColor' => ['rgba(100, 12, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)'],
                ],
            ],
        ];

        return $this->json($chartData);
    }

}
