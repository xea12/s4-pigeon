<?php

// src/Controller/LogController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * @Route("/clear-logs", name="clear_logs")
     */
    #[Route('/clear-logs', name: 'clear-logs')]
    public function clearLogs(): Response
    {
        $logFile = $this->getParameter('kernel.project_dir') . '/var/log/dev.log';

        if (file_exists($logFile)) {
            file_put_contents($logFile, ''); // Clears the content of the log file
            $message = 'Log file cleared successfully.';
        } else {
            $message = 'Log file does not exist.';
        }

        return new Response($message);
    }
}