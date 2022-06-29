<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Action;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]

    public function index(HttpClientInterface $client, ManagerRegistry $doctrine): Response
    {
        if (isset($_POST["nom"]) && isset($_POST["montant"])) {

            $entreprise = $_POST["nom"];
            $montant = $_POST["montant"];
            dump('https://yfapi.net/v6/finance/quote?region=US&lang=en&symbols='.$entreprise);
            dump($_POST["nom"]);

        $response = $client->request(
            'GET', 
            'https://yfapi.net/v6/finance/quote?region=US&lang=en&symbols='.$entreprise,
            [
                'headers' => ['x-api-key' => 'sa0xLYwIeT2h7mOtgMGZu8vjUKHlIiIf1Eah7BHV',]
            ]
    );

        if ($response->getStatusCode() === 200) {
            $result = $response->toArray();
            $content = $result["quoteResponse"]["result"][0];

            $name = $content["shortName"];
            $value = $content["regularMarketPrice"];
            $amount = $montant;
            $date = date("Y-m-d");




            $entityManager = $doctrine->getManager();

            $action = new Action();
                $action->setName($name);
                $action->setDate($date);
                $action->setActionValue($value);
                $action->setAmount($amount);

                $entityManager->persist($action);
                $entityManager->flush();

            $count = "SELECT count(id) FROM action";
            $db_count = mysql_query($count);

            for ($i = 1; $i <= $db_count; $i++) {
                $action = $doctrine->getRepository(Action::class)->find($id);
                $nom = $action->getName();
                $amount = $action->getAmount();

                $div = "<div class=\"flex flex-row w-full mt-5 mb-12\">
                <div class=\"flex w-1/2 justify-center text-xl font-bold\"><h5>".$nom."</h5></div>
                <div class=\"flex w-1/2 justify-center text-xl font-bold\"><h5>".$amount."</h5></div>
                </div>";
            }

            return $this->render('home/index.html.twig', [
            ]);

        }
            
        }else{

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'display' => $div;
            ]);

        } 

        }
        
    }

    