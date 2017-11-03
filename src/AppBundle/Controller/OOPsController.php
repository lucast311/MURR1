<?php
namespace AppBundle\Controller;

use AppBundle\Entity\OOPs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser; 

class OOPsController extends Controller
{
    /**
     * @Route("/oops/add", name="contact_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        // create an OOPs and give it some dummy data for this example
        $oops = new OOPs('','');
        //$oops->setStatus('Not in progress');

        $oopsForm = $this->createFormBuilder($oops)
            ->add('binSerial', TextType::class,array(
                    'data' => ''
                    ))
            ->add('problemType', ChoiceType::class, array(
                    'choices' => OOPs::getProblemOptions()))
            ->add('description', TextType::class, array('required' => false))
            ->add('imageFile', FileType::class, array('required' => false))
            ->add('status', HiddenType::class, array(
                    'data' => 'Not yet started'))
            ->add('save', SubmitType::class, array('label' => 'Create OOPs Notice'))
            ->getForm();

        $oopsForm->handleRequest($request);

        if($oopsForm->isSubmitted() && $oopsForm->isValid())
        {
            
            //form submition
            $em = $this->getDoctrine()->getManager();

            $oops->upload();

            $em->getRepository(OOPs::class)->insert($oops);
            //return new Response('Created a new OOPs notice!');


            return $this->render('oops/addOOPsFormSuccess.html.twig');
        }

        return $this->render('oops/addOOPsForm.html.twig', array(
            'form' => $oopsForm->createView(),
        ));
    }

}