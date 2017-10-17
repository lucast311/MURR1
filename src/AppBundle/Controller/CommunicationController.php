<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/communication", name="communication")
 */
class CommunicationController extends Controller
{
    public function indexAction(Request $request)
    {

    }

    /**
     * @Route("/communication/submit", name="communication_submit")
     */
    public function submitAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
    'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
]);
    }
}