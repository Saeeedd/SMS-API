<?php

namespace App\Controller;

use App\Entity\Attempt;
use App\Service\ApplicationGlobal;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Redis;
use App\Entity\Sms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class HomeController extends AbstractController
{
    /**
     * Matches /send exactly
     * @Route("/send", name="show_results", Methods={"GET"})
     * @param Request $request Incoming HTTP Request
     * @return Response
     * @throws
     */
    public function show_results(Request $request)
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../../app.log', Logger::WARNING));
        $entityManager = $this->getDoctrine()->getManager();

        if ($request->query->has('number') and $request->query->has('body')) {
            $log->warning('New Message Request with body: ' . $request->query->has('body') .
                " to phone number: " . $request->query->has('number'));

            $sms = new Sms();

            $sms->setMessageBody($request->query->get('body'));
            $sms->setPhoneNumber($request->query->get('number'));
            $sms->setSentState(0);

            $entityManager->persist($sms);
            $entityManager->flush();

            $this->sendSms($sms, $log, true);

            $entityManager->persist($sms);
            $entityManager->flush();

        } else {
            $log->warning('Empty request -> resending top message');
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);

            $id = $redis->lPop('messageId');
            $sms = $this->getDoctrine()
                ->getRepository(Sms::class)
                ->findById($id);

            if ($sms != null) {
                $this->sendSms($sms, $log,true);

                $entityManager->persist($sms);
                $entityManager->flush();
            }
        }

        $messages = $this->getDoctrine()
            ->getRepository(Sms::class)
            ->getAllMessages();

        $api1Usage = $this->getDoctrine()
            ->getRepository(Sms::class)
            ->findApiUsage(1);

        $api1Attempts = $this->getDoctrine()
            ->getRepository(Attempt::class)
            ->findApiAttempts(1);

        $api2Attempts = $this->getDoctrine()
            ->getRepository(Attempt::class)
            ->findApiAttempts(2);

        $api2Usage = $this->getDoctrine()
            ->getRepository(Sms::class)
            ->findApiUsage(2);

        $mostUsedNumbers = $this->getDoctrine()
            ->getRepository(Sms::class)
            ->findMostUsedNumbers();

        return $this->render('homepage.html.twig',
            [
                'messages' => $messages,
                'numOfAll' => count($messages),
                'Api1AvailabilityRatio' => count($api1Attempts) == 0 ? 'Nan' : count($api1Usage) / count($api1Attempts),
                'Api2AvailabilityRatio' => count($api2Attempts) == 0 ? 'Nan' : count($api2Usage) / count($api2Attempts),
                'apiUsage1' => count($api1Usage),
                'apiUsage2' => count($api2Usage),
                'numbers' => $mostUsedNumbers
            ]);
    }

    private function sendSms($unsentMessage, $log, $forkFlag)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $client = new Client();
            $attempt = new Attempt();
            $attempt->setApi(1);
            $attempt->setMessageId($unsentMessage);

            $entityManager->persist($attempt);
            $entityManager->flush();

            $client->request('GET', 'http://localhost:81/send', [
                'query' => ['body' => $unsentMessage->getMessageBody(),
                    'number' => $unsentMessage->getPhoneNumber()]
            ]);

            $unsentMessage->setSentState(1);
            $unsentMessage->setApi(1);
        } catch (RequestException $exception) {
            try {
                $client = new Client();

                $attempt = new Attempt();
                $attempt->setApi(2);
                $attempt->setMessageId($unsentMessage);
                $entityManager->persist($attempt);
                $entityManager->flush();

                $client->request('GET', 'http://localhost:82/send', [
                    'query' => ['body' => $unsentMessage->getMessageBody(),
                        'number' => $unsentMessage->getPhoneNumber()]
                ]);
                $unsentMessage->setSentState(1);
                $unsentMessage->setApi(2);
            } catch (RequestException $exception) {
                if ($forkFlag) {
                    $log->warning("Message");
                    $redis = new Redis();
                    $redis->connect('127.0.0.1', 6379);
                    $redis->lpush('messageId', $unsentMessage->getId());
                }
            }
        }
    }

}




