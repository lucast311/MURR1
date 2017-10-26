<?php
namespace AppBundle\Controller;

use AppBundle\Entity\OOPs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class OOPsController extends Controller
{
    /**
     * @Route("/OOPsForm")
     * @param Request $request 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $oops = new OOPs();
        $oops->setStatus('Unresolved');

        $oopsForm = $this->createFormBuilder($oops)
            ->add('binSerial', TextType::class)
            ->add('problemType', TextType::class)
            ->add('description', TextType::class)
            ->add('image', FileType::class)
            ->add('save', SubmitType::class, array('label' => 'Create OOPs Notice'))
            ->getForm();

        return $this->render('default/new.html.twig', array(
            'form' => $oopsForm->createView(),
        ));
    }
}