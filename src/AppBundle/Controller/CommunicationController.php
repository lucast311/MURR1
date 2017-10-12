<?
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommunicationController extends Controller
{
    /**
     * @Route("/communication/submit", name="communication_submit")
     */
    public function submitAction(Request $request)
    {

    }
}